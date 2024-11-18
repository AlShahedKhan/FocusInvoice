<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\BusinessInformation;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BusinessInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Log::info('Business Info Index: User from Auth', ['user' => Auth::user()]);
        Log::info('Business Info Index: User from Request', ['user' => $request->user()]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $businessInformation = BusinessInformation::where('user_id', $user->id)->get();
        return response()->json($businessInformation, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('Business Info Store: User from Auth', ['user' => Auth::user()]);
        Log::info('Business Info Store: User from Request', ['user' => $request->user()]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_id' => 'nullable|string|max:255',
            'tax_identification_number' => 'nullable|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone_number' => 'required|string|max:20',
            'company_address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Error:', ['errors' => $validator->errors()]);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        Log::info('Business Info Index: User from Auth', ['user' => Auth::user()]);
        Log::info('Business Info Index: User from Request', ['user' => $request->user()]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $businessInformation = BusinessInformation::where('id', $id)->where('user_id', $user->id)->first();
        if ($businessInformation) {
            return response()->json($businessInformation, 200);
        }

        return response()->json(['error' => 'Not Found'], 404);
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
        Log::info('Business Info Store: User from Auth', ['user' => Auth::user()]);
        Log::info('Business Info Store: User from Request', ['user' => $request->user()]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        Log::info('Business Info Store: User from Auth', ['user' => Auth::user()]);
        Log::info('Business Info Store: User from Request', ['user' => $request->user()]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $businessInformation = BusinessInformation::where('id', $id)->where('user_id', $user->id)->first();
        if ($businessInformation) {
            $businessInformation->delete();
            return response()->json(null, 204);
        }

        return response()->json(['error' => 'Not Found'], 404);
    }
}
