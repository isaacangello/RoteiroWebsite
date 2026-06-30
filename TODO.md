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

## Hospedagem

- [ ] Criar pasta `public_html/dev-preview/` no FTP
- [ ] Verificar suporte a FTPS explícito na hospedagem
- [ ] Enviar `wp-content/roteiro-backup/` para o servidor via FTP
- [ ] Testar backup manual: `https://site.com/wp-content/roteiro-backup/backup.php?key=SEGREDO`
- [ ] Configurar cron no cPanel para backup diário (03:00)

## Backup

- [x] Criar script PHP de backup no servidor (`wp-content/roteiro-backup/backup.php`)
- [x] Criar script para baixar backups do servidor (`scripts/pull-server-backups.sh`)
- [x] Criar documentação (`BACKUP.md`)

## Desenvolvimento — Tema

- [x] Adicionar screenshot do tema (`screenshot.png`)
- [x] Adicionar theme supports: custom-logo, custom-header, custom-background, align-wide, responsive-embeds, editor-styles
- [x] Adicionar paleta de cores do editor (6 cores do tema)
- [x] Adicionar font-sizes do editor (5 tamanhos)
- [x] Criar `editor-style.css` para o editor Gutenberg
- [x] Exibir screenshot no front-page.php (com fallback)
- [x] Criar templates 404.php, archive.php, search.php
- [x] Extrair sidebars para template-parts/ (reutilização)
- [x] Adicionar theme.json com paleta, fontes e layout
- [x] Adicionar block styles (checkmark list, button outline)
- [x] Adicionar pattern categories (destinos, CTAs)
- [x] Adicionar suporte a post formats
- [x] Migrar Google Fonts de @import para enqueue PHP
- [x] Tipografia fluida com clamp()
- [x] Acessibilidade: focus-visible, text-wrap pretty
- [ ] Criar/customizar plugins WordPress
- [ ] Testar localmente com Docker
- [ ] Fazer deploy via develop → preview
- [ ] Homologar em dev-preview
- [ ] Fazer merge para main → produção
