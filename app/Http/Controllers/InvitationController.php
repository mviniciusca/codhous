<?php

namespace App\Http\Controllers;

use App\Models\UserInvitation;
use App\Services\Team\InvitationService;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * Página pública do convite: a pessoa define nome e senha e entra no painel.
 */
class InvitationController extends Controller
{
    public function show(string $token)
    {
        if (Auth::check()) {
            return response()->view('invitation.status', ['state' => 'signed-in'], 409);
        }

        $invitation = UserInvitation::findUsable($token);

        if (! $invitation) {
            return response()->view('invitation.status', ['state' => 'invalid'], 410);
        }

        return view('invitation.accept', [
            'invitation' => $invitation->loadMissing('role', 'inviter'),
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token, InvitationService $invitations)
    {
        if (Auth::check()) {
            return response()->view('invitation.status', ['state' => 'signed-in'], 409);
        }

        $invitation = UserInvitation::findUsable($token);

        if (! $invitation) {
            return response()->view('invitation.status', ['state' => 'invalid'], 410);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()->min(8)],
        ], [], [
            'name' => 'nome',
            'password' => 'senha',
        ]);

        $user = $invitations->accept($invitation, $data['name'], $data['password']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->to(Filament::getPanel('admin')->getUrl());
    }
}
