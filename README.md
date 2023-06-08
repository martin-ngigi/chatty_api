# Chatt_api
- API for chatty application.

## requirements:
- XAMP or MYSQL DB
- php skills

## Steps
1. Create laravel project:
```
laravel new chatty_api
```
2. Generate key for security:
```
php artisan key:generate
```
3. run the project's server:
```
php artisan serve
```
- the website in -> http://127.0.0.1:8000


## Database:
1. Create database name chatty_db

## Laravel admin
1. Documentation : [Laravel Admin Documentation](https://laravel-admin.org/docs/en/installation)
2. First, install laravel, and make sure that the database connection settings are correct.
```
composer require encore/laravel-admin:1.*
```
3. Then run these commands to publish assets and config：
```
php artisan vendor:publish --provider="Encore\Admin\AdminServiceProvider"
```
4. Run following command to finish install.
```
php artisan admin:install
```
5. Type http://127.0.0.1:8000/admin so as to access admin panel.
``` 
username : admin
password : admin
```

<br><br>
- To solve "Disk [admin] not configured, please add a disk config in `config/filesystems.php`" Error:
- Then the solution is configure in your config/filesystems.php like below:
```
       'admin' => [
            'driver'     => 'local',
            'root'       => public_path('uploads'),
            'visibility' => 'public',
            'url' => env('APP_URL').'uploads/',
        ],
```
# API

- create a test controller
```
php artisan make:controller TestController
```
- create a login controller inside Api directory
```
php artisan make:controller Api\LoginController
```
- remove cache i.e. login data
```
php artisan config:cache && php artisan config:clear && composer dump-autoload -o
```
- create user model, thats if there user model doest exist
```
php artisan make:model User
```
- migrate the existing user migration to the new, but first rename the migration and delee user's table from the db
```
php artisan migrate
```
- create a middle to check user whether is logged in or not before viewing all contacts
```
php artisan make:middleware CheckUser
```

## Exposing localhost laravel webiste globally  for common WIFI users
- While on localhost, to expose the website globally, (Change IP accordingly)
run:
```
php artisan serve --host=192.168.1.104 --port=8001 
```
- NB, Both devices must be connected on the same WIFI
