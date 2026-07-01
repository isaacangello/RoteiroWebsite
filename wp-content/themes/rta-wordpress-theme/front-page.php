<?php
/**
 * Front page template.
 *
 * @package RTA
 */

get_header();
?>

<section class="rta-home-grid">
	<?php get_template_part( 'template-parts/sidebar-left' ); ?>

	<div class="rta-center">
		<?php echo rta_get_visitor_counter_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<p class="rta-counter__caption">
			<?php esc_html_e( 'pessoas já passaram por aqui!', 'rta' ); ?>
		</p>

		<div class="rta-hero-logo">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/logo.png' ); ?>"
				alt="<?php bloginfo( 'name' ); ?>"
				class="rta-screenshot">
		</div>
	</div>

	<?php get_template_part( 'template-parts/sidebar-right' ); ?>
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
