# Book Storage
Backend API для мобильного приложения Библиотека книг разработанное на Laravel 11 (PHP 8.2) с использованием MySQL 8. Бизнес логика из сервинсного слоя покрыта unit тестами 

### Запуск тестов

```bash
make test-unit
```

## Запуск через Docker

### 1. Запустите контейнеры

```bash
make run
```

### 2. Установите зависимости

```bash
make install-deps
```

### 3. Настройте окружение

Создайте файл `.env` на основе `.env.example`:

```bash
cp .env.example .env
```

### 4. Выполните миграции базы данных

```bash
make migrate
```

### 5. Доступ к приложению

Приложение будет доступно по адресу: **http://localhost:80/**

### Доступ к контейнеру приложения

```bash
make exec
```

### Выполнение Artisan-команд

```bash
make exec
# Затем внутри контейнера:
php artisan <команда>
```

### Прогнать линтер
```bash
make lint
```

### Остановка контейнеров

```bash
make down
```

Postman-коллекция:
https://anastasiia-5972998.postman.co/workspace/Anastasiia's-Workspace~371144cf-77f5-41d6-8999-98121403714b/collection/44472827-0659ccb3-5afe-439b-a82e-7a64a610d7cd?action=share&source=copy-link&creator=44472827
