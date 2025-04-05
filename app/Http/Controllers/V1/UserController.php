<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\DriverInfo;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = $request->paging === 'false' ? User::all() : User::paginate(10);
        return UserResource::collection($users);
    }

    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();
        $data['first_name'] = ucfirst($data['first_name']);
        $data['last_name']  = ucfirst($data['last_name']);
        $user = User::create($data);

        if ($user->jobPosition->name === 'Driver') {
            DriverInfo::create([
                'user_id' => $user->id,
                'status'  => 'off trip',
            ]);
        }

        return new UserResource($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();
        if(isset($data['first_name'])) {
            $data['first_name'] = ucfirst($data['first_name']);
        }
        if(isset($data['last_name'])) {
            $data['last_name'] = ucfirst($data['last_name']);
        }
        $user->update($data);
        return new UserResource($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
