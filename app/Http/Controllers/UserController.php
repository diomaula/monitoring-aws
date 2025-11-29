<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::orderBy('id')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'min:4',
                'max:20',
                'regex:/^[a-zA-Z0-9._-]+$/',
                'unique:users,username'
            ],
            'password' => 'required|min:5',
            'role' => 'required'
        ],[
            // name
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',

            // username
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar, silahkan gunakan username lain.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 20 karakter.',
            'username.regex' => 'Username hanya boleh huruf, angka, titik, underscore (_) atau dash (-).',

            // password
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 5 karakter.',

            // role
            'role.required' => 'Role wajib dipilih.',
        ]);


        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'min:4',
                'max:20',
                'regex:/^[a-zA-Z0-9._-]+$/',
                'unique:users,username,' . $user->id, 
            ],
            'password' => 'nullable|min:5',
            'role' => 'required',
        ], [
            //name
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',

            //username
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan user lain.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 20 karakter.',
            'username.regex' => 'Username tidak boleh menggunakan spasi.',

            //password
            'password.min' => 'Password tidak boleh kurang dari 5 karakter.',

            //role
            'role.required' => 'Role wajib dipilih.',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']); // ← ini yang membuat password tidak ikut update saat kosong
        }

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "User {$name} berhasil dihapus.");

    }
}