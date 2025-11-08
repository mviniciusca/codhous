# 📊 User Profile & Analytics - Documentação

## Visão Geral

Ao editar um usuário, você terá acesso a uma visão completa do perfil, estatísticas e histórico de atividades.

---

## 📍 Como Acessar

1. Vá em **Security → Team Management**
2. Clique no **ícone de edição** (lápis) em qualquer usuário
3. Você verá a página completa de gerenciamento do usuário

---

## 🎯 Componentes da Página

### **1. 📊 Dashboard de Estatísticas (Topo)**

4 cards informativos exibindo:

#### **Total Budgets**
- Quantidade total de orçamentos criados pelo usuário
- Ícone: 💰 Currency Dollar
- Cor: Azul (Primary)

#### **Completed Budgets**
- Orçamentos finalizados (status: `done`)
- Ícone: ✅ Check Circle
- Cor: Verde (Success)

#### **Potential Revenue**
- Valor total em R$ dos orçamentos concluídos
- Calculado somando apenas orçamentos com `status = 'done'`
- Formato: R$ 1.234,56
- Ícone: 💵 Banknotes
- Cor: Verde (Success)
- Inclui mini-gráfico de tendência

#### **Pending Budgets**
- Orçamentos aguardando aprovação (status: `pending`)
- Ícone: ⏰ Clock
- Cor: Amarelo (Warning)

---

### **2. 📝 Formulário de Edição**

Permite editar:
- Nome completo
- Email
- Senha (opcional ao editar)
- Cargo/Permissões

---

### **3. 📋 Aba "Budgets History"**

Histórico completo de orçamentos criados pelo usuário.

#### **Colunas:**
| Coluna | Descrição | Recurso |
|--------|-----------|---------|
| **Code** | Código do orçamento | Pesquisável, Ordenável |
| **Customer** | Nome do cliente | Pesquisável, Limitado a 30 chars |
| **Status** | Status atual | Badge colorido |
| **Total Value** | Valor total | Formato: R$ moeda |
| **Active** | Se está ativo | Ícone boolean |
| **Created** | Data de criação | Formato: dd/mm/YYYY HH:mm |

#### **Badges de Status:**
- 🟡 **Pending** - Aguardando
- 🟢 **Done** - Concluído
- 🔴 **Cancelled** - Cancelado
- 🔵 **Outros** - Demais status

#### **Filtros:**
- **Por Status:** Pending, Done, Cancelled
- **Por Ativo:** Ativo/Inativo/Todos

#### **Ações:**
- **View** 👁️ - Abre o orçamento em nova aba

#### **Estado Vazio:**
- Mensagem: "No budgets yet"
- Descrição: "This user has not created any budgets yet."
- Ícone: 💰

---

### **4. 📝 Aba "Activity Log"**

Histórico completo de atividades rastreadas do usuário.

#### **Colunas:**
| Coluna | Descrição | Badge |
|--------|-----------|-------|
| **Log** | Nome do log | 🔵 Info |
| **Action** | Descrição da ação | Pesquisável |
| **Subject** | Tipo de registro | Badge |
| **Event** | Tipo de evento | Colorido |
| **Date** | Data/hora | dd/mm/YYYY HH:mm:ss |

#### **Badges de Evento:**
- 🟢 **created** - Criado
- 🟡 **updated** - Atualizado
- 🔴 **deleted** - Deletado
- 🔵 **outros** - Demais eventos

#### **Tipos de Subject:**
- Budget
- Customer
- Mail
- Product
- Newsletter
- Etc.

#### **Filtros:**
- **Por Evento:** Created, Updated, Deleted
- **Por Subject Type:** Budget, Customer, Mail

#### **Ações:**
- **View** 👁️ - Modal com detalhes completos da atividade
  - Event
  - Data/Hora
  - Log Name
  - Subject Type
  - Description
  - Properties (JSON formatado)

#### **Estado Vazio:**
- Mensagem: "No activity yet"
- Descrição: "This user has not performed any tracked actions yet."
- Ícone: ⏰

---

## 🎨 Layout Visual

