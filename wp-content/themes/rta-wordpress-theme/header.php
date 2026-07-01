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
	<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/favicon.ico' ); ?>" sizes="32x32">
	<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/favicon-512.png' ); ?>" sizes="512x512" type="image/png">
	<link rel="apple-touch-icon" href="<?php echo esc_url( get_template_directory_uri() . '/favicon-512.png' ); ?>">
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
<nav class="rta-nav" aria-label="<?php esc_attr_e( 'Menu principal', 'rta' ); ?>">
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
<main class="rta-main" id="content">
