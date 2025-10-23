<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function getAll()
    {
        $customers = Customers::orderByDesc('customer_id')->get();
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
            'customer_image' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except(['customer_image']);

        if ($request->hasFile('customer_image')) {
            $imagePath = $request->file('customer_image')->store('pledges/customers', 'public');
            $data['customer_image'] = $imagePath;
            $this->copyToPublic($imagePath); // ✅ Auto copy for cPanel
        }

        $customer = Customers::create($data);

        $imageUrl = $customer->customer_image ? asset('storage/' . $customer->customer_image) : null;

        return response()->json([
            'message' => 'CUSTOMER CREATED',
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

        if ($customer->customer_image) {
            $customer->customer_image_url = Storage::url($customer->customer_image);
        } else {
            $customer->customer_image_url = null;
        }
        return response()->json($customer);
    }

    public function getCustomerById($id)
    {
        return response()->json(Customers::find($id));
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
