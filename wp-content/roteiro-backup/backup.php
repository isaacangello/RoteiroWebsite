<?php
/**
 * Roteiro Backup Script
 *
 * Cria backups completos do site: banco de dados + uploads + temas + plugins.
 * Envia notificação por email para a lista de administradores.
 *
 * Uso:
 *   Web:   https://site.com/wp-content/roteiro-backup/backup.php?key=SEGREDO
 *   CLI:   php backup.php key=SEGREDO
 *   Cron:  wget -q --delete-after "https://site.com/wp-content/roteiro-backup/backup.php?key=SEGREDO"
 */

// ─── Configuração ─────────────────────────────────────────────
define('BACKUP_SECRET_KEY', 'b2161d95f1972a624a06d645e8ebb5573b0a93ac2f0e046b734f44174fa1413a');
define('BACKUP_RETENTION_DAYS', 7);
define('BACKUP_DIR', __DIR__ . '/backups');
define('WP_CONTENT_DIR_REAL', dirname(__DIR__));
define('WP_ROOT_DIR', dirname(WP_CONTENT_DIR_REAL));

// ─── Notificação por Email ───────────────────────────────────
// Lista de emails que receberão notificação do backup
$notify_emails = [
    // 'admin@exemplo.com',
    // 'outro@exemplo.com',
];

// Nome do remetente e email de origem
$notify_from_name  = 'Roteiro Backup';
$notify_from_email = 'backup@roteiroturisticodosaposentados.com';

// Assunto do email
$notify_subject = function ($zip_name, $zip_size) {
    $size_mb = number_format($zip_size / 1048576, 2);
    return "[Roteiro] Backup concluído — $zip_name ({$size_mb}MB)";
};

// ─── Utilitários ─────────────────────────────────────────────
function log_msg($msg) {
    $prefix = php_sapi_name() === 'cli' ? '' : '<br>';
    echo sprintf('[%s] %s%s', date('Y-m-d H:i:s'), $msg, $prefix) . "\n";
}

function fail($msg) {
    log_msg("ERRO: $msg");
    exit(1);
}

// ─── Autenticação ────────────────────────────────────────────
$key = php_sapi_name() === 'cli'
    ? (isset($_SERVER['argv'][1]) ? explode('=', $_SERVER['argv'][1])[1] ?? '' : '')
    : ($_GET['key'] ?? '');

if ($key !== BACKUP_SECRET_KEY) {
    if (php_sapi_name() !== 'cli') {
        http_response_code(403);
    }
    fail('Chave secreta inválida.');
}

log_msg('Iniciando backup...');

// ─── Garantir diretório de backup ────────────────────────────
if (!is_dir(BACKUP_DIR)) {
    mkdir(BACKUP_DIR, 0755, true) or fail("Não foi possível criar " . BACKUP_DIR);
}
if (!is_writable(BACKUP_DIR)) {
    fail("Diretório de backup não tem permissão de escrita: " . BACKUP_DIR);
}

// ─── Ler credenciais do banco do wp-config.php ────────────────
$wp_config_path = WP_ROOT_DIR . '/wp-config.php';
if (!file_exists($wp_config_path)) {
    // Tenta um nível acima (wp-config.php pode estar fora do public_html)
    $wp_config_path = dirname(WP_ROOT_DIR) . '/wp-config.php';
}
if (!file_exists($wp_config_path)) {
    fail("wp-config.php não encontrado. Verifique WP_ROOT_DIR ($wp_config_path)");
}

$wp_config = file_get_contents($wp_config_path) or fail("Não foi possível ler wp-config.php");

