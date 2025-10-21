<?php

use App\Http\Controllers\APINewsController;
use App\Http\Controllers\APITenantController;
use Illuminate\Support\Facades\Route;

Route::get('/tenant/{tenant_id}', [APITenantController::class, 'info']);
Route::get('/news/{tenant_id}', [APINewsController::class, 'list']);
Route::get('/news/{tenant_id}/{news_id}', [APINewsController::class, 'read']);
