<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;
use Spatie\Permission\Models\Permission;


/**
 * Class UserController
 */
class UserController extends Controller
{
    
    /**
     * Register 
     *
     * @param UserRequest $request
     * @return Json
     */
    public function register(UserRequest $request)
    {
        $request->validated();
        
    try {
        if ($request->has('role') && $request->has('permission')) {
            $roleName = $request->input('role');
            $permissionName = $request->input('permission');

            $roleToAssign = Role::where('name', $roleName)->first();

            if (!$roleToAssign) {
                return response()->json(['error' => 'Role not found! Please contact admin'], 404);
            }
            $permissionToAssign = Permission::findOrCreate($permissionName);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($roleToAssign);
            $user->givePermissionTo($permissionToAssign);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
            ]);
        }
    } catch (\Exception $exception) {
        return response()->json(['error' => 'Permission not found! Please contact admin'], 404);
    }
}

    /**
     * Logging 
     *
     * @param LoginRequest $request
     * @param Guard $guard
     * @return \Json
     */
    public function login(LoginRequest $request, Guard $guard)
    {
       $request->validated();
        if (!$token = $guard->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized']);
        }

        return $this->respondWithToken($token);
    }
    
    /**
     * New access token.
     *
     * @param $token
     * @return Json
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
    
    /**
     * View profile and roles/permissions.
     *
     * @param ProfileRequest $request
     * @return Json
     */
    public function profile(ProfileRequest $request)
    {
        $request->validated();

    try {
        if ($request->has('role') && $request->has('permission')) {
            $roleName = $request->input('role');
            $permissionName = $request->input('permission');

            $roleToAssign = Role::where('name', $roleName)->first();

            if (!$roleToAssign) {
                return response()->json(['error' => 'Role not found! Please contact admin'], 404);
            }

            $permissionToAssign = Permission::findByName($permissionName);

            $user = auth()->user();
            $user->assignRole($roleToAssign);
            $user->givePermissionTo($permissionToAssign);

            return response()->json([
                'message' => 'You are permission for view',
                'user' => $user,
            ]);
        }
    } catch (\Exception $exception) {
        return response()->json(['error' => 'Permission not found! Please contact admin'], 404);
    }
}

    /**
     * Refresh Token.
     *
     * @return Json
     */
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ],
        ]);
    }
    /**
     * Log out 
     *
     * @return json 
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User Successfully logged out']);
    }
}
