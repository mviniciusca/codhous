<?php

namespace App\Enums;

/**
 * Áreas do painel. Cada área é uma permissão (area.<valor>) e cada resource ou
 * página declara a sua (App\Filament\Concerns\BelongsToArea). Um perfil de acesso
 * é o conjunto de áreas liberadas; o que aparece na tela de perfis é o que o
 * servidor aplica.
 */
enum AccessArea: string
{
    case Comercial = 'comercial';
    case Catalogo = 'catalogo';
    case Website = 'website';
    case Configuracoes = 'configuracoes';
    case Equipe = 'equipe';
    case Logs = 'logs';

    public function permission(): string
    {
        return 'area.'.$this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::Comercial => 'Comercial',
            self::Catalogo => 'Produtos e operação',
            self::Website => 'Website e marketing',
            self::Configuracoes => 'Configurações do sistema',
            self::Equipe => 'Equipe e acessos',
            self::Logs => 'Registro de atividades',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Comercial => 'Orçamentos, clientes, agenda de contatos e caixa de entrada.',
            self::Catalogo => 'Produtos e preços, equipamentos, locais e áreas de entrega.',
            self::Website => 'Páginas, seções, obras, marcas, newsletter, posts e Design e SEO.',
            self::Configuracoes => 'Dados da empresa, segurança e modo de manutenção.',
            self::Equipe => 'Usuários, convites e perfis de acesso.',
            self::Logs => 'Histórico de acessos e ações no painel.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Comercial => 'heroicon-o-document-text',
            self::Catalogo => 'heroicon-o-cube',
            self::Website => 'heroicon-o-globe-alt',
            self::Configuracoes => 'heroicon-o-cog-6-tooth',
            self::Equipe => 'heroicon-o-user-group',
            self::Logs => 'heroicon-o-clock',
        };
    }

    /**
     * Só o perfil Administrador recebe: quem gerencia a equipe pode dar qualquer
     * acesso, então liberar isso a outro perfil seria escalada de privilégio.
     */
    public function isAdminOnly(): bool
    {
        return $this === self::Equipe;
    }

    /**
     * @return array<string, string> valor => rótulo, das áreas que perfis personalizados podem ter
     */
    public static function assignableOptions(): array
    {
        return collect(self::cases())
            ->reject(fn (self $area) => $area->isAdminOnly())
            ->mapWithKeys(fn (self $area) => [$area->value => $area->label()])
            ->all();
    }
}
