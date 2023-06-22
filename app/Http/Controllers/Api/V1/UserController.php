<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Traits\HttpResponse;
use App\Models\User;
use App\Http\Resources\V1\UserResource;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    use HttpResponse;

    public function index()
    {
        return UserResource::collection(User::paginate(10));
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "email" => "required|email",
        ]);

        if($validator->fails()) {
            return $this->error("Data invalid", 422, $validator->errors());
        }

        $validated = (object) $validator->validated();

        $updated = $user->update([
            "name" => $validated->name,
            "email" => $validated->email,
        ]);

        if($updated) {
            return $this->success("User updated with success", 200, new UserResource($user));
        }

        return $this->error("Updated failed", 400);
    }

    public function destroy(User $user)
    {

        $deleted = $user->delete();

        if($deleted) {
            return $this->success("User deleted with success");
        }
        
        return $this->error("Failed to delete user");
    }
}
