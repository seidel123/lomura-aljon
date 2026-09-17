<?php
/**
 * Lumora Aljon Child theme setup, second concept (cinematic contemporary).
 *
 * @package LumoraAljonChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NOVA_CHILD_VERSION', '1.6.2' );

/**
 * Enqueue parent + child styles, brand stylesheet and Google Fonts.
 *
 * Fonts (no plugin):
 * - Space Grotesk (display)
 * - Inter (body / navigation)
 */
function nova_child_enqueue_assets() {
	wp_enqueue_style(
		'hello-elementor',
		get_template_directory_uri() . '/assets/css/reset.css',
		[],
		'3.5.1'
	);

	wp_enqueue_style(
		'hello-elementor-theme-style',
		get_template_directory_uri() . '/assets/css/theme.css',
		[ 'hello-elementor' ],
		'3.5.1'
	);

	wp_enqueue_style(
		'nova-child-style',
		get_stylesheet_uri(),
		[ 'hello-elementor-theme-style' ],
		NOVA_CHILD_VERSION
	);

	wp_enqueue_style(
		'nova-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap',
		[],
		null
	);

	wp_enqueue_style(
		'nova-brand',
		get_stylesheet_directory_uri() . '/assets/css/nova.css',
		[ 'nova-child-style', 'nova-fonts' ],
		NOVA_CHILD_VERSION
	);

	wp_enqueue_style(
		'aljon-cinema',
		get_stylesheet_directory_uri() . '/assets/css/aljon-cinema.css',
		[ 'nova-brand' ],
		NOVA_CHILD_VERSION
	);

	wp_enqueue_style(
		'aljon-daylight',
		get_stylesheet_directory_uri() . '/assets/css/aljon-daylight.css',
		[ 'aljon-cinema' ],
		NOVA_CHILD_VERSION
	);

	wp_enqueue_script(
		'nova-slider',
		get_stylesheet_directory_uri() . '/assets/js/nova-slider.js',
		[],
		NOVA_CHILD_VERSION,
		true
	);

	wp_enqueue_script(
		'nova-enhance',
		get_stylesheet_directory_uri() . '/assets/js/nova-enhance.js',
		[],
		NOVA_CHILD_VERSION,
		true
	);
	wp_localize_script(
		'nova-enhance',
		'NOVA_ENHANCE',
		[
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'lumora_inquiry' ),
		]
	);
}
add_action( 'wp_enqueue_scripts', 'nova_child_enqueue_assets', 20 );

/**
 * Preconnect to Google Fonts.
 */
function nova_child_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = 'https://fonts.gstatic.com';
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'nova_child_resource_hints', 10, 2 );

/**
 * Canvas pages bypass theme header/footer; in-page Elementor header/footer wins.
 */
function nova_child_hide_theme_header_footer( $display ) {
	if ( is_page_template( 'elementor_canvas' ) ) {
		return false;
	}
	return $display;
}
add_filter( 'hello_elementor_header_footer', 'nova_child_hide_theme_header_footer' );

/**
 * Live-Link-safe frontend asset URLs: rewrite same-site absolute URLs to
 * root-relative so Local Live Links resolve on any host.
 */
function nova_use_relative_frontend_urls() {
	if ( is_admin() ) {
		return false;
	}
	if ( is_feed() ) {
		return false;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return false;
	}
	return true;
}

function nova_maybe_relative_url( $url ) {
	if ( ! is_string( $url ) || '' === $url || '/' === $url[0] ) {
		return $url;
	}
	$url_host = wp_parse_url( $url, PHP_URL_HOST );
	if ( ! $url_host ) {
		return $url;
	}
	$home_host   = wp_parse_url( home_url(), PHP_URL_HOST );
	$local_hosts = array_filter( [ $home_host, 'localhost', 'lumora-aljon.local' ] );
	$match       = false;
	foreach ( $local_hosts as $host ) {
		if ( 0 === strcasecmp( (string) $url_host, (string) $host ) ) {
			$match = true;
			break;
		}
	}
	if ( ! $match ) {
		return $url;
	}
	$path     = wp_parse_url( $url, PHP_URL_PATH );
	$query    = wp_parse_url( $url, PHP_URL_QUERY );
	$fragment = wp_parse_url( $url, PHP_URL_FRAGMENT );
	if ( ! $path ) {
		return $url;
	}
	$relative = $path;
	if ( $query ) {
		$relative .= '?' . $query;
	}
	if ( $fragment ) {
		$relative .= '#' . $fragment;
	}
	return $relative;
}

