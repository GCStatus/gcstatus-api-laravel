<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Developer extends Model
{
    /** @use HasFactory<\Database\Factories\DeveloperFactory> */
    use HasFactory;

    use HasSlug;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'acting',
    ];

    /**
     * The sluggable attribute for store.
     *
     * @var string
     */
    protected $sluggable = 'name';

    /**
     * The attributes that should be casting.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'acting' => 'bool',
        ];
    }
}
