# Roteiro Turístico dos Aposentados

Website em WordPress com setup de desenvolvimento Docker e deploy via GitHub Actions + FTP.

## Stack

- **WordPress 7.0.0** com PHP 8.3 + Apache
- **MariaDB 11** via Docker
- **phpMyAdmin** para gerenciamento do banco
- **Docker Compose** para ambiente local

## Desenvolvimento Local

### Pré-requisitos

- Docker e Docker Compose instalados

### Iniciar ambiente

```bash
docker compose up -d
```

| Serviço | URL |
|---|---|
| WordPress | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |

## Bancos de Dados

| Ambiente | Banco | Acesso |
|----------|-------|--------|
| Local (Docker) | `roteirot_wordpress` | `localhost:8080` |
| Preview | `roteirot_dev` | cPanel / phpMyAdmin |
| Produção | `roteirot_wordpress` | cPanel / phpMyAdmin |

> MySQL remoto (`148.72.177.185:3306`) bloqueado pelo firewall da Hostinger.
> Para importar/exportar dados, use o phpMyAdmin do cPanel ou os scripts PHP no servidor.

### Setup inicial do dev-preview (uma vez)

1. Crie o banco `roteirot_dev` no cPanel (já feito)
2. Exporte o banco de producao via `backup.php` ou phpMyAdmin
3. Importe o SQL para `roteirot_dev` via phpMyAdmin
4. Envie o `wp-config.php` manualmente via FTP para `public_html/dev-preview/wp-config.php`
   (use o comando `./scripts/deploy.sh preview` que gera o wp-config automaticamente,
    ou crie manualmente com WP_HOME = https://preview.roteiroturisticodosaposentados.com)

### Dados do banco (local)

### Parar ambiente

```bash
docker compose down
```

### Dados do banco (local)

| Parâmetro | Valor |
|---|---|
| Database | `roteiro_website` |
| Usuário | `wordpress` |
| Senha | `secret` |
| Root password | `rootsecret` |
| Porta | `3307` |

## Estrutura de Branches

| Branch | Ambiente | Deploy para |
|---|---|---|
| `main` | Produção | `public_html/` |
| `develop` | Preview | `public_html/dev-preview/` |

## Fluxo de Desenvolvimento

```
develop ── git add → git commit → git push
            └→ GitHub Actions → FTP → /public_html/dev-preview/

main ── git checkout main → git merge develop → git push
        └→ GitHub Actions → FTP → /public_html/
```

1. Todo desenvolvimento é feito na branch `develop`
2. Commits e push na `develop` disparam deploy automático para preview
3. Quando a funcionalidade estiver pronta, faz merge `develop → main`
4. Push na `main` dispara deploy automático para produção

## Deploy via GitHub Actions

### Workflow: `.github/workflows/deploy.yml`

- **Trigger**: push nas branches `main` ou `develop`
- **Ação**: FTP via FTPS explícito (porta 21) do diretório `wp-content/`
- **Destino**: definido automaticamente conforme a branch

### Secrets do GitHub

| Secret | Valor |
|---|---|
| `FTP_HOST` | `ftp.roteiroturisticodosaposentados.com` |
| `FTP_USERNAME` | `seu-email@exemplo.com` |
| `FTP_PASSWORD` | *configurar no GitHub Secrets* |

### Pré-requisitos na hospedagem

A pasta `public_html/dev-preview/` deve existir no FTP antes do primeiro deploy.

## Deploy Manual via Script

Faz backup do banco + copia o WordPress core + envia tudo via FTP.

### Configurar credenciais

```bash
cp .env.ftp.example .env.ftp
# Edite .env.ftp com suas credenciais (já está no .gitignore)
```

### Usar

```bash
# Deploy para preview (develop)
./scripts/deploy.sh preview

# Deploy para produção
./scripts/deploy.sh prod
```

O script:
1. Copia o WordPress core do container para `wordpress/`
2. Exporta o banco MySQL para `database/roteiro_website_<data>.sql.gz`
3. Envia tudo via FTPS para a hospedagem

### Apenas copiar o core (sem deploy)

```bash
./scripts/update-core.sh
```

### Apenas exportar o banco

O banco é exportado automaticamente durante o deploy em `database/`.

## Estrutura de Diretórios

```
roteirowebsite/
├── wordpress/           ← Cópia do WP core (gitignorado)
├── wp-content/          ← Temas/plugins/personalizações
├── database/            ← Exports SQL (gitignorado)
├── exports/             ← Exportação WXR de conteúdo
├── scripts/
│   ├── deploy.sh        ← Deploy completo via FTP
│   └── update-core.sh   ← Apenas copiar core do container
├── .env.ftp             ← Credenciais FTP (gitignorado)
└── .github/workflows/   ← GitHub Actions
```

## Funcionalidades Implementadas

- **Menu dropdown hierárquico**: 11 regiões com cidades como subitens, hover com 150ms de delay, suporte mobile
- **52 block patterns**: 41 padrões de cidade + 11 de região, registrados via `rta_register_patterns()`
- **Google Maps preview**: iframe embed do mapa após o botão "Abrir no Google Maps" em 41 páginas de cidades
- **Featured images**: imagens destacadas no topo das páginas via `the_post_thumbnail()` em `page.php`
- **Tipografia fluida**: `clamp()` para responsividade, Google Fonts enfileiradas via WordPress
- **Tema clássico PHP**: customizações via `add_theme_support()` + `theme.json`

## Ambiente Local vs Produção

| Item | Local (Docker) | Produção (Hospedagem) |
|---|---|---|
| WordPress Core | Imagem oficial | Enviado via script/FTP |
| Temas/Plugins | Montados por volume | Enviados via Git Actions |
| Banco | MariaDB no container | Exportado via script |
| Uploads | Volume local | Gerenciado no próprio site |
