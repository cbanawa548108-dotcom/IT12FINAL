<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlockedIp;
use App\Models\User;

class BlockedIpController extends Controller
{
    public function index()
    {
        $blockedIps = BlockedIp::with('blocker', 'blockedUser')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $users = User::orderBy('fname')->get();

        return view('admin.blocked-ips', compact('blockedIps', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'block_type'  => 'required|in:user,ip',
            'user_id'     => 'required_if:block_type,user|nullable|exists:users,User_ID',
            'ip_address'  => 'required_if:block_type,ip|nullable|ip',
            'reason'      => 'nullable|string|max:255',
        ]);

        if ($request->block_type === 'user') {
            // Block specific user
            $user = User::where('User_ID', $request->user_id)->first();

            BlockedIp::updateOrCreate(
                ['user_id' => $user->User_ID],
                [
                    'ip_address'    => $user->last_login_ip ?? $request->ip(),
                    'reason'        => $request->reason,
                    'blocked_until' => null,
                    'blocked_by'    => auth()->user()->User_ID,
                ]
            );

            return back()->with('success', "User {$user->fname} {$user->lname} has been blocked.");
        }

        // Block by IP (affects all users on that IP)
        BlockedIp::updateOrCreate(
            ['ip_address' => $request->ip_address, 'user_id' => null],
            [
                'reason'        => $request->reason,
                'blocked_until' => null,
                'blocked_by'    => auth()->user()->User_ID,
            ]
        );

        return back()->with('success', "IP {$request->ip_address} has been blocked.");
    }

    public function destroy($id)
    {
        $blocked = BlockedIp::findOrFail($id);

        $label = $blocked->user_id
            ? optional($blocked->blockedUser)->fname . ' ' . optional($blocked->blockedUser)->lname
            : $blocked->ip_address;

        $blocked->delete();

        return back()->with('success', "{$label} has been unblocked.");
    }
}