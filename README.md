# Тестовое задание для вакансии "бэкэнд-разработчик", МИП ЦКИ, 2024

-----------------------------------

## Требования

- Сервер Apache 2.4 или Nginx Stable версия со стандартным набором расширений (для Apache требуется активный модуль mod_rewrite)
- PHP >= 8.1, с набором расширений [требуемых для работы Laravel 10.x](https://laravel.com/docs/10.x/deployment#server-requirements)
- PHP Composer

### Пример конфигурации хоста для <domain> для Apache 2.4

```
<VirtualHost *:80>
    ServerName <domain>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/<domain>/public
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### Пример конфигурации хоста для <domain> для Nginx

```
server {
    listen 80;
    listen [::]:80;
    server_name <domain>;
    root /var/www/<domain>/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Установка

- Переместить содержимое репозитория в папку хоста
    ```
    cd /var/www/<domain>
    git clone https://github.com/radiofan/task2024.git
    ```

- Установить зависимости
    ```
    composer install --optimize-autoloader --no-dev
    ```

- Произвести настройку окружения
    - Создать файл `.env` (копия файла `.env.example`)
    - Внести в файл `.env` следующие изменения:
        ```
        APP_NAME=TASK2024
        APP_ENV=production
        APP_DEBUG=false
        APP_URL=http://<domain>
        
        LOG_LEVEL=info
        
        DB_CONNECTION=sqlite
        ```
      
    - Сгенерировать шифро-ключ
        ```
        php artisan key:generate
        ```
    
- Применить миграции БД
    ```
    php artisan migrate
    ```

- Заполнить БД данными
    ```
    php artisan db:seed --class=Sensors
    ```

- Создать кэши
    ```
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

## Использование

- Записать измерение
    ```
    POST|PUT http://<domain>/api/measures/sensors/{sensors.id}
    <parameters.key>=<float>
    ```
    Ответ (формат JSON):  
        `404` - если sensors.id не найден  
        `400` - если тело запроса имеет не верный формат  
        `406` - если <float> не является числом с плавающей точкой или указаный сенсор (sensors.id) не имеет параметр с указанным ключом (parameters.key)  
        `200` - данные сохранены

- Получить измерения
    ```
    GET http://<domain>/api/measures/
    Параметры:
        null|string(datetime) start - Начало временного отрезка получения измерений, если null, то это начало текущего дня
        null|string(datetime) end - Конец временного отрезка получения измерений, если null, то это конец текущего дня
        'all'|sensors.id|sensors.id[] sensors - Ограничение списка измерений по сенсорам
        'all'|parameters.key|parameters.key[] parameters - Ограничение списка измерений по параметрам
    ```
  
    Ответ (формат JSON):  
        `406` - если start или end переданы и имеют неверный формат или sensors или parameters имеют не верный формат  
        `200`, тело:  
        
    ```
    [
        sensors.id => [
            parameters.key => [
                [
                    'value' => float,
                    'time' => int, (timestamp)
                    'microseconds' => int,
                ],
                ...
            ],
            ...
        ],
        ...
    ]
    ```