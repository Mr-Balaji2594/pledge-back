<?php

namespace App\Http\Controllers;

use App\Models\Pledges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hashids\Hashids;

class PledgesController extends Controller
{
    public function getAll()
    {
        $hashids = new Hashids('', 10);

        $pledges = Pledges::all()->map(function ($pledge) use ($hashids) {
            return [
                'pledge_id' => $hashids->encode((int)$pledge->pledge_id),
                'customer_id' => $pledge->customer_id,
                'customer_name' => $pledge->customer_name,
                'loan_id' => $pledge->loan_id,
                'ornament_name' => $pledge->ornament_name,
                'ornament_nature' => $pledge->ornament_nature,
                'date_of_pledge' => $pledge->date_of_pledge,
                'current_rate_per_gram' => $pledge->current_rate_per_gram,
                'weight' => $pledge->weight,
                'fixed_percent_loan' => $pledge->fixed_percent_loan,
                'interest_rate' => $pledge->interest_rate,
                'date_of_maturity' => $pledge->date_of_maturity,
                'late_payment_interest' => $pledge->late_payment_interest,
                'amount' => $pledge->amount,
                'sgst' => $pledge->sgst,
                'cgst' => $pledge->cgst,
                'grand_total' => $pledge->grand_total,
                'created_at' => $pledge->created_at,
                'updated_at' => $pledge->updated_at,
            ];
        });

        return response()->json($pledges);
    }

    public function createData(Request $request)
    {
        // ✅ Validation rules
        $rules = [
            'customer_id'           => 'required|exists:customers,customer_id',
            'customer_name'         => 'required|string|max:100',
            'loan_id'               => 'required|string|max:20|unique:pledges,loan_id',
            'ornament_name'         => 'required|string|max:100',
            'ornament_nature'       => 'required|in:Silver,Gold,Platinum',
            'date_of_pledge'        => 'required|date',
            'current_rate_per_gram' => 'required|numeric|min:0',
            'weight'                => 'required|numeric|min:0',
            'fixed_percent_loan'    => 'required|numeric|min:0|max:100',
            'interest_rate'         => 'required|numeric|min:0|max:100',
            'date_of_maturity'      => 'required|date|after_or_equal:date_of_pledge',
            'late_payment_interest' => 'required|numeric|min:0|max:100',
            'amount'                => 'required|numeric|min:0',
            'sgst'                  => 'required|numeric|min:0',
            'cgst'                  => 'required|numeric|min:0',
            'grand_total'           => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        // ❌ Validation failed
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ✅ Create pledge
        $pledge = Pledges::create($request->all());

        return response()->json([
            'message' => 'PLEDGE CREATED',
        ], 201);
    }

    public function deleteData($id)
    {
        $hashids = new Hashids('', 10);
        $decodedId = $hashids->decode($id);

        if (empty($decodedId)) {
            return response()->json(['error' => 'INVALID PLEDGE ID'], 400);
        }

        $pledge = Pledges::find($decodedId[0]);

        if (!$pledge) {
            return response()->json(['error' => 'PLEDGE NOT FOUND'], 404);
        }

        $pledge->delete();

        return response()->json(['message' => 'PLEDGE DELETED'], 200);
    }
}
