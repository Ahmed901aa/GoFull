<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateEmployeeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')->latest()->paginate(15);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(CreateEmployeeRequest $request)
    {
        User::create([
            'name'          => $request->name,
            'phone'         => $request->phone,
            'password'      => Hash::make($request->password),
            'role'          => 'employee',
            'employee_type' => $request->employee_type,
            'status'        => 'active',
        ]);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee account created successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'employee') {
            return back()->with('error', 'Only employee accounts can be deleted here.');
        }

        $user->delete();
        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}