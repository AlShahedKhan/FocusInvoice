<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BusinessInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user) {
            $businessInformation = BusinessInformation::where('user_id', $user->id)->get();
            return response()->json($businessInformation, 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $validator = Validator::make($request->all(), [
                'company_name' => 'required|string|max:255',
                'company_id' => 'nullable|string|max:255',
                'tax_identification_number' => 'nullable|string|max:255',
                'company_email' => 'required|email|max:255',
                'company_phone_number' => 'required|string|max:20',
                'company_address' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $data = $request->only([
                'company_name',
                'company_id',
                'tax_identification_number',
                'company_email',
                'company_phone_number',
                'company_address'
            ]);
            $data['user_id'] = $user->id;

            $businessInformation = BusinessInformation::create($data);
            return response()->json($businessInformation, 201);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        if ($user) {
            $businessInformation = BusinessInformation::where('id', $id)->where('user_id', $user->id)->first();
            if ($businessInformation) {
                return response()->json($businessInformation, 200);
            }
            return response()->json(['error' => 'Not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user) {
            $validator = Validator::make($request->all(), [
                'company_name' => 'sometimes|required|string|max:255',
                'company_id' => 'nullable|string|max:255',
                'tax_identification_number' => 'nullable|string|max:255',
                'company_email' => 'sometimes|required|email|max:255',
                'company_phone_number' => 'sometimes|required|string|max:20',
                'company_address' => 'sometimes|required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $businessInformation = BusinessInformation::where('id', $id)->where('user_id', $user->id)->first();
            if ($businessInformation) {
                $businessInformation->update($request->only([
                    'company_name',
                    'company_id',
                    'tax_identification_number',
                    'company_email',
                    'company_phone_number',
                    'company_address'
                ]));
                return response()->json($businessInformation, 200);
            }
            return response()->json(['error' => 'Not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user) {
            $businessInformation = BusinessInformation::where('id', $id)->where('user_id', $user->id)->first();
            if ($businessInformation) {
                $businessInformation->delete();
                return response()->json(null, 204);
            }
            return response()->json(['error' => 'Not Found'], 404);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
