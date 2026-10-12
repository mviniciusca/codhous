<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Pages\Dashboard;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

/**
 * O perfil agora é um modal (App\Livewire\ProfileModal). A rota /admin/profile
 * continua existindo e abre o painel com o modal aberto.
 */
class EditProfile extends BaseEditProfile
{
    public function mount(): void
    {
        $this->redirect(Dashboard::getUrl(['perfil' => 1]));
    }
}
