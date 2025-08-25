#!/bin/bash

# Script de configuração inicial do servidor
# Deve ser executado apenas uma vez na nova instância EC2

set -e

# Configurações
APP_DIR="/var/www/api-automotiva"

# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências
sudo apt install apache2 libapache2-mod-php php php-mysql php-xml php-curl php-zip unzip git -y

# Instalar Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Configurar Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Criar diretório da aplicação
sudo mkdir -p ${APP_DIR}
sudo chown -R www-data:www-data ${APP_DIR}
sudo chmod -R 775 ${APP_DIR}

# Configurar virtual host
sudo cat > /etc/apache2/sites-available/000-default.conf << 'EOL'
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    <Directory /var/www/api-automotiva/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOL

# Reiniciar Apache
sudo systemctl restart apache2

echo "Configuração inicial do servidor concluída!"