function nova_relative_attachment_image_src( $image, $attachment_id, $size, $icon ) {
	if ( ! nova_use_relative_frontend_urls() || ! is_array( $image ) || empty( $image[0] ) ) {
		return $image;
	}
	$image[0] = nova_maybe_relative_url( $image[0] );
	return $image;
}
add_filter( 'wp_get_attachment_image_src', 'nova_relative_attachment_image_src', 10, 4 );

function nova_relative_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
	if ( ! nova_use_relative_frontend_urls() || ! is_array( $sources ) ) {
		return $sources;
	}
	foreach ( $sources as $width => $source ) {
		if ( isset( $source['url'] ) ) {
			$sources[ $width ]['url'] = nova_maybe_relative_url( $source['url'] );
		}
	}
	return $sources;
}
add_filter( 'wp_calculate_image_srcset', 'nova_relative_image_srcset', 10, 5 );

function nova_relative_loader_src( $src, $handle ) {
	if ( ! nova_use_relative_frontend_urls() || ! is_string( $src ) ) {
		return $src;
	}
	return nova_maybe_relative_url( $src );
}
add_filter( 'script_loader_src', 'nova_relative_loader_src', 10, 2 );
add_filter( 'style_loader_src', 'nova_relative_loader_src', 10, 2 );

/* ============================================================
 * LUMORA Aljon enhancements:
 * SEO head, hero preload, inquiry form, mobile/footer UI.
 * ============================================================ */

/**
 * SEO + social head tags on the front page (test site had none).
 */
