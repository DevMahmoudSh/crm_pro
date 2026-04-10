<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['client_id', 'amount', 'method', 'notes'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}