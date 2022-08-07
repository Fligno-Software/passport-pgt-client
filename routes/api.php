<?php

use Illuminate\Support\Facades\Route;

collect([
    'register' => 'post',
    'login' => 'post',
    'logout' => 'post',
    'me' => 'get',
    'refresh_token' => 'post',
])->each(function ($http_method, $method) {
    $method = \Illuminate\Support\Str::of($method);
    if ($controller = passportPgtClient()->getControllers($method->camel())) {
        Route::$http_method($method->slug('-'), $controller)->name('client_'.$method);
    }
});
