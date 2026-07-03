<?php
/**
 * Header template.
 *
 * @package RTA
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	$theme_uri = get_template_directory_uri();
	$theme_dir = get_template_directory();
	$v = '?v=' . filemtime( $theme_dir . '/favicon-512.png' );
	?>
	<link rel="icon" href="<?php echo esc_url( $theme_uri . '/favicon.ico' . $v ); ?>" sizes="48x48">
	<link rel="icon" href="<?php echo esc_url( $theme_uri . '/favicon-16x16.png' . $v ); ?>" sizes="16x16" type="image/png">
	<link rel="icon" href="<?php echo esc_url( $theme_uri . '/favicon-32x32.png' . $v ); ?>" sizes="32x32" type="image/png">
	<link rel="icon" href="<?php echo esc_url( $theme_uri . '/favicon-96x96.png' . $v ); ?>" sizes="96x96" type="image/png">
	<link rel="icon" href="<?php echo esc_url( $theme_uri . '/favicon-512.png' . $v ); ?>" sizes="512x512" type="image/png">
	<link rel="apple-touch-icon" href="<?php echo esc_url( $theme_uri . '/apple-touch-icon-180x180.png' . $v ); ?>">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo esc_url( $theme_uri . '/apple-touch-icon-60x60.png' . $v ); ?>">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo esc_url( $theme_uri . '/apple-touch-icon-76x76.png' . $v ); ?>">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo esc_url( $theme_uri . '/apple-touch-icon-120x120.png' . $v ); ?>">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo esc_url( $theme_uri . '/apple-touch-icon-152x152.png' . $v ); ?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $theme_uri . '/apple-touch-icon-180x180.png' . $v ); ?>">
	<link rel="manifest" href="<?php echo esc_url( $theme_uri . '/site.webmanifest' . $v ); ?>">
	<meta name="msapplication-TileColor" content="#c40000">
	<meta name="msapplication-TileImage" content="<?php echo esc_url( $theme_uri . '/mstile-150x150.png' . $v ); ?>">
	<meta name="theme-color" content="#c40000">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'rta-site' ); ?>>
<?php wp_body_open(); ?>
<div class="rta-top-bar" aria-hidden="true"></div>
<header class="rta-header" role="banner">
	<div class="rta-header__inner">
		<div class="rta-logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/logo.png' ); ?>"
					alt="<?php bloginfo( 'name' ); ?>"
					class="rta-logo__img">
			</a>
		</div>
		<div class="rta-branding">
			<?php if ( is_front_page() && is_home() ) : ?>
				<h1 class="rta-branding__title"><?php bloginfo( 'name' ); ?></h1>
			<?php else : ?>
				<p class="rta-branding__title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
				</p>
			<?php endif; ?>
			<?php
			$description = get_bloginfo( 'description', 'display' );
			if ( $description || is_customize_preview() ) :
				?>
				<p class="rta-branding__tagline"><?php echo esc_html( $description ); ?></p>
			<?php else : ?>
				<p class="rta-branding__tagline"><?php esc_html_e( 'As melhores opções para suas viagens!', 'rta' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</header>
<button class="rta-menu-toggle" id="rta-menu-toggle"
	aria-controls="rta-nav-drawer" aria-expanded="false"
	aria-label="<?php esc_attr_e( 'Abrir menu', 'rta' ); ?>">
	<span class="rta-hamburger-box">
		<span class="rta-hamburger-inner"></span>
	</span>
</button>
<nav class="rta-nav" id="rta-nav-drawer"
	aria-label="<?php esc_attr_e( 'Menu principal', 'rta' ); ?>"
	role="navigation">
	<div class="rta-nav__header">
		<span class="rta-nav__title"><?php esc_html_e( 'Menu', 'rta' ); ?></span>
		<button class="rta-nav__close" id="rta-nav-close"
			aria-label="<?php esc_attr_e( 'Fechar menu', 'rta' ); ?>">
			&times;
		</button>
	</div>
	<div class="rta-nav__inner">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'rta-menu',
				'fallback_cb'    => 'rta_default_menu',
				'depth'          => 2,
			)
		);
		?>
	</div>
</nav>
<div class="rta-overlay" id="rta-overlay" aria-hidden="true"></div>
<main class="rta-main" id="content">
