<?php

use App\Enums\AccessArea;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\DesignAndSeo;
use App\Filament\Resources\ActivityResource;
use App\Filament\Resources\BudgetResource;
use App\Filament\Resources\BudgetResource\Pages\ListBudgets;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Livewire\ProfileModal;
use App\Livewire\Team\RolesPanel;
use App\Models\Budget;
use App\Models\Role;
use App\Models\User;
use App\Notifications\TeamInvitation;
use App\Services\Team\InvitationService;
use App\Services\Team\TeamService;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['name' => 'Ana Admin', 'password' => 'senha-atual-123']);
    $this->sales = User::factory()->withRole('Comercial')->create(['name' => 'Bruno Vendas']);
    $this->marketing = User::factory()->withRole('Marketing')->create(['name' => 'Carla Marketing']);
});

// ---------------------------------------------------------------- Áreas

test('perfil Comercial acessa orçamentos e não acessa equipe, configurações, website e logs', function () {
    $this->actingAs($this->sales);

    expect(BudgetResource::canViewAny())->toBeTrue()
        ->and(UserResource::canViewAny())->toBeFalse()
        ->and(SettingResource::canViewAny())->toBeFalse()
        ->and(ActivityResource::canViewAny())->toBeFalse()
        ->and(DesignAndSeo::canAccess())->toBeFalse();

    Livewire::test(ListUsers::class)->assertForbidden();
    Livewire::test(DesignAndSeo::class)->assertForbidden();
});

test('perfil Marketing não abre a lista de orçamentos nem pelo link', function () {
    $this->actingAs($this->marketing);

    expect(BudgetResource::canViewAny())->toBeFalse();
    Livewire::test(ListBudgets::class)->assertForbidden();
});

test('rota de prévia do Design e SEO exige a área Website', function () {
    $this->actingAs($this->sales)
        ->get(route('design.preview', ['token' => str_repeat('a', 64)]))
        ->assertForbidden();
});

test('usuário inativo não entra no painel e perde todas as áreas', function () {
    $this->sales->forceFill(['is_active' => false])->save();

    expect($this->sales->canAccessPanel(Filament::getPanel('admin')))->toBeFalse()
        ->and($this->sales->canAccessArea(AccessArea::Comercial))->toBeFalse();
});

test('is_active e perfil não passam por atribuição em massa', function () {
    $user = User::create(['name' => 'X', 'email' => 'x@example.com', 'password' => 'secret-123', 'is_active' => false, 'roles' => ['Administrador']]);

    expect($user->fresh()->is_active)->toBeTrue()
        ->and($user->roles()->count())->toBe(0);
});

test('perfil personalizado nunca recebe a área Equipe', function () {
    $role = Role::create(['name' => 'Tentativa', 'guard_name' => 'web']);
    $role->syncAreas(['comercial', 'equipe']);

    expect($role->fresh()->areas()->map->value->all())->toBe(['comercial']);
});

test('perfil Administrador não pode ser editado pelo painel de perfis', function () {
    $this->actingAs($this->admin);

    Livewire::test(RolesPanel::class)
        ->call('mountAction', 'editRole', ['role' => Role::admin()->id])
        ->assertForbidden();

    Livewire::test(RolesPanel::class)
        ->call('mountAction', 'deleteRole', ['role' => Role::admin()->id])
        ->assertForbidden();

    expect(Role::admin()->fresh()->areas())->toHaveCount(count(AccessArea::cases()));
});

// ---------------------------------------------------------------- Regras por usuário

test('ninguém altera o próprio acesso pela gestão de equipe', function () {
    $this->actingAs($this->admin);

    expect(Gate::inspect('delete', $this->admin)->allowed())->toBeFalse()
        ->and(Gate::inspect('toggleActive', $this->admin)->allowed())->toBeFalse()
        ->and(Gate::inspect('changeRole', $this->admin)->allowed())->toBeFalse()
        ->and(Gate::inspect('update', $this->admin)->allowed())->toBeFalse();

    Livewire::test(EditUser::class, ['record' => $this->admin->getRouteKey()])->assertForbidden();
});

