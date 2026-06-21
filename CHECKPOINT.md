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
