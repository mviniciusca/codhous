import sys

def main():
    with open('app/Filament/Resources/ContentSectionResource.php', 'r') as f:
        content = f.read()

    # 1. Replace Tabs::make with Wizard::make
    tabs_start = """                Forms\\Components\\Tabs::make('Tabs')
                    ->persistTabInQueryString()
                    ->tabs(["""
    wizard_start = """                Forms\\Components\\Wizard::make(["""
    content = content.replace(tabs_start, wizard_start)

    # 2. Add Step 1 (Tipo de Seção) and replace Step 2 (Conteúdo)
    tab_conteudo = """                        Forms\\Components\\Tabs\\Tab::make('Conteúdo Principal')
                            ->icon('heroicon-o-document-text')
                            ->schema(["""

    step1_and_2 = """                    Forms\\Components\\Wizard\\Step::make('1. Tipo da Seção')
                        ->icon('heroicon-o-squares-2x2')
                        ->description('Escolha o formato e a função da seção.')
                        ->schema([
                            Forms\\Components\\Section::make('Identificação')
                                ->description('Informações básicas de identificação e configuração da seção no sistema.')
                                ->icon('heroicon-o-identification')
                                ->schema([
                                    Forms\\Components\\ToggleButtons::make('type')
                                        ->label('Tipo da seção')
                                        ->options(ContentSection::typeLabels())
                                        ->icons([
                                            ContentSection::TYPE_HERO => 'heroicon-o-star',
                                            ContentSection::TYPE_PARTNERS => 'heroicon-o-building-office',
                                            ContentSection::TYPE_COMMERCIAL_PARTNERS => 'heroicon-o-briefcase',
                                            ContentSection::TYPE_SERVICES => 'heroicon-o-wrench-screwdriver',
                                            ContentSection::TYPE_FAQ => 'heroicon-o-question-mark-circle',
                                            ContentSection::TYPE_TESTIMONIALS => 'heroicon-o-chat-bubble-left-right',
                                            ContentSection::TYPE_COVERAGE => 'heroicon-o-map',
                                            ContentSection::TYPE_DIFFERENTIALS => 'heroicon-o-sparkles',
                                            ContentSection::TYPE_TIMELINE => 'heroicon-o-clock',
                                            ContentSection::TYPE_CTA_CONTACT => 'heroicon-o-phone',
                                            ContentSection::TYPE_CONTACT_BANNER => 'heroicon-o-megaphone',
                                            ContentSection::TYPE_BUDGET_FORM => 'heroicon-o-currency-dollar',
                                            ContentSection::TYPE_CALCULATOR => 'heroicon-o-calculator',
                                            ContentSection::TYPE_PAYMENT_OFFER => 'heroicon-o-credit-card',
                                            ContentSection::TYPE_SIMPLE_BANNER => 'heroicon-o-photo',
                                            ContentSection::TYPE_TEAM => 'heroicon-o-users',
                                            ContentSection::TYPE_SHOWCASE => 'heroicon-o-camera',
                                            ContentSection::TYPE_EQUIPMENT_SHOWCASE => 'heroicon-o-truck',
                                        ])
                                        ->inline()
                                        ->columnSpanFull()
                                        ->required()
                                        ->disabled(fn (string $operation): bool => $operation === 'edit')
                                        ->helperText('Define os campos de conteúdo disponíveis. Não pode ser alterado depois de criado.')
                                        ->live()
                                        ->afterStateUpdated(function (Forms\\Set $set, ?string $state) {
                                            if ($state) {
                                                $set('slug', ContentSection::slugForType($state));
                                                $set('name', ContentSection::typeLabels()[$state] ?? $state);
                                            }
                                        }),
                                    Forms\\Components\\TextInput::make('slug')
                                        ->label('Slug (identificador único)')
                                        ->required()
                                        ->disabled(fn (string $operation): bool => $operation === 'edit')
                                        ->unique(ignoreRecord: true)
                                        ->maxLength(255)
                                        ->helperText('Identificador no sistema. Não editável após criação.'),
                                    Forms\\Components\\TextInput::make('name')
                                        ->label('Nome (admin)')
                                        ->required()
                                        ->maxLength(255)
                                        ->helperText('Nome para identificação interna nesta lista.'),
                                ])->columns(2),
                        ]),

                    Forms\\Components\\Wizard\\Step::make('2. Conteúdo Principal')
                        ->icon('heroicon-o-document-text')
                        ->description('Preencha os textos, imagens e dados da seção.')
                        ->schema(["""
    content = content.replace(tab_conteudo, step1_and_2)


    # 3. Replace the old "Configurações" tab
    old_config_start = """                        Forms\\Components\\Tabs\\Tab::make('Configurações')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema(["""
    
    # We will find old_config_start, and then find the next Forms\Components\Section::make('Aparência Global')
    # and replace everything in between.
    
    idx_config_start = content.find(old_config_start)
    if idx_config_start == -1:
        print("Config start not found")
        sys.exit(1)
        
    idx_aparencia_start = content.find("                                Forms\\Components\\Section::make('Aparência Global')", idx_config_start)
    if idx_aparencia_start == -1:
        print("Aparencia start not found")
        sys.exit(1)

    step3 = """                        ]), // Fim do Passo 2
                    
                    Forms\\Components\\Wizard\\Step::make('3. Aparência e Publicação')
                        ->icon('heroicon-o-sparkles')
                        ->description('Ajuste o visual geral e publique sua seção.')
                        ->schema([
                            Forms\\Components\\Section::make('Publicação')
                                ->icon('heroicon-o-globe-alt')
                                ->schema([
                                    Forms\\Components\\Toggle::make('is_active')
                                        ->label('Ativo')
                                        ->onIcon('heroicon-m-check')->offIcon('heroicon-m-x-mark')
                                        ->default(true)
                                        ->helperText('Se inativo, a seção não aparece no site e usa o conteúdo estático.'),
                                    Forms\\Components\\TextInput::make('sort_order')
                                        ->label('Ordem')
                                        ->numeric()
                                        ->helperText('Ordem na listagem.')
                                        ->minValue(0),
                                ])->columns(2),

"""
    
    content = content[:idx_config_start] + step3 + content[idx_aparencia_start:]

    # 4. Replace the closing of the tabs with the closing of the wizard
    end_tabs = "                    ])->columnSpanFull()"
    end_wizard = "                ])->skippable()->columnSpanFull()"
    content = content.replace(end_tabs, end_wizard)

    with open('app/Filament/Resources/ContentSectionResource.php', 'w') as f:
        f.write(content)

    print("Refactor complete.")

if __name__ == "__main__":
    main()
