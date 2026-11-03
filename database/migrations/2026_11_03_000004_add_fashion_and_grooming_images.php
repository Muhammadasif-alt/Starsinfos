<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Photos for the two Beauty & Style subcategories that still showed only an icon. */
return new class extends Migration
{
    private const IMAGES = [
        'fashion-clothing' => 'categories/beauty-fashion.jpg',
        'mens-grooming' => 'categories/beauty-mens-grooming.jpg',
    ];

    public function up(): void
    {
        foreach (self::IMAGES as $slug => $image) {
            DB::table('categories')->where('slug', $slug)->whereNull('image')->update(['image' => $image]);
        }
    }
};
