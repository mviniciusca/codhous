<?php

namespace App\Filament\Resources;

use App\Models\Mail;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Services\SendMailService;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\MailResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MailResource extends Resource
{
    protected static ?string $model = Mail::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public static function getNavigationBadge(): ?string
    {
        return self::count();
    }

    protected static ?string $navigationGroup = 'Comunicação';
    protected static ?int $navigationSort = 1;

    public static function count(): ?string
    {
        $count = Mail::query()
            ->where('is_read', 0)
            ->where('is_sent', 0)
            ->where('is_spam', 0)
            ->count();

        return $count > 0 ? (string) $count : null;
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

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Caixa de Entrada')
            ->headerActions([
                Action::make('compose_mail')
                    ->label('Nova Mensagem')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->modal(true)
                    ->closeModalByClickingAway(false)
                    ->form([
                        Hidden::make('is_sent')
                            ->default(true),
                        TextInput::make('name')
                            ->label('Remetente:')
                            ->helperText('Seu nome ou nome da empresa que aparecerá no e-mail.')
                            ->required()
                            ->placeholder('Ex: Codhous Software')
                            ->maxLength(255)
                            ->default(env('MAIL_FROM_NAME') ?? 'Codhous Software'),
                        TextInput::make('sending_mail')
                            ->label('E-mail de Envio:')
                            ->helperText('O endereço de e-mail configurado para disparos.')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->default(env('MAIL_FROM_ADDRESS')),
                        TextInput::make('email')
                            ->label('Para:')
                            ->helperText('Endereço de e-mail do destinatário.')
                            ->email()
                            ->maxLength(200)
                            ->required()
                            ->placeholder('email@exemplo.com'),
                        TextInput::make('subject')
                            ->label('Assunto:')
                            ->helperText('O título/assunto que aparecerá na caixa do destinatário.')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Assunto do e-mail'),
                        RichEditor::make('message')
                            ->label('Mensagem:')
                            ->helperText('Escreva o conteúdo da sua mensagem. Máximo de 5000 caracteres.')
                            ->required()
                            ->maxLength(5000),
                    ])
                    ->action(function (?array $data): void {
                        $mail = new SendMailService($data);
                        $mail->send();
                    }),
            ])
            ->description('Gerencie as mensagens e e-mails recebidos através do site.')
            ->columns([
                Split::make([
                    TextColumn::make('name')
                        ->label('Nome')
                        ->weight('bold')
                        ->searchable()
                        ->formatStateUsing(fn($state) => self::normalizeText($state))
                        ->description(fn(Mail $record) => $record->email)
                        ->grow(false),
                    Stack::make([
                        TextColumn::make('subject')
                            ->label('Assunto')
                            ->weight('semibold')
                            ->searchable()
                            ->limit(100),
                        TextColumn::make('message')
                            ->label('Mensagem')
                            ->limit(150)
                            ->color('gray')
                            ->wrap()
                            ->html(),
                    ]),
                    TextColumn::make('created_at')
                        ->label('Enviado em')
                        ->dateTime('d/m/y H:i')
                        ->color('gray')
                        ->alignEnd()
                        ->description(fn(Mail $record) => $record->created_at->diffForHumans()),
                ])->extraAttributes([
                    'class' => 'py-3',
                ]),
                IconColumn::make('is_read')
                    ->label('')
                    ->boolean()
                    ->trueIcon('')
                    ->falseIcon('heroicon-m-sparkles')
                    ->falseColor('primary')
                    ->alignCenter()
                    ->grow(false),
            ])
            ->recordAction('view')
            ->recordClasses(fn(Mail $record) => match ($record->is_read) {
                0, false => ['bg-primary-50/50 dark:bg-primary-500/5 border-l-4 border-l-primary-500'],
                default => ['opacity-70'],
            })
            ->paginated(25)
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_sent')
                    ->label('Tipo de Mensagem')
                    ->indicator('Mensagens')
                    ->trueLabel('Enviadas')
                    ->falseLabel('Recebidas')
                    ->default(false),
                TernaryFilter::make('is_favorite')
                    ->label('Importante')
                    ->indicator('Favoritos')
                    ->trueLabel('Importantes')
                    ->falseLabel('Normais'),
                TernaryFilter::make('is_read')
                    ->label('Status de Leitura')
                    ->indicator('Leitura')
                    ->trueLabel('Lidas')
                    ->falseLabel('Não Lidas'),
                TernaryFilter::make('is_spam')
                    ->label('Filtro de Spam')
                    ->indicator('Spam')
                    ->trueLabel('Marcadas como Spam')
                    ->falseLabel('Não é Spam')
                    ->default(false),
                TrashedFilter::make()
                    ->indicator('Lixeira')
                    ->label('Lixeira'),
            ], FiltersLayout::BelowContent)
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->label('Responder')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('primary')
                    ->modalHeading('Responder Mensagem')
                    ->hidden(fn(Mail $record) => $record->is_sent)
                    ->form([
                        TextInput::make('email')
                            ->label('Para:')
                            ->helperText('Destinatário da resposta.')
                            ->default(fn($record) => $record->email)
                            ->readOnly(),
                        TextInput::make('subject')
                            ->label('Assunto:')
                            ->helperText('Título da resposta.')
                            ->default(fn($record) => "Re: " . $record->subject)
                            ->required(),
                        RichEditor::make('message')
                            ->label('Mensagem:')
                            ->helperText('Escreva sua resposta abaixo.')
                            ->required(),
                    ])
                    ->action(function (Mail $record, array $data) {
                        $data['name'] = env('MAIL_FROM_NAME') ?? 'Codhous Software';
                        $service = new \App\Services\SendMailService($data);
                        $service->send();
                    }),
                Tables\Actions\Action::make('mark_as_read')
                    ->label('Marcar como Lida')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn(Mail $record) => $record->is_read)
                    ->action(fn(Mail $record) => $record->update(['is_read' => true])),
                Tables\Actions\Action::make('mark_as_unread')
                    ->label('Marcar como Não Lida')
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->visible(fn(Mail $record) => $record->is_read)
                    ->action(fn(Mail $record) => $record->update(['is_read' => false])),
                ActionGroup::make([
                    Tables\Actions\DeleteAction::make()
                        ->label('Mover para Lixeira'),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Excluir Permanentemente'),
                    Tables\Actions\RestoreAction::make()
                        ->label('Restaurar Mensagem'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_read')
                        ->label('Marcar como Lidas')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn($records) => $records->each->update(['is_read' => true])),
                    Tables\Actions\BulkAction::make('mark_as_unread')
                        ->label('Marcar como Não Lidas')
                        ->icon('heroicon-o-envelope')
                        ->action(fn($records) => $records->each->update(['is_read' => false])),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Mover Selecionadas para Lixeira'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
            'index'   => Pages\ListMails::route('/'),
            'view'    => Pages\ViewMail::route('/{record}/view'),
            'create'  => Pages\CreateMail::route('/create'),
        ];
    }

    public static function normalizeText($string): string
    {
        $words = explode(' ', $string);
        $words = array_map('ucfirst', $words);

        return implode(' ', $words);
    }
}