```
┌─────────────────────────────────────────────────────────────┐
│                   USER OVERVIEW & MANAGEMENT                │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │  Total   │  │Completed │  │Potential │  │ Pending  │  │
│  │ Budgets  │  │ Budgets  │  │ Revenue  │  │ Budgets  │  │
│  │    15    │  │    10    │  │R$ 45.000 │  │    5     │  │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📝 USER INFORMATION                                        │
│  ┌─────────────────┐  ┌─────────────────┐                │
│  │ Full Name       │  │ Email           │                │
│  └─────────────────┘  └─────────────────┘                │
│  ┌─────────────────┐  ┌─────────────────┐                │
│  │ Password        │  │ Confirm Pass    │                │
│  └─────────────────┘  └─────────────────┘                │
│                                                             │
│  🛡️ ROLE & PERMISSIONS                                     │
│  ┌─────────────────────────────────────┐                  │
│  │ User Role: 🟢 Vendedor (Sales)     │                  │
│  └─────────────────────────────────────┘                  │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  [Budgets History] [Activity Log]                          │
├─────────────────────────────────────────────────────────────┤
│  📋 BUDGETS HISTORY                                         │
│  ┌───────────────────────────────────────────────────┐    │
│  │ Code        │ Customer  │ Status │ Value │ Date   │    │
│  ├───────────────────────────────────────────────────┤    │
│  │ BD202511... │ João      │ 🟢 Done│ R$ 5k │ 07/11 │    │
│  │ BD202511... │ Maria     │ 🟡 Pend│ R$ 3k │ 06/11 │    │
│  └───────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

---

## 💡 Casos de Uso

### **Caso 1: Avaliar Desempenho de Vendedor**
1. Abra o perfil do vendedor
2. Veja **Completed Budgets** - quantos fechou
3. Veja **Potential Revenue** - quanto gerou em vendas
4. Compare com **Total Budgets** - taxa de conversão
5. Acesse aba **Budgets History** para ver detalhes

### **Caso 2: Auditar Atividades de Usuário**
1. Abra o perfil do usuário
2. Vá na aba **Activity Log**
3. Filtre por tipo de evento (created, updated, deleted)
4. Clique em **View** para ver detalhes de cada ação
5. Verifique propriedades alteradas

### **Caso 3: Acompanhar Orçamentos Pendentes**
1. Abra o perfil do usuário
2. Veja card **Pending Budgets**
3. Acesse aba **Budgets History**
4. Filtre por Status: **Pending**
5. Clique em **View** para revisar cada orçamento

### **Caso 4: Revisar Histórico Completo**
1. Abra o perfil do usuário
2. Aba **Budgets History** - todos os orçamentos
3. Aba **Activity Log** - todas as ações
4. Exporte dados se necessário (via filtros)

---

## 🔒 Permissões

### **Quem pode acessar:**

| Cargo | Acesso |
|-------|--------|
| **super_admin** | ✅ Todos os usuários |
| **admin** | ✅ Todos os usuários |
| **vendedor** | ❌ Sem acesso |
| **financeiro** | ❌ Sem acesso |
| **atendimento** | ❌ Sem acesso |

---

## 📊 Cálculos de Estatísticas

### **Total Budgets:**
```php
Budget::where('user_id', $userId)->count()
```

### **Completed Budgets:**
```php
Budget::where('user_id', $userId)
      ->where('status', 'done')
      ->count()
```

### **Potential Revenue:**
```php
Budget::where('user_id', $userId)
      ->where('status', 'done')
      ->sum('content->total')
```

### **Pending Budgets:**
```php
Budget::where('user_id', $userId)
      ->where('status', 'pending')
      ->count()
```

**Nota:** Usa `withoutGlobalScopes()` para super_admin/admin verem todos os orçamentos do usuário.

---

## 🎯 Próximas Melhorias Sugeridas

### **Estatísticas Avançadas:**
1. Taxa de conversão (done/total) %
2. Valor médio por orçamento
3. Orçamentos por mês (gráfico)
4. Comparação com média da equipe
5. Tempo médio para fechar orçamento

### **Histórico de Budgets:**
1. Exportar para Excel/PDF
2. Filtro por período (data)
3. Filtro por valor mínimo/máximo
4. Gráfico de evolução de vendas
5. Top clientes do vendedor

### **Activity Log:**
1. Exportar logs
2. Filtro por data
3. Pesquisa avançada em properties
4. Timeline visual das atividades
5. Alertas de ações críticas

### **Novos Widgets:**
1. **Gráfico de Vendas** - Evolução mensal
2. **Top 5 Clientes** - Por valor
3. **Performance Score** - Nota geral
4. **Comparativo** - vs média da equipe

### **Novas Abas:**
1. **Customers** - Clientes do vendedor
2. **Inbox** - E-mails do usuário
3. **Notes** - Anotações sobre o usuário
4. **Performance** - Métricas detalhadas

---

## 🐛 Troubleshooting

### **Estatísticas não aparecem:**
- Verifique se o usuário tem `user_id` nos budgets
- Rode: `php artisan permission:cache-reset`
- Limpe cache do navegador

### **Activity Log vazio:**
- Verifique se o ActivityLog está configurado
- Confirme que os models têm trait `LogsActivity`
- Verifique tabela `activity_log` no banco

### **Erro ao visualizar budget:**
- Confirme que a rota existe
- Verifique permissões do usuário logado
- Teste URL manualmente

---

## 📞 Referências

- **Models:** `app/Models/User.php`, `app/Models/Budget.php`
- **Widget:** `app/Filament/Resources/UserResource/Widgets/UserStatsWidget.php`
- **Relations:** 
  - `app/Filament/Resources/UserResource/RelationManagers/BudgetsRelationManager.php`
  - `app/Filament/Resources/UserResource/RelationManagers/ActivitiesRelationManager.php`
- **Page:** `app/Filament/Resources/UserResource/Pages/EditUser.php`

---

**Documentação criada em:** 07/11/2025  
**Última atualização:** 07/11/2025  
**Versão:** 1.0
