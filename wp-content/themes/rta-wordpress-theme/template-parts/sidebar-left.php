<?php
/**
 * Left sidebar template part.
 *
 * @package RTA
 */
?>

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
