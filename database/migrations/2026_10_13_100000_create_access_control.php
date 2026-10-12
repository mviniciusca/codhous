<?php

use App\Enums\AccessArea;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

/**
 * Controle de acesso por área, usuários ativos/inativos e convites.
 *
 * Até aqui qualquer usuário tinha acesso total ao painel. Para não trancar ninguém
 * do lado de fora no deploy, todo usuário sem perfil vira Administrador; quem tinha
 * os papéis antigos do seeder recebe as áreas equivalentes.
 */
return new class extends Migration
{
    /** Papéis antigos (RolesAndPermissionsSeeder) => áreas equivalentes */
    protected const LEGACY_ROLES = [
        'vendedor' => ['comercial', 'catalogo'],
        'financeiro' => ['comercial', 'catalogo'],
        'atendimento' => ['comercial'],
        'admin' => ['comercial', 'catalogo', 'website', 'configuracoes', 'logs'],
        'super_admin' => ['comercial', 'catalogo', 'website', 'configuracoes', 'logs'],
    ];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('password');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('guard_name');
            $table->boolean('is_system')->default(false)->after('description');
        });

        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name')->nullable();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            // Só o hash do token fica no banco; o link enviado por e-mail leva o token em claro
            $table->string('token_hash', 64)->unique();
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['email', 'accepted_at']);
        });

        $now = now();
        $permissionIds = [];
        foreach (AccessArea::cases() as $area) {
            $permissionIds[$area->value] = $this->firstOrInsert('permissions', ['name' => $area->permission(), 'guard_name' => 'web'], ['created_at' => $now, 'updated_at' => $now]);
        }

        $adminId = $this->firstOrInsert('roles', ['name' => 'Administrador', 'guard_name' => 'web'], [
            'description' => 'Acesso total, inclusive equipe, perfis e configurações.',
            'is_system' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('roles')->where('id', $adminId)->update(['is_system' => true]);
        $this->grant($adminId, array_values($permissionIds));

        $defaults = [
            'Comercial' => ['Orçamentos, clientes, caixa de entrada e catálogo.', ['comercial', 'catalogo']],
            'Marketing' => ['Website, conteúdo, newsletter e posts.', ['website']],
        ];
        foreach ($defaults as $name => [$description, $areas]) {
            $roleId = $this->firstOrInsert('roles', ['name' => $name, 'guard_name' => 'web'], ['description' => $description, 'created_at' => $now, 'updated_at' => $now]);
            $this->grant($roleId, array_map(fn ($area) => $permissionIds[$area], $areas));
        }

        foreach (self::LEGACY_ROLES as $name => $areas) {
            if ($roleId = DB::table('roles')->where('name', $name)->where('guard_name', 'web')->value('id')) {
                $this->grant($roleId, array_map(fn ($area) => $permissionIds[$area], $areas));
            }
        }

        $userType = 'App\\Models\\User';
        $legacyAdmins = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', $userType)
            ->whereIn('roles.name', ['admin', 'super_admin'])
            ->pluck('model_has_roles.model_id');
        $withRole = DB::table('model_has_roles')->where('model_type', $userType)->pluck('model_id');

        DB::table('users')
            ->where(fn ($query) => $query->whereNotIn('id', $withRole)->orWhereIn('id', $legacyAdmins))
            ->pluck('id')
            ->each(fn ($userId) => DB::table('model_has_roles')->insertOrIgnore([
                'role_id' => $adminId,
                'model_type' => $userType,
                'model_id' => $userId,
            ]));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::dropIfExists('user_invitations');

        DB::table('roles')->whereIn('name', ['Administrador', 'Comercial', 'Marketing'])->delete();
        DB::table('permissions')->where('name', 'like', 'area.%')->delete();

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_system']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function firstOrInsert(string $table, array $match, array $extra = []): int
    {
        return DB::table($table)->where($match)->value('id') ?? DB::table($table)->insertGetId($match + $extra);
    }

    protected function grant(int $roleId, array $permissionIds): void
    {
        foreach ($permissionIds as $permissionId) {
            DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permissionId, 'role_id' => $roleId]);
        }
    }
};
