<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::fallback(function () {
    return file_get_contents(public_path('dist/index.html'));
});
