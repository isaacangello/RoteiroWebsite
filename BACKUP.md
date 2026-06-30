# Backup Automático — Roteiro Turístico dos Aposentados

Sistema de backup completo do site em produção, executado diretamente no servidor
via PHP com agendamento via cPanel Cron.

---

## Visão Geral

```
[SERVIDOR HOSPEDAGEM]
  backup.php (via cron ou acesso manual)
    → exporta MySQL (mysqli)
    → compacta: database.sql + uploads/ + temas/ + plugins/
    → salva em wp-content/roteiro-backup/backups/
    → apaga backups com mais de 7 dias

[LOCAL / DEV]
  scripts/pull-server-backups.sh
    → lftp conecta via FTP
    → baixa backups do servidor
    → salva em database/server-backups/
    → mantém backups locais por 30 dias
```

---

## Estrutura

```
wp-content/
└── roteiro-backup/              ← Versionado no Git
    ├── backup.php               ← Script de backup (PHP)
    ├── download.php             ← Lista e baixa backups (PHP)
    ├── .htaccess                ← Protege o diretório
    └── backups/                 ← Zips gerados aqui (gitignorado)
        └── .htaccess            ← Nega acesso público

database/
└── server-backups/              ← Cópia local dos backups do servidor (gitignorado)

scripts/
└── pull-server-backups.sh       ← Baixa backups do servidor para máquina local
```

---

## 1. Configuração Inicial

### 1.1 Escolher uma chave secreta

Edite `wp-content/roteiro-backup/backup.php` e altere a linha:

```php
define('BACKUP_SECRET_KEY', 'change-this-to-a-random-secret');
```

Gere uma chave segura com:

```bash
openssl rand --hex 32
```

### 1.2 Enviar para o servidor

Envie a pasta `wp-content/roteiro-backup/` via FTP para o servidor de produção:

```
public_html/wp-content/roteiro-backup/
```

**Importante:** mantenha o `backup.php` com a chave secreta já alterada.

### 1.3 Testar manualmente

Acesse no navegador:

```
https://roteiroturisticodosaposentados.com/wp-content/roteiro-backup/backup.php?key=SUA_CHAVE
```

Ou via CLI (se tiver SSH):

```bash
php /home/USER/public_html/wp-content/roteiro-backup/backup.php key=SUA_CHAVE
```

O backup será gerado em `wp-content/roteiro-backup/backups/`.

### 1.4 Configurar notificação por email

Edite `wp-content/roteiro-backup/backup.php` e preencha a lista de emails:

```php
$notify_emails = [
    'admin@exemplo.com',
    'outro@exemplo.com',
];
```

O script tentará usar o `wp_mail()` do WordPress (respeita plugin SMTP)
e cai para `mail()` do PHP como fallback.

O email contém: status, data, nome do arquivo, tamanho, lista de backups
armazenados e link para download.

---

## 2. Agendar no cPanel (Cron)

### Passo a passo:

1. Acesse o **cPanel** da hospedagem
2. Clique em **"Cron Jobs"** (na seção "Avançado")
3. Role até **"Add New Cron Job"**
4. Escolha:
   - **Common Settings:** `Once Per Day` (ou customizar)
   - **Minuto:** `0`
   - **Hora:** `3` (3h da manhã)
   - **Dia:** `*`
   - **Mês:** `*`
   - **Dia da Semana:** `*`
5. No campo **Command**, cole:

```bash
wget -q --delete-after "https://roteiroturisticodosaposentados.com/wp-content/roteiro-backup/backup.php?key=SUA_CHAVE" 2>/dev/null
```

6. Clique em **"Add New Cron Job"**

### Resultado esperado:

Todos os dias às **03:00** o backup será gerado automaticamente.
Backups com mais de **7 dias** são removidos automaticamente.

---

## 3. Download via Navegador

Acesse a **página de listagem** com a mesma chave secreta:

```
https://roteiroturisticodosaposentados.com/wp-content/roteiro-backup/download.php?key=SUA_CHAVE
```

Você verá uma lista com todos os backups disponíveis e poderá baixá-los
diretamente pelo navegador.

Download direto de um arquivo específico:

```
https://.../download.php?key=CHAVE&file=roteiro-backup-2026-06-25-0300.zip
```

---

## 4. Baixar Backups para o Ambiente Local

### Script automático

```bash
./scripts/pull-server-backups.sh
```

Requisitos: `lftp` instalado, `.env.ftp` configurado (já usado pelo deploy.sh).

O que faz:
- Conecta ao servidor via FTPS
- Baixa todos os backups de `wp-content/roteiro-backup/backups/`
- Salva em `database/server-backups/`
- Mantém backups locais por 30 dias

### Agendar localmente (opcional)

Para baixar os backups automaticamente no seu PC, adicione ao cron local:

```bash
0 5 * * * /caminho/para/roteirowebsite/scripts/pull-server-backups.sh
```

---

## 5. Notificação por Email

Quando o backup é gerado (via cron ou manual), o script envia automaticamente
um email para a lista configurada com:
- Status do backup
- Data e hora
- Nome e tamanho do arquivo
- Lista de todos os backups armazenados
- Link para download (protegido pela mesma chave secreta)

**Para configurar:** edite a variável `$notify_emails` no início do `backup.php`.

---

## 6. Segurança

- O `backup.php` só executa com a chave secreta correta
- A pasta `backups/` tem `.htaccess` com `Deny from all`
- Arquivos `.zip` e `.sql` são bloqueados pelo `.htaccess` do diretório pai
- As pastas de backup estão no `.gitignore` (não vão para o repositório)
- A chave secreta NUNCA deve ser commitada

---

## 7. Solução de Problemas

| Problema | Causa provável | Solução |
|---|---|---|
| "Chave secreta inválida" | KEY errada ou não enviada | Verificar o parâmetro `?key=` |
| "wp-config.php não encontrado" | Script não está em `wp-content/roteiro-backup/` | Verificar se enviou a pasta correta |
| "Não foi possível criar o ZIP" | Permissão de escrita | Dar permissão 755 na pasta `backups/` |
| "Erro na conexão MySQL" | Credenciais do banco inválidas | Verificar wp-config.php no servidor |
| Backup não é gerado pelo cron | Caminho errado no comando wget | Testar o URL manualmente primeiro |

---

## 8. Retenção

| Local | Período | Configurável |
|---|---|---|
| Servidor (roteiro-backup/backups/) | 7 dias | `BACKUP_RETENTION_DAYS` em backup.php |
| Local (database/server-backups/) | 30 dias | `RETENTION_DAYS` em pull-server-backups.sh |
