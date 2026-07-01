# Relatório Completo do Projeto

**Roteiro Turístico dos Aposentados**
**Data:** 01/07/2026
**Repositório:** github.com/isaacangello/RoteiroWebsite

---

## 1. Linha do Tempo (Commits)

| # | Data | Commit | Descrição |
|---|---|---|---|
| 1 | — | `cc6bbc7` | Setup inicial: Docker, workflow deploy, documentação |
| 2 | — | `a1fa832` | Ajuste parâmetro security no workflow |
| 3 | — | `d3d5fbb` | Comenta strict param no workflow |
| 4 | — | `2f78e1b` | Exportação de conteúdo WordPress (páginas, menus) |
| 5 | — | `3b77e02` | Script de deploy com backup DB + FTP |
| 6 | — | `ad4cc21` | Merge develop → main |
| 7 | — | `c5244d1` | Alinha DB local com hosting + WP-CLI no Dockerfile |
| 8 | — | `3a18604` | Merge branch develop |
| 9 | — | `8c803f1` | Gera wp-config.php para deploy |
| 10 | — | `257aa56` | Melhorias tema: templates, acessibilidade, fluido, theme.json |
| 11 | — | `0c881eb` | Deploy produção + menu hierárquico + padrões região/cidade |
| 12 | — | `2e0d42c` | Plugin maintenance mode |
| 13 | — | `1b67e7b` | AGENTS.md com contexto do projeto |
| 14 | — | `af779c5` | Plugin maintenance mode (rta-maintenance) |
| 15 | — | `eceb1d9` | Remove box-shadow da logo hero |
| 16 | — | `7605ece` | Remove box-shadow do screenshot |
| 17 | — | `16e114f` | Remove border-radius do logo |
| 18 | — | `c1d2683` | Página em construção com logo e cores do tema |
| 19 | — | `b6587ee` | Remove plugin manutenção do develop |
| 20 | — | `bed722e` | Fix: fundo cinza página em construção |
| 21 | — | `210d637` | Fix: fundo cinza/border-radius página em construção |
| 22 | — | `2955281` | Google Maps preview + featured images nas páginas |
| 23 | — | `5bf9a1e` | Docs: README, CHANGELOG, CHECKPOINT, AGENTS |
| 24 | 01/07 | `17c9526` | Dev-preview com banco separado + charset utf8mb4 |
| 25 | 01/07 | `956e6c1` | Docker nginx + MariaDB 10.11 latin1 + homepage real |
| 26 | 01/07 | `9fab82e` | Páginas institucionais, notícias, redirect Penedo |

**28 commits no total** (21 em develop, 7 em main)

---

## 2. Stack Final

| Componente | Local (Docker) | Dev-Preview | Produção |
|---|---|---|---|
| **Web Server** | nginx 1.31 | Apache + LiteSpeed | nginx |
| **PHP** | 8.3 (FPM) | 8.3 (ea-php83) | 8.3 |
| **Banco** | MariaDB 10.11 | MariaDB 10.11 | MariaDB 10.11 |
| **Charset** | latin1 (cp1252) | utf8mb4 | latin1 (cp1252) |
| **WordPress** | 7.0.0 | 7.0 | 7.0 |
| **Cache** | — | LiteSpeed | — |

---

## 3. Conteúdo do Site

### Páginas Publicadas: 57

| Tipo | Quantidade | Com Featured Image |
|---|---|---|
| **Cidades** | 41 | 41 (100%) |
| **Regiões** | 11 | 11 (100%) |
| **Utilitárias** | 5 | 0 |
| *Início* | 1 | — |
| *Sobre Nós* | 1 | — |
| *Fale Conosco* | 1 | — |
| *Últimas Notícias* | 1 | — |
| *Sample Page* | 1 | — |

### Posts: 4
- 3 na categoria **Notícias**
- 1 em Uncategorized (Hello World)

### Menu Principal: 65 itens
- 11 regiões (top-level)
- 41 cidades (sub-items)
- 13 links institucionais/duplicatas

### Block Patterns: 52
- 41 patterns de cidade
- 11 patterns de região

---

## 4. Featured Images

**Workflow:**
1. **32 imagens baixadas** do servidor dev-preview original
2. **5 cidades sem imagem**: Unsplash (3) + Wikimedia Commons (2)
3. **11 regiões sem imagem**: Unsplash (6) + Wikimedia Commons (5)
4. **18 duplicatas corrigidas**: imagens repetidas substituídas por únicas via Wikimedia Commons

