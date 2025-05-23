<?php

namespace App\Http\Controllers\V1;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class FcmController extends Controller
{
    /**
     * Register FCM token
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'device_type' => 'required|in:android,ios',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
        
        try {
            // Update or create token
            FcmToken::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'token' => $request->token
                ],
                [
                    'device_type' => $request->device_type
                ]
            );
            
            return response()->json([
                'message' => 'Token registered successfully',
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to register token',
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }
    
    /**
     * Unregister FCM token
     */
    public function unregister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }
        
        try {
            FcmToken::where('user_id', auth()->id())
                ->where('token', $request->token)
                ->delete();
            
            return response()->json([
                'message' => 'Token unregistered successfully',
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to unregister token',
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }
}