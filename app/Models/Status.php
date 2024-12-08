<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    /** @use HasFactory<\Database\Factories\StatusFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The available status id.
     *
     * @var int
     */
    public const AVAILABLE_STATUS_ID = 1;

    /**
     * The unavailable status id.
     *
     * @var int
     */
    public const UNAVAILABLE_STATUS_ID = 2;
}
