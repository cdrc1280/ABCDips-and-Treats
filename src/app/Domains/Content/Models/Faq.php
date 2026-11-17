<?php

namespace App\Domains\Content\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasUuids;

    protected $fillable = ['question', 'answer', 'category', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
