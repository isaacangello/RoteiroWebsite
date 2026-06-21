<?php
/**
 * Footer template.
 *
 * @package RTA
 */
?>
</main>

<footer class="rta-footer" role="contentinfo">
	<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Todos os direitos reservados.', 'rta' ); ?></p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
