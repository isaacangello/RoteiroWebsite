# CHECKPOINT - Planejamento Inicial

## Data: 21/06/2026

## Decisões Tomadas

1. **Docker** continuará sendo usado para desenvolvimento local
2. **GitHub Actions** com workflow de FTP para deploy
3. Duas branches: `main` (produção) e `develop` (preview)
4. Temas padrão do WordPress mantidos no repositório
5. `wp-content/` completo enviado via FTP
6. Repositório será criado no GitHub via CLI (`gh`)

## Workflow de Deploy

| Branch | Trigger | Destino FTP |
|---|---|---|
| `develop` | `git push` | `public_html/dev-preview/` |
| `main` | `git push` | `public_html/` |

## Secrets Necessários no GitHub

| Secret | Valor |
|---|---|
| `FTP_HOST` | `ftp.roteiroturisticodosaposentados.com` |
| `FTP_USERNAME` | `seu-email@exemplo.com` |
| `FTP_PASSWORD` | `SUA_SENHA_AQUI` |

## Comandos Pendentes (ordem de execução)

```bash
# 1. Inicializar git
git init -b main

# 2. Criar repo no GitHub (autenticação via gh necessária)
gh repo create RoteiroWebsite --public --source=. --remote=origin --push

# 3. Criar branch develop
git checkout -b develop
git push -u origin develop

# 4. Verificar Actions no repositório
```

## Observações

- A pasta `public_html/dev-preview/` precisa ser criada manualmente no FTP
- FTPS explícito na porta 21
- O workflow usa `SamKirkland/FTP-Deploy-Action@v4.3.4`

---

## Sessão: 01/07/2026 — Google Maps Preview, Featured Images, Recuperação de Conteúdo

### Decisões Tomadas

1. **Google Maps embed sem API key**: usa `https://www.google.com/maps?q=QUERY&output=embed` em iframe HTML block
2. **MySQL direto para operações em lote**: `wp post update --post_content=@file` não funciona (trata como string literal). Usar mysqli + prepared statements
3. **Featured images via Pixabay e Wikimedia Commons**: sem necessidade de API key
4. **page.php**: `the_post_thumbnail()` no topo das páginas de destino

### Mudanças Realizadas

| Mudança | Detalhes |
|---|---|
| Cover block removido | 52 páginas tiveram bloco `rta-red-dark` removido |
| Conteúdo restaurado | 52 páginas recuperadas do backup via JOIN UPDATE |
| Google Maps embed | 41 páginas de cidades com iframe do mapa |
| Featured images | 4 imagens configuradas (Manaus, Noronha, Campos, Porto Seguro) |

### Lições Aprendidas

- `wp post update --post_content=@caminho/arquivo` NÃO lê conteúdo de arquivo — interpreta como literal
- Para conteúdo grande com caracteres especiais, operar diretamente no MySQL (mysqli) com prepared statements é mais confiável

---

## Sessão: 30/06/2026 — Customizações do Tema RTA

### Decisões Tomadas

1. **Screenshot do tema**: `screenshot.png` (padrão WordPress, 1200×900px) copiado da raiz do projeto para a raiz do tema
2. **Tema clássico PHP** (não FSE) — customizações via `add_theme_support()` e CSS
3. **Paleta de cores do editor**: espelha as variáveis CSS do tema (vermelho RTA, vermelho escuro, laranja, branco, texto, borda)
4. **Fontes do editor**: Montserrat (corpo) + Great Vibes (tagline) via Google Fonts no `style.css`
5. **Arquivos de registro**: `CHECKPOINT.md` (contexto), `CHANGELOG.md` (mudanças), `TODO.md` (planejamento)

### Theme Supports Adicionados

| Suporte | Descrição |
|---|---|
| `custom-logo` | Logo 120×120px, flex-height/width |
| `custom-header` | Faixa vermelha de 8px de altura |
| `custom-background` | Cor de fundo personalizável |
| `align-wide` | Blocos largos no editor |
| `responsive-embeds` | Vídeos responsivos |
| `editor-styles` | Estilo do tema no editor Gutenberg |
| `editor-color-palette` | 6 cores do tema |
| `editor-font-sizes` | 5 tamanhos (12px a 32px) |

### Problema de Permissão

O diretório do tema é propriedade do usuário `http:http` (Docker). Operações de escrita exigem `sudo` com senha root (`rootpw`). O diretório `content/` estava com proprietário `root` — corrigido para `http`.

---

## Sessão: 30/06/2026 — Melhorias no Tema (Baseado no Twenty Twenty-Five)

### Decisões Tomadas

1. **Tipografia fluida**: `clamp()` para responsividade em todos os tamanhos de fonte
2. **Google Fonts**: movido de `@import` no CSS para `wp_enqueue_style()` (performance)
3. **Post formats**: adicionado suporte a aside, gallery, image, link, quote, video
4. **Block styles**: registrados estilos customizados para lista (checkmark) e botão (outline)
5. **Pattern categories**: registradas categorias `rta_destinos` e `rta_cta`
6. **Template parts**: sidebars extraídas para `template-parts/sidebar-left.php` e `sidebar-right.php`
7. **`theme.json`**: centralizada configuração de cores, fontes e layout
8. **Novos templates**: `404.php`, `archive.php`, `search.php` com CSS dedicado
