<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $users = User::with('roles')
            ->when($search, fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('carnet', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:255', 'unique:users'],
            'carnet'    => ['required', 'string', 'max:255', 'unique:users'],
            'email'     => ['required', 'email', 'unique:users'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'role'      => ['required', 'exists:roles,name'],
            'photo'     => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $data['password'] = Hash::make($data['password']);
        unset($data['role']);

        $user = User::create($data);
        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', "Usuario {$user->name} creado correctamente.");
    }

    public function edit(User $user)
    {
        $roles    = Role::orderBy('name')->get();
        $userRole = $user->roles->first()?->name;
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'carnet'    => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'      => ['required', 'exists:roles,name'],
            'photo'     => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        } else {
            unset($data['photo']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        unset($data['role']);
        $user->update($data);
        $user->syncRoles($request->role);

        return redirect()->route('users.index')
            ->with('success', "Usuario {$user->name} actualizado correctamente.");
    }

    public function destroy(User $user)
    {
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Usuario eliminado correctamente.");
    }
}
