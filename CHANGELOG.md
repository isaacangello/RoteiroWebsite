# Changelog

## 30/06/2026 — Customizações do Tema RTA

### Adicionado
- `screenshot.png` na raiz do tema (padrão WordPress) — exibido no front-page.php como hero
- `editor-style.css` com estilos do tema para o editor Gutenberg (cores, fontes, larguras)
- `functions.php`: novos theme supports — `custom-logo`, `custom-header`, `custom-background`, `align-wide`, `responsive-embeds`, `editor-styles`, `editor-color-palette` (6 cores do tema), `editor-font-sizes` (5 tamanhos)

### Modificado
- `front-page.php`: hero agora exibe `screenshot.png` com fallback para custom-logo e fallback visual RTA
- `style.css`: adicionada classe `.rta-screenshot` com bordas arredondadas e sombra
- `content/` diretório no tema: permissão corrigida (`root` → `http`)

### Corrigido
- Permissão de escrita no diretório do tema (`http:http`) — uso de `sudo` para operações que exigem root

## 30/06/2026 — Melhorias no Tema (Fase 2)

### Adicionado
- `404.php`: template personalizado com mensagem e formulário de busca
- `archive.php`: template com título do arquivo, descrição, data/categoria
- `search.php`: template com termo buscado, formulário e resultados
- `template-parts/sidebar-left.php`: sidebar esquerda extraída (parceiros)
- `template-parts/sidebar-right.php`: sidebar direita extraída (relógio/clima)
- `theme.json`: configuração centralizada de cores, fontes e layout
- `logo.png`: logo do tema (RTA-NOVALOGO.png, 847×1000)
- Post formats support: aside, gallery, image, link, quote, video
- Block styles: `rta-checkmark-list` (lista com checkmarks) e `rta-button-outline` (botão contorno)
- Pattern categories: `rta_destinos` e `rta_cta`

### Modificado
- `style.css`: Google Fonts movido de `@import` para enqueue via PHP; tipografia fluida com `clamp()` em todos os tamanhos; estilos de foco (`:focus-visible`); `text-wrap: pretty`; CSS para novos templates (archive, search, 404); `scroll-behavior: smooth`
- `functions.php`: Google Fonts enfileirado via `wp_enqueue_style` com dependência; post formats; block styles; pattern categories
- `front-page.php`: sidebars agora incluídas via `get_template_part()`
- `header.php`: logo aponta para `logo.png` em vez de fallback "RTA"

### Melhorado
- Acessibilidade: focus visible, text-wrap pretty, smooth scroll
- Performance: Google Fonts carregado via enqueue em vez de @import
- Responsividade: fontes adaptativas com clamp()
- Organização: sidebars em template parts reutilizáveis
- Consistência: theme.json unifica configuração de design
