<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TorrentProvider extends Model
{
    use HasSlug;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'name',
        'slug',
    ];

    /**
     * The sluggable attribute for store.
     *
     * @var string
     */
    protected $sluggable = 'name';
}