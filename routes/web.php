<?php

use Illuminate\Console\View\Components\Factory;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get(
    uri: '/',
    action: function (): Factory|View {
        return view(view: 'welcome');
    }
);

Route::post(
    uri: '/form/login',
    action: [
        App\Http\Controllers\FormController::class,
        'login'
    ]
);
