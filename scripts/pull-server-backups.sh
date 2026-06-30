#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

# ─── Cores ─────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

log()  { echo -e "${CYAN}[pull-backup]${NC} $1"; }
ok()   { echo -e "${GREEN}[✓]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[✗]${NC} $1"; exit 1; }

# ─── Configuração ──────────────────────────────────────
REMOTE_DIR="public_html/wp-content/roteiro-backup/backups"
LOCAL_DIR="$PROJECT_DIR/database/server-backups"
RETENTION_DAYS=30

# ─── Utilitário para ler .env ────────────────────────
parse_env() {
  local file="$1"
  while IFS='=' read -r key value; do
    if [[ -n "$key" && "$key" != \#* ]]; then
      value="${value%\"}"
      value="${value#\"}"
      value="${value%\'}"
      value="${value#\'}"
      export "$key=$value"
    fi
  done < "$file"
}

# ─── Carrega credenciais FTP ──────────────────────────
FTP_ENV="$PROJECT_DIR/.env.ftp"
[ ! -f "$FTP_ENV" ] && fail "Arquivo .env.ftp não encontrado. Crie a partir de .env.ftp.example"
parse_env "$FTP_ENV"

: "${FTP_HOST:?FTP_HOST não definido}"
: "${FTP_USERNAME:?FTP_USERNAME não definido}"
: "${FTP_PASSWORD:?FTP_PASSWORD não definido}"

# ─── Verifica lftp ─────────────────────────────────────
if ! command -v lftp &>/dev/null; then
  fail "lftp não encontrado. Instale com: brew install lftp  (ou apt install lftp)"
fi

# ─── Prepara diretório local ───────────────────────────
mkdir -p "$LOCAL_DIR"

log "Baixando backups do servidor..."
log "  Servidor: $FTP_HOST"
log "  Remoto:   $REMOTE_DIR"
log "  Local:    $LOCAL_DIR"

# ─── Download via FTP ──────────────────────────────────
LFTP_SCRIPT=$(cat <<EOF
open -u "$FTP_USERNAME","$FTP_PASSWORD" "$FTP_HOST"
set ftp:ssl-auth TLS
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ftp:ssl-allow true
set ssl:verify-certificate no

mirror --delete \
  "$REMOTE_DIR" \
  "$LOCAL_DIR"
EOF
)

lftp -c "$LFTP_SCRIPT" 2>&1 | grep -v "^$"

# ─── Limpeza local ─────────────────────────────────────
ok "Download concluído"
log "Limpando backups locais com mais de $RETENTION_DAYS dias..."

find "$LOCAL_DIR" -name "*.zip" -type f -mtime "+$RETENTION_DAYS" -delete 2>/dev/null || true

# ─── Resumo ────────────────────────────────────────────
BACKUP_COUNT=$(find "$LOCAL_DIR" -name "*.zip" -type f | wc -l)
LATEST=$(find "$LOCAL_DIR" -name "*.zip" -type f -printf '%T@ %p\n' 2>/dev/null | sort -rn | head -1 | cut -d' ' -f2-)

echo ""
echo "📌 Resumo:"
echo "   Backups armazenados: $BACKUP_COUNT"
if [ -n "$LATEST" ]; then
  echo "   Último backup:       $(basename "$LATEST") ($(du -h "$LATEST" | cut -f1))"
fi
echo "   Pasta local:          $LOCAL_DIR"
