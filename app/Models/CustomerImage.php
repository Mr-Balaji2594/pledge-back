<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerImage extends Model
{
    protected $fillable = [
        'customer_id',
        'filename',
        'path',
        'size',
        'mime_type',
    ];

    public function customer(){
        return $this->belongsTo(Customers::class);
    }
}
