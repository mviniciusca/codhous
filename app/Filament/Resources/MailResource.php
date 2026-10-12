<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailResource\Pages;
use App\Models\Mail;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Caixa de entrada: mensagens do formulário do site e e-mails enviados pelo painel.
 * A tela é uma caixa de dois painéis (Pages\ListMails), sem tabela do Filament.
 */
class MailResource extends Resource
{
    use \App\Filament\Concerns\BelongsToArea;

    public static function getAccessArea(): \App\Enums\AccessArea
    {
        return \App\Enums\AccessArea::Comercial;
    }

    protected static ?string $model = Mail::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $activeNavigationIcon = 'heroicon-s-inbox';

    protected static ?string $navigationGroup = 'Website';

    protected static ?int $navigationSort = 1;

    /** Pastas da caixa, na ordem das abas */
    public const FOLDERS = [
        'inbox' => ['label' => 'Entrada', 'icon' => 'heroicon-m-inbox'],
        'unread' => ['label' => 'Não lidas', 'icon' => 'heroicon-m-envelope'],
        'starred' => ['label' => 'Importantes', 'icon' => 'heroicon-m-star'],
        'sent' => ['label' => 'Enviadas', 'icon' => 'heroicon-m-paper-airplane'],
        'spam' => ['label' => 'Spam', 'icon' => 'heroicon-m-shield-exclamation'],
        'trash' => ['label' => 'Lixeira', 'icon' => 'heroicon-m-trash'],
    ];

    public static function getNavigationBadge(): ?string
    {
        return self::count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Mensagens não lidas';
    }

    /** Mensagens recebidas ainda não lidas (fora do spam e da lixeira) */
    public static function count(): ?string
    {
        $count = self::folderQuery('unread')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function folderQuery(string $folder): Builder
    {
        $query = Mail::query();

        return match ($folder) {
            'unread' => $query->where('is_sent', false)->where('is_spam', false)->where('is_read', false),
            'starred' => $query->where('is_favorite', true)->where('is_spam', false),
            'sent' => $query->where('is_sent', true),
            'spam' => $query->where('is_spam', true),
            'trash' => $query->onlyTrashed(),
            default => $query->where('is_sent', false)->where('is_spam', false),
        };
    }

    public static function getNavigationLabel(): string
    {
        return 'Caixa de Entrada';
    }

    public static function getModelLabel(): string
    {
        return 'Mensagem';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Caixa de Entrada';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMails::route('/'),
            // Links antigos (notificação por e-mail) continuam funcionando: abrem a mensagem na caixa
            'view' => Pages\ViewMail::route('/{record}/view'),
            'create' => Pages\CreateMail::route('/create'),
        ];
    }

    public static function normalizeText($string): string
    {
        $words = explode(' ', $string);
        $words = array_map('ucfirst', $words);

        return implode(' ', $words);
    }
}
