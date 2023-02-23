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

.
