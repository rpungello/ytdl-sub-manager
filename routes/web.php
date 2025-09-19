<?php

use App\Livewire\ViewSubscription;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\ListSubscriptions::class)->name('subscriptions.index');
Route::get('/subscriptions/{key}', ViewSubscription::class)->name('subscriptions.view');
