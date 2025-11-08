<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;

class WargaProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nik',
        'kk',
        'phone',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'address',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'postal_code',
        'occupation',
        'marital_status',
        'religion',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'phone' => E164PhoneNumberCast::class.':ID',
        ];
    }

    /**
     * Get the user that owns the warga profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
