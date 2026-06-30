<?php
/**
 * Roteiro Backup Download
 *
 * Lista e permite download dos backups disponíveis, protegido pela mesma
 * chave secreta do backup.php.
 *
 * Uso:
 *   Listar:  https://site.com/wp-content/roteiro-backup/download.php?key=SEGREDO
 *   Baixar:  https://site.com/wp-content/roteiro-backup/download.php?key=SEGREDO&file=NOME_DO_ZIP
 */

// ─── Configuração (mesma chave do backup.php) ─────────────────
define('BACKUP_SECRET_KEY', 'b2161d95f1972a624a06d645e8ebb5573b0a93ac2f0e046b734f44174fa1413a');
define('BACKUP_DIR', __DIR__ . '/backups');

// ─── Utilitários ─────────────────────────────────────────────
function http_error($code, $msg) {
    http_response_code($code);
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: text/plain; charset=UTF-8');
    }
    echo "ERRO $code: $msg\n";
    exit;
}

// ─── Autenticação ────────────────────────────────────────────
$key = $_GET['key'] ?? '';
if ($key !== BACKUP_SECRET_KEY) {
    http_error(403, 'Chave secreta inválida.');
}

$requested_file = $_GET['file'] ?? '';

// ─── Download de arquivo específico ──────────────────────────
if ($requested_file) {
    $safe_name = basename($requested_file);
    $file_path = BACKUP_DIR . '/' . $safe_name;

    // Validações de segurança
    if (!preg_match('/^roteiro-backup-\d{4}-\d{2}-\d{2}-\d{4}\.zip$/', $safe_name)) {
        http_error(400, 'Nome de arquivo inválido.');
    }
    if (!file_exists($file_path)) {
        http_error(404, 'Arquivo não encontrado.');
    }

    $file_size = filesize($file_path);

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $safe_name . '"');
    header('Content-Length: ' . $file_size);
    header('Cache-Control: no-cache');
    header('Pragma: no-cache');

    readfile($file_path);
    exit;
}

// ─── Listagem de backups ────────────────────────────────────
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Backups — Roteiro Turístico dos Aposentados</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #f5f5f5;
    color: #333;
    padding: 2rem;
    max-width: 800px;
    margin: 0 auto;
}
h1 {
    font-size: 1.5rem;
    color: #1a1a2e;
    margin-bottom: 0.25rem;
}
p.subtitle {
    color: #666;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}
.backup-list { list-style: none; }
.backup-item {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    margin-bottom: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: box-shadow 0.2s;
}
.backup-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.backup-info { flex: 1; }
.backup-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #1a1a2e;
}
.backup-meta {
    font-size: 0.8rem;
    color: #888;
    margin-top: 0.2rem;
}
.backup-download a {
    display: inline-block;
    background: #1a73e8;
    color: #fff;
    text-decoration: none;
    padding: 0.4rem 1rem;
    border-radius: 5px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: background 0.2s;
}
.backup-download a:hover { background: #1557b0; }
.empty {
    text-align: center;
    padding: 3rem;
    color: #999;
    background: #fff;
    border-radius: 8px;
    border: 1px dashed #ddd;
}
.footer {
    margin-top: 2rem;
    text-align: center;
    font-size: 0.8rem;
    color: #aaa;
}
</style>
</head>
<body>
<h1>📦 Backups do Site</h1>
<p class="subtitle">Roteiro Turístico dos Aposentados — Gerados automaticamente</p>

<?php
$backups = glob(BACKUP_DIR . '/roteiro-backup-*.zip');
if (!$backups): ?>
    <div class="empty">Nenhum backup encontrado ainda.</div>
<?php else:
    rsort($backups);
?>
    <ul class="backup-list">
    <?php foreach ($backups as $b):
        $bname = basename($b);
        $bsize = filesize($b);
        $bdate = date('d/m/Y H:i', filemtime($b));
        $size_mb = number_format($bsize / 1048576, 2);
        $dl_link = '?key=' . urlencode(BACKUP_SECRET_KEY) . '&file=' . urlencode($bname);
    ?>
        <li class="backup-item">
            <div class="backup-info">
                <div class="backup-name"><?= htmlspecialchars($bname) ?></div>
                <div class="backup-meta"><?= $bdate ?> — <?= $size_mb ?> MB</div>
            </div>
            <div class="backup-download">
                <a href="<?= htmlspecialchars($dl_link) ?>">Download</a>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="footer">
    Retenção: <?= BACKUP_RETENTION_DAYS ?> dias &middot;
    Total: <?= $backups ? count($backups) : 0 ?> backup(s)
</div>
</body>
</html>
