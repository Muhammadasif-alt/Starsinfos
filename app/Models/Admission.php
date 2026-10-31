<?php

namespace App\Models;

use App\Models\Concerns\FlushesSiteCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use FlushesSiteCache, HasFactory;

    protected $fillable = ['program', 'type', 'last_date', 'test_date', 'link', 'sort_order'];

    protected function casts(): array
    {
        return [
            'last_date' => 'date',
            'test_date' => 'date',
        ];
    }
}
