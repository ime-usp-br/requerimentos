<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\GlobalController;

Route::get('/', function () {
    return Inertia::render('Home');
});

# Login é uma rota criada automaticamente pela biblioteca de login da USP.

Route::get('/callback', [GlobalController::class, 'callbackHandler']);

