<?php
/**
 * RTA theme functions.
 *
 * @package RTA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme setup.
 */
function rta_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 120,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'custom-header',
		array(
			'default-image' => '',
			'header-text'   => false,
			'width'         => 980,
			'height'        => 8,
			'flex-height'   => true,
		)
	);

	add_theme_support( 'custom-background' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'editor-style.css' );

	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Vermelho RTA', 'rta' ),
				'slug'  => 'rta-red',
				'color' => '#c40000',
			),
			array(
				'name'  => __( 'Vermelho Escuro', 'rta' ),
				'slug'  => 'rta-red-dark',
				'color' => '#7a0000',
			),
			array(
				'name'  => __( 'Laranja', 'rta' ),
				'slug'  => 'rta-orange',
				'color' => '#ff6600',
			),
			array(
				'name'  => __( 'Branco', 'rta' ),
				'slug'  => 'rta-white',
				'color' => '#ffffff',
			),
			array(
				'name'  => __( 'Texto', 'rta' ),
				'slug'  => 'rta-text',
				'color' => '#333333',
			),
			array(
				'name'  => __( 'Borda', 'rta' ),
				'slug'  => 'rta-border',
				'color' => '#d9d9d9',
			),
		)
	);

	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name'      => __( 'Pequeno', 'rta' ),
				'shortName' => __( 'P', 'rta' ),
				'size'      => 12,
				'slug'      => 'small',
			),
			array(
				'name'      => __( 'Normal', 'rta' ),
				'shortName' => __( 'N', 'rta' ),
				'size'      => 14,
				'slug'      => 'normal',
			),
			array(
				'name'      => __( 'Médio', 'rta' ),
				'shortName' => __( 'M', 'rta' ),
				'size'      => 18,
				'slug'      => 'medium',
			),
			array(
				'name'      => __( 'Grande', 'rta' ),
				'shortName' => __( 'G', 'rta' ),
				'size'      => 24,
				'slug'      => 'large',
			),
			array(
				'name'      => __( 'Muito Grande', 'rta' ),
				'shortName' => __( 'XG', 'rta' ),
				'size'      => 32,
				'slug'      => 'x-large',
			),
		)
	);

	add_theme_support(
		'post-formats',
		array( 'aside', 'gallery', 'image', 'link', 'quote', 'video' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Menu Principal', 'rta' ),
		)
	);
}
add_action( 'after_setup_theme', 'rta_theme_setup' );

/**
 * Enqueue theme assets.
 */
