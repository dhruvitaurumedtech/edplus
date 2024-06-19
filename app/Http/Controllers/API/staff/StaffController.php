<?php

namespace App\Http\Controllers\API\staff;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Feature;
use App\Models\Institute_detail;
use App\Models\Module;
use App\Models\RoleHasPermission;
use App\Models\Roles;
use App\Models\Staff_detail_Model;
use App\Models\User;
use App\Models\UserHasRole;
use App\Traits\ApiTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StaffController extends Controller
{
    use ApiTrait;

    public function create_role(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $role = new Roles();
            $role->role_name = $request->role_name;
            $role->created_by = Auth::id();
            if ($role->save()) {
                $user_has_role = new UserHasRole();
                $user_has_role->role_id = $role->id;
                $user_has_role->user_id = auth()->id();
                $user_has_role->save();
            }
            return $this->response([], "Role Created");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }

    public function edit_role(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edit_id' => 'required|exists:roles,id',
            'role_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $role = Roles::find($request->edit_id);
            $role->role_name = $request->role_name;
            $role->save();
            return $this->response([], "Role Updated");
        } catch (Exception $e) {
            return $this->response($e, "Invalid token.", false, 400);
        }
    }


    public function view_roles(Request $request)
    {
        try {
            $userHasRole = UserHasRole::where('role_id', '!=', 3)->where('user_id', Auth::id())->pluck('role_id');
            $roles = Roles::whereIn('id', $userHasRole)->get();
            $data = [];
            foreach ($roles as $value) {
                $data[] = ['role_id' => $value->id, 'role_name' => $value->role_name];
            }
            return $this->response($data, "Successfully Display Roles.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }

    public function Get_Permission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $actions = Action::select('id', 'name')->get();
            $modules = Module::where('type', 2)->with(['Features' => function ($query) {
                $query->select('id', 'module_id', 'feature_name');
            }])->select('id', 'module_name')->where('status', 1)->get();

            foreach ($modules as $module) {
                foreach ($module->Features as $feature) {
                    $featureActions = [];

                    foreach ($actions as $action) {
                        if (empty($request->role_id)) {
                            $hasPermission = false;
                        } else {
                            $user_has_roles = UserHasRole::where('role_id', $request->role_id)->where('user_id', Auth::id())->first();
                            $hasPermission = DB::table('role_has_permissions')
                                ->where('user_has_role_id', $user_has_roles->id)
                                ->where('feature_id', $feature->id)
                                ->where('action_id', $action->id)
                                ->exists();
                        }

                        $featureActions[] = [
                            'id' => $action->id,
                            'name' => $action->name,
                            'has_permission' => $hasPermission
                        ];
                    }

                    $feature->actions = $featureActions;
                }
            }

            return $this->response($modules, "Permissions retrieved successfully.", true, 200);
        } catch (Exception $e) {
            return $e->getMessage();
            return $this->response([], "An error occurred.", false, 400);
        }
    }

    public function User_Get_Permission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'nullable'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $actions = Action::select('id', 'name')->get();
            $modules = Module::where('type', 2)->with(['Features' => function ($query) {
                $query->select('id', 'module_id', 'feature_name');
            }])->select('id', 'module_name')->where('status', 1)->get();

            $permissionsIds = collect();
            if (empty($request->institute_id)) {
                $user = Auth::user();
                $user_has_role = UserHasRole::where('role_id', $user->role_type)->first();
                $permissionsIds = RoleHasPermission::where('user_has_role_id', $user_has_role->id)
                    ->get(['feature_id', 'action_id']);
            } else {
                $user = Auth::user();
                $institute = Institute_detail::where('id', $request->institute_id)->first();
                $user_has_role = UserHasRole::where('role_id', $user->role_type)->where('user_id', $institute->user_id)->first();
                $permissionsIds = RoleHasPermission::where('user_has_role_id', $user_has_role->id)
                    ->get(['feature_id', 'action_id']);
            }

            foreach ($modules as $module) {
                foreach ($module->Features as $feature) {
                    $featureActions = [];

                    foreach ($actions as $action) {
                        $hasPermission = $permissionsIds->contains(function ($permission) use ($feature, $action) {
                            return $permission->feature_id == $feature->id && $permission->action_id == $action->id;
                        });

                        $featureActions[] = [
                            'id' => $action->id,
                            'name' => $action->name,
                            'has_permission' => $hasPermission
                        ];
                    }
                    $feature->actions = $featureActions;
                }
            }
            $cacheKey = "user_permissions_{$user->id}";
            Cache::put($cacheKey, $modules, now()->addHours(8));
            return $this->response($modules, "Permissions retrieved successfully.", true, 200);
        } catch (Exception $e) {
            return $this->response([], "An error occurred.", false, 400);
        }
    }


    public function updateRolePermissions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*.feature_id' => 'required|exists:features,id',
            'permissions.*.actions' => 'required|array',
            'permissions.*.actions.*' => 'exists:actions,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $roleId = $request->role_id;
            $user_has_role = UserHasRole::where('role_id', $roleId)->where('user_id', Auth::id())->first();
            DB::beginTransaction();
            DB::table('role_has_permissions')->where('user_has_role_id', $user_has_role->id)->delete();
            $permissions = [];
            foreach ($request->permissions as $permission) {
                foreach ($permission['actions'] as $actionId) {
                    $permissions[] = [
                        'user_has_role_id' => $user_has_role->id,
                        'feature_id' => $permission['feature_id'],
                        'action_id' => $actionId,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }

            DB::table('role_has_permissions')->insert($permissions);
            DB::commit();
            return $this->response([], "Permissions updated successfully.", true, 200);
        } catch (Exception $e) {
            return $e->getMessage();
            DB::rollBack();
            return $this->response([], "An error occurred while updating permissions.", false, 400);
        }
    }





    public function add_staff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|min:10',
            'password' => 'required|string|min:6',
            'role_type' => 'required|integer',
            'confirm_password' => 'required|string|same:password',
            'device_key' => 'nullable',
            'country_code' => 'required',
        ]);
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
        $user = User::where('email', $request->email)->first();
        if (isset($user)) {
            return $this->response([], 'This email already exists.', false, 400);
        }
        try {
            $user = new User();
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->country_code = $request->country_code;
            $user->mobile = $request->mobile;
            $user->role_type = $request->role_type;
            $user->otp_num = rand(100000, 999999);
            $user->device_key = $request->device_key;
            $user->save();

            $staff = new Staff_detail_Model();
            $staff->institute_id = $request->institute_id;
            $staff->user_id = $user->id;
            $staff->save();

            $token = JWTAuth::fromUser($user);
            $user->token = $token;
            $user->save();
            $userdata = user::join('staff_detail', 'staff_detail.user_id', '=', 'users.id')
                ->join('institute_detail', 'institute_detail.id', '=', 'staff_detail.institute_id')
                ->where('users.email', $user->email)
                ->select('institute_detail.id')
                ->first();
            if (!empty($userdata->id)) {
                $institute_id = $userdata->id;
            } else {
                $institute_id = null;
            }
            $data = [
                'user_id' => $user->id,
                'user_name' => $user->firstname . ' ' . $user->lastname,
                'country_code' => $user->country_code,
                'mobile_no' => (int)$user->mobile,
                'user_email' => $user->email,
                'user_image' => $user->image,
                'role_type' => (int)$user->role_type,
                'institute_id' => $institute_id,
                'token' => $token,
            ];
            return $this->response($data, "Staff Added Successfully !");
        } catch (Exception $e) {
            return $this->response($e, "Something want Wrong!!", false, 400);
        }
    }
    public function view_staff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institute_id' => 'required|integer',
        ]);
        $userdata = user::join('staff_detail', 'staff_detail.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_type')
            ->where('staff_detail.institute_id', $request->institute_id)
            ->select('users.*', 'roles.role_name')
            ->get();
        try {
            $data = [];
            foreach ($userdata as $value) {
                $data[] = [
                    'user_id' => $value->id,
                    'fullname' => $value->firstname . ' ' . $value->lastname,
                    'role_name' => $value->role_name,
                    'photo' => asset('profile/no-image.png')
                ];
            }
            return $this->response($data, "Staff Fetch Successfully !");
        } catch (Exception $e) {

            return $this->response($e, "Something want Wrong!!", false, 400);
        }
        if ($validator->fails()) return $this->response([], $validator->errors()->first(), false, 400);
    }
    public function delete_staff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            user::where('id', $request->staff_id)->delete();
            return $this->response([], "Successfully Deleted Staff.");
        } catch (Exception $e) {
            return $this->response([], "Invalid token.", false, 400);
        }
    }
}
