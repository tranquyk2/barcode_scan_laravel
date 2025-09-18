<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode1', 'barcode2', 'quantity', 'result', 'user_id', 'time', 'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
