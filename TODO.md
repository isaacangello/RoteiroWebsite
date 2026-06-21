# TODO - Projeto Roteiro Turístico dos Aposentados

## Configuração Inicial

- [x] Criar README.md
- [x] Criar TODO.md
- [ ] Remover `.idea/` do versionamento
- [ ] Inicializar repositório Git (`git init -b main`)
- [ ] Criar repositório no GitHub (`gh repo create`)
- [ ] Adicionar remote origin e fazer push inicial
- [ ] Criar branch `develop` e fazer push
- [ ] Criar workflow GitHub Actions (`.github/workflows/deploy.yml`)

## Configuração no GitHub

- [ ] Configurar secrets no repositório:
  - [ ] `FTP_HOST`
  - [ ] `FTP_USERNAME`
  - [ ] `FTP_PASSWORD`
- [ ] Verificar se Actions está habilitado no repositório

## Hospedagem

- [ ] Criar pasta `public_html/dev-preview/` no FTP
- [ ] Verificar suporte a FTPS explícito na hospedagem

## Desenvolvimento

- [ ] Criar/customizar tema WordPress
- [ ] Criar/customizar plugins WordPress
- [ ] Testar localmente com Docker
- [ ] Fazer deploy via develop → preview
- [ ] Homologar em dev-preview
- [ ] Fazer merge para main → produção
