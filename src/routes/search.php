<?php

use Illuminate\Support\Facades\Route;
use SebastianSulinski\Search\Controllers\SearchController;

Route::post('/search', SearchController::class)
    ->name('search');
