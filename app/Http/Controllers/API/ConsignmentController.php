<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consignment;
use App\Traits\ApiResponseTrait;
use App\Services\ConsignmentService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ConsignmentController extends Controller
{
    use ApiResponseTrait;

    protected $consignmentService;

    public function __construct(ConsignmentService $consignmentService)
    {
        $this->consignmentService = $consignmentService;
    }

    /*
    public function index()
    {
        $consignments = Consignment::all();
        return $this->ApiSendResponse('Consignment Fetched Successfully!', '201', $consignments);
    }
    */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['type', 'shipment_status', 'received_date', 'sort_by', 'sort_order', 'search']);
            $consignments = $this->consignmentService->getAllConsignments($filters);

            if ($consignments->isEmpty()) {
                return $this->ApiSendResponse('Consignment not found', 404, null);
            }

            return $this->ApiSendResponse('Consignments fetched successfully!', 200, $consignments);

        } catch (Exception $e) {
            return $this->ApiSendResponse('An error occurred while fetching consignments', 500, null);
        }
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

            $consignment = $this->consignmentService->createConsignment($data);

            return $this->ApiSendResponse('Consignment Created Successfully!', '201', $consignment);

        } catch (ValidationException $e) {
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


    public function show($id)
    {
        try {
            $consignment = $this->consignmentService->getConsignmentById($id);
            return $this->ApiSendResponse('Consignment fetched successfully!', 200, $consignment);
        } catch (ModelNotFoundException $e) {
            return $this->ApiSendResponse('Consignment not found', 404, null);
        } catch (Exception $e) {
            return $this->ApiSendResponse('An error occurred while fetching the consignment', 500, null);
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
                'received_date' => 'required|date',
                'release_date' => 'nullable|date'
            ]);

            $updatedConsignment = $this->consignmentService->updateConsignment($consignment, $data);

            return $this->ApiSendResponse('Consignment Updated Successfully!', '200', $updatedConsignment);

        } catch (ValidationException $e) {
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
        try {
            $this->consignmentService->deleteConsignment($consignment);
            return $this->ApiSendResponse('Consignment Deleted Successfully!', 204, null);
        } catch (Exception $e) {
            return $this->ApiSendResponse('An error occurred while deleting the consignment', 500, null);
        }
    }

}
