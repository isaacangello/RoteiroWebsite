#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
WORDPRESS_DIR="$PROJECT_DIR/wordpress"

echo "📦 Copiando WordPress core do container..."

if ! docker ps --filter name=roteiro-wordpress --format "{{.Status}}" | grep -q "Up"; then
  echo "❌ Container roteiro-wordpress não está rodando. Inicie com: docker compose up -d"
  exit 1
fi

rm -rf "$WORDPRESS_DIR"
mkdir -p "$WORDPRESS_DIR"

docker cp roteiro-wordpress:/var/www/html/. "$WORDPRESS_DIR"

echo "✅ WordPress core copiado para $WORDPRESS_DIR"
echo "   Tamanho: $(du -sh "$WORDPRESS_DIR" | cut -f1)"
