<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tag\TagController;
use App\Http\Controllers\Translation\TranslationController;

Route::get('/', function () {
    return view('welcome');
});


