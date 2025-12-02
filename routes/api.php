<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIJobController;
use App\Http\Controllers\APINewsController;
use App\Http\Controllers\APIPostController;
use App\Http\Controllers\APITermController;
use App\Http\Controllers\APITenantController;
use App\Http\Controllers\APIProgramController;

Route::get('/tenant/{tenant_id}', [APITenantController::class, 'info']);
Route::get('/news/{tenant_id}', [APINewsController::class, 'list']);
Route::get('/news/{tenant_id}/{news_slug}', [APINewsController::class, 'read']);

Route::get('/{tenant_id}/posts', [APIPostController::class, 'list']);
Route::get('/{tenant_id}/posts/{slug}', [APIPostController::class, 'read']);

Route::get('/{tenant_id}/jobs', [APIJobController::class, 'list']);
Route::get('/{tenant_id}/jobs/{slug}', [APIJobController::class, 'read']);

Route::get('/{tenant_id}/programs', [APIProgramController::class, 'list']);
Route::get('/{tenant_id}/programs/{slug}', [APIProgramController::class, 'read']);

Route::get('/{tenant_id}/terms', [APITermController::class, 'list']);
Route::get('/{tenant_id}/terms/{slug}', [APITermController::class, 'read']);