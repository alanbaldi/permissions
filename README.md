

<h2>Instalación</h2> 

<pre>
  composer require lamplighter/permissions
</pre>



Despues de haber instalado procede a agregar <strong>\Lamplighter\Permissions\PermissionServiceProvider::class</strong> en los providers que se encuentra en <strong>config/app.php</strong>


Ejecutar los siguientes comandos

<pre>

php artisan vendor:publish--tag="permissions-config"

php artisan vendor:publish --tag="permissions-migrations"

php artisan migrate

</pre>

<h2>Uso</h2>

Para agregar tus rutas a los permisos se debe poner "permissions" al final del nombre de tu ruta

```php Route::get('foo',[Controller::class,'foo'])->name('foo:permissions')```

Despues de esto ejecutar el comando

<pre>php artisan permissions:generate</pre>







