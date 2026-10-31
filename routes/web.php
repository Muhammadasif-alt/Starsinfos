<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/search', SearchController::class)->name('search');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/author/{user}', [BlogController::class, 'author'])->name('blog.author');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::post('/subscribe', SubscriberController::class)->middleware('throttle:5,1')->name('subscribe');
Route::post('/contact', ContactController::class)->middleware('throttle:5,1')->name('contact');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// Catch-all slugs: reserved routes above win, then categories, then pages.
Route::get('/{slug}', [ContentController::class, 'show'])
    ->where('slug', '(?!admin$|livewire$|filament$)[a-z0-9-]+')
    ->name('content.show');
Route::get('/{category}/{slug}', [ContentController::class, 'nested'])
    ->where(['category' => '(?!admin$|livewire$|filament$|storage$|build$)[a-z0-9-]+', 'slug' => '[a-z0-9-]+'])
    ->name('content.nested');
