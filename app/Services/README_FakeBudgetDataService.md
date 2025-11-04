# FakeBudgetDataService

Serviço para gerar dados fake/temporários para orçamentos durante testes e desenvolvimento.

## 📋 Descrição

Este serviço permite preencher rapidamente os campos de orçamento com dados de teste, facilitando o desenvolvimento e testes sem precisar criar dados reais no banco de dados.

## 🚀 Uso

### No BudgetResource (Filament)

O serviço está integrado no formulário de orçamento com botões no header das seções:

#### Botões Disponíveis:

1. **Fill All with Fake Data** (Azul/Info)
   - Preenche TODOS os campos do formulário com dados fake
   - Inclui: Cliente, Endereço, Construção e Preços

2. **Fill Customer** (Laranja/Warning)
   - Preenche apenas dados do cliente
   - Nome, Email, Telefone

3. **Fill Address** (Verde/Success)
   - Preenche apenas dados de endereço
   - CEP, Rua, Número, Cidade, Bairro, Estado

4. **Fill Construction** (Azul/Primary)
   - Preenche dados de construção
   - Quantidade (m³), Local, FCK, Produto

5. **Fill Pricing** (Laranja/Warning - na seção Pricing)
   - Preenche valores de precificação
   - Preço unitário, Taxa, Desconto, Total

6. **Clear All** (Vermelho/Danger)
   - Limpa todos os campos
   - Requer confirmação

### Uso Programático

```php
use App\Services\FakeBudgetDataService;

// Criar instância do serviço
$fakeService = new FakeBudgetDataService();

// Gerar dados de cliente
$customerData = $fakeService->generateCustomerData();
// Retorna: ['customer_name' => '...', 'customer_email' => '...', 'customer_phone' => '...']

// Gerar dados de endereço
$addressData = $fakeService->generateAddressData();
// Retorna: ['postcode' => '...', 'street' => '...', 'number' => '...', ...]

// Gerar dados de construção
$constructionData = $fakeService->generateConstructionData();
// Retorna: ['quantity' => 25.50, 'location' => 'Laje', 'fck' => 30, 'product' => '...']

// Gerar dados de precificação (opcionalmente com quantidade específica)
$pricingData = $fakeService->generatePricingData(25.50);
// Retorna: ['price' => '...', 'tax' => '...', 'discount' => '...', 'total' => '...']

// Gerar todos os dados de uma vez
$allData = $fakeService->generateCompleteBudgetData();

// Gerar por seção específica
$data = $fakeService->generateSectionData('customer'); // 'customer'|'address'|'construction'|'pricing'|'all'
```

## 📦 Dados Gerados

### Customer Data (pt_BR)
- **customer_name**: Nome completo brasileiro
- **customer_email**: Email válido
- **customer_phone**: Telefone celular brasileiro

### Address Data (pt_BR)
- **postcode**: CEP brasileiro
- **street**: Nome de rua
- **number**: Número do imóvel
- **city**: Cidade brasileira
- **neighborhood**: Bairro
- **state**: Sigla do estado (UF)

### Construction Data
- **quantity**: Volume em m³ (mínimo 3m³ conforme ABNT NBR 7212)
- **location**: Local da aplicação (Laje, Piso, Viga, Pilar, etc.)
- **fck**: Resistência característica (15, 20, 25, 30, 35, 40, 45, 50)
- **product**: Tipo de concreto

### Pricing Data
- **price**: Preço por m³
- **tax**: Taxa adicional
- **discount**: Desconto
- **total**: Total calculado automaticamente

## 🔧 Tecnologias

- **Faker PHP**: Biblioteca para geração de dados fake
- **Locale**: pt_BR (dados brasileiros)

## ⚠️ Importante

- Os dados gerados são **APENAS PARA TESTE**
- Não salve dados fake em produção
- Use o botão "Clear All" para limpar os campos antes de criar orçamentos reais
- Os campos permanecem desabilitados para edição direta (use as actions)

## 📝 Exemplo de Fluxo de Trabalho

1. Acesse a página de criação/edição de orçamento
2. Clique em "Fill All with Fake Data" para preencher tudo rapidamente
3. Teste as funcionalidades (PDF, Email, WhatsApp, etc.)
4. Se precisar de dados reais, clique em "Clear All"
5. Preencha com dados reais do cliente

## 🎯 Benefícios

✅ Agiliza testes durante desenvolvimento  
✅ Não polui o banco de dados com dados de teste  
✅ Dados realistas em português brasileiro  
✅ Facilita demonstrações para clientes  
✅ Permite testar validações e cálculos rapidamente
