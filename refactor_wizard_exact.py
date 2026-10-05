import re

def find_matching_bracket(text, start_index, open_bracket='[', close_bracket=']'):
    count = 1
    for i in range(start_index, len(text)):
        if text[i] == open_bracket:
            count += 1
        elif text[i] == close_bracket:
            count -= 1
            if count == 0:
                return i
    return -1

def main():
    with open('app/Filament/Resources/ContentSectionResource.php', 'r') as f:
        text = f.read()

    # 1. Extract Conteúdo Principal
    conteudo_start_str = "Forms\\Components\\Tabs\\Tab::make('Conteúdo Principal')\n                            ->icon('heroicon-o-document-text')\n                            ->schema(["
    idx_conteudo_start = text.find(conteudo_start_str)
    if idx_conteudo_start == -1:
        print("Conteudo start not found")
        return
    idx_conteudo_start += len(conteudo_start_str)
    idx_conteudo_end = find_matching_bracket(text, idx_conteudo_start)
    conteudo_content = text[idx_conteudo_start:idx_conteudo_end]

    # 2. Extract Identificação schema
    identificacao_start_str = "Forms\\Components\\Section::make('Identificação')\n                                    ->description('Informações básicas de identificação e configuração da seção no sistema.')\n                                    ->icon('heroicon-o-identification')\n                                    ->schema(["
    idx_ident_start = text.find(identificacao_start_str)
    idx_ident_start += len(identificacao_start_str)
    idx_ident_end = find_matching_bracket(text, idx_ident_start)
    ident_content = text[idx_ident_start:idx_ident_end]

    # 3. Extract Aparência Global schema
    aparencia_start_str = "Forms\\Components\\Section::make('Aparência Global')\n                                    ->description('Configurações visuais gerais aplicadas a esta seção.')\n                                    ->icon('heroicon-o-swatch')\n                                    ->schema(["
    idx_apar_start = text.find(aparencia_start_str)
    idx_apar_start += len(aparencia_start_str)
    idx_apar_end = find_matching_bracket(text, idx_apar_start)
    apar_content = text[idx_apar_start:idx_apar_end]

    # Modify ident_content to use ToggleButtons
    ident_content = ident_content.replace(
        "Forms\\Components\\Select::make('type')",
        """Forms\\Components\\ToggleButtons::make('type')
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
                                            ->columnSpanFull()"""
    )
    
    # Extract Ativo and Ordem from Ident_content
    # They are the last two fields in ident_content
    # We will just split by "Forms\Components\Toggle::make('is_active')"
    ident_parts = ident_content.split("Forms\\Components\\Toggle::make('is_active')")
    new_ident_content = ident_parts[0]
    publicacao_content = "Forms\\Components\\Toggle::make('is_active')" + ident_parts[1]


    # Now we find the start of the Tabs array and replace it entirely
    tabs_main_str = "Forms\\Components\\Tabs::make('Tabs')\n                    ->persistTabInQueryString()\n                    ->tabs(["
    idx_tabs_main = text.find(tabs_main_str)
    
    idx_tabs_content_start = idx_tabs_main + len(tabs_main_str)
    idx_tabs_content_end = find_matching_bracket(text, idx_tabs_content_start)
    
    # We will replace text[idx_tabs_main : idx_tabs_content_end + 25] (up to columnSpanFull())
    # Actually just replace from idx_tabs_main to text.find("->columnSpanFull()", idx_tabs_content_end) + len("->columnSpanFull()")
    idx_end_replace = text.find("->columnSpanFull()", idx_tabs_content_end) + len("->columnSpanFull()")

    wizard_code = f"""Forms\\Components\\Wizard::make([
                    Forms\\Components\\Wizard\\Step::make('1. Tipo da Seção')
                        ->icon('heroicon-o-squares-2x2')
                        ->description('Escolha o formato e a função da seção.')
                        ->schema([
                            Forms\\Components\\Section::make('Identificação')
                                ->description('Informações básicas de identificação e configuração da seção no sistema.')
                                ->icon('heroicon-o-identification')
                                ->schema([{new_ident_content}])->columns(2),
                        ]),
                    Forms\\Components\\Wizard\\Step::make('2. Conteúdo Principal')
                        ->icon('heroicon-o-document-text')
                        ->description('Preencha os textos, imagens e dados da seção.')
                        ->schema([{conteudo_content}]),
                    Forms\\Components\\Wizard\\Step::make('3. Aparência e Publicação')
                        ->icon('heroicon-o-sparkles')
                        ->description('Ajuste o visual geral e publique sua seção.')
                        ->schema([
                            Forms\\Components\\Section::make('Publicação')
                                ->icon('heroicon-o-globe-alt')
                                ->schema([
                                    {publicacao_content}
                                ])->columns(2),
                            Forms\\Components\\Section::make('Aparência Global')
                                ->description('Configurações visuais gerais aplicadas a esta seção.')
                                ->icon('heroicon-o-swatch')
                                ->schema([{apar_content}])->columns(2),
                        ]),
                ])->skippable()->columnSpanFull()"""

    new_text = text[:idx_tabs_main] + wizard_code + text[idx_end_replace:]

    with open('app/Filament/Resources/ContentSectionResource.php', 'w') as f:
        f.write(new_text)

    print("Success")

if __name__ == "__main__":
    main()