function rta_enqueue_assets() {
	wp_enqueue_style(
		'rta-fonts',
		'https://fonts.googleapis.com/css2?family=Great+Vibes&family=Montserrat:wght@400;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'rta-style',
		get_stylesheet_uri(),
		array( 'rta-fonts' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'rta_enqueue_assets' );

/**
 * Enqueue editor styles for the block editor.
 */
function rta_editor_styles() {
	add_editor_style( 'editor-style.css' );
}
add_action( 'after_setup_theme', 'rta_editor_styles' );

/**
 * Register widget areas.
 */
function rta_register_sidebars() {
	register_sidebar(
		array(
			'name'          => __( 'Barra Lateral Esquerda', 'rta' ),
			'id'            => 'sidebar-left',
			'description'   => __( 'Área para logos de apoio e parceiros.', 'rta' ),
			'before_widget' => '<div id="%1$s" class="widget rta-partner %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="rta-partner__name">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Barra Lateral Direita', 'rta' ),
			'id'            => 'sidebar-right',
			'description'   => __( 'Área para relógio, clima e widgets.', 'rta' ),
			'before_widget' => '<div id="%1$s" class="widget rta-widget %2$s">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<div class="rta-widget__header">',
			'after_title'   => '</div><div class="rta-widget__body">',
		)
	);
}
add_action( 'widgets_init', 'rta_register_sidebars' );

/**
 * Register custom block styles.
 */
function rta_block_styles() {
	register_block_style(
		'core/list',
		array(
			'name'         => 'rta-checkmark-list',
			'label'        => __( 'Lista com checkmarks', 'rta' ),
			'inline_style' => '
			ul.is-style-rta-checkmark-list {
				list-style-type: "\2713";
				color: var(--rta-red, #c40000);
			}
			ul.is-style-rta-checkmark-list li {
				padding-inline-start: 1ch;
			}',
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'         => 'rta-button-outline',
			'label'        => __( 'Contorno RTA', 'rta' ),
			'inline_style' => '
			.wp-block-button.is-style-rta-button-outline .wp-block-button__link {
				background: transparent;
				color: var(--rta-red, #c40000);
				border: 2px solid var(--rta-red, #c40000);
			}
			.wp-block-button.is-style-rta-button-outline .wp-block-button__link:hover {
				background: var(--rta-red, #c40000);
				color: #fff;
			}',
		)
	);
}
add_action( 'init', 'rta_block_styles' );

/**
 * Register pattern categories.
 */
function rta_pattern_categories() {
	register_block_pattern_category(
		'rta_destinos',
		array(
			'label'       => __( 'Destinos RTA', 'rta' ),
			'description' => __( 'Padrões de destinos de viagem.', 'rta' ),
		)
	);

	register_block_pattern_category(
		'rta_cta',
		array(
			'label'       => __( 'Chamadas RTA', 'rta' ),
			'description' => __( 'Padrões de chamada para ação.', 'rta' ),
		)
	);
}
add_action( 'init', 'rta_pattern_categories' );

/**
 * Fallback menu matching the original site structure.
 */
function rta_default_menu() {
	$items = array(
		array( 'label' => 'Início', 'url' => home_url( '/' ) ),
		array( 'label' => 'Sobre nós', 'url' => home_url( '/sobre-nos/' ) ),
		array( 'label' => 'Fale Conosco', 'url' => home_url( '/fale-conosco/' ) ),
		array( 'label' => 'Últimas Notícias', 'url' => home_url( '/ultimas-noticias/' ) ),
		array( 'label' => 'Hoteis Fazenda', 'url' => home_url( '/hoteis-fazenda/' ) ),
		array( 'label' => 'Amazônia +', 'url' => home_url( '/amazonia/' ) ),
		array( 'label' => 'Bahia - Costa Do Descobrimento +', 'url' => home_url( '/bahia-costa-do-descobrimento/' ) ),
		array( 'label' => 'Cachoeira Paulista', 'url' => home_url( '/cachoeira-paulista/' ) ),
		array( 'label' => 'Campos do Jordão', 'url' => home_url( '/campos-do-jordao/' ) ),
		array( 'label' => 'Circuito das Águas +', 'url' => home_url( '/circuito-das-aguas/' ) ),
		array( 'label' => 'Circuito Histórico +', 'url' => home_url( '/circuito-historico/' ) ),
		array( 'label' => 'Circuito Religioso +', 'url' => home_url( '/circuito-religioso/' ) ),
		array( 'label' => 'Circuito Serras de Ibitipoca +', 'url' => home_url( '/circuito-serras-de-ibitipoca/' ) ),
		array( 'label' => 'Costa Verde +', 'url' => home_url( '/costa-verde/' ) ),
		array( 'label' => 'Fernando de Noronha', 'url' => home_url( '/fernando-de-noronha/' ) ),
		array( 'label' => 'Mantiqueira +', 'url' => home_url( '/mantiqueira/' ) ),
		array( 'label' => 'Penedo - Pq Itatiaia', 'url' => home_url( '/penedo-pq-itatiaia/' ) ),
		array( 'label' => 'Petrópolis', 'url' => home_url( '/petropolis/' ) ),
		array( 'label' => 'Pq Nac. Itatiaia +', 'url' => home_url( '/pq-nac-itatiaia/' ) ),
		array( 'label' => 'Visconde de Mauá', 'url' => home_url( '/visconde-de-maua/' ) ),
		array( 'label' => 'Região dos Lagos +', 'url' => home_url( '/regiao-dos-lagos/' ) ),
		array( 'label' => 'Sana / Macaé', 'url' => home_url( '/sana-macae/' ) ),
		array( 'label' => 'São Lourenço', 'url' => home_url( '/sao-lourenco/' ) ),
		array( 'label' => 'Vale do Café +', 'url' => home_url( '/vale-do-cafe/' ) ),
	);

	echo '<ul class="rta-menu">';
	foreach ( $items as $item ) {
		$current = is_front_page() && trailingslashit( $item['url'] ) === trailingslashit( home_url( '/' ) );
		$class   = $current ? ' class="current-menu-item"' : '';
		printf(
			'<li%s><a href="%s">%s</a></li>',
			$class,
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Render a simple visitor counter.
 *
 * @return string
 */
function rta_get_visitor_counter_html() {
	$count = (int) get_option( 'rta_visitor_count', 306884 );
	$digits = str_split( str_pad( (string) $count, 6, '0', STR_PAD_LEFT ) );

	$html = '<div class="rta-counter" aria-label="' . esc_attr__( 'Contador de visitantes', 'rta' ) . '">';
	foreach ( $digits as $digit ) {
		$html .= '<span class="rta-counter__digit">' . esc_html( $digit ) . '</span>';
	}
	$html .= '</div>';

	return $html;
}

/**
 * Increment visitor count once per session.
 */
function rta_track_visitor() {
	if ( is_admin() || wp_doing_ajax() || ! empty( $_COOKIE['rta_counted'] ) ) {
		return;
	}

	$count = (int) get_option( 'rta_visitor_count', 306884 );
	update_option( 'rta_visitor_count', $count + 1 );

	setcookie(
		'rta_counted',
		'1',
		time() + DAY_IN_SECONDS,
		COOKIEPATH ? COOKIEPATH : '/',
		COOKIE_DOMAIN,
		is_ssl(),
		true
	);
}
add_action( 'template_redirect', 'rta_track_visitor' );
