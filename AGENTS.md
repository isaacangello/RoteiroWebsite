# AGENTS.md — Contexto do Projeto

## Projeto
Roteiro Turístico dos Aposentados — Site WordPress de turismo.

## Stack
- WordPress 7.0 (Docker local), PHP 8.3 (produção nginx)
- MariaDB 11 (Docker), MySQL 8 (produção Hostinger)
- Tema customizado: `rta-wordpress-theme`

## Ambientes

### Local (Docker)
- URL: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- DB: `roteirot_wordpress`@db:3306 (root/SUA_SENHA_AQUI)
- Tabelas prefixo: `rw_`
- Tema bind mount: `./wp-content/themes` → `/var/www/html/wp-content/themes`
- Plugins bind mount: `./wp-content/plugins` → `/var/www/html/wp-content/plugins`
- Container: `roteiro-wordpress`, `roteiro-db`, `roteiro-phpmyadmin`

### Produção
- URL: https://roteiroturisticodosaposentados.com
- Servidor: nginx, PHP 8.3, cPanel
- DB: `roteirot_wordpress`@localhost, user `roteirot_user`, senha `SUA_SENHA_AQUI`, prefixo `rw_`
- FTP: `ftp.roteiroturisticodosaposentados.com` / `seu-email@exemplo.com` / `SUA_SENHA_AQUI` (FTPS, TLS)
- Raiz web: `/home/roteirot/public_html/`
- Pasta dev-preview (para branch develop): `public_html/dev-preview/wp-content/`

### MySQL Remoto
- Host: `148.72.177.185:3306` (porta **fechada** — firewall da hospedagem bloqueia)

## Git
- Repo: `github.com/isaacangello/RoteiroWebsite`
- Branches: `main` (produção), `develop` (dev/preview)
- CI/CD: `.github/workflows/deploy.yml` — deploy FTP automático via GitHub Actions (push em main/develop). Precisa das secrets FTP_HOST, FTP_USERNAME, FTP_PASSWORD no GitHub.

## Tema (`rta-wordpress-theme`)
- `functions.php`: menu tree (`rta_menu_tree()`), patterns registration, navigation.js enqueue, theme setup
- `header.php`: wp_nav_menu com depth=2, container ul, theme_location=primary
- `js/navigation.js`: hover (150ms delay), mobile click toggle (<768px), fecha ao clicar fora
- `style.css`: dropdown com !important, hover bridge (::before), sem box-shadow na logo
- `patterns/`: 52 block patterns (41 cidade, 11 região), registrados em `rta_register_patterns()`

## Banco de Dados
- Export: `database/roteiro_website_YYYY-MM-DD_HH-MM-SS.sql.gz`
- Scripts PHP de importação via FTP (gzgets + MySQLi) — shell_exec desabilitado no servidor
- Prefixo: `rw_`

## Comandos Úteis
```bash
# Subir/descer Docker
docker compose up -d
docker compose down

# WP-CLI no container
docker exec -it roteiro-wordpress wp --allow-root <comando>

# Exportar banco
docker exec -i roteiro-db mysqldump -uroot -pSUA_SENHA_AQUI roteirot_wordpress | gzip > database/dump.sql.gz

# FTP produção (lftp)
lftp -c "open -u 'seu-email@exemplo.com','SUA_SENHA_AQUI' ftp.roteiroturisticodosaposentados.com; set ftp:ssl-auth TLS; set ftp:ssl-force true; set ssl:verify-certificate no; <comandos>"
```

## Credenciais Produção
- DB: `roteirot_wordpress` / `roteirot_user` / `SUA_SENHA_AQUI`
- FTP: `seu-email@exemplo.com` / `SUA_SENHA_AQUI`
- AUTH keys no `wp-config.php` (produção)
- WP_DEBUG: **false** na produção

## Observações
- Tema mountado como bind mount → arquivos do tema têm permissão `http:http` (Docker). Para criar/editar arquivos no tema, usar `pkexec` ou `sudo`.
- `wp-config.php` local NÃO existe (gerado pelo Docker). Produção tem `wp-config.php` próprio.
- MySQL remoto (148.72.177.185:3306) bloqueado pelo firewall da hospedagem.
- Ao publicar no `dev-preview`, não esquecer de criar a pasta no servidor e os secrets do GitHub Actions.
- **WP-CLI `--post_content=@file` não funciona**: flag tratada como string literal. Usar MySQL direto para operações em lote.

## Estado Atual (01/07/2026)
- Site funcionando em produção
- Página "Em Construção" ativa (plugin `rta-maintenance`)
- Menu hierárquico com 11 regiões + cidades implementado
- 52 block patterns criados
- Cover block vermelho removido de todas as 52 páginas
- Google Maps preview (iframe embed) adicionado em 41 páginas de cidades
- Featured images configuradas: Manaus, Fernando de Noronha, Campos do Jordão, Porto Seguro
- `page.php` com `the_post_thumbnail()` no topo das páginas
- Backup do banco: `database/roteiro_website_2026-07-01_03-28-41.sql.gz`
