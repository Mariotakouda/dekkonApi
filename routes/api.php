<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(base_path('routes/api_auth.php'));

    require base_path('routes/api_client.php');

    Route::prefix('admin')->group(base_path('routes/api_admin.php'));

});
