# Changelog

## 02/07/2026 (Sessão 4) — Menu responsivo off-canvas + Grid mobile

### Adicionado
- **Drawer off-canvas**: hamburger abre/fecha drawer lateral com overlay, swipe gesture, submenus toggle no mobile
- **Header.php**: botão `.rta-menu-toggle` + `<div id="rta-nav-drawer">` + overlay
- **navigation.js**: drawer toggle, swipe (touch/pointer), submenu toggle, escape key, resize auto-close
- **Admin bar**: z-index do drawer e hamburger acima do admin bar (99999) quando logado
- **Bind mounts nginx**: `wp-content/{themes,plugins,uploads,languages}` no container nginx para servir estáticos

### Fix
- **Drawer invisível**: z-index do drawer (999) estava abaixo do admin bar (99999) — corrigido para 100001
- **Header mobile**: `.rta-header__inner` agora `width: 100%` (sem gaps laterais) em ≤1024px
- **Search widget**: botão não transbordava — `flex-wrap: wrap` + `margin-left: 0`
- **Grid mobile**: `min-width: 0` nos grid items (`sidebar-left`, `center`, `sidebar-right`) para evitar overflow do conteúdo dos widgets quando a tela é estreita

## 01/07/2026 (Sessão 3) — Docker nginx + MariaDB 10.11 latin1 + Homepage real

### Alterado
- **Docker**: Apache → nginx (`wordpress:...-fpm` + `nginx:alpine`)
- **MariaDB**: `mariadb:11` → `mariadb:10.11` com charset **latin1** (cp1252) — igual produção
- **Homepage**: Lorem Ipsum substituído por conteúdo real sobre o projeto
- **Contador**: crédito "AnalogMix.com" removido

### Fix
- **dev-preview .htaccess**: regras de rewrite do WordPress estavam vazias — corrigido
- **Páginas de destino**: URLs hierárquicas e planas funcionando (301 → 200)

## 01/07/2026 (Sessão 2) — Featured Images Completas (52/52 páginas)

### Adicionado
- **32 imagens de cidades** baixadas do servidor dev-preview e importadas localmente
- **5 imagens de cidades faltantes** via Unsplash + Wikimedia Commons (Brasópolis, Fumaça, Maringá, São Lourenço, Valença)
- **11 imagens de regiões** via Unsplash + Wikimedia Commons (Amazônia até Vale do Café)
- **Favicon** no `header.php` (favicon.ico + favicon-512.png)

### Corrigido
- **18 imagens duplicadas** identificadas e substituídas por imagens únicas:
  - Vassouras/Parte Baixa, Fumaça/Itatiaia, Aparecida/Conservatória/Macaé/Marmelópolis/Penedo/Sana,
    Arraial do Cabo/Cabo Frio/Visconde de Mauá, Circuito Águas/Religioso,
    Parte Alta/Passa Quatro, Itaipava/Petrópolis
- **header.php**: linha `rootpw` removida (estava corrompendo o DOCTYPE)
- Permissão dos favicons ajustada para `http:http`
- Arquivo `header.php.bak` removido

### Lições Aprendidas
- **Unsplash API**: rate limit de 50 req/h. Fallback necessário: Wikimedia Commons
- **wp media import --post_id**: não funciona em lote — `--post_id=` é aplicado globalmente ao último valor.
  Usar `media_handle_sideload()` + `set_post_thumbnail()` individualmente via `wp eval`
- **Imagens do servidor (dev-preview)**: continham duplicatas — mesma imagem para várias cidades diferentes
- **Permissão Docker**: bind mount do tema e uploads é `http:http`, necessário `sudo` para alterar arquivos

## 01/07/2026 — Google Maps Preview, Featured Images, Cover Block Removal

### Adicionado
- **Google Maps embed iframe**: preview do mapa inserido via HTML block após o botão "Abrir no Google Maps" em 41 páginas de cidades (IDs 36-76)
- `page.php`: `the_post_thumbnail("full", ["class" => "rta-featured-image"])` — exibe imagem destacada no topo de cada página
- `style.css`: classe `.rta-featured-image { max-height: 500px; border-radius: 8px; }`
- **4 featured images configuradas**: Manaus (Pixabay), Fernando de Noronha (Wikimedia), Campos do Jordão (Wikimedia), Porto Seguro (Wikimedia — ID 330)

### Modificado
- Conteúdo de 52 páginas restaurado do backup (`roteiro_website_2026-06-30_20-00-13.sql.gz`) via temp table + MySQL JOIN UPDATE
- Cover block vermelho (`rta-red-dark`) removido de todas as 52 páginas via script PHP com MySQL direto

### Corrigido
- **WP-CLI `--post_content=@file` não funciona**: flag tratada como string literal. Operações em lote agora usam MySQL direto (mysqli + prepared statements)
- Conteúdo corrompido de 52 páginas restaurado com sucesso do backup

## 30/06/2026 — Menu Dropdown, Patterns, Logo e Deploy

### Adicionado
- `js/navigation.js`: script de navegação com hover (delay 150ms), suporte mobile (click to toggle) e fecha ao clicar fora — enfileirado via WordPress
- `patterns/*.php`: 52 block patterns (41 cidades + 11 regiões) registrados via `rta_register_patterns()`
- Menu hierárquico no WordPress com 11 regiões (dropdown) contendo "Home" + cidades como filhos

### Modificado
- `style.css`: removida `box-shadow` da logo (`.rta-logo img`, `.rta-logo__fallback`); submenu com `!important` e hover bridge (`::before`)
- `functions.php`: nova estrutura `rta_menu_tree()` com regiões e filhos; enqueue do `navigation.js`
- `header.php`: `depth` alterado de `1` para `2` no `wp_nav_menu`
- Patterns de região regenerados com links reais (`/manaus/`) em vez de âncoras (`#`)
- Páginas WordPress atualizadas via WP-CLI com novo conteúdo

### Corrigido
- URLs do menu fallback: âncoras (`/amazonia/#manaus`) → URLs reais (`/manaus/`)
- Permissão do diretório `js/` e `patterns/` no tema
