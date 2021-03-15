<?php


use Illuminate\Support\Facades\Route;
use Lamplighter\Permissions\LamplighterPermissionsController;

Route::get('/permissions/get-modules',[LamplighterPermissionsController::class,'get_modules'])->name('permissions.get_modules');

Route::get('hola',function(){
    return 'hola';
});