test('quem não é administrador não executa ações de equipe nem chamando o serviço direto', function () {
    $this->actingAs($this->sales);

    expect(fn () => app(TeamService::class)->changeRole($this->marketing, Role::admin()))
        ->toThrow(AuthorizationException::class);

    expect($this->marketing->fresh()->isAdmin())->toBeFalse();
});

test('usuário com orçamentos é desativado, não removido', function () {
    $this->actingAs($this->admin);
    Budget::create(['user_id' => $this->sales->id, 'status' => 'pending', 'content' => ['customer_name' => 'Cliente']]);

    expect(Gate::inspect('delete', $this->sales)->message())->toContain('Desative');
    expect(fn () => app(TeamService::class)->delete($this->sales))->toThrow(AuthorizationException::class);
    expect(User::find($this->sales->id))->not->toBeNull();
});

test('administrador altera perfil de outro usuário e a ação fica registrada', function () {
    $this->actingAs($this->admin);

    Livewire::test(ListUsers::class)
        ->callTableAction('changeRole', $this->marketing, data: ['role_id' => Role::where('name', 'Comercial')->value('id')])
        ->assertHasNoTableActionErrors();

    expect($this->marketing->fresh()->role()->name)->toBe('Comercial');
    expect(Activity::where('event', 'role_changed')->latest('id')->first()->description)->toContain('Marketing → Comercial');
});

test('desativar encerra as sessões abertas e invalida o lembrar de mim', function () {
    config(['session.driver' => 'database']);
    $this->actingAs($this->admin);
    DB::table('sessions')->insert(['id' => 'sess-bruno', 'user_id' => $this->sales->id, 'payload' => '', 'last_activity' => time()]);
    $token = $this->sales->remember_token;

    Livewire::test(ListUsers::class)->callTableAction('toggleActive', $this->sales);

    expect($this->sales->fresh()->is_active)->toBeFalse()
        ->and(DB::table('sessions')->where('user_id', $this->sales->id)->exists())->toBeFalse()
        ->and($this->sales->fresh()->remember_token)->not->toBe($token);
});

// ---------------------------------------------------------------- Convites

test('convite guarda só o hash do token e é aceito uma única vez', function () {
    Notification::fake();
    $this->actingAs($this->admin);

    $invitation = app(InvitationService::class)->invite('Nova@Example.com', 'Nova Pessoa', Role::where('name', 'Marketing')->first(), $this->admin);

    $url = null;
    Notification::assertSentOnDemand(TeamInvitation::class, function (TeamInvitation $n) use (&$url) {
        $url = $n->url;

        return true;
    });
    $token = basename(parse_url($url, PHP_URL_PATH));

    expect($invitation->email)->toBe('nova@example.com')
        ->and($invitation->token_hash)->toBe(hash('sha256', $token))
        ->and(DB::table('user_invitations')->where('token_hash', $token)->exists())->toBeFalse();

    auth()->logout();

    $this->get($url)->assertOk()->assertSee('Crie seu acesso');
    $this->post($url, ['name' => 'Nova Pessoa', 'password' => 'senha-forte-1', 'password_confirmation' => 'senha-forte-1'])
        ->assertRedirect();

    $user = User::where('email', 'nova@example.com')->first();
    expect($user->role()->name)->toBe('Marketing')
        ->and(Hash::check('senha-forte-1', $user->password))->toBeTrue();
    $this->assertAuthenticatedAs($user);

    auth()->logout();
    $this->get($url)->assertStatus(410);
    $this->post($url, ['name' => 'Outra', 'password' => 'senha-forte-2', 'password_confirmation' => 'senha-forte-2'])->assertStatus(410);
    expect(User::where('email', 'nova@example.com')->count())->toBe(1);
});

