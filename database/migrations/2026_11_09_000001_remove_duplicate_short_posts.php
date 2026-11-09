<?php

use App\Models\Post;
use App\Support\RetiredPosts;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Deleting through the model flushes the site cache.
        Post::whereIn('slug', RetiredPosts::slugs())->get()->each->delete();
    }

    public function down(): void
    {
        // Deleted posts are kept in the server backup taken before this migration ran.
    }
};
