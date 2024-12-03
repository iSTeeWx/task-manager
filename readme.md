# Symfony Task Manager

## Features
- Create task
- Modify Task
- Delete Task
- List Task (10 per page)
- Search Task by title or description

## Prerequisites

To start make sure to have all those installed:
- [PHP](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download)
- [MySQL](https://dev.mysql.com/downloads/installer/)

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/iSTeeWx/task-manager.git
cd task-manager
```

### 2. Intall dependencies

```bash
composer install
```

### 3. Clone the .env

```bash
cp .env .env.local
```

### 4. Change the database in the .env.local

```env
DATABASE_URL="mysql://{username}:{password}@127.0.0.1:3306/task_manager"
```

### 5. Create the databases

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Start the app

```bash
symfony serve
```

## Tests

### Modify the .env.test

```env
DATABASE_URL="mysql://{username}:{password}@127.0.0.1:3306/task_manager"
```

### Create the test database

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update --force --env=test
```

### Start the tests

```bash
php bin/phpunit
```