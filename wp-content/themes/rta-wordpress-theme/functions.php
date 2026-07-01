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
		add_query_arg( 't', time(), get_stylesheet_uri() ),
		array( 'rta-fonts' ),
		null
	);

	wp_enqueue_script(
		'rta-nav',
		get_template_directory_uri() . '/js/navigation.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
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

	register_block_pattern_category(
		'rta_cidades',
		array(
			'label'       => __( 'Cidades RTA', 'rta' ),
			'description' => __( 'Padrões de páginas de cidades individuais.', 'rta' ),
		)
	);
}
add_action( 'init', 'rta_pattern_categories' );

/**
 * Register custom block patterns from the /patterns directory.
 */
function rta_register_patterns() {
	$pattern_dir = get_template_directory() . '/patterns';
	if ( ! is_dir( $pattern_dir ) ) {
		return;
	}

	$files = glob( $pattern_dir . '/*.php' );
	sort( $files );

	foreach ( $files as $file ) {
		$content = file_get_contents( $file );
		if ( ! $content ) {
			continue;
		}

		$headers = get_file_data(
			$file,
			array(
				'title'       => 'Title',
				'slug'        => 'Slug',
				'description' => 'Description',
				'categories'  => 'Categories',
			)
		);

		if ( empty( $headers['title'] ) || empty( $headers['slug'] ) ) {
			continue;
		}

		$categories = array_map( 'trim', explode( ',', $headers['categories'] ) );

		register_block_pattern(
			$headers['slug'],
			array(
				'title'       => $headers['title'],
				'description' => $headers['description'],
				'content'     => $content,
				'categories'  => $categories,
			)
		);
	}
}
add_action( 'init', 'rta_register_patterns', 20 );

/**
 * Build a hierarchical menu tree from flat item definitions.
 */
function rta_menu_tree() {
	$tree = array(
		array(
			'label' => 'Amazônia',
			'url'   => home_url( '/amazonia/' ),
			'children' => array(
				array( 'label' => 'Manaus (AM)', 'url' => home_url( '/manaus/' ) ),
				array( 'label' => 'Santarém (PA)', 'url' => home_url( '/santarem/' ) ),
				array( 'label' => 'Presidente Figueiredo (AM)', 'url' => home_url( '/presidente-figueiredo/' ) ),
			),
		),
		array(
			'label' => 'Bahia - Costa do Descobrimento',
			'url'   => home_url( '/bahia-costa-do-descobrimento/' ),
			'children' => array(
				array( 'label' => 'Porto Seguro (BA)', 'url' => home_url( '/porto-seguro/' ) ),
				array( 'label' => 'Trancoso (BA)', 'url' => home_url( '/trancoso/' ) ),
				array( 'label' => 'Arraial d\'Ajuda (BA)', 'url' => home_url( '/arraial-dajuda/' ) ),
			),
		),
		array(
			'label' => 'Circuito das Águas',
			'url'   => home_url( '/circuito-das-aguas/' ),
			'children' => array(
				array( 'label' => 'São Lourenço (MG)', 'url' => home_url( '/sao-lourenco/' ) ),
				array( 'label' => 'Caxambu (MG)', 'url' => home_url( '/caxambu/' ) ),
				array( 'label' => 'Cambuquira (MG)', 'url' => home_url( '/cambuquira/' ) ),
			),
		),
		array(
			'label' => 'Circuito Histórico',
			'url'   => home_url( '/circuito-historico/' ),
			'children' => array(
				array( 'label' => 'Aiuruoca (MG)', 'url' => home_url( '/aiuruoca/' ) ),
				array( 'label' => 'Baependi (MG)', 'url' => home_url( '/baependi/' ) ),
				array( 'label' => 'Itamonte (MG)', 'url' => home_url( '/itamonte/' ) ),
			),
		),
		array(
			'label' => 'Circuito Religioso',
			'url'   => home_url( '/circuito-religioso/' ),
			'children' => array(
				array( 'label' => 'Aparecida (SP)', 'url' => home_url( '/aparecida/' ) ),
				array( 'label' => 'Santana do Lourenço (MG)', 'url' => home_url( '/santana-do-lourenco/' ) ),
				array( 'label' => 'Brasópolis (MG)', 'url' => home_url( '/brasopolis/' ) ),
			),
		),
		array(
			'label' => 'Circuito Serras de Ibitipoca',
			'url'   => home_url( '/circuito-serras-de-ibitipoca/' ),
			'children' => array(
				array( 'label' => 'Lima Duarte (MG)', 'url' => home_url( '/lima-duarte/' ) ),
				array( 'label' => 'Santa Rita de Ibitipoca (MG)', 'url' => home_url( '/santa-rita-de-ibitipoca/' ) ),
			),
		),
		array(
			'label' => 'Costa Verde',
			'url'   => home_url( '/costa-verde/' ),
			'children' => array(
				array( 'label' => 'Paraty (RJ)', 'url' => home_url( '/paraty/' ) ),
				array( 'label' => 'Angra dos Reis (RJ)', 'url' => home_url( '/angra-dos-reis/' ) ),
			),
		),
		array(
			'label' => 'Mantiqueira',
			'url'   => home_url( '/mantiqueira/' ),
			'children' => array(
				array( 'label' => 'Delfim Moreira (MG)', 'url' => home_url( '/delfim-moreira/' ) ),
				array( 'label' => 'Marmelópolis (MG)', 'url' => home_url( '/marmelopolis/' ) ),
				array( 'label' => 'Passa Quatro (MG)', 'url' => home_url( '/passa-quatro/' ) ),
			),
		),
		array(
			'label' => 'Parque Nacional de Itatiaia',
			'url'   => home_url( '/pq-nac-itatiaia/' ),
			'children' => array(
				array( 'label' => 'Parte Baixa (RJ)', 'url' => home_url( '/parte-baixa/' ) ),
				array( 'label' => 'Parte Alta (MG)', 'url' => home_url( '/parte-alta/' ) ),
			),
		),
		array(
			'label' => 'Região dos Lagos',
			'url'   => home_url( '/regiao-dos-lagos/' ),
			'children' => array(
				array( 'label' => 'Arraial do Cabo (RJ)', 'url' => home_url( '/arraial-do-cabo/' ) ),
				array( 'label' => 'Búzios (RJ)', 'url' => home_url( '/buzios/' ) ),
				array( 'label' => 'Cabo Frio (RJ)', 'url' => home_url( '/cabo-frio/' ) ),
			),
		),
		array(
			'label' => 'Vale do Café',
			'url'   => home_url( '/vale-do-cafe/' ),
			'children' => array(
				array( 'label' => 'Vassouras (RJ)', 'url' => home_url( '/vassouras/' ) ),
				array( 'label' => 'Valença (RJ)', 'url' => home_url( '/valenca/' ) ),
				array( 'label' => 'Conservatória (RJ)', 'url' => home_url( '/conservatoria/' ) ),
			),
		),
	);

	return $tree;
}