**Fontes:**
- Unsplash API: 9 imagens (key: `h6yUMt76lzpelDulT57tZ3ixvVWs-nVmkfKhioyg9UU`)
- Wikimedia Commons API: 14 imagens
- Servidor original: 32 imagens

---

## 5. Tema (`rta-wordpress-theme`)

| Métrica | Valor |
|---|---|
| Arquivos | 73 |
| Diretórios | 5 |
| CSS | 2 (style.css + style-rtl.css) |
| PHP | 9 |
| JS | 1 (navigation.js) |
| Patterns | 52 |
| Fontes | Google Fonts (wp_enqueue_style) |

### Funcionalidades implementadas:
- Menu dropdown hierárquico (hover 150ms + mobile click)
- Bridge ::before para evitar fechamento acidental
- Tipografia fluida com clamp()
- theme.json com cores, fontes, layout
- Block styles customizados (checkmark, outline)
- Template parts (sidebar-left, sidebar-right)
- Página inicial com contador de visitantes
- Favicon configurado

---

## 6. Banco de Dados

| Tabela | Prefixo | rw_ |
|---|---|---|
| Posts (páginas + posts) | ~60 | |
| Termos (categorias) | 2 (Notícias, Uncategorized) | |
| Menu items | 65 | |
| Opções | WP padrão + rta_visitor_count | |

---

## 7. Docker

### Containers
| Nome | Imagem | Porta | Função |
|---|---|---|---|
| `roteiro-nginx` | nginx:alpine | 8080 → 80 | Servidor web |
| `roteiro-wordpress` | roteirowebsite-wordpress (custom) | — | PHP-FPM 8.3 |
| `roteiro-db` | mariadb:10.11 | 3307 → 3306 | Banco (latin1) |
| `roteiro-phpmyadmin` | phpmyadmin:latest | 8081 → 80 | Admin DB |

### Volumes
- `wordpress_data`: compartilhado (nginx + wordpress FPM)
- `db_data`: dados do MariaDB
- Bind mounts: `wp-content/{themes,plugins,uploads,languages}`

### Config
- `.docker/nginx/default.conf` — rewrite rules do WordPress
- `Dockerfile` — `wordpress:7.0.0-php8.3-fpm` + WP-CLI + PHP config
- `docker-compose.yml` — 4 serviços

---

## 8. Deploy (CI/CD)

### Workflow: `.github/workflows/deploy.yml`
- **Trigger:** push em `main` ou `develop`
- **Ação:** SamKirkland/FTP-Deploy-Action v4.3.5
- **Destino:** `wp-content/` no FTP

| Branch | Servidor | Caminho FTP |
|---|---|---|
| `develop` | Dev-Preview | `public_html/dev-preview/wp-content/` |
| `main` | Produção | `public_html/wp-content/` |

### Secrets necessários (GitHub)
- `FTP_HOST`: ftp.roteiroturisticodosaposentados.com
- `FTP_USERNAME`: seu-email@exemplo.com
- `FTP_PASSWORD`: SUA_SENHA_AQUI

### Limitações
- Workflow só sincroniza `wp-content/` (não inclui core WordPress)
- Banco de dados precisa ser importado manualmente
- Servidor usa .htaccess (Apache/LiteSpeed) para rewrite rules

---

## 9. Credenciais

### Docker Local
| Serviço | User | Senha |
|---|---|---|
| DB (root) | root | PowerRoot26:) |
| DB (app) | roteirot_user | PowerRoot26:) |
| phpMyAdmin | — | — |

### Dev-Preview
| Serviço | Valor |
|---|---|
| DB Name | roteirot_dev |
| DB User | roteirot_user |
| DB Pass | PowerRoot26:) |
| DB Host | localhost |
| URL | https://preview.roteiroturisticodosaposentados.com |

### Produção
| Serviço | Valor |
|---|---|
| DB Name | roteirot_wordpress |
| DB User | roteirot_user |
| DB Pass | SUA_SENHA_AQUI |
| URL | https://roteiroturisticodosaposentados.com |

---

## 10. Pendências

- [ ] Cidades órfãs (11) sem região pai — corrigir post_parent
- [ ] Adicionar Sobre Nós, Fale Conosco, Últimas Notícias ao menu principal
- [ ] Criar página Hotéis Fazenda (link já existe no menu)
- [ ] Backup automático do banco antes de cada deploy
- [ ] Deploy para produção (merge develop → main)
- [ ] Unsplash API: 50 req/h — considerar upgrade ou fallback mais robusto

---

*Relatório gerado em 01/07/2026 • 28 commits • 1 contribuidor (isaacangello)*
