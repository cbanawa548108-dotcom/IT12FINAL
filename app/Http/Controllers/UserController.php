<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cashier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // Create Cashier
    // ─────────────────────────────────────────────────────────

    public function createCashier()
    {
        return view('users.create-cashier');
    }

    public function storeCashier(Request $request)
    {
        $validated = $request->validate([
            'fname'          => 'required|string|alpha_dash|max:255',
            'lname'          => 'required|string|alpha_dash|max:255',
            'contact_number' => 'nullable|string|regex:/^[0-9]{11}$/',
            'email'          => 'required|string|email|max:255|unique:users,email',
            'password'       => [
                'required', 'confirmed',
                Password::min(16)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        DB::beginTransaction();
        try {
            User::create([
                'fname'          => $validated['fname'],
                'lname'          => $validated['lname'],
                'contact_number' => $validated['contact_number'],
                'email'          => $validated['email'],
                'role'           => 'cashier',
                'password'       => Hash::make($validated['password']),
            ]);

            Cashier::create([
                'first_name'     => $validated['fname'],
                'last_name'      => $validated['lname'],
                'contact_number' => $validated['contact_number'],
                'email'          => $validated['email'],
                'password'       => Hash::make($validated['password']),
            ]);

            DB::commit();
            return redirect()->route('users.create-cashier')
                ->with('success', 'Cashier account created successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create cashier: ' . $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────
    // Create Manager
    // ─────────────────────────────────────────────────────────

    public function createManager()
    {
        return view('users.create-manager');
    }

    public function storeManager(Request $request)
    {
        $validated = $request->validate([
            'fname'          => 'required|string|alpha_dash|max:255',
            'lname'          => 'required|string|alpha_dash|max:255',
            'contact_number' => 'nullable|string|regex:/^[0-9]{11}$/',
            'email'          => 'required|string|email|max:255|unique:users,email',
            'password'       => [
                'required', 'confirmed',
                Password::min(16)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        User::create([
            'fname'          => $validated['fname'],
            'lname'          => $validated['lname'],
            'contact_number' => $validated['contact_number'],
            'email'          => $validated['email'],
            'role'           => 'manager',
            'password'       => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.create-manager')
            ->with('success', 'Manager account created successfully!');
    }

    // ─────────────────────────────────────────────────────────
    // Index
    // ─────────────────────────────────────────────────────────

    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('users.index', compact('users'));
    }

    // ─────────────────────────────────────────────────────────
    // Edit / Update
    // ─────────────────────────────────────────────────────────

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'fname'          => 'required|string|alpha_dash|max:255',
            'lname'          => 'required|string|alpha_dash|max:255',
            'contact_number' => 'nullable|string|regex:/^[0-9]{11}$/',
            'email'          => 'required|string|email|max:255|unique:users,email,' . $user->User_ID . ',User_ID',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', "{$user->fname} {$user->lname} updated successfully!");
    }

    // ─────────────────────────────────────────────────────────
    // Archive (soft delete)
    // ─────────────────────────────────────────────────────────

    public function archive(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('users.index')
                ->with('error', 'Cannot archive admin accounts!');
        }

        $name = "{$user->fname} {$user->lname}";
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "{$name} has been archived.");
    }

    // ─────────────────────────────────────────────────────────
    // Archived list
    // ─────────────────────────────────────────────────────────

    public function archived()
    {
        $users = User::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('users.archived', ['users' => $users]);
    }

    // ─────────────────────────────────────────────────────────
    // Restore
    // ─────────────────────────────────────────────────────────

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.archived')
            ->with('success', "{$user->fname} {$user->lname} has been restored.");
    }

    // ─────────────────────────────────────────────────────────
    // Force delete (permanent)
    // ─────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->route('users.archived')
                ->with('error', 'Cannot delete admin accounts!');
        }

        $name = "{$user->fname} {$user->lname}";
        $user->forceDelete();

        return redirect()->route('users.archived')
            ->with('success', "{$name} has been permanently deleted.");
    }

    // ─────────────────────────────────────────────────────────
    // List Cashiers / Managers
    // ─────────────────────────────────────────────────────────

    public function listCashiers()
    {
        $cashiers = User::where('role', 'cashier')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('users.list-cashiers', compact('cashiers'));
    }

    public function listManagers()
    {
        $managers = User::where('role', 'manager')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('users.list-managers', compact('managers'));
    }
}