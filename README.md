# Ina Zaoui

Portfolio of the photographer Ina Zaoui, developped with Symfony

## Prerequisite

- PHP 8.2+
- Composer
- PostrgreSQL 16+

## Installation

### 1. Clone the repository

> git clone https://github.com/CGirr/P15-InaZaoui

### 2. Install dependencies

> composer install

### 3. Configure your environment

Copy the '.env' file and rename it to '.env.local'
> cp .env .env.local

In .env.local modify "DATABASE_URL" with your credentials
> DATABASE_URL="postgresql://user:password@127.0.0.1:5432/ina_zaoui?serverVersion=16&charset=utf8"

### 4. Create the database and execute the migrations

> symfony console doctrine:database:create
> 
> symfony console doctrine:migrations:migrate

### 5. Load the fixtures

> symfony console doctrine:fixtures:load

### 6. Start the development server

> symfony serve

You can now access the app at https://localhost:8000

Once logged in administration panel is accessible via:
>https://localhost:8000/admin/guest
> 
>https://localhost:8000/admin/album
> 
>https://localhost:8000/admin/media

## Test accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | ina@zaoui.com | 123456   |
| Guest | isy@isy.com | 123456   |
| Blocked guest | cora.g@gmail.com | 123456   |

## Running the tests

> php bin/phpunit --coverage-html coverage/

Coverage report will be available in the 'coverage/' directory.
