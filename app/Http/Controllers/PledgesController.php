<?php

namespace App\Http\Controllers;

use App\Models\Pledges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hashids\Hashids;
use Illuminate\Support\Facades\Storage;

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
        // 🧩 1. Validation rules
        $rules = [
            'customer_id' => 'required|exists:customers,customer_id',
            'customer_name' => 'required|string',
            'ornament_name' => 'required|string|max:100',
            'ornament_nature' => 'required|string',
            'weight' => 'required|numeric|min:0.01',
            'current_rate_per_gram' => 'required|numeric|min:0.01',
            'fixed_percent_loan' => 'required|numeric|min:1|max:100',
            'date_of_pledge' => 'required|date',
            'date_of_maturity' => 'required|date|after:date_of_pledge',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'late_payment_interest' => 'nullable|numeric|min:0|max:100',
            'amount' => 'required|numeric|min:0.01',
            'gst_rate' => 'required|numeric',
            'sgst' => 'required|numeric',
            'cgst' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'image_upload' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'aadhar_upload' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 🧩 2. Prepare data
        $data = $request->except(['image_upload', 'aadhar_upload', 'loan_id']);

        // 🧩 3. Handle file uploads (with fallback for cPanel)
        if ($request->hasFile('image_upload')) {
            $imagePath = $request->file('image_upload')->store('pledges/images', 'public');
            $data['image_upload'] = $imagePath;
            $this->copyToPublic($imagePath); // ✅ Auto copy for cPanel
        }

        if ($request->hasFile('aadhar_upload')) {
            $aadharPath = $request->file('aadhar_upload')->store('pledges/aadhar', 'public');
            $data['aadhar_upload'] = $aadharPath;
            $this->copyToPublic($aadharPath); // ✅ Auto copy for cPanel
        }

        // 🧩 4. Auto-generate Loan ID
        if ($request->filled('loan_id')) {
            $data['loan_id'] = $request->input('loan_id');
        } else {
            $latestPledge = Pledges::latest('pledge_id')->first();
            $nextId = $latestPledge ? $latestPledge->pledge_id + 1 : 1;
            $data['loan_id'] = 'LN' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        }

        // 🧩 5. Create pledge
        $pledge = Pledges::create($data);

        // 🧩 6. Generate full URLs (for API/frontend)
        $imageUrl = $pledge->image_upload ? asset('storage/' . $pledge->image_upload) : null;
        $aadharUrl = $pledge->aadhar_upload ? asset('storage/' . $pledge->aadhar_upload) : null;

        // 🧩 7. Return API response
        return response()->json([
            'message' => 'PLEDGE CREATED',
        ], 201);
    }

    private function copyToPublic($relativePath)
    {
        $source = storage_path('app/public/' . $relativePath);
        $destination = public_path('storage/' . $relativePath);

        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }

        if (file_exists($source)) {
            copy($source, $destination);
        }
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

    public function getPledgeById($id)
    {
        $hashids = new Hashids('', 10);
        $decodedId = $hashids->decode($id);

        if (empty($decodedId)) {
            return response()->json(['error' => 'INVALID PLEDGE ID'], 400);
        }

        $pledge = Pledges::with('customer')->find($decodedId[0]);

        if (!$pledge) {
            return response()->json(['error' => 'PLEDGE NOT FOUND'], 404);
        }

        if ($pledge->image_upload) {
            $pledge->image_url = Storage::url($pledge->image_upload);
        } else {
            $pledge->image_url = null;
        }

        if ($pledge->aadhar_upload) {
            $pledge->aadhar_url = Storage::url($pledge->aadhar_upload);
        } else {
            $pledge->aadhar_url = null;
        }

        return response()->json($pledge);
    }
}
