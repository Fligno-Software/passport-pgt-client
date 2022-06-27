<?php

use Illuminate\Support\Facades\Route;

Route::post('login', passportPgtClient()->getLoginAuthController())->name('login');
Route::post('logout', passportPgtClient()->getLogoutAuthController())->name('logout');
Route::get('me', passportPgtClient()->getMeAuthController())->name('me');
Route::post('refresh-token', passportPgtClient()->getRefreshTokenAuthController())->name('refresh_token');
