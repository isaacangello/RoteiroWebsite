# TODO - Projeto Roteiro Turístico dos Aposentados

## Configuração Inicial

- [x] Criar README.md
- [x] Criar TODO.md
- [x] Criar CHANGELOG.md
- [ ] Remover `.idea/` do versionamento
- [x] Inicializar repositório Git (`git init -b main`)
- [x] Criar repositório no GitHub (`gh repo create`)
- [x] Adicionar remote origin e fazer push inicial
- [x] Criar branch `develop` e fazer push
- [x] Criar workflow GitHub Actions (`.github/workflows/deploy.yml`)

## Configuração no GitHub

- [ ] Configurar secrets no repositório:
  - [ ] `FTP_HOST`
  - [ ] `FTP_USERNAME`
  - [ ] `FTP_PASSWORD`
- [ ] Verificar se Actions está habilitado no repositório

## Hospedagem / Deploy

- [x] Pasta `public_html/dev-preview/` criada no FTP
- [x] Suporte a FTPS explícito na hospedagem
- [ ] Enviar `wp-content/roteiro-backup/` para o servidor via FTP
- [ ] Testar backup manual: `https://site.com/wp-content/roteiro-backup/backup.php?key=SEGREDO`
- [ ] Configurar cron no cPanel para backup diário (03:00)

## Backup

- [x] Criar script PHP de backup no servidor (`wp-content/roteiro-backup/backup.php`)
- [x] Criar script para baixar backups do servidor (`scripts/pull-server-backups.sh`)
- [x] Criar documentação (`BACKUP.md`)

## Desenvolvimento — Tema

- [x] Screenshot, theme supports, paleta, fontes, editor-style.css
- [x] Templates (front-page, 404, archive, search), template-parts sidebars
- [x] theme.json, block styles, pattern categories, post formats
- [x] Google Fonts enqueue, tipografia fluida, acessibilidade
- [x] page.php com `the_post_thumbnail()` no topo
- [x] Favicon configurado no header.php
- [ ] Criar/customizar plugins WordPress

---

## 🖼️ Featured Images — CONCLUÍDO ✅

### Fase 1: Baixar imagens do servidor para local

- [x] Baixar 32 imagens do servidor dev-preview para `wp-content/uploads/2026/07/` local
- [x] Importar via WP-CLI e configurar featured images para 32 cidades

### Fase 2: Buscar imagens faltantes

**5 cidades sem imagem → resolvido:**
- [x] Brasópolis (MG) — Unsplash
- [x] Fumaça (Distrito de Itatiaia) — Wikimedia Commons
- [x] Maringá (Distrito de Resende) — Unsplash
- [x] São Lourenço (MG) — Unsplash
- [x] Valença (RJ) — Wikimedia Commons

**11 regiões sem imagem → resolvido:**
- [x] Amazônia — Unsplash
- [x] Bahia — Costa do Descobrimento — Unsplash
- [x] Circuito das Águas — Wikimedia Commons
- [x] Circuito Histórico — Unsplash
- [x] Circuito Religioso — Wikimedia Commons
- [x] Circuito Serras de Ibitipoca — Unsplash
- [x] Costa Verde — Unsplash
- [x] Mantiqueira — Unsplash
- [x] Parque Nacional de Itatiaia — Wikimedia Commons
- [x] Região dos Lagos — Wikimedia Commons
- [x] Vale do Café — Wikimedia Commons

### Fase 3: Corrigir duplicatas

- [x] Identificar 18 imagens duplicadas (mesmo arquivo para múltiplas cidades)
- [x] Substituir cada uma por imagem única via Wikimedia Commons
- [x] Reimportar e reatribuir featured images

### Fase 4: Resultado final

- [x] **51 páginas de destino com featured image única**
- [x] Verificar exibição em http://localhost:8080
- [ ] Fazer backup do banco local (`database/`)

### Fase 5: Conteúdo e Infraestrutura

- [x] Substituir Lorem Ipsum da homepage por conteúdo real
- [x] Remover crédito "AnalogMix.com" do contador de visitantes
- [x] Docker: Apache → nginx + PHP-FPM
- [x] Docker: MariaDB 11 → 10.11 com charset latin1 (cp1252)
- [x] Criar página Fale Conosco
- [x] Criar página Sobre Nós
- [x] Criar página Últimas Notícias
- [x] Criar 3 posts de notícias (Melhor época, Acessibilidade, Roteiro Costa Verde)
- [x] Adicionar redirect de /penedo-pq-itatiaia/ → /penedo/
- [x] Corrigir .htaccess do dev-preview (rewrite rules do WordPress vazias)

### Fase 6: Deploy

- [x] git commit (develop)
- [ ] git push → GitHub Actions → dev-preview
- [ ] Homologar em https://preview.roteiroturisticodosaposentados.com
- [ ] Se aprovado, merge `develop` → `main` → produção
- [ ] Homologar em https://roteiroturisticodosaposentados.com

---

## SEO — CONCLUÍDO ✅

- [x] Meta description tag dinâmica
- [x] Open Graph tags (og:title, og:description, og:url, og:type, og:image, og:site_name, og:locale)
- [x] Twitter Card tags (summary_large_image)
- [x] Canonical URL
- [x] JSON-LD structured data (Organization + WebSite com SearchAction)

## Revisão de Textos — CONCLUÍDO ✅

- [x] Descrições das 11 regiões enriquecidas com base nos arquivos de referência em `destinos/`
- [x] Cada região agora tem 2-3 frases descritivas com dados históricos e geográficos

## Deploy dev-preview — CONCLUÍDO ✅

- [x] Commit e push para branch develop
- [ ] Homologar em https://preview.roteiroturisticodosaposentados.com
- [ ] Se aprovado, merge develop → main → produção

## Pendências

- **Cidades órfãs:** 11 cidades sem região pai (post_parent = 0) — podem quebrar URLs hierárquicas
- **Menu:** Adicionar Sobre Nós, Fale Conosco e Últimas Notícias ao menu principal
- **Backup do banco:** pendente

## Observações

- **Stack:** desenvolver local (Docker), testar, depois deploy
- **Unsplash API:** taxa limite de 50 requisições/hora (modo gratuito). Fallback: Wikimedia Commons
- **wp media import --post_id:** não funciona em lote (sempre associa ao último post_id). Usar PHP `media_handle_sideload()` + `set_post_thumbnail()` individualmente
- **Imagens do servidor (32):** tinham duplicatas — mesma imagem para várias cidades. Corrigido com Wikimedia Commons
- **TODO.md atualizado em:** 01/07/2026
