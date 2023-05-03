### Backend веб-сервис "MiniNote"
> Сервис коротких заметок

#### Запуск
```shell
$ make production
```
Или
```shell
$ docker-compose up -d --build 
```
Или
```shell
$ docker build -t mininote .
$ docker run -d -p 2880:80 -e APP_DEBUG=false APP_URL='URL' DB_HOST='MYSQL_HOST' DB_DATABASE='DATABASE_NAME' DB_USERNAME='DATABASE_USER' DB_PASSWORD='DATABASE_PASSWORD' APP_VERSION='0.1.0' mininote
```
#### Разработка
1. Клонируем репозиторий `git clone https://github.com/OrlangurDux/backend-mininote-service-php.git`

#### Разработка БД
1. Структуру, ключи и связи делаем через [миграции](https://laravel.com/docs/9.x/migrations)
2. Используя `artisan` оперируем миграциями (отслеживаем и накатываем новые миграции регулярно)

###### Проблемы
Если возникли какие, либо проблемы с БД удаляем все таблицы через phpMyAdmin, дата менеджер или консоль накатываем миграции

#### <a name="start_config"></a>Стартовая конфигурация
##### Docker Config
В корне проекта копируем файл `.env.example` в `.env` далее выполняем команды в консоли 
```shell
$ id -u #получение USER_ID
$ id -g #получение GROUP_ID
```
Результат этих команд вставляем в файл `.env` в UID и GID
Или
```shell
$ sh extra/create_env.sh
```
Создаём сеть в docker окружении командой
```shell
$ docker network create mini_note_network
```
Собираем контейнер для PHP
```shell
$ docker-compose -f docker-compose.dev.yml build dev-mininote-php-fpm
```
##### Lumen Config
В папке `/src` копируем файл `.env.example` в `.env`  
В консоли в корне проекта выполняем
```shell
$ docker-compose -f docker-compose.dev.yml run dev-mininote-composer install
$ docker-compose -f docker-compose.dev.yml run dev-mininote-artisan optimize
```

#### <a name="start_stop"></a>Запуск / Остановка
Для запуска проекта в корне проекта в консоли (или создаем конфигурацию в VSCode или phpStorm с использованием Docker Compose)
```shell
$ docker-compose -f docker-compose.dev.yml up -d dev-mininote-service
```
Остановка проекта в корне проекта в консоли
```shell
$ docker-compose -f docker-compose.dev.yml down
```

##### Список всех сервисов
* dev-mininote-service
* dev-mininote-php-fpm
* dev-mininote-mysql
* dev-mininote-redis
* dev-mininote-composer
* dev-mininote-artisan
* dev-mininote-smtp
* dev-mininote-phpmyadmin

#### Вспомогательные команды
Выполняются в корне проекта
###### Artisan
```shell
$ docker-compose -f docker-compose.dev.yml run dev-mininote-artisan [COMMAND] #доступные команды artisan
```
###### Composer
```shell
$ docker-compose -f docker-compose.dev.yml run dev-mininote-composer [COMMAND] #доступные команды composer
```
###### PHP
```shell
$ docker-compose -f docker-compose.dev.yml run dev-mininote-php-fpm php [COMMAND] #доступные команды PHP
```
Так же есть возможность подключится к консоли контейнера если он запущен для выполнения дополнительных команд и действий
```shell
$ docker exec -it [ИМЯ_СЕРВИСА] sh
```
#### Дополнительные конфигурации (связь внутри контейнеров)
###### MySQL
Хост: dev-mininote-mysql  
БД: dev_mini-note  
Логин: mininote  
Пароль: mininote123  
Порт: 3306
###### smtp4dev
Хост: dev-mininote-smtp  
Порт SMTP: 25  
Порт IMAP: 143

#### Инструменты
* Основной сервис - [http://localhost:8880](http://localhost:8880)
* phpMyAdmin - [http://localhost:8089](http://localhost:8089)
* smtp4dev (шлюз для тестирования отправки писем) - [http://localhost:5005](http://localhost:5005)
* mysql - внешний порт 3309 / внутренний в контейнере 3306
* redis - внешний порт 6394 / внутренний в контейнере 6379
* xDebug - внешний порт 9053 / внутренний в контейнере 9003

#### Reverse Proxy Nginx
При использовании nginx как реверс прокси пример конфигурации в файле `/extra/nginx-courts.conf`

#### Короткие команды (альтернатива описанным выше)
В консоли в корне проекта запускаем
```shell
$ make help
```
Данные команды упрощенные варианты основных команд описанных выше для более быстрого доступа
