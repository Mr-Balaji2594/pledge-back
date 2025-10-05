<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function getAll()
    {
        $customers = Customers::all();
        return response()->json($customers);
    }

    public function createData(Request $request)
    {
        // Validation rules
        $rules = [
            'customer_name' => 'required|string|max:100',
            'dob'           => 'nullable|date',
            'door_street'   => 'required|string|max:100',
            'area'          => 'required|string|max:100',
            'taluk'         => 'required|string|max:100',
            'district'      => 'required|string|max:100',
            'state'         => 'required|string|max:50',
            'pincode'       => 'required|string|max:10',
            'mobile_no'     => 'required|string|max:15|unique:customers,mobile_no',
            'email_id'      => 'nullable|string|email|max:100|unique:customers,email_id',
            'pan_no'        => 'nullable|string|max:20',
            'aadhar_no'     => 'required|string|max:20|unique:customers,aadhar_no',
            'gst_no'        => 'nullable|string|max:20',
            'bank_name'     => 'required|string|max:100',
            'account_no'    => 'required|string|max:50',
            'ifsc_code'     => 'required|string|max:20',
            'micr_code'     => 'nullable|string|max:20',
            'branch'        => 'required|string|max:100',
            'address'       => 'sometimes|string|max:255',
            // 'customer_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ];

        // Validate request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create customer
        $customer = Customers::create($request->all());

        return response()->json([
            'message' => 'CUSTOMER CREATED',
        ], 201);
    }

    public function DeleteCustomer($id)
    {
        $customer = Customers::find($id);
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json([
            'message' => 'CUSTOMER DELETED',
        ], 200);
    }

    public function getCustomerByUuid($uuid)
    {
        $customer = Customers::where('uuid', $uuid)->first();
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }
        return response()->json($customer);
    }

    public function updateData(Request $request, $uuid)
    {
        $customer = Customers::where('uuid', $uuid)->first();
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        // Validation rules
        $rules = [
            'customer_name' => 'required|string|max:100',
            'dob'           => 'nullable|date',
            'door_street'   => 'required|string|max:100',
            'area'          => 'required|string|max:100',
            'taluk'         => 'required|string|max:100',
            'district'      => 'required|string|max:100',
            'state'         => 'required|string|max:50',
            'pincode'       => 'required|string|max:10',
            'mobile_no'     => 'required|string|max:15|unique:customers,mobile_no,NULL,id,uuid,<>' . $customer->uuid,
            'email_id'      => 'nullable|string|email|max:100|unique:customers,email_id,NULL,id,uuid,<>' . $customer->uuid,
            'pan_no'        => 'nullable|string|max:20',
            'aadhar_no'     => 'required|string|max:20|unique:customers,aadhar_no,NULL,id,uuid,<>' . $customer->uuid,
            'gst_no'        => 'nullable|string|max:20',
            'bank_name'     => 'required|string|max:100',
            'account_no'    => 'required|string|max:50',
            'ifsc_code'     => 'required|string|max:20',
            'micr_code'     => 'nullable|string|max:20',
            'branch'        => 'required|string|max:100',
            'address'       => 'sometimes|string|max:255',
            // 'customer_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ];

        // Validate request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update customer
        $customer->update($request->all());

        return response()->json([
            'message' => 'CUSTOMER UPDATED',
        ], 200);
    }
}
