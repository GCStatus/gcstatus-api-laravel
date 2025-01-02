<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory;

    use HasSlug;
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
        'logo',
    ];

    /**
     * The sluggable attribute for store.
     *
     * @var string
     */
    protected $sluggable = 'name';
}
