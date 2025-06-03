# Проект бронирования номеров (Hotel Booking API)

Тестовый проект: реализация API для управления бронированием номеров с авторизацией и базовым функционалом.

## 📋 Техническое задание

Полное ТЗ доступно в файле [TECHNICAL_SPECIFICATION.md](TECHNICAL_SPECIFICATION.md)

## 🚀 Быстрый старт

### Предварительные требования

-   Docker и Docker Compose установленные в системе

### Используемые стеки и технологии

-   PHP 8.2
-   PostgreSQL 16
-   Laravel 12

### Установка и запуск

1. Сборка и запуск контейнеров:

```bash
docker compose build
docker compose up -d
```

2. Установка зависимостей и настройка проекта:

```bash
docker compose exec php make init
```

После выполнения этих команд проект будет доступен по адресу http://localhost.

## 📚 Документация API

### Аутентификация

#### █ POST `/api/auth/login`

Авторизация пользователя.

**Пример запроса:**

```json
{
    "email": "test@example.com",
    "password": "password"
}
```

**Пример ответа:**

```json
{
    "token": "1|ra8GS0uHoISlScEJMO3XorUZLFBZ7livXJKqzUY89ef83d6e"
}
```

### Работа с номерами

#### █ GET `/api/rooms/list`

Получение списка доступных номеров с пагинацией.

**Параметры:**

-   `from` - дата начала периода (обязательный)
-   `to` - дата окончания периода (обязательный)
-   `page` - номер страницы (по умолчанию: 1)
-   `per_page` - элементов на странице (по умолчанию: 25)

**Пример запроса:**
`/api/rooms/list`
`/api/rooms/list?from=2025-06-03`
`/api/rooms/list?from=2025-06-03&to=2025-06-06`
`/api/rooms/list?from=2025-06-03&to=2025-06-06&page=1`
`/api/rooms/list?from=2025-06-03&to=2025-06-06&page=1&per_page=25`

**Пример ответа:**

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "sit quisquam cupiditate",
            "description": "Laborum dolorem quae ipsa est. Repellendus commodi non molestias vel molestias deleniti hic minima. Alias aut omnis vitae est architecto."
        },
        {
            "id": 2,
            "title": "molestiae illo veritatis",
            "description": "Error est velit et veniam et culpa adipisci. Explicabo autem aut eaque voluptatum in dolorum eos sapiente. Quo occaecati at sunt pariatur quia est. Libero vero deserunt non quia repellendus consequuntur quae."
        }
    ],
    "first_page_url": "http://localhost/api/rooms/list?page=1",
    "from": 1,
    "last_page": 50,
    "last_page_url": "http://localhost/api/rooms/list?page=50",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": "http://localhost/api/rooms/list?page=2",
            "label": "2",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=3",
            "label": "3",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=4",
            "label": "4",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=5",
            "label": "5",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=6",
            "label": "6",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=7",
            "label": "7",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=8",
            "label": "8",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=9",
            "label": "9",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=10",
            "label": "10",
            "active": false
        },
        {
            "url": null,
            "label": "...",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=49",
            "label": "49",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=50",
            "label": "50",
            "active": false
        },
        {
            "url": "http://localhost/api/rooms/list?page=2",
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": "http://localhost/api/rooms/list?page=2",
    "path": "http://localhost/api/rooms/list",
    "per_page": 2,
    "prev_page_url": null,
    "to": 2,
    "total": 100
}
```

#### █ POST `/api/rooms/order`

Бронирование номера (требуется авторизация).

**Пример запроса:**

```json
{
    "room_id": 1,
    "date": "2025-06-05"
}
```

**Пример ответа:**

```json
{
    "date": "2025-06-05",
    "id": 4,
    "room": {
        "id": 1,
        "title": "sunt qui dignissimos",
        "description": "Est id sunt maiores. Alias quo est occaecati necessitatibus. Corrupti id dolores repudiandae delectus."
    }
}
```

## 🛠 Технические особенности реализации

-   База данных:

    -   Миграции и сидеры для инициализации данных
    -   Фабрики для тестовых данных
    -   Справочник номеров (таблица rooms)

-   Безопасность:

    -   Аутентификация через Laravel Sanctum
    -   Генерация уникального ключа приложения

-   Производительность:

    -   Кеширование с использованием стандартного фасада Cache

-   Тестирование:

    -   Написаны Unit и Feature тесты
    -   Реализовано логирование в файл (/storage/logs/laravel.log)

-   Дополнительно:

    -   Имитация отправки почты через логирование
    -   Готовый docker-окружение для разработки
    -   Подготовленный makefile для исполнения типовых задач по развёртыванию проекта

## 🧪 Тестирование

Для запуска тестов выполните:

```bash
docker compose exec php make test
```

## 📝 Логи

Логи приложения и письма доступны в файле:

```
/storage/logs/laravel.log
```
