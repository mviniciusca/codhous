# Arquitetura de Temas e Page Builder (Contexto para LLMs)

Este documento descreve como o sistema de Temas Globais e o Page Builder do projeto **Codhous** estão estruturados. Ele foi escrito para servir de contexto para futuras manutenções ou adições de código por LLMs (Language Models).

## 1. Como a Aparência é Gerenciada

A aparência do site (Temas do Cabeçalho, Rodapé e Hero Section) é controlada de forma **Global** via banco de dados (tabela de Settings). 

- **Página de Configuração:** O usuário altera o tema no painel Filament, acessando a página `Design e SEO` (`app/Filament/Pages/DesignAndSeo.php`).
- **Armazenamento:** O valor é guardado no banco como um JSON em `Setting::get('website')` (nas chaves `header_theme` e `footer_theme`).

## 2. Estrutura Base (O Layout)

O ponto de entrada de todas as páginas é o `resources/views/components/layouts/app.blade.php`.

O fluxo de renderização funciona da seguinte forma:
1. O `app.blade.php` carrega os temas diretamente do banco de dados (usando `Setting::get('website')`).
2. Ele injeta a variável `$headerTheme` no componente `<x-site-header :theme="$headerTheme" />`.
3. Ele injeta a variável `$footerTheme` no componente `<x-site-footer :theme="$footerTheme" />`.
4. Ele renderiza o miolo da página (`{{ $slot }}`).

## 3. Renderização das Páginas (O Page Builder)

As páginas dinâmicas são controladas pelo `PageResource.php` e renderizadas pelo arquivo `resources/views/page.blade.php`.

1. O `page.blade.php` recupera a variável global `$pageTheme` (que espelha o `header_theme` definido no painel).
2. O conteúdo da página é um array de blocos (JSON) construído com o **Filament Builder**.
3. O `page.blade.php` faz um loop pelos blocos da página e passa a responsabilidade de renderização para o `resources/views/components/render-block.blade.php`.
4. O `render-block.blade.php` é um grande `@switch` que mapeia o nome do bloco (ex: `hero`, `services`, `faq`) para o componente visual correspondente.
5. **A Mágica da Hero Section:** O `render-block` repassa a variável global `$theme` diretamente para a `livewire:section-hero-cep`. Dessa forma, o layout da primeira dobra do site segue a mesma linguagem visual escolhida para o cabeçalho.

## 4. Como os Componentes de Tema Funcionam

Os três componentes que suportam "Temas" são:
- `resources/views/components/site-header.blade.php`
- `resources/views/components/site-footer.blade.php`
- `resources/views/livewire/section-hero-cep.blade.php`

Eles funcionam utilizando diretivas `@if($theme === 'nome_do_tema')` e `@elseif`. Não há arquivos separados para cada tema; toda a lógica estrutural (ex: Corporativo, Criativo, Padrão) fica dentro do mesmo arquivo do componente, facilitando a reutilização de variáveis e lógicas de negócios (ex: coleta de contatos, logos, etc).

## 5. Tutorial: Como Criar um NOVO Tema

Se for necessário criar um novo tema chamado `minimalist` (Minimalista), siga estes 4 passos obrigatórios:

### Passo 1: Adicionar a opção no Painel de Controle
Edite o arquivo `app/Filament/Pages/DesignAndSeo.php` e adicione a opção no array dos componentes `Select`:
```php
\Filament\Forms\Components\Select::make('settings.website.header_theme')
    ->options([
        'default' => 'Padrão', 
        'corporate' => 'Corporativo', 
        'creative' => 'Criativo',
        'minimalist' => 'Minimalista', // <-- NOVA OPÇÃO AQUI
    ])
```
*(Faça o mesmo para o `footer_theme` logo abaixo, se o tema também tiver um rodapé específico).*

### Passo 2: Implementar o design no Cabeçalho
Edite `resources/views/components/site-header.blade.php`.
Procure pela estrutura condicional e adicione um novo `@elseif`:
```blade
@elseif($theme === 'minimalist')
    {{-- IMPLEMENTAÇÃO DO HEADER MINIMALISTA AQUI --}}
    <header class="bg-white border-b-2 border-black">
        ...
    </header>
```

### Passo 3: Implementar o design no Rodapé
Edite `resources/views/components/site-footer.blade.php`.
Siga a mesma lógica:
```blade
@elseif($theme === 'minimalist')
    {{-- IMPLEMENTAÇÃO DO FOOTER MINIMALISTA AQUI --}}
    <footer class="bg-zinc-100 text-zinc-900">
        ...
    </footer>
```

### Passo 4: Implementar o design na Hero Section
Edite `resources/views/livewire/section-hero-cep.blade.php`.
Adicione o novo layout da Hero Section (lembre-se de respeitar o grid global, se necessário):
```blade
@elseif($theme === 'minimalist')
    {{-- IMPLEMENTAÇÃO DA HERO SECTION MINIMALISTA AQUI --}}
    <section class="min-h-screen bg-white">
        ... conteúdo e Swiper JS ...
        
        {{-- IMPORTANTE: Não esqueça de renderizar as variações do Card do WhatsApp ou CEP --}}
        @if($isWhatsapp)
            @include('livewire.partials.hero-whatsapp-card')
        @else
            @include('livewire.partials.hero-cep-card')
        @endif
    </section>
```

---
*Este documento atua como Source of Truth (Fonte da Verdade) para modificações arquiteturais no ecossistema de temas globais do site.*
