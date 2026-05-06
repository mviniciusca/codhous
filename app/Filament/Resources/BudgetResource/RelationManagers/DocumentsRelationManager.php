<?php

namespace App\Filament\Resources\BudgetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documentos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label('Título')
                    ->helperText('Título ou identificador do documento')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->helperText('Descrição opcional sobre este documento')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file_path')
                    ->label('Arquivo do Documento')
                    ->helperText('Faça o upload de PDF, imagens ou outros tipos de documentos')
                    ->disk('public')
                    ->directory('budget-documents')
                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxSize(10240) // 10MB
                    ->required()
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->columnSpanFull()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            // Get file information
                            $fileName = $state->getClientOriginalName();
                            $fileSize = $state->getSize();
                            $mimeType = $state->getMimeType();

                            $set('file_name', $fileName);
                            $set('file_size', $fileSize);
                            $set('file_type', $mimeType);
                        }
                    }),
                Forms\Components\Hidden::make('file_name'),
                Forms\Components\Hidden::make('file_size'),
                Forms\Components\Hidden::make('file_type'),
                Forms\Components\Hidden::make('uploaded_by')
                    ->default(fn () => Auth::id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Nome do Arquivo')
                    ->searchable()
                    ->icon('heroicon-o-document')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('file_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match (true) {
                        str_contains($state, 'pdf')   => 'PDF',
                        str_contains($state, 'image') => 'Imagem',
                        str_contains($state, 'word')  => 'Word',
                        default                       => 'Outro',
                    })
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'pdf')   => 'danger',
                        str_contains($state, 'image') => 'success',
                        str_contains($state, 'word')  => 'info',
                        default                       => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('file_size')
                    ->label('Tamanho')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2).' KB')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Enviado por')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data de Upload')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Enviar Documento')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Ensure uploaded_by is set
                        $data['uploaded_by'] = Auth::id();

                        return $data;
                    })
                    ->after(function () {
                        Notification::make()
                            ->title('Documento enviado com sucesso')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('download')
                        ->label('Baixar')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->action(function ($record) {
                            if ($record->fileExists()) {
                                return response()->download(
                                    $record->getFullPath(),
                                    $record->file_name
                                );
                            } else {
                                Notification::make()
                                    ->title('Arquivo não encontrado')
                                    ->body('O arquivo não existe no armazenamento.')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('preview')
                        ->label('Visualizar')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn ($record) => $record->getDownloadUrl())
                        ->openUrlInNewTab()
                        ->visible(fn ($record) => $record->fileExists()),
                    Tables\Actions\EditAction::make()
                        ->label('Editar'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir')
                        ->after(function () {
                            Notification::make()
                                ->title('Documento excluído com sucesso')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Nenhum documento ainda')
            ->emptyStateDescription('Faça o upload de documentos relacionados a este orçamento')
            ->emptyStateIcon('heroicon-o-document');
    }
}
