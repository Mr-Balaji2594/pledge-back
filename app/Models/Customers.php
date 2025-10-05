<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customers extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'customer_name',
        'customer_image',
        'dob',
        'door_street',
        'area',
        'taluk',
        'district',
        'state',
        'pincode',
        'mobile_no',
        'email_id',
        'pan_no',
        'aadhar_no',
        'gst_no',
        'bank_name',
        'account_no',
        'ifsc_code',
        'micr_code',
        'branch',
        'address'
    ];
    public $timestamps = true;

    protected $hidden = ['created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
