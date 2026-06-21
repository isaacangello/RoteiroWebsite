#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CONTAINER="${CONTAINER:-roteiro-wordpress}"

if ! docker ps --format '{{.Names}}' | grep -qx "$CONTAINER"; then
  echo "Container $CONTAINER não está rodando. Execute: docker compose up -d"
  exit 1
fi

docker exec "$CONTAINER" bash -c '
set -e
cd /var/www/html

if [ ! -f wp-cli.phar ]; then
  curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
  chmod +x wp-cli.phar
fi

WP="php wp-cli.phar --allow-root"

$WP theme activate rta-wordpress-theme
$WP option update blogname "ROTEIRO TURÍSTICO DOS APOSENTADOS"
$WP option update blogdescription "As melhores opções para suas viagens!"

PAGE_ID=$($WP post list --post_type=page --name=inicio --field=ID --format=ids)
if [ -z "$PAGE_ID" ]; then
  PAGE_ID=$($WP post create \
    --post_type=page \
    --post_title="Início" \
    --post_name=inicio \
    --post_status=publish \
    --post_content="$(cat wp-content/themes/rta-wordpress-theme/content/home-seed.html 2>/dev/null || cat /tmp/rta-home-content.html 2>/dev/null || echo "<p>Conteúdo inicial</p>")" \
    --porcelain)
fi

$WP option update show_on_front page
$WP option update page_on_front "$PAGE_ID"

if ! $WP menu list --fields=name --format=csv | grep -qx "Menu Principal"; then
  $WP menu create "Menu Principal"
fi

MENU_ID=$($WP menu list --fields=term_id,name --format=csv | awk -F, '\''$2=="Menu Principal"{print $1; exit}'\'')

if [ -n "$MENU_ID" ] && [ "$($WP menu item list "$MENU_ID" --format=count)" = "0" ]; then
  items=(
    "Início|/"
    "Sobre nós|/sobre-nos/"
    "Fale Conosco|/fale-conosco/"
    "Últimas Notícias|/ultimas-noticias/"
    "Hoteis Fazenda|/hoteis-fazenda/"
    "Amazônia +|/amazonia/"
    "Bahia - Costa Do Descobrimento +|/bahia-costa-do-descobrimento/"
    "Cachoeira Paulista|/cachoeira-paulista/"
    "Campos do Jordão|/campos-do-jordao/"
    "Circuito das Águas +|/circuito-das-aguas/"
    "Circuito Histórico +|/circuito-historico/"
    "Circuito Religioso +|/circuito-religioso/"
    "Circuito Serras de Ibitipoca +|/circuito-serras-de-ibitipoca/"
    "Costa Verde +|/costa-verde/"
    "Fernando de Noronha|/fernando-de-noronha/"
    "Mantiqueira +|/mantiqueira/"
    "Penedo - Pq Itatiaia|/penedo-pq-itatiaia/"
    "Petrópolis|/petropolis/"
    "Pq Nac. Itatiaia +|/pq-nac-itatiaia/"
    "Visconde de Mauá|/visconde-de-maua/"
    "Região dos Lagos +|/regiao-dos-lagos/"
    "Sana / Macaé|/sana-macae/"
    "São Lourenço|/sao-lourenco/"
    "Vale do Café +|/vale-do-cafe/"
  )

  for item in "${items[@]}"; do
    title="${item%%|*}"
    url="${item##*|}"
    $WP menu item add-custom "$MENU_ID" "$title" "$url" >/dev/null
  done
fi

$WP menu location assign "Menu Principal" primary
$WP rewrite structure "/%postname%/" --hard

echo "Tema RTA ativado. Página inicial ID: $PAGE_ID"
echo "Acesse: http://localhost:8080/"
'