test('convite vencido não abre', function () {
    Notification::fake();
    $this->actingAs($this->admin);
    $invitation = app(InvitationService::class)->invite('vence@example.com', null, Role::admin(), $this->admin);
    $token = $invitation->issueToken();
    $invitation->forceFill(['expires_at' => now()->subMinute()])->save();
    auth()->logout();

    $this->get(route('invitation.show', ['token' => $token]))->assertStatus(410);
});

test('não convida e-mail que já tem acesso', function () {
    Notification::fake();
    $this->actingAs($this->admin);

    Livewire::test(ListUsers::class)
        ->callAction('invite', data: ['email' => $this->sales->email, 'role_id' => Role::admin()->id])
        ->assertHasActionErrors(['email' => 'unique']);

    Notification::assertNothingSent();
});

// ---------------------------------------------------------------- Perfil

test('trocar e-mail no perfil exige a senha atual', function () {
    $this->actingAs($this->admin);

    Livewire::test(ProfileModal::class)
        ->callAction('editProfile', data: ['name' => 'Ana Admin', 'email' => 'nova-ana@example.com'])
        ->assertHasActionErrors(['current_password' => 'required']);

    Livewire::test(ProfileModal::class)
        ->callAction('editProfile', data: ['name' => 'Ana Admin', 'email' => 'nova-ana@example.com', 'current_password' => 'errada'])
        ->assertHasActionErrors(['current_password']);

    expect($this->admin->fresh()->email)->not->toBe('nova-ana@example.com');

    Livewire::test(ProfileModal::class)
        ->callAction('editProfile', data: ['name' => 'Ana Admin', 'email' => 'nova-ana@example.com', 'current_password' => 'senha-atual-123'])
        ->assertHasNoActionErrors();

    expect($this->admin->fresh()->email)->toBe('nova-ana@example.com');
});

test('trocar senha no perfil exige a senha atual e não grava senha no log', function () {
    $this->actingAs($this->admin);

    Livewire::test(ProfileModal::class)
        ->callAction('editProfile', data: [
            'name' => 'Ana Admin',
            'email' => $this->admin->email,
            'password' => 'nova-senha-456',
            'password_confirmation' => 'nova-senha-456',
            'current_password' => 'senha-atual-123',
        ])
        ->assertHasNoActionErrors();

    expect(Hash::check('nova-senha-456', $this->admin->fresh()->password))->toBeTrue();

    $logged = Activity::all()->map(fn ($a) => json_encode($a->properties).$a->description)->implode(' ');
    expect($logged)->not->toContain('nova-senha-456')->not->toContain('senha-atual-123');
});

test('perfil só edita nome, foto, e-mail e senha', function () {
    $this->actingAs($this->sales);

    Livewire::test(ProfileModal::class)
        ->callAction('editProfile', data: ['name' => 'Bruno V.', 'email' => $this->sales->email, 'is_active' => false, 'role_id' => Role::admin()->id])
        ->assertHasNoActionErrors();

    $fresh = $this->sales->fresh();
    expect($fresh->name)->toBe('Bruno V.')
        ->and($fresh->is_active)->toBeTrue()
        ->and($fresh->isAdmin())->toBeFalse();
});

test('/admin/profile abre o painel com o modal de perfil', function () {
    $this->actingAs($this->admin);

    Livewire::test(EditProfile::class)
        ->assertRedirect(Dashboard::getUrl(['perfil' => 1]));
});

// ---------------------------------------------------------------- Login

test('auto login de desenvolvimento não existe fora do ambiente local', function () {
    $this->get('/admin/login')->assertOk();

    $this->assertGuest();
});

test('visão geral não mostra números comerciais para quem não tem a área Comercial', function () {
    Budget::create(['user_id' => null, 'status' => 'pending', 'content' => ['customer_name' => 'Cliente Secreto', 'total' => 9999]]);
    $this->actingAs($this->marketing);

    Livewire::test(Dashboard::class)
        ->assertSee('Seus atalhos')
        ->assertSee('Páginas')
        ->assertDontSee('Cliente Secreto')
        ->assertDontSee('Agenda de entregas')
        ->assertActionHidden('create');
});
