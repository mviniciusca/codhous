# Padrão de Tratamento de Dinheiro (Moeda) no Filament

Para garantir consistência e evitar bugs de exibição ou cálculo (como valores multiplicados por 1000 devido à confusão entre vírgula e ponto), todo campo de dinheiro no sistema deve seguir o seguinte padrão:

## 1. Campos de Formulário (Inputs)

Em vez de usar `->numeric()`, que depende do locale do navegador do usuário e pode causar problemas de UX, usamos a máscara AlpineJS `$money` para forçar o padrão Brasileiro (BRL) e oferecer a melhor experiência de digitação.

Todos os campos de dinheiro devem ser configurados assim:

```php
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

TextInput::make('price')
    ->label('Preço')
    ->prefix('R$')
    ->mask(RawJs::make('$money($input, \',\', \'.\', 2)'))
    ->stripCharacters('.')
    ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (float) $state : (float) str_replace(',', '.', str_replace('.', '', (string) $state)))
```

**Regras Cruciais:**
- **NÃO use** `formatStateUsing` em inputs com a máscara `$money`. O Filament já lida com a hidratação inicial do banco de dados (que é float) para a máscara corretamente na montagem inicial do componente.
- **Preenchimento Dinâmico (`$set`)**: Quando você precisar preencher um campo de dinheiro dinamicamente (ex: ao selecionar um produto, calcular uma soma ou buscar um frete por CEP), você **DEVE** formatar o float original do banco para string no formato Brasileiro ANTES de usar o `$set`. 
  - *Motivo:* Caso contrário, a máscara receberá um float americano (ex: `10.00`), lerá o ponto como separador de milhar e transformará visualmente em `10.000` (Dez mil!).
  
  *Exemplo Incorreto:*
  `$set('shipping', $area->shipping_fee); // 10.00 vira 10.000 na UI`

  *Exemplo Correto:*
  `$set('shipping', number_format((float) $area->shipping_fee, 2, ',', '.')); // "10,00" é lido corretamente pela máscara como 10 reais`

## 2. Preenchimento de Formulários (`fill()`)

Sempre que utilizar `$this->form->fill()` com dados que populam campos de dinheiro (ex: ao recuperar dados via `query string` ou via `mutateFormDataBeforeFill`), certifique-se de iterar sobre os campos e formatá-los para a string BR (`"10,00"`):
```php
'subtotal' => number_format((float) $subtotal, 2, ',', '.'),
```

## 3. Cálculos Internos (PHP)

Sempre que precisar somar ou multiplicar valores pegos de um input de dinheiro (`$get('price')`), faça a limpeza antes de converter para float, pois o estado pode vir tanto como string (`"1.500,00"`) quanto numérico (`1500.00`):
```php
$rawPrice = $get('price') ?? 0;
$price = is_numeric($rawPrice) ? (float) $rawPrice : (float) str_replace(',', '.', str_replace('.', '', (string) $rawPrice));
```

## 4. Exibição em Tabelas (Columns) e Visualização (Infolists)

Nunca aplique máscaras aqui, basta usar o formatador nativo do Filament para BRL:
```php
TextColumn::make('price')
    ->money('BRL')
```
