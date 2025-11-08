# 🎯 Guia Rápido - Gerenciamento de Equipe

## 📍 Onde Gerenciar sua Equipe

### **No Painel Admin:**

1. **Acesse:** `http://127.0.0.1:8000/admin`
2. **Faça login** como super_admin
3. **No menu lateral, procure o grupo "Security"**
4. Você verá:
   - 👥 **Team Management** (Gerenciar Usuários)
   - 🛡️ **Shield → Roles** (Gerenciar Cargos)

---

## 👥 Gerenciar Usuários (Team Management)

### **Criar Novo Membro da Equipe:**

1. Clique em **"Security" → "Team Management"**
2. Clique no botão **"New Team Member"** (canto superior direito)
3. Preencha os dados:

   **Seção: User Information**
   - **Full Name:** Nome completo do usuário
   - **Email:** Email para login (único)
   - **Password:** Senha (mínimo 8 caracteres)
   - **Confirm Password:** Confirme a senha

   **Seção: Role & Permissions**
   - **User Role:** Selecione o cargo
     - 🔴 **Super Admin** - Acesso total (cuidado!)
     - 🟡 **Admin** - Gerente/Supervisor
     - 🟢 **Vendedor** - Equipe de vendas
     - 🔵 **Financeiro** - Setor financeiro
     - 🟣 **Atendimento** - Suporte/Recepção

4. Clique em **"Create"**

**✅ Pronto! O usuário já pode fazer login.**

---

### **Editar Usuário Existente:**

1. Em **"Team Management"**, encontre o usuário
2. Clique no ícone de **"Edit"** (lápis)
3. Modifique os dados necessários
4. **Para trocar o cargo:** vá na seção "Role & Permissions"
5. Salve

---

### **Deletar Usuário:**

1. Em **"Team Management"**, encontre o usuário
2. Clique no ícone de **"Delete"** (lixeira)
3. Confirme a ação

⚠️ **Cuidado:** Apenas super_admin pode deletar usuários!

---

### **Filtrar Usuários por Cargo:**

Na página **"Team Management"**, você verá **abas no topo**:
- **All Users** - Todos os usuários
- **Super Admins** - Apenas super admins
- **Admins** - Apenas admins
- **Sales Team** - Apenas vendedores
- **Financial** - Apenas financeiro
- **Customer Service** - Apenas atendimento

Clique na aba desejada para filtrar!

---

## 🛡️ Gerenciar Cargos e Permissões

### **Ver/Editar Permissões de um Cargo:**

1. Vá em **"Security" → "Shield" → "Roles"**
2. Clique em **"Edit"** no cargo desejado
3. Você verá todas as permissões organizadas por módulo:
   - **Budgets** (Orçamentos)
   - **Customers** (Clientes)
   - **Products** (Produtos)
   - **Mails** (E-mails)
   - **Newsletter** (Newsletter)
   - Etc.

4. **Marque/desmarque** as permissões conforme necessário
5. Salve

---

### **Criar Cargo Personalizado:**

1. Vá em **"Security" → "Shield" → "Roles"**
2. Clique em **"New Role"**
3. Defina:
   - **Name:** nome do cargo (ex: `coordenador`)
   - **Guard Name:** mantenha `web`
4. Marque as permissões desejadas
5. Salve

**Depois**, ao criar/editar usuários, o novo cargo estará disponível!

---

## 📊 Visualizar Estatísticas da Equipe

Na página **"Team Management"**, você verá:

| Coluna | Descrição |
|--------|-----------|
| **Name** | Nome do usuário |
| **Email** | Email do usuário (clique para copiar) |
| **Role** | Cargo com badge colorido |
| **Budgets** | Quantidade de orçamentos criados |
| **Created At** | Data de criação |

---

## 🎯 Cenários Práticos

### **Cenário 1: Adicionar novo vendedor**

```
1. Security → Team Management → New Team Member
2. Nome: João Silva
3. Email: joao@empresa.com
4. Senha: Senh@123
5. Role: Vendedor (Sales Team)
6. Create
```

**João agora pode:**
- ✅ Criar orçamentos
- ✅ Ver apenas seus orçamentos
- ✅ Gerenciar clientes
- ❌ Não vê orçamentos de outros vendedores

---

### **Cenário 2: Promover vendedor a admin**

```
1. Security → Team Management
2. Encontre o vendedor
3. Edit
4. Troque Role de "Vendedor" para "Admin"
5. Save
```

**Agora ele pode:**
- ✅ Ver todos os orçamentos
- ✅ Gerenciar produtos
- ✅ Acessar relatórios completos
- ❌ Ainda não pode criar usuários

---

### **Cenário 3: Adicionar pessoa do financeiro**

```
1. Security → Team Management → New Team Member
2. Nome: Maria Contadora
3. Email: maria@empresa.com
4. Senha: Financeir@456
5. Role: Financeiro (Financial)
6. Create
```

**Maria pode:**
- ✅ Ver todos os orçamentos (somente leitura)
- ✅ Exportar relatórios
- ❌ Não pode criar/editar orçamentos
- ❌ Não tem acesso ao inbox

---

## 🔍 Pesquisar Usuários

Use a **barra de pesquisa** no topo da tabela para buscar por:
- Nome
- Email

---

## 📈 Badges e Indicadores

**Cores dos Cargos:**
- 🔴 **Vermelho** → Super Admin (perigo - acesso total)
- 🟡 **Amarelo** → Admin (atenção - gerencial)
- 🟢 **Verde** → Vendedor (vendas)
- 🔵 **Azul** → Financeiro (financeiro)
- 🟣 **Roxo** → Atendimento (suporte)

**Badge de Orçamentos:**
- Mostra quantos orçamentos cada usuário criou
- Verde → usuário ativo

---

## ⚙️ Comandos Úteis (Terminal)

### **Criar super admin via comando:**
```bash
php artisan shield:super-admin
```

### **Atribuir super admin a usuário existente:**
```bash
php artisan shield:super-admin --user=1
```

### **Ver permissões de um usuário (Tinker):**
```bash
php artisan tinker
```
```php
$user = User::find(1);
$user->roles->pluck('name');
$user->getAllPermissions()->pluck('name');
```

---

## 🚨 Avisos Importantes

⚠️ **Super Admin:**
- Tenha **no máximo 2-3** super admins
- Super admin pode fazer **TUDO**
- Não dê esse cargo sem necessidade

⚠️ **Senhas:**
- Mínimo 8 caracteres
- Use senhas fortes
- Usuários podem alterar a própria senha

⚠️ **Deletar Usuários:**
- Só super_admin pode deletar
- Cuidado ao deletar: orçamentos vinculados podem ser afetados

---

## 📞 Onde Está Tudo?

```
Menu Lateral → Security
├── 👥 Team Management (Gerenciar Usuários)
└── 🛡️ Shield
    └── Roles (Gerenciar Cargos)
```

---

## ✅ Checklist Inicial

Após implementação, faça:

- [ ] Criar seu primeiro usuário admin
- [ ] Criar pelo menos 1 vendedor de teste
- [ ] Testar login com cada cargo
- [ ] Verificar se vendedor vê apenas seus orçamentos
- [ ] Testar criação de orçamento por vendedor
- [ ] Verificar permissões de cada cargo

---

**🎉 Sua equipe está pronta para trabalhar!**
