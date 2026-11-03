<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Adds Body Care as the eighth Beauty & Style topic and moves the section from pink to the site green. */
return new class extends Migration
{
    public function up(): void
    {
        $root = DB::table('categories')->where('slug', 'beauty-style')->value('id');

        if (! $root) {
            return;
        }

        if (! DB::table('categories')->where('slug', 'body-care')->exists()) {
            DB::table('categories')->insert([
                'parent_id' => $root,
                'name' => 'Body Care',
                'slug' => 'body-care',
                'color' => '#0B6B4F',
                'icon' => 'sun',
                'image' => 'categories/beauty-body-care.jpg',
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('categories')
            ->where(fn ($query) => $query->where('id', $root)->orWhere('parent_id', $root))
            ->where('color', '#B83280')
            ->update(['color' => '#0B6B4F']);
    }
};
