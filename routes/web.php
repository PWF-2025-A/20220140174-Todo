<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

//     Route::get('/todo', [TodoController::class, 'index'])->name('todo.index');
//     Route::get('/todo/create', [TodoController::class, 'create'])->name('todo.create');
//     Route::get('/todo/{todo}/edit', [TodoController::class, 'edit'])->name('todo.edit');
//     Route::patch('/todo/{todo}', [TodoController::class, 'update'])->name('todo.update');
//     Route::patch('/todo/{todo}/complete', [TodoController::class, 'complete'])->name('todo.complete');
//     Route::patch('/todo/{todo}/incomplete', [TodoController::class, 'uncomplete'])->name('todo.uncomplete');
//     Route::delete('/todo/{todo}', [TodoController::class, 'destroy'])->name('todo.destroy');
//     Route::delete('/todo', [TodoController::class, 'destroyCompleted'])->name('todo.deleteallcompleted');
    
//     Route::get('/user', [UserController::class, 'index'])->name('user.index');
//     Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
//     Route::patch('/user/{user}/makeadmin', [UserController::class, 'makeadmin'])->name('user.makeadmin');
//     Route::patch('/user/{user}/removeadmin', [UserController::class, 'removeadmin'])->name('user.removeadmin');
//     Route::resource('todo', TodoController::class)->except(['show']);
// });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Todo routes - specific routes first
    Route::delete('/todo/completed', [TodoController::class, 'destroyCompleted'])->name('todo.deleteallcompleted');
    Route::patch('/todo/{todo}/complete', [TodoController::class, 'complete'])->name('todo.complete');
    Route::patch('/todo/{todo}/uncomplete', [TodoController::class, 'uncomplete'])->name('todo.uncomplete');
    Route::resource('todo', TodoController::class)->except(['show']);
    
    // Add categories routes here inside auth middleware
    Route::resource('categories', CategoryController::class);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('user', UserController::class)->except(['show']);
    Route::patch('/user/{user}/makeadmin', [UserController::class, 'makeadmin'])->name('user.makeadmin');
    Route::patch('/user/{user}/removeadmin', [UserController::class, 'removeadmin'])->name('user.removeadmin');
});

require __DIR__.'/auth.php';