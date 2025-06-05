<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect('/admin');
        } elseif (auth()->user()->isDosen()) {
            return redirect('/dosen');
        }
    }

    return view('welcome');
});

Route::middleware('auth')->get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect('/admin');
    } elseif (auth()->user()->isDosen()) {
        return redirect('/dosen');
    }

    return redirect('/');
})->name('dashboard');
