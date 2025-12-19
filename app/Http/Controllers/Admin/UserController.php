<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,agent,landlord,tenant',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'balance' => 0,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Người dùng đã được tạo thành công.');
    }

    public function show($id)
    {
        $user = User::with(['listings', 'payments'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        // Chỉ super admin mới có quyền sửa
        if (!auth()->user()->canEdit()) {
            abort(403, 'Bạn không có quyền chỉnh sửa thông tin người dùng.');
        }

        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        // Chỉ super admin mới có quyền sửa
        if (!auth()->user()->canEdit()) {
            abort(403, 'Bạn không có quyền chỉnh sửa thông tin người dùng.');
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,agent,landlord,tenant',
            'balance' => 'nullable|numeric|min:0',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Người dùng đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        // Chỉ super admin mới có quyền xóa
        if (!auth()->user()->canDelete()) {
            abort(403, 'Bạn không có quyền xóa người dùng.');
        }

        $user = User::findOrFail($id);
        
        // Không cho phép xóa chính mình
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Không thể xóa tài khoản của chính bạn.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Người dùng đã được xóa thành công.');
    }
}