function nova_seo_head() {
	if ( ! is_front_page() && ! is_home() ) {
		return;
	}
	$desc  = 'LUMORA: distinctive Australian homes across Noosa, Adelaide and Hobart. Selected residences, field notes and private inspections.';
	$hero  = nova_abs_url( nova_hero_image_url() );
	$title = 'LUMORA: Distinctive Australian Homes';
	echo "\n" . '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta name="theme-color" content="#FFFFFF">' . "\n";
	echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:site_name" content="LUMORA">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( home_url( '/' ) ) . '">' . "\n";
	if ( $hero ) {
		echo '<meta property="og:image" content="' . esc_url( $hero ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	if ( $hero ) {
		echo '<meta name="twitter:image" content="' . esc_url( $hero ) . '">' . "\n";
	}
	$schema = [
		'@context'    => 'https://schema.org',
		'@graph'      => [
			[
				'@type'       => 'RealEstateAgent',
				'name'        => 'LUMORA Real Estate',
				'url'         => home_url( '/' ),
				'email'       => 'hello@lumora.com.au',
				'telephone'   => '+61 7 5440 8899',
				'address'     => [
					'@type'           => 'PostalAddress',
					'streetAddress'   => '14 Hastings Street',
					'addressLocality' => 'Noosa QLD 4567',
					'addressCountry'  => 'AU',
				],
				'areaServed'  => [ 'Noosa', 'Adelaide', 'Hobart' ],
				'openingHours' => 'Mo-Sa 08:30-17:30',
			],
			[
				'@type'           => 'WebSite',
				'name'            => 'LUMORA',
				'url'             => home_url( '/' ),
				'inLanguage'      => 'en-AU',
			],
		],
	];
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'nova_seo_head', 5 );

/**
 * Front-page document title (replaces thin bare site name).
 */
function nova_front_title( $title ) {
	if ( is_front_page() ) {
		return 'LUMORA: Exceptional Homes & Property in Noosa';
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'nova_front_title', 20 );

/**
 * Force absolute URLs for social/preload tags (validators reject root-relative).
 */
function nova_abs_url( $url ) {
	if ( is_string( $url ) && '' !== $url && '/' === $url[0] && 0 !== strpos( $url, '//' ) ) {
		return home_url( $url );
	}
	return $url;
}

/**
 * Main landmark for the Canvas landing page (canvas template ships none).
 * In-page header/footer stay inside; valid scoped landmarks.
 */
function nova_canvas_main_open() {
	if ( is_front_page() ) {
		echo '<main class="cn-main">';
	}
}
add_action( 'elementor/page_templates/canvas/before_content', 'nova_canvas_main_open', 5 );

function nova_canvas_main_close() {
	if ( is_front_page() ) {
		echo '</main>';
	}
}
add_action( 'elementor/page_templates/canvas/after_content', 'nova_canvas_main_close', 5 );

/**
 * Hero image URL (attachment 6, alj-hero-glass) for preload/OG.
 */
function nova_hero_image_url() {
	$url = wp_get_attachment_image_url( 6, 'full' );
	return $url ? $url : '';
}

/**
 * Preload LCP hero image with responsive srcset.
 */
function nova_preload_hero() {
	if ( ! is_front_page() && ! is_home() ) {
		return;
	}
	$srcset = wp_get_attachment_image_srcset( 6, 'full' );
	$sizes  = wp_get_attachment_image_sizes( 6, 'full' );
	$src    = nova_hero_image_url();
	if ( ! $src ) {
		return;
	}
	$tag = '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url( $src ) . '"';
	if ( $srcset ) {
		$tag .= ' imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="' . esc_attr( $sizes ? $sizes : '100vw' ) . '"';
	}
	$tag .= '>';
	echo "\n" . $tag . "\n";
}
add_action( 'wp_head', 'nova_preload_hero', 6 );

/**
 * Image loading strategy: hero eager + high priority, everything else async.
 */
function nova_image_loading_attrs( $attr, $attachment ) {
	$attr['decoding'] = 'async';
	if ( isset( $attachment->ID ) && 6 === (int) $attachment->ID ) {
		$attr['loading']       = 'eager';
		$attr['fetchpriority'] = 'high';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'nova_image_loading_attrs', 10, 2 );

/**
 * Private inquiry store (no plugin needed for the contact form).
 */
function nova_register_inquiry_type() {
	register_post_type(
		'lumora_inquiry',
		[
			'labels'       => [
				'name'          => 'Inquiries',
				'singular_name' => 'Inquiry',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => 'edit.php?post_type=page',
			'supports'     => [ 'title', 'editor' ],
			'capabilities' => [
				'create_posts' => 'do_not_allow',
			],
			'map_meta_cap' => true,
		]
	);
}
add_action( 'init', 'nova_register_inquiry_type' );

/**
 * [lumora_contact_form] — accessible inquiry form with honeypot + AJAX.
 */
function nova_contact_form_shortcode() {
	ob_start();
	$sent  = isset( $_GET['lumora_sent'] ) ? (int) $_GET['lumora_sent'] : 0;
	$error = isset( $_GET['lumora_error'] ) ? sanitize_key( $_GET['lumora_error'] ) : '';
	if ( 1 === $sent ) {
		echo '<p class="nova-form-success" role="status">Received, thank you. A consultant will reply within one business day. Prefer to talk? <a href="tel:+61754408899">+61 7 5440 8899</a>.</p>';
		return ob_get_clean();
	}
	if ( $error ) {
		echo '<p class="nova-form-error" role="alert">Something was missing: please add your name, a valid email and a few words.</p>';
	}
	?>
	<form class="nova-form" id="lumora-inquiry-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="lumora_inquiry">
		<?php wp_nonce_field( 'lumora_inquiry', 'lumora_nonce' ); ?>
		<p class="nova-form-honey" aria-hidden="true"><label>Leave empty<input type="text" name="lumora_company" value="" tabindex="-1" autocomplete="off"></label></p>
		<div class="nova-form-grid">
			<p><label for="lumora-name">Your name *</label><input id="lumora-name" name="lumora_name" type="text" autocomplete="name" required maxlength="120"></p>
			<p><label for="lumora-email">Email address *</label><input id="lumora-email" name="lumora_email" type="email" autocomplete="email" required maxlength="160"></p>
		</div>
		<div class="nova-form-grid">
			<p><label for="lumora-phone">Phone <span class="nova-form-opt">(optional)</span></label><input id="lumora-phone" name="lumora_phone" type="tel" autocomplete="tel" maxlength="40"></p>
			<p><label for="lumora-interest">I am interested in</label>
				<select id="lumora-interest" name="lumora_interest">
					<option>Arranging a private inspection</option>
					<option>Buying a home</option>
					<option>Selling a home</option>
					<option>A market appraisal</option>
				</select>
			</p>
		</div>
		<p><label for="lumora-message">Tell us about your search *</label><textarea id="lumora-message" name="lumora_message" rows="4" required maxlength="2000" placeholder="Tell us how you live: suburbs, timing and must-haves."></textarea></p>
		<p class="nova-form-foot">
			<button class="nova-form-submit" type="submit">Send message →</button>
			<span class="nova-form-alt">or <a href="mailto:hello@lumora.com.au">email the studio</a></span>
		</p>
		<p class="nova-form-status" id="lumora-form-status" role="status" aria-live="polite"></p>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'lumora_contact_form', 'nova_contact_form_shortcode' );

/**
 * Validate + store an inquiry, then redirect back with status.
 */
function nova_handle_inquiry_payload( $post ) {
	$name     = isset( $post['lumora_name'] ) ? sanitize_text_field( wp_unslash( $post['lumora_name'] ) ) : '';
	$email    = isset( $post['lumora_email'] ) ? sanitize_email( wp_unslash( $post['lumora_email'] ) ) : '';
	$phone    = isset( $post['lumora_phone'] ) ? sanitize_text_field( wp_unslash( $post['lumora_phone'] ) ) : '';
	$interest = isset( $post['lumora_interest'] ) ? sanitize_text_field( wp_unslash( $post['lumora_interest'] ) ) : '';
	$message  = isset( $post['lumora_message'] ) ? sanitize_textarea_field( wp_unslash( $post['lumora_message'] ) ) : '';
	$honey    = isset( $post['lumora_company'] ) ? sanitize_text_field( wp_unslash( $post['lumora_company'] ) ) : '';

	if ( '' !== $honey || '' === $name || ! is_email( $email ) || '' === $message ) {
		return new WP_Error( 'invalid', 'invalid' );
	}
	$id = wp_insert_post(
		[
			'post_title'   => $name . ' (' . $interest . ')',
			'post_content' => "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nInterest: {$interest}\n\n{$message}",
			'post_status'  => 'private',
			'post_type'    => 'lumora_inquiry',
			'meta_input'   => [
				'_lumora_email'    => $email,
				'_lumora_phone'    => $phone,
				'_lumora_interest' => $interest,
			],
		],
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	wp_mail(
		'hello@lumora.com.au',
		'New LUMORA message: ' . $name,
		"Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nInterest: {$interest}\n\n{$message}",
		[ 'Reply-To: ' . $name . ' <' . $email . '>' ]
	);
	return true;
}

function nova_inquiry_redirect_base() {
	$ref = wp_get_referer();
	if ( ! $ref ) {
		$ref = home_url( '/#contact' );
	}
	return remove_query_arg( [ 'lumora_sent', 'lumora_error' ], $ref );
}

function nova_handle_inquiry_post() {
	if ( ! isset( $_POST['lumora_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['lumora_nonce'] ) ), 'lumora_inquiry' ) ) {
		wp_safe_redirect( add_query_arg( 'lumora_error', '1', nova_inquiry_redirect_base() . '#contact' ) );
		exit;
	}
	$result = nova_handle_inquiry_payload( $_POST );
	if ( is_wp_error( $result ) ) {
		wp_safe_redirect( add_query_arg( 'lumora_error', '1', nova_inquiry_redirect_base() . '#contact' ) );
	} else {
		wp_safe_redirect( add_query_arg( 'lumora_sent', '1', nova_inquiry_redirect_base() . '#contact' ) );
	}
	exit;
}
add_action( 'admin_post_lumora_inquiry', 'nova_handle_inquiry_post' );
add_action( 'admin_post_nopriv_lumora_inquiry', 'nova_handle_inquiry_post' );

function nova_handle_inquiry_ajax() {
	check_ajax_referer( 'lumora_inquiry', 'nonce' );
	$result = nova_handle_inquiry_payload( $_POST );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( [ 'message' => 'Please add your name, a valid email and a few words.' ], 422 );
	}
	wp_send_json_success( [ 'message' => 'Received, thank you. A consultant will reply within one business day.' ] );
}
add_action( 'wp_ajax_lumora_inquiry', 'nova_handle_inquiry_ajax' );
add_action( 'wp_ajax_nopriv_lumora_inquiry', 'nova_handle_inquiry_ajax' );

/**
 * Injected UI: scroll progress, mobile menu mount point, sticky mobile CTA, back-to-top.
 * Markup is theme-level so Elementor content stays fully editable.
 */
function nova_inject_frontend_ui() {
	if ( is_admin() || ! is_front_page() ) {
		return;
	}
	?>
	<div class="nova-progress" aria-hidden="true"><i data-nova-progress></i></div>
	<div class="nova-mobilebar" role="navigation" aria-label="Quick contact">
		<a class="nova-mobilebar-call" href="tel:+61754408899">Call LUMORA</a>
		<a class="nova-mobilebar-enquire" href="#contact">Enquire →</a>
	</div>
	<button class="nova-top" type="button" data-nova-top aria-label="Back to top" hidden>
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 19V5m-7 7 7-7 7 7"/></svg>
	</button>
	<?php
}
add_action( 'wp_footer', 'nova_inject_frontend_ui', 5 );
