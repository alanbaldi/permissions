Intalación 

composer require lamplighter/permissions

Despues de haber instalado procede a agregarlo en los providers que se encuentra en config/app.php


Ejecutar los siguientes comandos

php artisan vendor:pusblish --tag="permissions-config"

php artisan vendor:publish --tag="permissions-migrations"

php artisan migrate

Para agregar tus rutas a los permisos se debe poner "permissions" al final del nombre de tu ruta

Route::get('foo',[Controller::class,'foo'])->name('foo:permissions');

despues de esto ejecutar el comando

php artisan permissions:generate






