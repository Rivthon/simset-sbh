<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::with('unit')
                ->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'pengelola' THEN 2 WHEN 'pimpinan' THEN 3 ELSE 4 END")
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('users.form', [
            'user' => new User(),
            'units' => Unit::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        User::create($this->validated($request));

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('users.form', [
            'user' => $user,
            'units' => Unit::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'User yang sedang login tidak bisa dihapus.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $passwordRule = $user ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'];

        $data = $request->validate([
            'unit_id' => ['nullable', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($user)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => $passwordRule,
            'role' => ['required', 'in:admin,pengelola,laboran,pimpinan'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($data['role'] === 'laboran') {
            $data['role'] = 'pengelola';
        }

        return $data;
    }
}
