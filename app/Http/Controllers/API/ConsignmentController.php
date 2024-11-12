<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consignment;
use App\Traits\ApiResponseTrait;
use Illuminate\Validation\ValidationException;

class ConsignmentController extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        $consignments = Consignment::all();
        return $this->ApiSendResponse('Consignment Fetched Successfully!', '201', $consignments);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'consignment_id' => 'required|string',
                'size' => 'required|string',
                'type' => 'required|string',
                'shipment_status' => 'required|in:completed,pending,received,error',
                'received_date' => 'nullable|date',
                'release_date' => 'nullable|date'
            ]);

            Consignment::newConsignment($request);

            return $this->ApiSendResponse('Consignment Created Successfully!', '201', $data);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the consignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Consignment $consignment)
    {

        try {
            $data = $request->validate([
                'consignment_id' => 'required|string',
                'size' => 'required|string',
                'type' => 'required|string',
                'shipment_status' => 'required|in:completed,pending,received,error',
                'received_date' => 'nullable|date',
                'release_date' => 'nullable|date'
            ]);

            Consignment::updateConsignment($request, $consignment);

            return $this->ApiSendResponse('Consignment Updated Successfully!', '200', $data);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the consignment',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function destroy(Consignment $consignment)
    {
        Consignment::deleteConsignment($consignment);
        return $this->ApiSendResponse('Consignment Deleted Successfully!', '200', $consignment);
    }

}
