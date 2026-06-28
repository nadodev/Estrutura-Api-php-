# PHP API

Projeto de API desenvolvido em **PHP puro**, com estrutura organizada para expor rotas HTTP e retornar dados em JSON.

A aplicação utiliza **Docker** para facilitar o ambiente de desenvolvimento, contendo:

* PHP 8.3 com PHP-FPM
* Nginx
* MySQL 8
* Composer
* Dotenv para variáveis de ambiente

---

## Tecnologias utilizadas

* PHP 8.3
* PHP-FPM
* Nginx
* MySQL 8
* Composer
* Docker
* Docker Compose
* vlucas/phpdotenv

---

## Estrutura do projeto

```bash
.
├── public/
│   └── index.php
├── src/
├── routes/
│   └── api.php
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
├── docker-compose.yml
├── composer.json
├── .env.example
├── .gitignore
└── README.md
```

---

## Sobre a arquitetura

A aplicação segue uma estrutura simples e organizada para projetos PHP puros.

### `public/index.php`

É o ponto de entrada da aplicação, também conhecido como **Front Controller**.

Todas as requisições passam por esse arquivo antes de serem encaminhadas para as rotas e controllers.

### `routes/`

Responsável por armazenar as definições de rotas da aplicação.

### `src/`

Responsável por armazenar o código principal da aplicação, como controllers, models, services, helpers e outras classes.

### `docker/`

Contém os arquivos de configuração do ambiente Docker, como Nginx e PHP-FPM.

---

## Pré-requisitos

Antes de iniciar, é necessário ter instalado:

* Docker
* Docker Compose
* Git

Para verificar se o Docker está instalado corretamente:

```bash
docker --version
```

```bash
docker compose version
```

---

## Configuração do ambiente

Clone o projeto:

```bash
git clone <url-do-repositorio>
```

Acesse a pasta do projeto:

```bash
cd nome-do-projeto
```

Copie o arquivo de exemplo das variáveis de ambiente:

```bash
cp .env.example .env
```

No Windows, caso não tenha o comando `cp`, use:

```bash
copy .env.example .env
```

---

## Exemplo de `.env`

```env
APP_NAME="PHP API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=app_db
DB_USERNAME=app_user
DB_PASSWORD=app_password
```

Importante: dentro do Docker, o host do banco deve ser:

```env
DB_HOST=mysql
```

Não use `localhost`, pois o banco está rodando em outro container.

---

## Subindo o projeto com Docker

Para construir e iniciar os containers:

```bash
docker compose up -d --build
```

Para verificar se os containers estão rodando:

```bash
docker compose ps
```

A aplicação estará disponível em:

```bash
http://localhost:8080
```

---

## Instalando as dependências

Acesse o container da aplicação:

```bash
docker compose exec app bash
```

Instale as dependências com Composer:

```bash
composer install
```

Ou execute direto pelo Docker:

```bash
docker compose exec app composer install
```

---

## Banco de dados

O projeto utiliza MySQL 8.

Dados padrão do banco:

```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=app_db
DB_USERNAME=app_user
DB_PASSWORD=app_password
```

Para acessar o MySQL pelo terminal:

```bash
docker compose exec mysql mysql -u app_user -p
```

Senha:

```bash
app_password
```

Para acessar como root:

```bash
docker compose exec mysql mysql -u root -p
```

Senha:

```bash
root_password
```

---

## Testando a API

Com os containers rodando, acesse:

```bash
http://localhost:8080
```

Exemplo de resposta esperada:

```json
{
  "status": "success",
  "message": "API PHP rodando com Docker"
}
```

---

## Comandos úteis

Subir os containers:

```bash
docker compose up -d
```

Subir reconstruindo as imagens:

```bash
docker compose up -d --build
```

Parar os containers:

```bash
docker compose down
```

Ver logs:

```bash
docker compose logs -f
```

Ver logs apenas do Nginx:

```bash
docker compose logs -f nginx
```

Ver logs apenas da aplicação PHP:

```bash
docker compose logs -f app
```

Acessar o container PHP:

```bash
docker compose exec app bash
```

Acessar o container MySQL:

```bash
docker compose exec mysql bash
```

Reiniciar os containers:

```bash
docker compose restart
```

---

## Ambiente de desenvolvimento

Fluxo básico para desenvolvimento:

```bash
docker compose up -d
```

```bash
docker compose exec app composer install
```

```bash
http://localhost:8080
```

Após alterações no código PHP, normalmente não é necessário reiniciar os containers. Basta atualizar a página ou chamar a rota novamente.
