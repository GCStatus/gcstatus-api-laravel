<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cracker extends Model
{
    /** @use HasFactory<\Database\Factories\CrackerFactory> */
    use HasFactory;

    use HasSlug;

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
     * The sluggable attribute for the cracker.
     *
     * @var string
     */
    protected $sluggable = 'name';

    /**
     * The attributes that should be casts.
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
