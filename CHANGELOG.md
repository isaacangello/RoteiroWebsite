# Changelog

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
