# 📊 Sistema de Versionamento de Budgets

## ✅ O que foi implementado

Sistema completo de histórico e versionamento de budgets usando **Spatie Activity Log**, com interface visual no Filament Admin.

## 🎯 Funcionalidades

### 1. **Histórico Completo de Alterações**
- ✓ Rastreamento automático de todas as mudanças
- ✓ Registro de quem fez cada alteração
- ✓ Data e hora precisa de cada modificação
- ✓ Comparação Before/After visual

### 2. **Interface no Filament**
Acesse qualquer budget e clique na aba **"Histórico de Alterações"** para ver:

- **Lista de Atividades** com:
  - Tipo de ação (Criado, Atualizado, Deletado)
  - Usuário responsável
  - Resumo do que mudou
  - Data/hora com "há X minutos"

- **Detalhes da Mudança** (botão "View Details"):
  - Comparação lado a lado Before → After
  - Todos os campos modificados
  - Valores antigos em vermelho
  - Valores novos em verde

### 3. **Filtros e Busca**
- Filtrar por usuário específico
- Filtrar por tipo de ação (criado/atualizado/deletado)
- Busca por nome do usuário

### 4. **Widget no Dashboard** (Opcional)
Widget `RecentBudgetActivitiesWidget` mostra as 10 atividades mais recentes de todos os budgets.

Para ativar, adicione ao seu Dashboard:
```php
// app/Filament/Pages/Dashboard.php
protected function getHeaderWidgets(): array
{
    return [
        // ... outros widgets
        \App\Filament\Widgets\RecentBudgetActivitiesWidget::class,
    ];
}
```

### 5. **Comando CLI** (Para debug/análise)
```bash
# Ver histórico de um budget específico
php artisan budget:history BD202511000001

# Ver últimas 20 atividades de todos os budgets
php artisan budget:history --limit=20

# Ver detalhes completos com prompt interativo
php artisan budget:history BD202511000001
```

## 🔧 Configuração Técnica

### Arquivo de Configuração
O config foi publicado em `config/activitylog.php`. Principais configurações:

```php
// Tabela onde os logs são salvos
'table_name' => 'activity_log',

// Tempo de retenção (null = infinito)
'delete_records_older_than_days' => 365, // 1 ano

// Modelos que devem logar atividades
'enabled' => true,
```

### O que é Logado Automaticamente

No modelo `Budget`, configuramos para logar:
- ✓ Todas as alterações em campos
- ✓ Apenas mudanças efetivas (não logs vazios)
- ✓ User ID de quem fez a mudança
- ✓ Snapshot completo antes e depois

## 📝 Como Funciona

### Fluxo Automático

1. **Usuário edita um budget no Filament**
2. Laravel dispara evento `updated`
3. Spatie Activity Log intercepta via trait `LogsActivity`
4. Sistema compara valores antigos vs novos
5. Gera registro na tabela `activity_log` com:
   - `subject_type`: 'App\Models\Budget'
   - `subject_id`: ID do budget
   - `causer_type`: 'App\Models\User'
   - `causer_id`: ID do usuário
   - `description`: 'updated'
   - `properties`: JSON com `old` e `attributes`
   - `created_at`: timestamp

### Estrutura do Log (JSON)
```json
{
  "old": {
    "status": "pending",
    "content": {"price": "100.00"}
  },
  "attributes": {
    "status": "done",
    "content": {"price": "150.00"}
  }
}
```

## 🎨 Customização

### Mudar campos logados
Em `app/Models/Budget.php`:
```php
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['status', 'is_active', 'content']) // Apenas esses campos
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
}
```

### Adicionar descrições personalizadas
Já implementado no método `getDescriptionForEvent()`:
```php
public function getDescriptionForEvent(string $eventName): string
{
    $userName = Auth::user()?->name ?? 'System';
    
    return match($eventName) {
        'created' => "Budget {$this->code} criado por {$userName}",
        'updated' => "Budget {$this->code} atualizado por {$userName}",
        // ...
    };
}
```

### Adicionar mais labels amigáveis
No `BudgetHistoryRelationManager::getFieldLabel()`:
```php
protected function getFieldLabel(string $key): string
{
    return match ($key) {
        'meu_campo' => __('Meu Campo Bonito'),
        // ... adicione mais aqui
        default => ucfirst(str_replace('_', ' ', $key)),
    };
}
```

## 🔍 Consultas Programáticas

### Ver histórico de um budget
```php
$budget = Budget::find(1);
$activities = $budget->activities()->latest()->get();

foreach ($activities as $activity) {
    echo $activity->description; // 'created', 'updated', etc
    echo $activity->causer->name; // Nome do usuário
    echo $activity->properties; // Array com old/attributes
}
```

### Ver atividades de um usuário
```php
$user = User::find(1);
$activities = $user->actions()
    ->where('subject_type', Budget::class)
    ->latest()
    ->get();
```

### Buscar mudanças em campo específico
```php
$activities = Activity::query()
    ->where('subject_type', Budget::class)
    ->where('properties->old->status', 'pending')
    ->get();
```

## 📊 Relatórios Úteis

### Budgets mais editados
```php
$mostEdited = Budget::withCount('activities')
    ->orderBy('activities_count', 'desc')
    ->take(10)
    ->get();
```

### Usuários mais ativos
```php
$mostActive = User::withCount(['actions' => function($q) {
    $q->where('subject_type', Budget::class);
}])->orderBy('actions_count', 'desc')->get();
```

## 🗄️ Manutenção

### Limpar logs antigos
```bash
# Manualmente
php artisan activitylog:clean

# Ou configurar no cron (recomendado)
# app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('activitylog:clean')->daily();
}
```

### Tamanho da tabela
```bash
# Verificar quantos registros
php artisan tinker
>>> Spatie\Activitylog\Models\Activity::count();

# Ver espaço usado (MySQL)
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_name = 'activity_log';
```

## 🚀 Performance

### Índices já criados na migration
- `subject` (type + id)
- `causer` (type + id)
- `created_at`

### Evoluções futuras (se necessário)

Se o histórico crescer muito (>100k registros):

1. **Particionamento por data**
```sql
ALTER TABLE activity_log PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026)
);
```

2. **Tabela de snapshot dedicada**
```php
// budget_snapshots
- id
- budget_id
- version
- snapshot (JSON completo)
- user_id
- created_at
```

3. **Arquivamento em S3**
```bash
# Exportar logs antigos para S3
php artisan activity:archive --older-than=1year
```

## ❓ FAQ

**P: Os logs afetam a performance?**
R: Mínimo. O Spatie usa eventos assíncronos. Em produção, considere usar queue jobs.

**P: Posso restaurar versões antigas?**
R: Atualmente é view-only. Para restaurar, precisaria criar um método `restore()` que aplica os valores do `old`.

**P: Como desabilitar logs temporariamente?**
R: Use `Activity::disableLogging()` e `Activity::enableLogging()`.

**P: Logs consomem muito espaço?**
R: Um log típico = ~1KB. Para 100k logs = ~100MB. Configure retenção em `config/activitylog.php`.

---

## 📞 Comandos Rápidos

```bash
# Ver histórico de budget específico
php artisan budget:history BD202511000001

# Limpar logs antigos (>365 dias)
php artisan activitylog:clean

# Ver estatísticas
php artisan tinker
>>> Activity::where('subject_type', 'App\Models\Budget')->count()
>>> Activity::where('description', 'updated')->count()

# Testar criação de log manualmente
>>> activity()->log('test')
```

---

**Criado em:** 07/11/2025  
**Versão:** 1.0  
**Desenvolvedor:** GitHub Copilot + Marvin Coelho