$extract_define = function ($constant) use ($wp_config) {
    if (preg_match("/define\s*\(\s*['\"]" . preg_quote($constant, '/') . "['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $wp_config, $m)) {
        return $m[1];
    }
    return null;
};

$db_name = $extract_define('DB_NAME');
$db_user = $extract_define('DB_USER');
$db_pass = $extract_define('DB_PASSWORD');
$db_host = $extract_define('DB_HOST');

if (!$db_name || !$db_user || $db_pass === null || !$db_host) {
    fail("Não foi possível extrair todas as credenciais do banco do wp-config.php");
}

log_msg("Conectando ao banco de dados...");

$mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    fail("Erro na conexão MySQL: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// ─── Exportar banco para SQL ──────────────────────────────────
log_msg("Exportando banco de dados...");

$sql = "-- Roteiro Backup - " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- Banco: $db_name\n\n";
$sql .= "SET NAMES utf8mb4;\n";
$sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

$tables = $mysqli->query("SHOW TABLES");
if (!$tables) {
    fail("Erro ao listar tabelas: " . $mysqli->error);
}

$total_tables = $tables->num_rows;
$count = 0;

while ($row = $tables->fetch_row()) {
    $table = $row[0];
    $count++;

    log_msg("  Exportando tabela ($count/$total_tables): $table");

    // CREATE TABLE
    $create = $mysqli->query("SHOW CREATE TABLE `$table`");
    if ($create) {
        $create_row = $create->fetch_row();
        $sql .= "\n-- Estrutura: $table\n";
        $sql .= $create_row[1] . ";\n\n";
        $create->free();
    }

    // INSERTs em lotes
    $result = $mysqli->query("SELECT * FROM `$table`");
    if ($result && $result->num_rows > 0) {
        $columns = $result->fetch_fields();
        $col_names = array_map(function ($c) { return "`$c->name`"; }, $columns);
        $col_list = implode(', ', $col_names);

        $sql .= "INSERT INTO `$table` ($col_list) VALUES\n";

        $row_count = 0;
        $total_rows = $result->num_rows;

        while ($data_row = $result->fetch_row()) {
            $row_count++;
            $escaped = array_map(function ($val) use ($mysqli) {
                return $val === null ? 'NULL' : "'" . $mysqli->real_escape_string($val) . "'";
            }, $data_row);
            $sql .= "(" . implode(', ', $escaped) . ")";
            $sql .= ($row_count < $total_rows) ? ",\n" : ";\n\n";

            // Limpa buffer a cada 100 linhas para evitar estouro de memória
            if ($row_count % 100 === 0) {
                $sql .= "-- Lote $row_count de $total_rows\n\n";
            }
        }
        $result->free();
    }
}

$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

$tables->free();
$mysqli->close();

// ─── Salvar SQL temporário ────────────────────────────────────
$timestamp = date('Y-m-d-Hi');
$sql_file = BACKUP_DIR . "/database-$timestamp.sql";
file_put_contents($sql_file, $sql) or fail("Não foi possível salvar o arquivo SQL");
unset($sql);
log_msg("SQL exportado (" . number_format(filesize($sql_file)) . " bytes)");

// ─── Criar ZIP ────────────────────────────────────────────────
$zip_name = "roteiro-backup-$timestamp.zip";
$zip_path = BACKUP_DIR . "/$zip_name";

log_msg("Compactando backup em $zip_name...");

$zip = new ZipArchive();
$res = $zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
if ($res !== true) {
    unlink($sql_file);
    fail("Não foi possível criar o ZIP (erro $res)");
}

// Adiciona o SQL
$zip->addFile($sql_file, 'database.sql');

// Adiciona diretórios de forma recursiva
function zip_add_dir($zip, $real_dir, $zip_prefix) {
    if (!is_dir($real_dir)) return;

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($real_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if (!$file->isFile()) continue;

        $real_path = $file->getRealPath();
        $relative_path = $zip_prefix . '/' . $file->getFilename();

        // Pega o caminho completo relativo dentro do ZIP
        $full_relative = $zip_prefix . '/' . substr($real_path, strlen($real_dir) + 1);
        $zip->addFile($real_path, $full_relative);
    }
}

// Uploads
$uploads_dir = WP_CONTENT_DIR_REAL . '/uploads';
if (is_dir($uploads_dir)) {
    log_msg("  Adicionando uploads/");
    zip_add_dir($zip, $uploads_dir, 'uploads');
}

// Temas (exceto os padrões do WordPress)
$themes_dir = WP_CONTENT_DIR_REAL . '/themes';
if (is_dir($themes_dir)) {
    $theme_dirs = array_filter(glob($themes_dir . '/*'), 'is_dir');
    foreach ($theme_dirs as $theme_path) {
        $theme_name = basename($theme_path);
        // Pula temas padrão do WordPress
        if (preg_match('/^twenty(?:twenty|fifteen|sixteen|seventeen|nineteen)/', $theme_name)) {
            continue;
        }
        log_msg("  Adicionando themes/$theme_name");
        zip_add_dir($zip, $theme_path, "themes/$theme_name");
    }
}

// Plugins (exceto akismet e hello.php)
$plugins_dir = WP_CONTENT_DIR_REAL . '/plugins';
if (is_dir($plugins_dir)) {
    $plugin_items = glob($plugins_dir . '/*');
    foreach ($plugin_items as $plugin_path) {
        $plugin_name = basename($plugin_path);
        if ($plugin_name === 'akismet' || $plugin_name === 'hello.php' || $plugin_name === 'index.php') {
            continue;
        }
        if (is_dir($plugin_path)) {
            log_msg("  Adicionando plugins/$plugin_name");
            zip_add_dir($zip, $plugin_path, "plugins/$plugin_name");
        } elseif (is_file($plugin_path)) {
            $zip->addFile($plugin_path, "plugins/$plugin_name");
        }
    }
}

$zip->close();

// Remove o SQL temporário
unlink($sql_file);

$zip_size = filesize($zip_path);
log_msg("Backup concluído: $zip_name (" . number_format($zip_size) . " bytes)");

// ─── Limpeza: apagar backups antigos ──────────────────────────
log_msg("Limpando backups com mais de " . BACKUP_RETENTION_DAYS . " dias...");
$cutoff = strtotime("-" . BACKUP_RETENTION_DAYS . " days");
$deleted = 0;

$existing = glob(BACKUP_DIR . '/roteiro-backup-*.zip');
if ($existing) {
    foreach ($existing as $old_zip) {
        if (filemtime($old_zip) < $cutoff) {
            unlink($old_zip);
            $deleted++;
        }
    }
}

log_msg("Limpeza concluída: $deleted arquivo(s) removido(s).");

// ─── Notificação por Email ────────────────────────────────────
if (!empty($notify_emails)) {
    log_msg("Enviando notificação para " . count($notify_emails) . " destinatário(s)...");

    $download_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
        . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost')
        . dirname($_SERVER['SCRIPT_NAME'] ?? '/wp-content/roteiro-backup')
        . "/download.php?key=" . urlencode(BACKUP_SECRET_KEY);

    $subject = $notify_subject($zip_name, $zip_size);

    $body = "Backup do site Roteiro Turístico dos Aposentados\n";
    $body .= str_repeat("=", 50) . "\n\n";
    $body .= "Status:  Concluído com sucesso\n";
    $body .= "Data:    " . date('d/m/Y H:i:s') . "\n";
    $body .= "Arquivo: $zip_name\n";
    $body .= "Tamanho: " . number_format($zip_size / 1048576, 2) . " MB\n";
    $body .= "Retenção: " . BACKUP_RETENTION_DAYS . " dias\n\n";
    $body .= "Backups armazenados:\n";

    $all_backups = glob(BACKUP_DIR . '/roteiro-backup-*.zip');
    if ($all_backups) {
        rsort($all_backups);
        $count = 0;
        foreach ($all_backups as $b) {
            $count++;
            $bsize = filesize($b);
            $bname = basename($b);
            $bdate = date('d/m/Y H:i', filemtime($b));
            $body .= "  $count. $bname  ({$bdate}, " . number_format($bsize / 1048576, 2) . " MB)\n";
        }
    }

    $body .= "\nDownload: $download_url\n";
    $body .= "\n---\nEsta mensagem foi enviada automaticamente pelo sistema de backup.\n";

    // Tenta usar wp_mail() se WordPress estiver acessível
    $wp_load = WP_ROOT_DIR . '/wp-load.php';
    $mail_sent = false;

    if (file_exists($wp_load)) {
        try {
            // Suprime output do WordPress
            ob_start();
            $suppress = !defined('WP_DEBUG') || !WP_DEBUG;
            if ($suppress) {
                error_reporting(0);
            }
            require_once $wp_load;
            ob_end_clean();

            $headers = 'From: ' . $notify_from_name . ' <' . $notify_from_email . '>' . "\r\n";

            foreach ($notify_emails as $recipient) {
                $recipient = trim($recipient);
                if (!empty($recipient)) {
                    wp_mail($recipient, $subject, $body, $headers);
                }
            }
            $mail_sent = true;
            if ($suppress) {
                error_reporting(E_ALL);
            }
        } catch (\Throwable $e) {
            log_msg("  wp_mail() falhou, tentando mail(): " . $e->getMessage());
        }
    }

    if (!$mail_sent) {
        // Fallback para mail() do PHP
        $headers = 'From: ' . $notify_from_name . ' <' . $notify_from_email . '>' . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";

        foreach ($notify_emails as $recipient) {
            $recipient = trim($recipient);
            if (!empty($recipient)) {
                @mail($recipient, $subject, $body, $headers);
            }
        }
    }

    log_msg("Notificações enviadas.");
}

log_msg("Backup finalizado com sucesso!");
