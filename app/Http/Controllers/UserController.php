<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show(){
        $param['roles'] = Role::all();
        // return $param;
        return view('welcome',$param);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $employee = User::with('role')
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                            ->orWhereHas('role', function($q) use ($search) {
                                $q->where('role_name', 'like', '%' . $search . '%');
                            });
            })
            ->orderBy('created_at', 'desc')->get();

        return response()->json($employee);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone|regex:/^[6-9][0-9]{9}$/',
            'description' => 'required|max:250',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'required|mimes:jpeg,png,jpg|max:2048'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        if ($file = $request->file('profile_image')) {
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'description' => $request->description,
            'role_id' => $request->role_id,
            'profile_image' => $fileName,
        ]);

        return response()->json(['message' => 'User created successfully']);
    }

    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }


}
