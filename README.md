# PHP 8.3 Development Environment

A lightweight Docker-based PHP 8.3 development environment.

## Features

- PHP 8.3
- Composer

## Installation

1. Clone the repository

```bash
git clone https://github.com/yourusername/php83-dev-env.git
``` 

2. Build the Docker image

```bash
docker-compose up -d --build
```

3. Install PHP dependencies

```bash
docker-compose exec php composer install
```

### Run Application

1. Open console

```bash
docker-compose exec php bash
```

2. Run application

```bash
php app.php
```

### TESTING

Run PHPUnit tests

```bash
docker-compose exec php vendor/bin/phpunit
```


