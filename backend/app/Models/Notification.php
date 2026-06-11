<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'notification_type', 'title', 'message',
        'channel', 'read_status', 'data', 'sent_at', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_status' => 'boolean',
            'data'        => 'json',
            'sent_at'     => 'datetime',
            'read_at'     => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['read_status' => true, 'read_at' => now()]);
    }

    public function scopeUnread($query)
    {
        return $query->where('read_status', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('sent_at');
    }
}
