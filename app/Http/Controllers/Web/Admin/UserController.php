<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['driver', 'provider'])
            ->with('providerProfile')
            ->withCount([
                'serviceRequests as completed_orders_count' => fn($q) => $q->where('status', 'completed'),
                'serviceRequests as total_orders_count',
            ]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role'))   { $query->where('role', $request->role); }
        if ($request->filled('status')) { $query->where('status', $request->status); }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load([
            'providerProfile.documents',
            'serviceRequests' => fn($q) => $q->latest()->take(10)
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function suspend(User $user)
    {
        if (in_array($user->role, ['admin', 'employee'])) {
            return back()->with('error', 'Cannot suspend admin or employee accounts.');
        }

        $user->update(['status' => 'suspended']);
        return back()->with('success', "User '{$user->name}' has been suspended.");
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', "User '{$user->name}' has been activated.");
    }

    public function destroy(User $user)
    {
        $hasActive = $user->serviceRequests()
            ->whereIn('status', ['pending', 'accepted', 'en_route', 'arrived', 'in_progress'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'Cannot delete user with active requests.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}