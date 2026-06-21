<?php
/**
 * Front page template.
 *
 * @package RTA
 */

get_header();
?>

<section class="rta-home-grid">
	<aside class="rta-sidebar-left" aria-label="<?php esc_attr_e( 'Apoio', 'rta' ); ?>">
		<p class="rta-sidebar-left__label"><?php esc_html_e( 'apoio', 'rta' ); ?></p>

		<?php if ( is_active_sidebar( 'sidebar-left' ) ) : ?>
			<?php dynamic_sidebar( 'sidebar-left' ); ?>
		<?php else : ?>
			<div class="rta-partner">
				<p class="rta-partner__name rta-partner__name--aafbb">aafbb</p>
				<p class="rta-partner__desc"><?php esc_html_e( 'Associação dos Antigos Funcionários do Banco do Brasil', 'rta' ); ?></p>
			</div>

			<div class="rta-partner">
				<div class="rta-partner__logo-box" aria-hidden="true"><span>👥</span></div>
				<p class="rta-partner__name">APOSCEG</p>
				<p class="rta-partner__desc"><?php esc_html_e( 'Associação dos Funcionários Aposentados da Companhia Estadual de Gás do RJ', 'rta' ); ?></p>
			</div>
		<?php endif; ?>
	</aside>

	<div class="rta-center">
		<?php echo rta_get_visitor_counter_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<p class="rta-counter__caption">
			<?php esc_html_e( 'pessoas já passaram por aqui!', 'rta' ); ?>
			<span class="rta-counter__credit">AnalogMix.com</span>
		</p>

		<div class="rta-hero-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<div class="rta-hero-logo__fallback" aria-hidden="true"><span>RTA</span></div>
			<?php endif; ?>
		</div>
	</div>

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
</section>

<?php if ( have_posts() ) : ?>
	<div class="rta-content-area">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
<?php endif; ?>

<script>
(function () {
	var clock = document.getElementById('rta-clock');
	if (!clock) return;

	function updateClock() {
		var now = new Date();
		clock.textContent = now.toLocaleTimeString('pt-BR', {
			hour: '2-digit',
			minute: '2-digit'
		});
	}

	updateClock();
	setInterval(updateClock, 1000);
})();
</script>

<?php
get_footer();
