#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

# ─── Cores ─────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; NC='\033[0m'

log()  { echo -e "${CYAN}[deploy]${NC} $1"; }
ok()   { echo -e "${GREEN}[✓]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[✗]${NC} $1"; exit 1; }

# ─── Help ──────────────────────────────────────────────
usage() {
  cat <<EOF
Uso: $(basename "$0") <preview|prod>

Ambientes:
  preview   Deploy para preview.roteiroturisticodosaposentados.com
            DB: roteirot_dev  |  wp-config com WP_HOME=preview.*
  prod      Deploy para roteiroturisticodosaposentados.com
            DB: roteirot_wordpress  |  wp-config com WP_HOME=produção

Requer:
  - Container Docker rodando (docker compose up -d)
  - Arquivo .env.ftp na raiz do projeto
  - Arquivo .env com credenciais dos bancos
EOF
  exit 1
}

[ $# -ne 1 ] && usage

# ─── Utilitário para ler .env ────────────────────────
parse_env() {
  local file="$1"
  while IFS='=' read -r key value; do
    [[ -n "$key" && "$key" != \#* ]] || continue
    value="${value%\"}"; value="${value#\"}"
    value="${value%\'}"; value="${value#\'}"
    export "$key=$value"
  done < "$file"
}

# ─── Carrega credenciais ──────────────────────────────
FTP_ENV="$PROJECT_DIR/.env.ftp"
[ ! -f "$FTP_ENV" ] && fail "Arquivo .env.ftp não encontrado. Crie a partir de .env.ftp.example"
parse_env "$FTP_ENV"

: "${FTP_HOST:?FTP_HOST não definido}"
: "${FTP_USERNAME:?FTP_USERNAME não definido}"
: "${FTP_PASSWORD:?FTP_PASSWORD não definido}"

DOT_ENV="$PROJECT_DIR/.env"
[ -f "$DOT_ENV" ] && parse_env "$DOT_ENV"

# ─── Configuração por ambiente ───────────────────────
case "$1" in
  preview)
    TARGET="public_html/dev-preview"
    DB_NAME="${DB_NAME_DEV:-roteirot_dev}"
    WP_HOME="https://preview.roteiroturisticodosaposentados.com"
    ;;
  prod)
    TARGET="public_html"
    DB_NAME="${DB_NAME_PROD:-roteirot_wordpress}"
    WP_HOME="https://roteiroturisticodosaposentados.com"
    ;;
  *) usage ;;
esac

WP_SITEURL="$WP_HOME"
DB_USER="${DB_USER:-roteirot_user}"
DB_PASS="${DB_PASSWORD:-SUA_SENHA_AQUI}"

# ─── Verifica container ───────────────────────────────
log "Verificando container roteiro-wordpress..."
docker ps --filter name=roteiro-wordpress --format "{{.Status}}" | grep -q "Up" \
  || fail "Container não está rodando. Execute: docker compose up -d"
ok "Container OK"

# ─── Atualiza core do WordPress ───────────────────────
log "Copiando WordPress core do container..."
WORDPRESS_DIR="$PROJECT_DIR/wordpress"
rm -rf "$WORDPRESS_DIR"
mkdir -p "$WORDPRESS_DIR"
docker cp roteiro-wordpress:/var/www/html/. "$WORDPRESS_DIR"
ok "WordPress core copiado ($(du -sh "$WORDPRESS_DIR" | cut -f1))"

# ─── Exporta banco de dados ────────────────────────────
log "Exportando banco de dados ($DB_NAME)..."
DB_DIR="$PROJECT_DIR/database"
mkdir -p "$DB_DIR"
DB_FILE="roteiro_website_$(date +%Y-%m-%d_%H-%M-%S).sql.gz"

docker exec roteiro-db \
  mariadb-dump -u "${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" \
  | gzip > "$DB_DIR/$DB_FILE"
ok "Banco exportado: database/$DB_FILE"

# ─── Verifica lftp ─────────────────────────────────────
command -v lftp &>/dev/null || fail "lftp não encontrado. Instale com: brew install lftp"
ok "lftp disponível"

# ─── Gera wp-config.php para deploy ──────────────────
log "Gerando wp-config.php para $1..."
DEPLOY_WP_CONFIG="$WORDPRESS_DIR/wp-config.php"
cat > "$DEPLOY_WP_CONFIG" <<WPCONFIG
<?php
define('DB_NAME', '${DB_NAME}');
define('DB_USER', '${DB_USER}');
define('DB_PASSWORD', '${DB_PASS}');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

define('WP_HOME', '${WP_HOME}');
define('WP_SITEURL', '${WP_SITEURL}');

\$table_prefix = 'rw_';

define('AUTH_KEY',        '$(openssl rand --hex 48 2>/dev/null)');
define('SECURE_AUTH_KEY', '$(openssl rand --hex 48 2>/dev/null)');
define('LOGGED_IN_KEY',   '$(openssl rand --hex 48 2>/dev/null)');
define('NONCE_KEY',       '$(openssl rand --hex 48 2>/dev/null)');
define('AUTH_SALT',       '$(openssl rand --hex 48 2>/dev/null)');
define('SECURE_AUTH_SALT','$(openssl rand --hex 48 2>/dev/null)');
define('LOGGED_IN_SALT',  '$(openssl rand --hex 48 2>/dev/null)');
define('NONCE_SALT',      '$(openssl rand --hex 48 2>/dev/null)');

if (!defined('ABSPATH')) define('ABSPATH', __DIR__ . '/');
require_once ABSPATH . 'wp-settings.php';
WPCONFIG
ok "wp-config.php gerado (DB: $DB_NAME, WP_HOME: $WP_HOME)"

# ─── Deploy via FTPS ───────────────────────────────────
log "Enviando para $TARGET..."

LFTP_SCRIPT=$(cat <<EOF
open -u "$FTP_USERNAME","$FTP_PASSWORD" "$FTP_HOST"
set ftp:ssl-auth TLS
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ftp:ssl-allow true
set ssl:verify-certificate no

mirror -R --delete \
  "$WORDPRESS_DIR" \
  "$TARGET"
EOF
)

lftp -c "$LFTP_SCRIPT" 2>&1 | grep -v "^$"

ok "Deploy concluído com sucesso!"
echo ""
echo "Resumo:"
echo "   Ambiente: $1"
echo "   Destino:  $TARGET"
echo "   DB:       $DB_NAME"
echo "   WP_HOME:  $WP_HOME"
echo "   Core:     $(du -sh "$WORDPRESS_DIR" | cut -f1)"
echo "   Database: $DB_FILE ($(du -sh "$DB_DIR/$DB_FILE" | cut -f1))"
