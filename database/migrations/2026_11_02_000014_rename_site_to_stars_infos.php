<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Replaces the old site name written into seeded page text. */
return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('pages')->where('body', 'like', '%InfoHub PK%')->get(['id', 'body']) as $page) {
            DB::table('pages')->where('id', $page->id)->update(['body' => str_replace('InfoHub PK', 'Stars Infos', $page->body)]);
        }
    }
};
