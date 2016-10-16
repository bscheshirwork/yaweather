# yaweather
Dockerized yandex weather parsrt

Пример кода yii2, созданный в Docker-образоах окружения. 
###[Текст задания](/docs/taskinfo.md) 
Пример представляет собой проект на шаблоне basic фреймворка yii2. Добавлены библиотеки Guzzle и PhpQuery (создан fork для этого примера). Изменения самого шаблона только в рамках задачи. 

Для запуска данного примера необходимы [docker](https://docs.docker.com/engine/getstarted/step_one/) и [docker-compose](https://docs.docker.com/compose/install/)

Ниже приведу последовательность действий для запуска на Ubuntu

1.Установить docker и docker-compose по ссылкам выше.

2.Создать папку проекта 

```
$ git clone https://github.com/bscheshirwork/yaweather
```

либо вручную, если отсутствует git - из [архива](https://github.com/bscheshirwork/yaweather/archive/master.zip)
В ней `docker-compose.yml` служит для установки конфигурации Вашей будущей связки сервисов. Для дебага не забудьте изменить соответствующую переменную окружения, подставив адрес вашей машины вместо указанного для примера.
```
version: '2'
services:
  php:
    image: bscheshir/php:7.0.11-fpm-4yii2-xdebug
    restart: always
    volumes:
      - ./php-code:/var/www/html #php-code
    depends_on:
      - db
    environment:
      XDEBUG_CONFIG: remote_host=192.168.1.39
  nginx:
    image: nginx:1.11.5-alpine
    restart: always
    ports:
      - "8080:80"
    depends_on:
      - php
    volumes_from:
      - php
    volumes:
      - ./nginx-conf:/etc/nginx/conf.d #nginx-conf
      - ./nginx-logs:/var/log/nginx #nginx-logs
  db:
    image: mysql:5.7.15
    restart: always
    volumes:
      - ./mysql-data/db:/var/lib/mysql #mysql-data
    environment:
      MYSQL_ROOT_PASSWORD: yaweather
      MYSQL_DATABASE: yaweather
      MYSQL_USER: yaweather
      MYSQL_PASSWORD: yaweather
```

3.Загрузить и запустить сервис `php`

```
$ cd yaweather
$ docker-compose run php /bin/bash
Creating network "yaweather_default" with the default driver
Creating yaweather_db_1
root@abfe3b3ca645:/var/www/html#
```
4.Загрузить зависимости `composer`в контейнере. Обнление потребует github token (см. [установку yii2](https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/start-installation.md) ), его вы можете найти на своей странице в разделе `https://github.com/settings/tokens`

```
root@abfe3b3ca645:/var/www/html# composer update
```
5.Выполнить миграции
```
root@abfe3b3ca645:/var/www/html# chmod go+rw -R web/assets/ runtime/
root@abfe3b3ca645:/var/www/html# chmod +x yii
root@abfe3b3ca645:/var/www/html# ./yii migrate/up
```
> Если Вы хотите запустить на одной машине несколько копий такой сборки - обратите внимани на то, чтобы папки (и соответственно префикс композиции) имели разное название. Также переменные окружения для mysql необходимо дифференцировать по проектам. Несоблюдение данного правила будет приводить к ошибкам подключения к базе. 


6.Выйти из контейнера (`exit`, ctrl+c) и запустить оркестровку
```
$ docker-compose up -d
Creating network "yaweather_default" with the default driver
Creating yaweather_db_1
Creating yaweather_php_1
Creating yaweather_nginx_1
```

Сервис доступен по адресу `0.0.0.0:8080`. 


Для работы с xdebug необходимо установить куки (например плагином firefox "The easiest Xdebug") и настроить IDE - на примере PHPStorm: 
В настройках оркестровки объявлена переменная окружения, содержащая адрес машины, на которой будет происходит отладка `XDEBUG_CONFIG: remote_host=192.168.1.39`
Сервер удобнее настроить после принятия подключения. С данными настройками xdebug настройка IDE минимальна. В предложенном изменить path mapping.
`Settings > Languages & Frameworks > PHP > Servers: Use path mapping (/home/user/yaweather/php-code:/var/www/html)`
