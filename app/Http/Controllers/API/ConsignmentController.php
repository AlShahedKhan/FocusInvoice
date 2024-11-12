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

    /*
    public function index()
    {
        $consignments = Consignment::all();
        return $this->ApiSendResponse('Consignment Fetched Successfully!', '201', $consignments);
    }
    */
    public function index(Request $request)
    {
        $query = Consignment::query();

        if ($request->has('sort_by') && $request->has('sort_order')) {
            $sortOrder = $request->get('sort_order') === 'desc' ? 'desc' : 'asc';

            switch ($request->get('sort_by')) {
                case 'received_date':
                    $query->orderBy('received_date', $sortOrder);
                    break;

                case 'size':
                    $query->orderByRaw("CAST(REGEXP_REPLACE(size, '[^0-9]', '') AS UNSIGNED) $sortOrder");
                    break;

                case 'name':
                    $query->orderBy('consignment_id', $sortOrder);
                    break;
            }
        }

        $consignments = $query->get();
        return $this->ApiSendResponse('Consignment Fetched Successfully!', '200', $consignments);
    }



    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'consignment_id' => 'required|string',
                'size' => 'required|string',
                'type' => 'required|string',
                'shipment_status' => 'required|in:completed,pending,received,error',
                'received_date' => 'required|date',
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


    public function show(Consignment $consignment)
    {
        return $this->ApiSendResponse('Consignment Showed Successfully!', '201', $consignment);
    }


    public function update(Request $request, Consignment $consignment)
    {
        try {
            $data = $request->validate([
                'consignment_id' => 'required|string',
                'size' => 'required|string',
                'type' => 'required|string',
                'shipment_status' => 'required|in:completed,pending,received,error',
                'received_date' => 'required|date',
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
        return $this->ApiSendResponse('Consignment Deleted Successfully!', '204', $consignment);
    }

}
