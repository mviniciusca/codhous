# 🚀 Guia de Uso - Sistema Multi-Usuário

## ✅ Status da Implementação

**Implementação concluída com sucesso!** 

Todos os componentes do sistema multi-usuário foram implementados:
- ✅ Migrations executadas
- ✅ Cargos e permissões criados
- ✅ Policies implementadas
- ✅ Scopes globais configurados
- ✅ Models atualizados
- ✅ Super admin criado

---

## 📌 Acessar o Sistema

### URL de Acesso:
```
http://127.0.0.1:8000/admin/login
```

### Credenciais Super Admin:
```
Email: codhous@codhous.app
Senha: [sua senha configurada]
```

---

## 👥 Gerenciar Usuários e Cargos

### 1. **Acessar Gerenciamento de Cargos**

No painel admin, procure por:
- **Shield → Roles** (Menu lateral)

Aqui você pode:
- Ver todos os cargos criados
- Editar permissões de cada cargo
- Criar novos cargos personalizados

### 2. **Criar Novo Usuário**

1. Vá em **Users** no menu lateral
2. Clique em **New User**
3. Preencha:
   - Nome
   - Email
   - Senha
4. **Atribua o cargo** na aba **Roles**
5. Salve

### 3. **Atribuir Cargo a Usuário Existente**

1. Vá em **Users**
2. Edite o usuário desejado
3. Na aba **Roles**, selecione o cargo
4. Salve

---

## 🔐 Cargos Disponíveis

### **super_admin**
- Acesso total ao sistema
- Gerencia usuários e permissões
- Único que pode deletar permanentemente

**Como atribuir:**
```bash
php artisan shield:super-admin --user={user_id}
```

### **admin**
- Gerente/Supervisor
- Gerencia orçamentos, clientes, produtos
- Não pode criar/deletar usuários

### **vendedor**
- Equipe de vendas
- Cria e gerencia seus próprios orçamentos
- Não vê orçamentos de outros vendedores

### **financeiro**
- Setor financeiro
- Acesso somente leitura a orçamentos
- Exporta relatórios

### **atendimento**
- Suporte/Recepção
- Gerencia inbox e clientes
- Visualização básica de orçamentos

---

## 📝 Funcionalidades por Cargo

### **Orçamentos (Budgets)**

| Ação | super_admin | admin | vendedor | financeiro | atendimento |
|------|-------------|-------|----------|------------|-------------|
| Ver todos | ✅ | ✅ | ❌ Só próprios | ✅ | ✅ Básico |
| Criar | ✅ | ✅ | ✅ | ❌ | ❌ |
| Editar | ✅ Todos | ✅ Todos | ✅ Próprios | ❌ | ❌ |
| Deletar | ✅ Permanente | ✅ Arquivar | ✅ Próprios | ❌ | ❌ |
| Enviar email | ✅ | ✅ | ✅ Próprios | ❌ | ❌ |

### **E-mails (Inbox)**

| Ação | super_admin | admin | vendedor | financeiro | atendimento |
|------|-------------|-------|----------|------------|-------------|
| Ver todos | ✅ | ✅ | ❌ Só próprios | ❌ | ❌ Só próprios |
| Enviar | ✅ | ✅ | ✅ Limitado | ❌ | ✅ |
| Deletar | ✅ | ✅ | ✅ Próprios | ❌ | ❌ |

### **Clientes (Customers)**

| Ação | super_admin | admin | vendedor | financeiro | atendimento |
|------|-------------|-------|----------|------------|-------------|
| Ver | ✅ | ✅ | ✅ | ✅ Básico | ✅ |
| Criar | ✅ | ✅ | ✅ | ❌ | ✅ |
| Editar | ✅ | ✅ | ✅ | ❌ | ✅ |
| Deletar | ✅ | ✅ | ❌ | ❌ | ❌ |

---

## 🎯 Casos de Uso Práticos

### **Cenário 1: Novo Vendedor na Equipe**

1. **Super admin** cria usuário:
   ```
   Nome: João Silva
   Email: joao@empresa.com
   Cargo: vendedor
   ```

2. João faz login e vê:
   - Apenas seus próprios orçamentos
   - Pode criar novos orçamentos
   - Pode gerenciar clientes
   - Visualiza produtos

