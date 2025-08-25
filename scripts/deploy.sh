#!/bin/bash

# Script de deploy automatizado para API Automotiva na AWS EC2
# Autor: José Carvalho

set -e  # Sai imediatamente se qualquer comando falhar

# Configurações
SERVER_IP="18.223.212.147"
SERVER_USER="ubuntu"
APP_DIR="/var/www/api-automotiva"
BACKUP_DIR="/var/www/backups"
BRANCH="main"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para log colorido
log() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    error "Script deve ser executado a partir do diretório raiz do projeto Laravel"
    exit 1
fi

# Função para criar backup
create_backup() {
    log "Criando backup da aplicação atual..."
    ssh ${SERVER_USER}@${SERVER_IP} "sudo mkdir -p ${BACKUP_DIR} && sudo cp -r ${APP_DIR} ${BACKUP_DIR}/api-automotiva_${TIMESTAMP}"
    log "Backup criado em: ${BACKUP_DIR}/api-automotiva_${TIMESTAMP}"
}

# Função para deploy
deploy() {
    log "Iniciando processo de deploy..."
    
    # 1. Criar backup
    create_backup
    
    # 2. Sincronizar arquivos com o servidor
    log "Sincronizando arquivos com o servidor..."
    rsync -avz --delete \
        --exclude='.env' \
        --exclude='storage/' \
        --exclude='.git/' \
        --exclude='node_modules/' \
        --exclude='.idea/' \
        -e ssh ./ ${SERVER_USER}@${SERVER_IP}:${APP_DIR}/
    
    # 3. Executar comandos no servidor
    log "Executando comandos de deploy no servidor..."
    ssh ${SERVER_USER}@${SERVER_IP} << EOF
        set -e
        
        cd ${APP_DIR}
        
        # Manter arquivo .env existente
        if [ -f .env ]; then
            log "Preservando arquivo .env existente"
        else
            error "Arquivo .env não encontrado!"
            exit 1
        fi
        
        # Instalar dependências
        log "Instalando dependências do Composer..."
        composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist
        
        # Executar migrações
        log "Executando migrações do banco de dados..."
        php artisan migrate --force
        
        # Limpar cache
        log "Limpando cache da aplicação..."
        php artisan optimize:clear
        php artisan optimize
        
        # Configurar permissões
        log "Configurando permissões..."
        sudo chown -R www-data:www-data ${APP_DIR}
        sudo chmod -R 775 ${APP_DIR}/storage
        sudo chmod -R 775 ${APP_DIR}/bootstrap/cache
        
        # Reiniciar serviços
        log "Reiniciando Apache..."
        sudo systemctl restart apache2
        
        # Verificar status
        log "Verificando status do Apache..."
        sudo systemctl status apache2 --no-pager
EOF
    
    log "Deploy concluído com sucesso!"
    log "API disponível em: http://${SERVER_IP}/api/vehicles"
}

# Função para rollback
rollback() {
    warning "Iniciando rollback..."
    
    # Encontrar backup mais recente
    LATEST_BACKUP=$(ssh ${SERVER_USER}@${SERVER_IP} "ls -td ${BACKUP_DIR}/api-automotiva_* | head -1")
    
    if [ -z "$LATEST_BACKUP" ]; then
        error "Nenhum backup encontrado para rollback!"
        exit 1
    fi
    
    log "Restaurando backup: $LATEST_BACKUP"
    
    ssh ${SERVER_USER}@${SERVER_IP} << EOF
        set -e
        
        # Restaurar backup
        sudo rm -rf ${APP_DIR}
        sudo cp -r ${LATEST_BACKUP} ${APP_DIR}
        
        # Configurar permissões
        sudo chown -R www-data:www-data ${APP_DIR}
        sudo chmod -R 775 ${APP_DIR}/storage
        sudo chmod -R 775 ${APP_DIR}/bootstrap/cache
        
        # Reiniciar Apache
        sudo systemctl restart apache2
        
        log "Rollback concluído!"
        log "Aplicação restaurada para o estado do backup: $LATEST_BACKUP"
EOF
}

# Função para verificar status
status() {
    log "Verificando status da aplicação..."
    
    ssh ${SERVER_USER}@${SERVER_IP} << EOF
        echo "=== Status do Apache ==="
        sudo systemctl status apache2 --no-pager | head -5
        
        echo ""
        echo "=== Últimos logs de erro ==="
        sudo tail -20 /var/log/apache2/error.log
        
        echo ""
        echo "=== Espaço em disco ==="
        df -h
        
        echo ""
        echo "=== Backups disponíveis ==="
        ls -la ${BACKUP_DIR}/
EOF
}

# Menu principal
case "$1" in
    deploy)
        deploy
        ;;
    rollback)
        rollback
        ;;
    status)
        status
        ;;
    *)
        echo "Uso: $0 {deploy|rollback|status}"
        echo ""
        echo "  deploy    - Realiza deploy da aplicação"
        echo "  rollback  - Reverte para o último backup"
        echo "  status    - Verifica status da aplicação"
        exit 1
        ;;
esac