

<h2>Instalación</h2> 

```sh 
composer require lamplighter/permissions
```



Despues de haber instalado procede a agregar en los providers que se encuentra en <strong>config/app.php</strong>
```php
\Lamplighter\Permissions\PermissionServiceProvider::class
```

Ejecutar los siguientes comandos

```php
php artisan permissions:install
```

```php
php artisan migrate
```

<h2>Uso</h2>

Para agregar tus rutas a los permisos se debe poner "permissions" al final del nombre de tu ruta

```php 
Route::get('foo',[Controller::class,'foo'])->name('foo:permissions')
```

Despues de esto ejecutar el comando

```sh 
artisan permissions:generate
```







