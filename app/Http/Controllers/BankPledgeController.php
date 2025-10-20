<?php

namespace App\Http\Controllers;

use App\Models\Pledges;
use Illuminate\Http\Request;

class BankPledgeController extends Controller
{

    public function getLoanId($loanId)
    {
        if (!$loanId) {
            return response()->json(['error' => 'LOAN ID NOT PROVIDED'], 400);
        }

        $pledge = Pledges::where('loan_id', $loanId)->first();

        if (!$pledge) {
            return response()->json(['error' => 'PLEDGE NOT FOUND'], 404);
        }

        return response()->json(['pledge' => $pledge]);
    }
}
