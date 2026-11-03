<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Adds the Nail Care subcategory and photos for Beauty & Style subcategories that have none yet. */
return new class extends Migration
{
    private const IMAGES = [
        'skincare' => 'categories/beauty-skincare.jpg',
        'makeup' => 'categories/beauty-makeup.jpg',
        'hair-care' => 'categories/beauty-hair-care.jpg',
        'nail-care' => 'categories/beauty-nail-care.jpg',
        'bridal' => 'categories/beauty-bridal.jpg',
    ];

    public function up(): void
    {
        $root = DB::table('categories')->where('slug', 'beauty-style')->value('id');

        if (! $root) {
            return;
        }

        if (! DB::table('categories')->where('slug', 'nail-care')->exists()) {
            DB::table('categories')->insert([
                'parent_id' => $root,
                'name' => 'Nail Care',
                'slug' => 'nail-care',
                'color' => '#B83280',
                'icon' => 'sparkles',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (self::IMAGES as $slug => $image) {
            DB::table('categories')->where('slug', $slug)->whereNull('image')->update(['image' => $image]);
        }
    }
};
