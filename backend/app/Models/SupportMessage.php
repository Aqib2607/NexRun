<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    public $timestamps = false;

    protected $fillable = ['ticket_id', 'sender_id', 'message', 'attachments'];

    protected function casts(): array
    {
        return [
            'attachments' => 'json',
            'created_at'  => 'datetime',
        ];
    }

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
