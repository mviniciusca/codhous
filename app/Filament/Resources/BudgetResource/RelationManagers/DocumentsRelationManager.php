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

    protected static ?string $title = 'Documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->label(__('Title'))
                    ->helperText(__('Document title or identifier'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('Description'))
                    ->helperText(__('Optional description about this document'))
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('file_path')
                    ->label(__('Document File'))
                    ->helperText(__('Upload PDF, images, or other document types'))
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
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('file_name')
                    ->label(__('File Name'))
                    ->searchable()
                    ->icon('heroicon-o-document')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('file_type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match (true) {
                        str_contains($state, 'pdf')   => 'PDF',
                        str_contains($state, 'image') => 'Image',
                        str_contains($state, 'word')  => 'Word',
                        default                       => 'Other',
                    })
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'pdf')   => 'danger',
                        str_contains($state, 'image') => 'success',
                        str_contains($state, 'word')  => 'info',
                        default                       => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('file_size')
                    ->label(__('Size'))
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2).' KB')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('uploader.name')
                    ->label(__('Uploaded By'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Upload Date'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Upload Document'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Ensure uploaded_by is set
                        $data['uploaded_by'] = Auth::id();

                        return $data;
                    })
                    ->after(function () {
                        Notification::make()
                            ->title(__('Document uploaded successfully'))
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('download')
                        ->label(__('Download'))
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
                                    ->title(__('File not found'))
                                    ->body(__('The file does not exist in storage.'))
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('preview')
                        ->label(__('Preview'))
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn ($record) => $record->getDownloadUrl())
                        ->openUrlInNewTab()
                        ->visible(fn ($record) => $record->fileExists()),
                    Tables\Actions\EditAction::make()
                        ->label(__('Edit')),
                    Tables\Actions\DeleteAction::make()
                        ->label(__('Delete'))
                        ->after(function () {
                            Notification::make()
                                ->title(__('Document deleted successfully'))
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
            ->emptyStateHeading(__('No documents yet'))
            ->emptyStateDescription(__('Upload documents related to this budget'))
            ->emptyStateIcon('heroicon-o-document');
    }
}
