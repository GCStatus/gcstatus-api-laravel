<?php

namespace App\Models;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    /** @use HasFactory<\Database\Factories\StatusFactory> */
    use HasFactory;
    use SoftDeletes;
    use CacheQueryBuilder;

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

    /**
     * The cracked status id.
     *
     * @var int
     */
    public const CRACKED_STATUS_ID = 3;

    /**
     * The uncracked status id.
     *
     * @var int
     */
    public const UNCRACKED_STATUS_ID = 4;

    /**
     * The cracked on release date status id.
     *
     * @var int
     */
    public const CRACKED_ONEDAY_STATUS_ID = 5;

    /**
     * Translate the statuses to id.
     *
     * @var array<string, int>
     */
    public const TRANSLATE_TO_ID = [
        'cracked' => self::CRACKED_STATUS_ID,
        'uncracked' => self::UNCRACKED_STATUS_ID,
        'cracked-oneday' => self::CRACKED_ONEDAY_STATUS_ID,
        null => self::UNCRACKED_STATUS_ID,
    ];
}
