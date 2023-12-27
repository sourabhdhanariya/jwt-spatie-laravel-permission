
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
Error: Call to a member function assignRole() on null in file C:\xampp\htdocs\jwt\app\Http\Controllers\UserController.php on line 125
solve error this code 