

<h2>Instalación</h2> 

<div style="border:1px solid #ccc !important;padding:10px;height:100px">
  <strong>composer require lamplighter/permissions</strong>
</div>

<pre>
  fasdfasdfasdfasd
</pre>



Despues de haber instalado procede a agregar \Lamplighter\Permissions\PermissionServiceProvider::class en los providers que se encuentra en config/app.php


Ejecutar los siguientes comandos

php artisan vendor:pusblish--tag="permissions-config"

php artisan vendor:publish --tag="permissions-migrations"

php artisan migrate

Para agregar tus rutas a los permisos se debe poner "permissions" al final del nombre de tu ruta

Route::get('foo',[Controller::class,'foo'])->name('foo:permissions');

despues de esto ejecutar el comando

php artisan permissions:generate







