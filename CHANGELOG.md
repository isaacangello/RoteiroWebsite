# Changelog

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
