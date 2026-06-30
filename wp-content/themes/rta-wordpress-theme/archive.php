<?php
/**
 * Archive template.
 *
 * @package RTA
 */

get_header();
?>

<div class="rta-content-area">
	<header class="rta-archive-header">
		<?php
		the_archive_title( '<h1>', '</h1>' );
		the_archive_description( '<div class="rta-archive-desc">', '</div>' );
		?>
	</header>

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p class="rta-post-meta">
					<?php echo esc_html( get_the_date() ); ?>
					<?php if ( has_category() ) : ?>
						| <?php the_category( ', ' ); ?>
					<?php endif; ?>
				</p>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>

		<?php the_posts_navigation(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nenhum conteúdo encontrado.', 'rta' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
