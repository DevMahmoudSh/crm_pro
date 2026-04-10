<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    'client_id', 
    'total_amount', 
    'payment_status', 
    'details', // <--- تأكد من وجود هذا
    'stage'   // <--- تأكد من وجود هذا
];

    // الطلب يتبع لعميل واحد (Client) وليس User
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}