/**
 * Render the fallback menu tree recursively.
 */
function rta_render_menu_tree( $items, $parent_current = false ) {
	$output = '<ul class="rta-menu">';

	foreach ( $items as $item ) {
		$has_children = ! empty( $item['children'] );
		$is_current   = $parent_current || ( is_page() && trailingslashit( get_permalink() ) === trailingslashit( $item['url'] ) );
		$classes      = array();

		if ( $is_current ) {
			$classes[] = 'current-menu-item';
		}
		if ( $has_children ) {
			$classes[] = 'menu-item-has-children';
		}

		$class_attr = $classes ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' : '';

		$output .= '<li' . $class_attr . '>';
		$output .= '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';

		if ( $has_children ) {
			$output .= '<ul class="sub-menu">';
			foreach ( $item['children'] as $child ) {
				$child_current = is_page() && trailingslashit( get_permalink() ) === trailingslashit( $child['url'] );
				$child_class   = $child_current ? ' class="current-menu-item"' : '';
				$output .= '<li' . $child_class . '>';
				$output .= '<a href="' . esc_url( $child['url'] ) . '">' . esc_html( $child['label'] ) . '</a>';
				$output .= '</li>';
			}
			$output .= '</ul>';
		}

		$output .= '</li>';
	}

	$output .= '</ul>';
	return $output;
}

/**
 * Fallback menu matching the original site structure with dropdown support.
 */
function rta_default_menu() {
	echo rta_render_menu_tree( rta_menu_tree() );
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

/**
 * Redirect alternate slugs to their canonical page URLs.
 */
function rta_redirect_alternate_slugs() {
	if ( is_404() ) {
		global $wp;
		$path = trim( $wp->request, '/' );

		$aliases = array(
			'penedo-pq-itatiaia' => 'penedo',
		);

		if ( isset( $aliases[ $path ] ) ) {
			$page = get_page_by_path( $aliases[ $path ], OBJECT, 'page' );
			if ( $page ) {
				wp_safe_redirect( get_permalink( $page->ID ), 301 );
				exit;
			}
		}
	}
}
add_action( 'template_redirect', 'rta_redirect_alternate_slugs' );
