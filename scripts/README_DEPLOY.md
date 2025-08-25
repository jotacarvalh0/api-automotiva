O script de deploy automatizado foi desenvolvido para simplificar e padronizar o processo de implantação da aplicação Laravel na instância EC2 da AWS. Ele gerencia todo o ciclo de deploy com recursos de backup, rollback e monitoramento

**Estrutura de Arquivos**

```
scripts/
├── deploy.sh              # Script principal de deploy
├── setup-server.sh        # Configuração inicial do servidor
├── deploy.config          # Arquivo de configuração
└── README_DEPLOY.md       # Esta documentação
```

## **Script Principal: `deploy.sh`**

### **Funcionalidades**

- **Deploy Automatizado**: Sincroniza arquivos e executa comandos necessários
- **Sistema de Backup**: Cria backup antes de cada deploy
- **Rollback**: Reverte para versão anterior em caso de problemas
- **Status**: Monitora a saúde da aplicação
- **Logs Coloridos**: Feedback visual claro do processo

### **Como Usar**

```
# Deploy completo
./scripts/deploy.sh deploy

# Rollback para versão anterior
./scripts/deploy.sh rollback

# Verificar status da aplicação
./scripts/deploy.sh status

# Ajuda
./scripts/deploy.sh help
```

### **Fluxo do Deploy**

1. **Backup** da versão atual
2. **Sincronização** segura de arquivos
3. **Instalação** de dependências Composer
4. **Migração** do banco de dados
5. **Otimização** da aplicação Laravel
6. **Configuração** de permissões
7. **Reinicialização** do Apache
8. **Verificação** final do status

## **Script de Setup: `setup-server.sh`**

*Configuração inicial da instância EC2 (executar apenas uma vez)*

### **Comandos Executados**

```bash
# Atualização do sistema
sudo apt update && sudo apt upgrade -y

# Instalação de dependências
sudo apt install apache2 libapache2-mod-php php php-mysql php-xml php-curl php-zip unzip git -y

# Instalação do Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Configuração do Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Criação de diretórios
sudo mkdir -p /var/www/api-automotiva
sudo chown -R www-data:www-data /var/www/api-automotiva
```

## **Arquivo de Configuração: `deploy.config`**

```bash
# Configurações de deploy
SERVER_IP="18.223.212.147"
SERVER_USER="ubuntu"
APP_DIR="/var/www/api-automotiva"
BACKUP_DIR="/var/www/backups"
BRANCH="main"
```

## **Pré-requisitos**

### **Localmente**

```bash
# 1. Instalar SSH client
# 2. Configurar chave SSH para a AWS
ssh-add ~/.ssh/sua-chave-aws.pem

# 3. Tornar scripts executáveis
chmod +x scripts/deploy.sh
chmod +x scripts/setup-server.sh
```

### **No Servidor EC2**

```bash
# Arquivo .env deve existir com:
APP_KEY=base64:...
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/api-automotiva/database/database.sqlite
```

## **Guia Passo a Passo**

### **Primeira Configuração**

- **Configurar instância EC2**

```bash
# Copiar script de setup
scp -i sua-chave.pem scripts/setup-server.sh ubuntu@18.223.212.147:/tmp/

# Executar setup remoto
ssh -i sua-chave.pem ubuntu@18.223.212.147 "bash /tmp/setup-server.sh"
```

- **Configurar ambiente**

```bash
# Acessar servidor
ssh ubuntu@18.223.212.147

# Criar arquivo .env
cp /var/www/api-automotiva/.env.example /var/www/api-automotiva/.env

# Gerar chave da aplicação
cd /var/www/api-automotiva
php artisan key:generate

# Criar banco SQLite
touch database/database.sqlite
```

### **Deploy Routineiro**

- **Desenvolvimento local**

```bash
git add .
git commit -m "Nova funcionalidade"
git push origin main
```

- **Executar deploy**

```bash
./scripts/deploy.sh deploy
```

- **Verificar deploy**

```bash
./scripts/deploy.sh status

# Testar API
curl http://18.223.212.147/api/vehicles
```

## **Fluxo de Rollback**

```bash
# Se o deploy falhar ou causar problemas
./scripts/deploy.sh rollback

# O script automaticamente:
# 1. Identifica o backup mais recente
# 2. Restaura os arquivos
# 3. Reconfigura permissões
# 4. Reinicia o Apache
```

## **Output do Script**

### **Deploy Bem-sucedido**

```bash
[2025-08-25 14:30:22] Iniciando processo de deploy...
[2025-08-25 14:30:23] Criando backup da aplicação atual...
[2025-08-25 14:30:25] Backup criado em: /var/www/backups/api-automotiva_20250825_143022
[2025-08-25 14:30:26] Sincronizando arquivos com o servidor...
[2025-08-25 14:30:45] Executando comandos de deploy no servidor...
[2025-08-25 14:31:15] Deploy concluído com sucesso!
[2025-08-25 14:31:15] API disponível em: http://18.223.212.147/api/vehicles
```

### **Rollback**

```bash
[2025-08-25 14:35:12] Iniciando rollback...
[2025-08-25 14:35:13] Restaurando backup: /var/www/backups/api-automotiva_20250825_143022
[2025-08-25 14:35:15] Rollback concluído!
[2025-08-25 14:35:15] Aplicação restaurada para o estado do backup: 20250825_143022
```

## **Troubleshooting**

### **Erro Comum: Permissão Negada**

```bash
# Solução: Configurar chave SSH
chmod 600 sua-chave-aws.pem
ssh-add sua-chave-aws.pem
```

### **Erro: Arquivo .env não encontrado**

```bash
# Solução: Criar manualmente no servidor
ssh ubuntu@18.223.212.147
nano /var/www/api-automotiva/.env
```

### **Erro: Apache não inicia**

```bash
# Verificar logs
ssh ubuntu@18.223.212.147
sudo tail -f /var/log/apache2/error.log
```

## **Monitoramento Pós-Deploy**
```bash
# Verificar status completo
./scripts/deploy.sh status

# Output inclui:
- Status do serviço Apache
- Últimos logs de erro
- Espaço em disco disponível
- Lista de backups
```