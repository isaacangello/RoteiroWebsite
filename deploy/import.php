<?php
/**
 * Importador cirúrgico — Sessão 4
 *
 * Uso:
 *   1. FTP: import.php + dados.sql + conteudo.html + hoteis-fazenda-featured.jpg
 *      para public_html/dev-preview/
 *   2. Acessar: https://preview.roteiroturisticodosaposentados.com/import.php
 *   3. Remover arquivos do servidor via FTP ap�s a importa��o
 */

$db_host = 'localhost';
$db_user = 'roteirot_user';
$db_pass = 'PowerRoot26:)';
$db_name = 'roteirot_dev';

echo "<pre>";
echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║    IMPORTADOR SESSAO 4 — DEV-PREVIEW                    ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

$ok = 0; $err = 0;

// ══════════════════════════════════════════════════════════
// PARTE 1 — SQL (limpeza + menu items)
// ══════════════════════════════════════════════════════════
echo "──────────────────────────────────────────────────────\n";
echo "PARTE 1 — SQL (menu items)\n";
echo "──────────────────────────────────────────────────────\n";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("❌ ERRO NA CONEXÃO: " . $conn->connect_error . "\n");
}
$conn->set_charset('latin1');
echo "✅ Conectado ao banco: {$db_name}\n";

$sql_file = __DIR__ . '/dados.sql';
if (!file_exists($sql_file)) {
    die("❌ ARQUIVO NÃO ENCONTRADO: {$sql_file}\n");
}

$sql_raw = file_get_contents($sql_file);

$statements = explode(";\n", $sql_raw);
unset($sql_raw);

$total = 0; $sql_errors = 0;
foreach ($statements as $query) {
    $query = trim($query);
    if (empty($query)) continue;
    if (str_starts_with($query, '--') || str_starts_with($query, '#')) continue;

    if ($conn->query($query)) {
        $total++;
    } else {
        echo "  ❌ ERRO SQL: " . $conn->error . "\n";
        echo "     Query: " . substr($query, 0, 120) . "...\n\n";
        $sql_errors++;
    }
}
$conn->close();

echo "✅ SQL: {$total} executadas, {$sql_errors} erros\n\n";

// ══════════════════════════════════════════════════════════
// PARTE 2 — WordPress API (página + imagem)
// ══════════════════════════════════════════════════════════
echo "──────────────────────────────────────────────────────\n";
echo "PARTE 2 — WordPress API (página + imagem)\n";
echo "──────────────────────────────────────────────────────\n";

$wp_load = $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
if (!file_exists($wp_load)) {
    foreach ([__DIR__ . '/wp-load.php', dirname(__DIR__) . '/wp-load.php'] as $p) {
        if (file_exists($p)) { $wp_load = $p; break; }
    }
}
if (!file_exists($wp_load)) {
    die("❌ wp-load.php não encontrado em {$wp_load}\n");
}
require_once $wp_load;
echo "✅ WordPress carregado\n";

// ─── 2a. Criar/atualizar página Hotéis Fazenda ──────────
$content_file = __DIR__ . '/conteudo.html';
$post_content = file_exists($content_file) ? file_get_contents($content_file) : '';

$existing = get_page_by_path('hoteis-fazenda', OBJECT, 'page');
if ($existing) {
    $page_id = wp_update_post([
        'ID'           => $existing->ID,
        'post_title'   => 'Hotéis Fazenda',
        'post_content' => $post_content,
        'post_status'  => 'publish',
    ], true);
    echo "📝 Página ATUALIZADA (ID: {$page_id})\n";
} else {
    $page_id = wp_insert_post([
        'post_title'    => 'Hotéis Fazenda',
        'post_name'     => 'hoteis-fazenda',
        'post_content'  => $post_content,
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => 1,
    ], true);
    echo "📝 Página CRIADA (ID: {$page_id})\n";
}

if (is_wp_error($page_id)) {
    echo "❌ ERRO AO CRIAR PÁGINA: " . $page_id->get_error_message() . "\n";
    $page_id = 0; $err++;
} else {
    $ok++;

    // Update menu item object_id to point to the new page
    $menu_item = get_posts([
        'post_type'  => 'nav_menu_item',
        'name'       => 'hoteis-fazenda',
        'numberposts'=> 1,
    ]);
    if (!empty($menu_item)) {
        update_post_meta($menu_item[0]->ID, '_menu_item_object_id', $page_id);
        update_post_meta($menu_item[0]->ID, '_menu_item_object', 'page');
        update_post_meta($menu_item[0]->ID, '_menu_item_type', 'post_type');
        echo "🔗 Menu item Hotéis Fazenda atualizado para page ID {$page_id}\n";
    }
}

// ─── 2b. Importar featured image ─────────────────────────
$image_path = __DIR__ . '/hoteis-fazenda-featured.jpg';
if (file_exists($image_path) && !empty($page_id)) {
    $filetype = wp_check_filetype(basename($image_path));

    $attachment = [
        'guid'           => home_url('/wp-content/uploads/2026/07/' . basename($image_path)),
        'post_mime_type' => $filetype['type'],
        'post_title'     => 'Hotéis Fazenda',
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];

    $attach_id = wp_insert_attachment($attachment, $image_path, $page_id);

    if (!is_wp_error($attach_id)) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($page_id, $attach_id);
        echo "🖼️  Featured image importada (ID: {$attach_id})\n";
        $ok++;
    } else {
        echo "❌ ERRO AO IMPORTAR IMAGEM: " . $attach_id->get_error_message() . "\n";
        $err++;
    }
} else {
    echo "⚠️  Imagem não encontrada ou page_id inválido\n";
}

// ══════════════════════════════════════════════════════════
echo "\n──────────────────────────────────────────────────────\n";
echo "RESUMO FINAL\n";
echo "──────────────────────────────────────────────────────\n";
echo "✅ Sucesso: {$ok}\n";
echo "❌ Erros:   {$err}\n";
echo "\n⚠️  Delete estes arquivos do servidor via FTP:\n";
echo "   - import.php\n";
echo "   - dados.sql\n";
echo "   - conteudo.html\n";
echo "   - hoteis-fazenda-featured.jpg\n";
echo "</pre>";
