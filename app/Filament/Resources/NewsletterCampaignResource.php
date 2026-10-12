<?php

namespace App\Filament\Resources;

use App\Enums\AccessArea;
use App\Filament\Clusters\NewsletterCluster;
use App\Filament\Clusters\NewsletterCluster\Pages\SendNewsletter;
use App\Filament\Concerns\BelongsToArea;
use App\Filament\Resources\NewsletterCampaignResource\Pages;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignRecipient;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Histórico de envios da newsletter (somente leitura). Cada envio guarda o conteúdo
 * enviado, os números e o motivo de cada falha.
 */
class NewsletterCampaignResource extends Resource
{
    use BelongsToArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Website;
    }

    protected static ?string $model = NewsletterCampaign::class;

    protected static ?string $slug = 'history';

    protected static ?string $cluster = NewsletterCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Histórico de envios';
    }

    public static function getModelLabel(): string
    {
        return 'Envio';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Histórico de envios';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Assunto')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn (NewsletterCampaign $record) => $record->subject),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => NewsletterCampaign::statusLabels()[$state] ?? $state)
                    ->color(fn (string $state) => NewsletterCampaign::statusColors()[$state] ?? 'gray'),
                TextColumn::make('recipients_count')
                    ->label('Destinatários')
                    ->numeric()
                    ->alignEnd(),
                TextColumn::make('sent_count')
                    ->label('Enviados')
                    ->numeric()
                    ->alignEnd(),
                TextColumn::make('failed_count')
                    ->label('Falhas')
                    ->numeric()
                    ->alignEnd()
                    ->color(fn (int $state) => $state > 0 ? 'danger' : null),
                TextColumn::make('user.name')
                    ->label('Enviado por')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(NewsletterCampaign::statusLabels()),
            ])
            ->actions([
                Tables\Actions\Action::make('resume')
                    ->label('Continuar envio')
                    ->icon('heroicon-m-play')
                    ->visible(fn (NewsletterCampaign $record) => $record->isSending())
                    ->url(fn (NewsletterCampaign $record) => SendNewsletter::getUrl(['envio' => $record->id])),
                Tables\Actions\ViewAction::make()
                    ->label('Detalhes'),
            ])
            ->emptyStateIcon('heroicon-o-paper-airplane')
            ->emptyStateHeading('Nenhum envio ainda')
            ->emptyStateDescription('Os comunicados enviados para a newsletter aparecem aqui, com o resultado de cada envio.');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(3)
            ->schema([
                Section::make('Resumo')
                    ->icon('heroicon-o-chart-bar')
                    ->compact()
                    ->columns(['default' => 2, 'md' => 4])
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => NewsletterCampaign::statusLabels()[$state] ?? $state)
                            ->color(fn (string $state) => NewsletterCampaign::statusColors()[$state] ?? 'gray'),
                        TextEntry::make('recipients_count')->label('Destinatários')->numeric(),
                        TextEntry::make('sent_count')->label('Enviados')->numeric(),
                        TextEntry::make('failed_count')->label('Falhas')->numeric()
                            ->color(fn (int $state) => $state > 0 ? 'danger' : null),
                        TextEntry::make('started_at')->label('Início')->dateTime('d/m/Y H:i:s')->placeholder('—'),
                        TextEntry::make('finished_at')->label('Conclusão')->dateTime('d/m/Y H:i:s')->placeholder('Em andamento'),
                        TextEntry::make('user.name')->label('Enviado por')->placeholder('—'),
                    ]),

                Section::make('Falhas')
                    ->description('Destinatários que o servidor de e-mail não aceitou, com o motivo informado.')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->compact()
                    ->collapsible()
                    ->columnSpanFull()
                    ->visible(fn (NewsletterCampaign $record) => $record->failed_count > 0)
                    ->schema([
                        RepeatableEntry::make('failedRecipients')
                            ->hiddenLabel()
                            ->state(fn (NewsletterCampaign $record) => $record->recipients()
                                ->where('status', NewsletterCampaignRecipient::STATUS_FAILED)
                                ->orderBy('id')
                                ->get(['email', 'name', 'error', 'attempts'])
                                ->toArray())
                            ->columns(['default' => 1, 'md' => 4])
                            ->schema([
                                TextEntry::make('email')->label('E-mail')->copyable(),
                                TextEntry::make('name')->label('Nome')->placeholder('—'),
                                TextEntry::make('error')->label('Motivo')->columnSpan(['md' => 2]),
                            ]),
                    ]),

                Section::make('Mensagem enviada')
                    ->icon('heroicon-o-envelope')
                    ->compact()
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('subject')->label('Assunto'),
                        ViewEntry::make('body')
                            ->hiddenLabel()
                            ->view('filament.newsletter.history-preview'),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterCampaigns::route('/'),
            'view' => Pages\ViewNewsletterCampaign::route('/{record}'),
        ];
    }
}