3. João **NÃO pode**:
   - Ver orçamentos de outros vendedores
   - Deletar orçamentos permanentemente
   - Acessar configurações

### **Cenário 2: Pessoa do Financeiro**

1. **Admin** cria usuário:
   ```
   Nome: Maria Contadora
   Email: financeiro@empresa.com
   Cargo: financeiro
   ```

2. Maria faz login e vê:
   - **Todos** os orçamentos (somente leitura)
   - Pode exportar relatórios
   - Visualiza clientes (informações básicas)

3. Maria **NÃO pode**:
   - Criar ou editar orçamentos
   - Acessar inbox
   - Deletar nada

### **Cenário 3: Atendimento/Recepção**

1. **Admin** cria usuário:
   ```
   Nome: Ana Recepção
   Email: atendimento@empresa.com
   Cargo: atendimento
   ```

2. Ana faz login e vê:
   - Inbox (mensagens do site)
   - Pode cadastrar clientes
   - Visualiza orçamentos (informações básicas)

3. Ana **NÃO pode**:
   - Criar orçamentos
   - Ver valores completos
   - Deletar clientes

---

## 🔧 Personalizações Avançadas

### **Criar Cargo Personalizado**

1. Vá em **Shield → Roles**
2. Clique em **New Role**
3. Defina o nome (ex: `gerente_financeiro`)
4. Marque as permissões desejadas
5. Salve

### **Ajustar Permissões de Cargo Existente**

1. Vá em **Shield → Roles**
2. Edite o cargo desejado
3. Marque/desmarque permissões
4. Salve

**Exemplo:** Permitir que `vendedor` veja orçamentos de toda equipe:
- Edite o cargo `vendedor`
- As permissões já estão configuradas, mas o filtro está no código
- Para alterar, modifique o Scope: `app/Models/Scopes/UserBudgetScope.php`

---

## 🛠️ Comandos Úteis

### **Criar Super Admin**
```bash
php artisan shield:super-admin
```

### **Atribuir Super Admin a Usuário Existente**
```bash
php artisan shield:super-admin --user={user_id}
```

### **Gerar Permissões para Novos Resources**
```bash
php artisan shield:generate --all
```

### **Limpar Cache de Permissões**
```bash
php artisan permission:cache-reset
```

### **Popular Cargos Novamente (cuidado!)**
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

---

## 🐛 Troubleshooting

### **Problema: Usuário não consegue ver nada**
**Solução:**
1. Verifique se o cargo foi atribuído corretamente
2. Limpe o cache: `php artisan permission:cache-reset`
3. Verifique se o usuário está logado

### **Problema: Vendedor vê orçamentos de outros**
**Solução:**
- Verifique o arquivo `app/Models/Scopes/UserBudgetScope.php`
- Certifique-se que o scope está aplicado no Model Budget

### **Problema: Super admin não consegue fazer algo**
**Solução:**
- Super admin deve ter bypass em todas as policies
- Verifique o método `before()` nas Policies

### **Problema: Erro ao criar orçamento**
**Solução:**
- Verifique se o `user_id` está sendo preenchido automaticamente
- Verifique o método `booted()` no Model Budget

---

## 📊 Monitoramento

### **Verificar Permissões de um Usuário**

No Tinker:
```bash
php artisan tinker
```

```php
$user = User::find(1);
$user->roles->pluck('name'); // Ver cargos
$user->getAllPermissions()->pluck('name'); // Ver permissões
$user->can('budget_create'); // Testar permissão específica
```

### **Listar Todos os Cargos**
```php
use Spatie\Permission\Models\Role;
Role::with('permissions')->get();
```

---

## 📞 Suporte

Para dúvidas ou problemas:
1. Consulte a documentação completa: `PERMISSIONS_STRUCTURE.md`
2. Verifique os logs: `storage/logs/laravel.log`
3. Execute testes de permissão no Tinker

---

## 🎉 Próximos Passos

1. ✅ Criar usuários para sua equipe
2. ✅ Atribuir cargos apropriados
3. ✅ Testar permissões com cada cargo
4. ✅ Ajustar conforme necessário
5. ⚠️ **IMPORTANTE:** Fazer backup antes de modificações em produção

---

**Sistema pronto para uso!** 🚀
