<?php
/**
 * Right sidebar template part.
 *
 * @package RTA
 */
?>

<aside class="rta-sidebar-right" aria-label="<?php esc_attr_e( 'Widgets', 'rta' ); ?>">
	<?php if ( is_active_sidebar( 'sidebar-right' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-right' ); ?>
	<?php else : ?>
		<div class="rta-widget">
			<div class="rta-widget__header"><?php esc_html_e( 'Relógio', 'rta' ); ?></div>
			<div class="rta-widget__body">
				<div class="rta-clock" id="rta-clock">--:--</div>
			</div>
		</div>

		<div class="rta-widget">
			<div class="rta-widget__header">CLIMATEMPO</div>
			<div class="rta-widget__body">
				<p class="rta-weather__city">SP - São Paulo</p>
				<p class="rta-weather__date">17/08 Sex</p>
				<p class="rta-weather__temp">13° / 18°</p>
				<p class="rta-weather__desc">0%, 2mm</p>
				<p class="rta-weather__desc"><?php esc_html_e( 'Céu nublado com possibilidade de garoa de dia e à noite.', 'rta' ); ?></p>
				<span class="rta-weather__video"><?php esc_html_e( 'VER VÍDEO', 'rta' ); ?></span>
			</div>
		</div>
	<?php endif; ?>
</aside>
