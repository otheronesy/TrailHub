<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserOauthToken extends Model
{
    protected $fillable = [
        'user_id', 'provider', 'access_token', 'refresh_token', 'expires_at'
    ];

    protected $casts = ['expires_at' => 'datetime'];

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
