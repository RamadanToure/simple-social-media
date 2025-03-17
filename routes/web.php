<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirection vers la page d'accueil
Route::get('/', function () {
    return redirect(route('home'));
});

// Route temporaire pour résoudre un bug inconnu (favicon.ico)
Route::get('/favicon.ico', function () {
    return redirect(route('home'));
});

// Groupes de routes protégées par l'authentification et la vérification de l'email
Route::group(['middleware' => ['auth', 'verified']], function () {
    // Page d'accueil
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    // Gestion des posts
    Route::resource('/posts', PostController::class)->names('posts');

    // Flux des publications des utilisateurs suivis
    Route::get('/feeds', [PostController::class, 'followers'])->name('feeds');

    // Gestion des utilisateurs (exclut certaines actions)
    Route::resource('/manage/users', "App\Http\Controllers\UserController")->except(['create', 'show', 'store'])->names('users');

    // Profil d'un utilisateur
    Route::get('/{username}', "App\Http\Controllers\ProfileController@show")->name('profile');
});
