<?php
/**
 * 404 template.
 *
 * @package RTA
 */

get_header();
?>

<div class="rta-content-area" style="text-align:center;padding:60px 0;">
	<h1><?php esc_html_e( 'Página não encontrada', 'rta' ); ?></h1>
	<p><?php esc_html_e( 'Desculpe, o conteúdo que você procura não está disponível.', 'rta' ); ?></p>
	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rta-404-link" style="color:var(--rta-red);font-weight:700;">&larr; <?php esc_html_e( 'Voltar para o início', 'rta' ); ?></a></p>
	<?php get_search_form(); ?>
</div>

<?php
get_footer();
