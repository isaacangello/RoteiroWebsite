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

## Ambiente Local vs Produção

| Item | Local (Docker) | Produção (Hospedagem) |
|---|---|---|
| WordPress Core | Imagem oficial | Instalação da hospedagem |
| Temas/Plugins | Montados por volume + imagem | Enviados via FTP |
| Banco | MariaDB no container | Banco da hospedagem |
| Uploads | Volume local | Gerenciado no próprio site |
