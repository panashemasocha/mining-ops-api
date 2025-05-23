<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\DeleteFcmTokenRequest;
use App\Http\Requests\StoreFcmTokenRequest;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class FcmController extends Controller
{
    /**
     * Register FCM token
     */
    public function register(StoreFcmTokenRequest $request)
    {
        try {
            FcmToken::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'token' => $request->token,
                ],
                [
                    'device_type' => $request->device_type,
                ]
            );

            return response()->json([
                'info' => 'Token registered successfully',
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to register token',
                'errors' => ['general' => [$e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Unregister FCM token
     */
    public function unregister(DeleteFcmTokenRequest $request)
    {

        try {
            FcmToken::where('user_id', $request->user_id)
                ->where('token', $request->token)
                ->delete();

            return response()->json([
                'info' => 'Token unregistered successfully',
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