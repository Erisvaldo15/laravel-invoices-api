<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Traits\HttpResponse;
use App\Models\User;
use App\Http\Resources\V1\UserResource;
use App\Http\Controllers\Controller;
use App\Traits\PaginateValidation;

class UserController extends Controller
{
    use PaginateValidation;
    use HttpResponse;

    public function index(Request $request)
    {
        return UserResource::collection(User::paginate($this->validation($request)));
    }

    public function show($id)
    {
        $validator = Validator::make(["id" => $id], [
            "id" => "required|numeric"
        ]);

        if($validator->fails()) {
            return $this->error("Data invalid", 422, $validator->errors());
        }

        $user = User::find($id);

        if(!$user) {
            return $this->error("User not found", 404, ["id" => "Id invalid"]);
        }

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

        return $this->error("Updated failed");
    }

    public function destroy($id)
    {
        $validator = Validator::make(["id" => $id], [
            "id" => "required|numeric"
        ]);

        if($validator->fails()) {
            return $this->error("Data invalid", 422, $validator->errors());
        }

        $user = User::find($id);

        if(!$user) {
            return $this->success("Failed to delete user");
        }

        $user->delete();
        
        return $this->success("Failed to delete user");
    }
}
