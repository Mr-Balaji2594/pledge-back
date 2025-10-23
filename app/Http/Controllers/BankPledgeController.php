<?php

namespace App\Http\Controllers;

use App\Models\Pledges;
use Illuminate\Support\Facades\Storage;

class BankPledgeController extends Controller
{

    public function getLoanDetails($loanId)
    {
        if (!$loanId) {
            return response()->json(['error' => 'LOAN ID NOT PROVIDED'], 400);
        }

        // Eager load customer
        $pledge = Pledges::with('customer')->where('loan_id', $loanId)->first();

        if (!$pledge) {
            return response()->json(['error' => 'PLEDGE NOT FOUND'], 404);
        }

        $pledge->customer_image_url = $pledge->customer && $pledge->customer->customer_image
            ? Storage::url($pledge->customer->customer_image)
            : null;

        // Merge Pledge + Customer as one array
        $merged = array_merge(
            $pledge->toArray(),
            $pledge->customer ? $pledge->customer->toArray() : []
        );

        // Remove duplicate nested customer object to avoid redundancy
        unset($merged['customer']);

        return response()->json([
            'pledge' => $merged,
        ]);
    }
}
