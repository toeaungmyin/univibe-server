<?php

namespace App\Http\Controllers\api\v1\admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\admin\UserCollection;
use App\Http\Resources\admin\UserDetailResource;
use App\Models\BannedUser;
use App\Models\User;
use App\Models\WarningUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile()
    {
        return response()->json(new UserDetailResource(Auth::user()));
    }

    public function index()
    {
        $users = User::role('user')->paginate(10);
        return response()->json(new UserCollection($users));
    }

    public function show(User $user)
    {
        return response()->json(new UserDetailResource($user));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->validated();

        if ($request->hasFile('profile_url')) {
            if ($user->profle_url) {
                Storage::disk('public')->delete($user->profile_url);
            }
            $data['profile_url'] = $this->uploadProfilePhoto($request->file('profile_url'));
        }

        $user->update($data);

        return response()->json(new UserDetailResource($user));
    }

    private function uploadProfilePhoto($photo)
    {
        $photoPath = $photo->store('uploads/profile', 'public');
        return $photoPath;
    }

    public function delete(User $user)
    {
        if (!$user) {
            return response()->json(['message' => 'User does not exist'], 404);
        }
        if ($user->profle_url) {
            Storage::disk('public')->delete($user->profile_url);
        }
        $user->tokens()->delete();
        $user->delete();
        return response()->json(['message' => 'Account deleted successfully']);
    }

    public function ban(User $user, Request $request)
    {
        // Assuming you have an authenticated admin user who initiates the banning
        $admin = User::role('admin')->find(Auth::user()->id);

        // Validate the reason title and description using Validator
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        // If validation fails, return a JSON response with error messages
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
        }

        $bannedUser = BannedUser::where('user_id', $user->id)->first();
        if ($bannedUser) {
            return response()->json(['message' => 'User is already banned.'], 422);
        }
        // Ban the user using the BannedUser model method
        BannedUser::create([
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Optionally you can perform additional actions like sending notifications, updating user status, etc.

        // Return a JSON response with success message
        return response()->json(['user' => new UserDetailResource($user),
            'message' => 'User has been banned successfully'
        ]);
    }

    public function unban(User $user)
    {
        $bannedUser = BannedUser::where('user_id', $user->id)->first();

        if (!$bannedUser) {
            return response()->json(['message' => 'User is not banned.'], 422); // Return a JSON response indicating the user is not banned.
        }

        $bannedUser->delete();


        return response()->json(['user' => new UserDetailResource($user),
            'message' => 'User has been unbanned successfully.'
        ]);
    }

    public function warn(User $user, Request $request)
    {
        // Assuming you have an authenticated admin user who issues the warning
        $admin = Auth::user();

        // Validate the warning title and description using Validator
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string',
        ]);

        // If validation fails, return a JSON response with error messages
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
        }

        // Issue the warning using the WarningUser model method
        WarningUser::create([
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);

        // Optionally you can perform additional actions like sending notifications, updating user status, etc.

        // Return a JSON response with success message
        return response()->json([
            'user' => new UserDetailResource($user),
            'message' => 'User has been warned successfully'
        ]);
    }

    public function deleteWarning(User $user, $warningId)
    {
        // Find the warning by its ID and ensure it belongs to the specified user
        $warning = WarningUser::where('id', $warningId)
            ->where('user_id', $user->id)
            ->first();

        // Check if the warning exists
        if (!$warning) {
            return response()->json(['message' => 'Warning not found'], 404);
        }

        // Delete the warning
        $warning->delete();

        // Optionally, you can perform additional actions like updating user status, logging the deletion, etc.

        // Return a JSON response with a success message
        return response()->json([
            'user' => new UserDetailResource($user),
            'message' => 'Warning has been deleted successfully'
        ]);
    }


    public function search(Request $request)
    {
        $query = $request->input('query'); // Get the search query from the request

        $users = User::where('username', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->paginate(20);

        return response()->json(new UserCollection($users));
    }

}
