<?php

use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\ListSubscriptions::class)->name('subscriptions.index');
