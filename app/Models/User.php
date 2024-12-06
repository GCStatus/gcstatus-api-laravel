<?php

namespace App\Models;

use App\Observers\UserObserver;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Notifications\{
    QueuedVerifyEmail,
    QueuedResetPassword,
};
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    BelongsTo,
    BelongsToMany,
};

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements
    MustVerifyEmail,
    JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'blocked',
        'password',
        'level_id',
        'nickname',
        'birthdate',
        'experience',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new QueuedResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueuedVerifyEmail());
    }

    /**
     * Get the level that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Level, $this>
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Get the wallet associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Wallet, $this>
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Get the profile associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Profile, $this>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get all of the transactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * The missions that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Mission, $this>
     */
    public function missions(): BelongsToMany
    {
        return $this->belongsToMany(Mission::class)->using(MissionUser::class);
    }
}
