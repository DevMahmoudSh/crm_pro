<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'phone', 
        'external_id', 
        'created_at', 
        'updated_at'
    ];

    // العميل لديه طلبات كثيرة
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function payments()
{
    return $this->hasMany(Payment::class);
}

    // علاقة لجلب آخر طلب (للعرض في الجدول)
    public function latestOrder() {
        return $this->hasOne(Order::class)->latestOfMany();
    }

    // علاقة لجلب آخر دفعة (للعرض في الجدول)
    public function latestPayment() {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}