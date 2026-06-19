<?php

use Illuminate\Support\Facades\Route;

// Public landing page
Route::get('/', function () {
    return view('public.home');
});

// Public pages
Route::get('/about', function () { return view('public.about'); });
Route::get('/verify', function () { return view('public.verify'); });
Route::get('/transparency', function () { return view('public.transparency'); });
Route::get('/contact', function () { return view('public.contact'); });
Route::get('/track-ticket', function () { return view('public.track-ticket'); });

// Public members page
Route::get('/members', function () {
    return view('public.members');
});

// Admin SPA (Vue.js) — all routes under /login, /dashboard, etc.
Route::get('/login', function () { return view('app'); });
Route::get('/dashboard', function () { return view('app'); });
Route::get('/villagers/{any?}', function () { return view('app'); })->where('any', '.*');
Route::get('/households', function () { return view('app'); });
Route::get('/grants/{any?}', function () { return view('app'); })->where('any', '.*');
Route::get('/payments', function () { return view('app'); });
Route::get('/healthcare', function () { return view('app'); });
Route::get('/education', function () { return view('app'); });
Route::get('/projects', function () { return view('app'); });
Route::get('/reports', function () { return view('app'); });
Route::get('/announcements', function () { return view('app'); });
Route::get('/messages', function () { return view('app'); });
Route::get('/audit', function () { return view('app'); });
Route::get('/users', function () { return view('app'); });
Route::get('/settings', function () { return view('app'); });
