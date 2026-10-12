<?php

namespace App\Filament\Resources;

use App\Enums\AccessArea;
use App\Enums\MeasureUnit;
use App\Enums\VolumeRounding;
use App\Filament\Clusters\ToolsCluster;
use App\Filament\Concerns\BelongsToArea;
use App\Filament\Resources\VolumeFormulaResource\Pages;
use App\Filament\Resources\VolumeFormulaResource\RelationManagers\VersionsRelationManager;
use App\Models\VolumeFormula;
use App\Services\VolumeCalculator\Expression\Parser;
use App\Services\VolumeCalculator\FormulaDefinition;
use App\Services\VolumeCalculator\VolumeCalculator;
use Closure;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

/**
 * Fórmulas da calculadora de volume. Nome, formato e status ficam na fórmula; a
 * regra (expressão, campos, margem e arredondamento) é salva como nova versão a
 * cada alteração, então cálculos antigos continuam apontando para a regra usada.
 */
class VolumeFormulaResource extends Resource
{
    use BelongsToArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Catalogo;
    }

    protected static ?string $model = VolumeFormula::class;

    protected static ?string $cluster = ToolsCluster::class;

    /** Submenu do cluster em abas no topo: a calculadora precisa da largura toda */
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $slug = 'formulas-de-volume';

    protected static ?string $navigationIcon = 'heroicon-o-variable';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'fórmula de volume';

    protected static ?string $pluralModelLabel = 'Fórmulas de volume';

    protected static ?string $navigationLabel = 'Fórmulas de volume';

    /** Campos do formulário que pertencem à versão (regra de cálculo) */
    public const DEFINITION_FIELDS = ['expression', 'variables', 'margin_percent', 'rounding', 'rounding_step'];

    public static function form(Form $form): Form
    {
        return $form
            ->columns(['default' => 1, 'xl' => 3])
            ->schema([
                Grid::make(1)
                    ->columnSpan(['default' => 1, 'xl' => 2])
                    ->schema([
                        Section::make('Identificação')
                            ->compact()
                            ->columns(['default' => 1, 'md' => 2])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(80)
                                    ->placeholder('Ex.: Sapata isolada'),
                                Select::make('shape')
                                    ->label('Ilustração')
                                    ->options(VolumeFormula::SHAPES)
                                    ->default('custom')
                                    ->required()
                                    ->native(false),
                                Textarea::make('description')
                                    ->label('Descrição')
                                    ->helperText('Aparece na calculadora, abaixo das abas.')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->columnSpanFull(),
                                Toggle::make('is_active')
                                    ->label('Disponível na calculadora')
                                    ->default(true),
                            ]),

                        Section::make('Campos de entrada')
                            ->description('Cada campo vira uma variável da fórmula. O valor digitado é convertido para metros antes do cálculo.')
                            ->compact()
                            ->schema([
                                Repeater::make('variables')
                                    ->hiddenLabel()
                                    ->addActionLabel('Adicionar campo')
                                    ->minItems(1)
                                    ->maxItems(FormulaDefinition::MAX_VARIABLES)
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state) => filled($state['label'] ?? null) ? $state['label'].' · '.($state['key'] ?? '') : null)
                                    ->live(onBlur: true)
                                    ->defaultItems(1)
                                    ->schema([
                                        Grid::make(['default' => 1, 'sm' => 2, 'lg' => 4])->schema([
                                            TextInput::make('label')
                                                ->label('Rótulo')
                                                ->required()
                                                ->maxLength(40)
                                                ->columnSpan(['lg' => 2]),
                                            TextInput::make('key')
                                                ->label('Variável')
                                                ->helperText('Nome usado na fórmula.')
                                                ->required()
                                                ->maxLength(32)
                                                ->regex('/^[a-z][a-z0-9_]*$/')
                                                ->validationMessages(['regex' => 'Use letras minúsculas sem acento, números e _, começando por letra.'])
                                                ->rule(fn (): Closure => function (string $attribute, $value, Closure $fail) {
                                                    if (Parser::isReserved((string) $value)) {
                                                        $fail('“'.$value.'” é um nome reservado da fórmula.');
                                                    }
                                                })
                                                ->distinct()
                                                ->extraInputAttributes(['class' => 'font-mono']),
                                            Select::make('unit')
                                                ->label('Unidade')
                                                ->options(MeasureUnit::class)
                                                ->default(MeasureUnit::M->value)
                                                ->required()
                                                ->native(false),
                                            TextInput::make('example')
                                                ->label('Valor de exemplo')
                                                ->helperText('Usado no teste e no “Preencher com um exemplo”.')
                                                ->numeric(),
                                            TextInput::make('min')
                                                ->label('Mínimo')
                                                ->helperText('Vazio: maior que zero.')
                                                ->numeric(),
                                            TextInput::make('max')
                                                ->label('Máximo')
                                                ->helperText('Vazio: 100.000.')
                                                ->numeric(),
                                            TextInput::make('help')
                                                ->label('Dica')
                                                ->maxLength(140)
                                                ->columnSpan(['sm' => 2, 'lg' => 4]),
                                        ]),
                                    ]),
                            ]),

                        Section::make('Fórmula')
                            ->compact()
                            ->schema([
                                Textarea::make('expression')
                                    ->label('Expressão do volume')
                                    ->required()
                                    ->rows(2)
                                    ->maxLength(Parser::MAX_LENGTH)
                                    ->live(onBlur: true)
                                    ->extraInputAttributes(['class' => 'font-mono', 'spellcheck' => 'false'])
                                    ->placeholder('largura * comprimento * espessura')
                                    ->rule(fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                        foreach (static::definitionFromState(static::definitionState($get))->errors() as $error) {
                                            $fail($error);
                                        }
                                    })
                                    ->helperText(new HtmlString(
                                        'Operadores <code>+ - * / ^</code> e parênteses · constante <code>pi</code> · funções '
                                        .collect(array_keys(Parser::FUNCTIONS))->map(fn ($f) => "<code>{$f}()</code>")->implode(' ')
                                        .' · decimais com ponto (<code>0.5</code>). O resultado precisa estar em m³: a unidade de cada campo é conferida.'
                                    )),
                                Placeholder::make('available_variables')
                                    ->label('Variáveis disponíveis')
                                    ->content(fn (Get $get) => new HtmlString(
                                        '<div class="cx-ui vf-vars">'
                                        .(collect($get('variables') ?? [])
                                            ->filter(fn ($v) => filled($v['key'] ?? null))
                                            ->map(fn ($v) => '<span><code class="vf-code">'.e($v['key']).'</code> <small>'.e(MeasureUnit::tryFrom($v['unit'] ?? '')?->symbol() ?? '').'</small></span>')
                                            ->implode('') ?: '<small>Adicione campos de entrada.</small>')
                                        .'</div>'
                                    )),
                            ]),

                        Section::make('Ajustes')
                            ->description('Aplicados depois do volume geométrico, nesta ordem: margem e arredondamento.')
                            ->compact()
                            ->columns(['default' => 1, 'md' => 3])
                            ->schema([
                                TextInput::make('margin_percent')
                                    ->label('Margem de segurança padrão')
                                    ->helperText('Recomendado de 5% a 10%.')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(10)
                                    ->suffix('%')
                                    ->required()
                                    ->live(onBlur: true),
                                Select::make('rounding')
                                    ->label('Arredondamento do volume final')
                                    ->options(VolumeRounding::class)
                                    ->default(VolumeRounding::None->value)
                                    ->required()
                                    ->native(false)
                                    ->live(),
                                TextInput::make('rounding_step')
                                    ->label('Passo')
                                    ->helperText('Ex.: 0,5 para pedir em meios metros cúbicos.')
                                    ->numeric()
                                    ->minValue(0.001)
                                    ->maxValue(10)
                                    ->default(0.5)
                                    ->suffix('m³')
                                    ->live(onBlur: true)
                                    ->visible(fn (Get $get) => ($get('rounding') ?? 'none') !== VolumeRounding::None->value)
                                    ->required(fn (Get $get) => ($get('rounding') ?? 'none') !== VolumeRounding::None->value),
                            ]),
                    ]),

                Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Testar fórmula')
                            ->description('Calcula com os valores de exemplo dos campos, sem salvar.')
                            ->icon('heroicon-o-beaker')
                            ->compact()
                            ->schema([
                                Placeholder::make('test')
                                    ->hiddenLabel()
                                    ->content(fn (Get $get) => static::renderTest(static::definitionState($get))),
                            ]),
                        Section::make('Nova versão')
                            ->description('Toda alteração na regra cria uma versão; as anteriores ficam guardadas para os cálculos já feitos.')
                            ->icon('heroicon-o-clock')
                            ->compact()
                            ->visibleOn('edit')
                            ->schema([
                                TextInput::make('version_notes')
                                    ->label('O que mudou?')
                                    ->placeholder('Ex.: margem de 10% para 8%')
                                    ->maxLength(250)
                                    ->dehydrated(),
                            ]),
                    ]),
            ]);
    }

    /** @return array<string, mixed> */
    public static function definitionState(Get $get): array
    {
        return collect(self::DEFINITION_FIELDS)->mapWithKeys(fn ($field) => [$field => $get($field)])->all();
    }

    public static function definitionFromState(array $state): FormulaDefinition
    {
        $state['variables'] = array_values((array) ($state['variables'] ?? []));

        return FormulaDefinition::fromArray($state);
    }

    public static function renderTest(array $state): HtmlString
    {
        $definition = static::definitionFromState($state);

        return new HtmlString(view('filament.volume-formulas.test-panel', [
            'definition' => $definition,
            'errors' => $definition->errors(),
            'calculator' => app(VolumeCalculator::class),
        ])->render());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('currentVersion')->withCount('calculations'))
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Fórmula')
                    ->description(fn (VolumeFormula $record) => $record->currentVersion?->definition['expression'] ?? null)
                    ->searchable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('shape')
                    ->label('Formato')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state) => VolumeFormula::SHAPES[$state] ?? $state),
                Tables\Columns\TextColumn::make('currentVersion.version')
                    ->label('Versão')
                    ->formatStateUsing(fn ($state) => 'v'.$state),
                Tables\Columns\TextColumn::make('margin')
                    ->label('Margem')
                    ->state(fn (VolumeFormula $record) => number_format((float) ($record->currentVersion?->definition['margin_percent'] ?? 0), 0, ',', '.').'%'),
                Tables\Columns\TextColumn::make('calculations_count')
                    ->label('Cálculos')
                    ->numeric(),
                Tables\Columns\IconColumn::make('is_system')
                    ->label('Padrão')
                    ->boolean()
                    ->trueIcon('heroicon-m-lock-closed')
                    ->falseIcon('heroicon-m-pencil-square')
                    ->tooltip(fn (VolumeFormula $record) => $record->is_system ? 'Fórmula que acompanha o sistema' : 'Fórmula criada pela empresa'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Ativa'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicar')
                    ->icon('heroicon-m-document-duplicate')
                    ->iconButton()
                    ->tooltip('Duplicar como fórmula personalizada')
                    ->action(function (VolumeFormula $record) {
                        $copy = VolumeFormula::create([
                            'name' => $record->name.' (cópia)',
                            'description' => $record->description,
                            'shape' => $record->shape,
                            'is_active' => false,
                            'sort' => VolumeFormula::max('sort') + 1,
                        ]);
                        $copy->saveVersion($record->definition(), auth()->user(), 'Copiada de '.$record->name.' '.$record->currentVersion?->label());

                        Notification::make()->title('Fórmula duplicada')->body('A cópia está inativa até você revisar e ativar.')->success()->send();

                        return redirect(static::getUrl('edit', ['record' => $copy]));
                    }),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (VolumeFormula $record) => $record->canBeDeleted()),
            ])
            ->emptyStateHeading('Nenhuma fórmula')
            ->emptyStateDescription('Crie uma fórmula para usá-la na calculadora de volume.');
    }

    public static function getRelations(): array
    {
        return [
            VersionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVolumeFormulas::route('/'),
            'create' => Pages\CreateVolumeFormula::route('/create'),
            'edit' => Pages\EditVolumeFormula::route('/{record}/edit'),
        ];
    }
}
