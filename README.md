# 🚗 API Automotiva - Laravel + AWS EC2

API RESTful para gerenciamento de dados de veículos, desenvolvida em Laravel e implantada na AWS EC2 com pipeline CI/CD automatizado.

➡️ **Documentação Completa do Projeto:** [https://night-sound-707.notion.site/Planejamento-do-Teste-T-cnico-Alpes-One-2573edbd06c380a09775e2ac546cabc1?source=copy_link]

## 🌟 Funcionalidades

- **CRUD Completo** de veículos
- **Importação automática** de dados via comando Artisan
- **API RESTful** com endpoints documentados
- **Testes automatizados** (unitários e integração)
- **Deploy contínuo** com GitHub Actions
- **Monitoramento** e sistema de rollback

## 🚀 URL da API

**Endpoint Base:** `http://18.223.212.147/api`

### Endpoints Disponíveis

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/vehicles` | Listar todos os veículos |
| `POST` | `/vehicles` | Criar novo veículo |
| `GET` | `/vehicles/{id}` | Buscar veículo por ID |
| `PUT` | `/vehicles/{id}` | Atualizar veículo |
| `DELETE` | `/vehicles/{id}` | Excluir veículo |

### 📝 Documentação da API

Uma collection do Postman está disponível para facilitar o teste de todos os endpoints.

- **Link para Download:** [baixe a collection aqui](./docs/api-automotiva.postman_collection.json)

## 📦 Tecnologias Utilizadas

- **Backend:** PHP 8.3, Laravel 11
- **Banco de Dados:** SQLite (dev), MySQL (test)
- **Infraestrutura:** AWS EC2 (Ubuntu 24.04), Apache2
- **CI/CD:** GitHub Actions
- **Testes:** PHPUnit, PHPStan

## 🛠️ Configuração do Ambiente Local

### Pré-requisitos

- PHP 8.3+
- Composer 2.6+
- SQLite3
- Git

### Instalação

1. **Clonar repositório**
```bash
git clone https://github.com/jotacarvalh0/api-automotiva.git
cd api-automotiva
```

2. **Instalar dependências**
```bash
composer install
```

3. **Configurar ambiente**
```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
```

4. **Executar migrações**
```bash
php artisan migrate
```

5. **Iniciar servidor**
```bash
php artisan serve
```
A API estará disponível em: http://localhost:8000/api


## 📊 Comandos Artisan

**Importar Veículos**
```bash
php artisan import:vehicles
```

**Executar Testes**
```bash
# Todos os testes
./vendor/bin/phpunit

# Apenas testes de API
./vendor/bin/phpunit --filter VehicleApiTest

# Apenas testes do comando
./vendor/bin/phpunit --filter ImportVehiclesCommandTest
```

**Análise de Código**
```bash
./vendor/bin/phpstan analyse
```

## 🧪 Testes Automatizados
**Testes Implementados**
*VehicleApiTest:* Testes de integração da API

*ImportVehiclesCommandTest:* Testes do comando de importação

**Executar Testes**
```bash
# Executar todos os testes
composer test

# Executar com coverage
composer test-coverage

# Executar análise estática
composer analyse
```

## 🌐 Deploy na AWS EC2
**Configuração Manual do Servidor**

1. **Conectar na instância EC2**
```bash
ssh -i sua-chave.pem ubuntu@18.223.212.147
```

2. **Executar script de setup**
```bash
bash /tmp/setup-server.sh
```

3. **Configurar ambiente**
```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
```

### Deploy Automatizado
*O deploy é automático via GitHub Actions ao fazer push na branch main.*

**Fluxo do pipeline:**

1. ✅ Executa testes

2. ✅ Análise estática com PHPStan

3. ✅ Deploy na EC2 via SSH

4. ✅ Verificação da API

## 🔧 Scripts de Deploy
**Deploy Manual**
```bash
./scripts/deploy.sh deploy
```

**Rollback**
```bash
./scripts/deploy.sh rollback
```

**Status**
```bash
./scripts/deploy.sh status
```

## 📋 Exemplos de Uso
**Listar Veículos**
```bash
curl http://18.223.212.147/api/vehicles
```

**Criar Veículo**
```bash
curl -X POST http://18.223.212.147/api/vehicles \
  -H "Content-Type: application/json" \
  -d '{
    "titulo": "Toyota Corolla",
    "marca": "Toyota", 
    "modelo": "Corolla",
    "ano": 2023,
    "preco": 120000,
    "cor": "Prata",
    "combustivel": "Flex"
  }'
```

**Buscar Veículo**
```bash
curl http://18.223.212.147/api/vehicles/1
```

## 🔐 Variáveis de Ambiente
```bash env
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/api-automotiva/database/database.sqlite

# Para testes com MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=automotive_test
DB_USERNAME=root
DB_PASSWORD=root
```

## 🚦 CI/CD Pipeline
### Workflow CI (.github/workflows/ci.yml)
- Executa em: push para main/develop e pull requests

- Testes PHPUnit com SQLite

- Análise estática com PHPStan

- Geração de coverage report

### Workflow CD (.github/workflows/cd.yml)
- Executa em: push para main

- Deploy automático via SSH

- Verificação pós-deploy

## 📊 Monitoramento
**Verificar Status da Aplicação**
```bash
./scripts/deploy.sh status
```

**Logs da Aplicação**
```bash
# Logs do Apache
tail -f /var/log/apache2/error.log

# Logs do Laravel  
tail -f storage/logs/laravel.log
```

**Métricas do Servidor**
```bash
# Uso de CPU/memória
htop

# Espaço em disco
df -h

# Tráfego de rede
nethogs
```

## 🐛 Troubleshooting
**Erro: Chave SSH não encontrada**
```bash
ssh-add ~/.ssh/sua-chave-aws.pem
```

**Erro: Permissão negada**
```bash
chmod 600 sua-chave-aws.pem
```

**Erro: Apache não inicia**
```bash
sudo systemctl status apache2
sudo journalctl -xe
```

**Erro: Migração falha**
```bash
php artisan migrate:fresh
php artisan db:seed
```

## 👨‍💻 Desenvolvedor
**José Carvalho**

*Email: josecarvalho11235@gmail.com*

*GitHub: @jotacarvalh0*

LinkedIn: [José Carvalho](https://www.linkedin.com/in/jotacarvalho-5568a01a4)

## API em produção: http://18.223.212.147/api/vehicles