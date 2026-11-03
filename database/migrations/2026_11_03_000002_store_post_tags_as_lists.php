<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Converts tags saved by the admin as one comma-separated string back into a JSON list. */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['posts', 'blog_posts'] as $table) {
            foreach (DB::table($table)->whereNotNull('tags')->get(['id', 'tags']) as $row) {
                $tags = json_decode($row->tags, true);

                if (is_string($tags)) {
                    $list = array_values(array_filter(array_map('trim', explode(',', $tags))));
                    DB::table($table)->where('id', $row->id)->update(['tags' => json_encode($list)]);
                }
            }
        }
    }
};
