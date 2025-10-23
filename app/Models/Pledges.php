<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pledges extends Model
{
    use HasFactory;

    // Define table name (optional if matches plural form)
    protected $table = 'pledges';

    // Define primary key
    protected $primaryKey = 'pledge_id';

    // Disable auto timestamps (we only have created_at)
    public $timestamps = false;

    // Allow mass assignment
    protected $fillable = [
        'customer_id',
        'customer_name',
        'loan_id',
        'ornament_name',
        'ornament_nature',
        'date_of_pledge',
        'current_rate_per_gram',
        'weight',
        'fixed_percent_loan',
        'interest_rate',
        'date_of_maturity',
        'late_payment_interest',
        'amount',
        'sgst',
        'cgst',
        'grand_total',
        'image_upload',
        'aadhar_upload',
        'created_at',
    ];

    // Casts for numeric/date fields
    protected $casts = [
        'date_of_pledge' => 'date:Y-m-d',
        'date_of_maturity' => 'date:Y-m-d',
        'current_rate_per_gram' => 'decimal:2',
        'weight' => 'decimal:2',
        'fixed_percent_loan' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'late_payment_interest' => 'decimal:2',
        'amount' => 'decimal:2',
        'sgst' => 'decimal:2',
        'cgst' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];


    // Relationship: Each pledge belongs to one customer
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'customer_id');
    }

    protected $appends = ['image_url', 'aadhar_url'];

    public function getImageUrlAttribute()
    {
        return $this->image_upload ? asset('storage/' . $this->image_upload) : null;
    }

    public function getAadharUrlAttribute()
    {
        return $this->aadhar_upload ? asset('storage/' . $this->aadhar_upload) : null;
    }
}
