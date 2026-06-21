<?php
/**
 * Page template.
 *
 * @package RTA
 */

get_header();
?>

<div class="rta-content-area">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</article>
	<?php endwhile; ?>
</div>

<?php
get_footer();
