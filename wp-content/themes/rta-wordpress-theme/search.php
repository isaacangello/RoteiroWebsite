<?php
/**
 * Search results template.
 *
 * @package RTA
 */

get_header();
?>

<div class="rta-content-area">
	<header class="rta-search-header">
		<h1>
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Resultados da busca: %s', 'rta' ),
				'<span>' . get_search_query() . '</span>'
			);
			?>
		</h1>
		<?php get_search_form(); ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p class="rta-post-meta"><?php echo esc_html( get_the_date() ); ?></p>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>

		<?php the_posts_navigation(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nenhum resultado encontrado. Tente novamente com outros termos.', 'rta' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
