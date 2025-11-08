# 🔐 Estrutura de Permissões e Cargos - Sistema Codhous

**Data:** 07 de Novembro de 2025  
**Versão:** 1.0  
**Sistema:** Multi-usuário para empresas de pequeno porte (até 20 funcionários)

---

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Cargos Definidos](#cargos-definidos)
3. [Módulos do Sistema](#módulos-do-sistema)
4. [Matriz de Permissões](#matriz-de-permissões)
5. [Regras Específicas](#regras-específicas)
6. [Implementação Técnica](#implementação-técnica)

---

## 🎯 Visão Geral

O sistema foi projetado para suportar trabalho em equipe com múltiplos usuários, cada um com diferentes níveis de acesso e responsabilidades. A estrutura de permissões garante:

- ✅ Segurança de dados sensíveis
- ✅ Controle granular de ações
- ✅ Rastreabilidade de operações
- ✅ Separação de responsabilidades
- ✅ Escalabilidade para crescimento da equipe

---

## 👥 Cargos Definidos

### 1. **super_admin** (Proprietário/Diretor)
**Quantidade sugerida:** 1-2 pessoas

**Responsabilidades:**
- Controle total do sistema
- Gerenciamento de usuários e permissões
- Acesso a todos os recursos e configurações
- Visualização de logs e auditoria completa
- Configuração de integrações e sistema

**Acesso:**
- ✅ TUDO

---

### 2. **admin** (Gerente/Supervisor)
**Quantidade sugerida:** 2-4 pessoas

**Responsabilidades:**
- Supervisão de orçamentos e vendas
- Gerenciamento de clientes e produtos
- Envio de campanhas de marketing
- Acesso a relatórios gerenciais

**Acesso:**
- ✅ Gerencia orçamentos (criar, editar, arquivar)
- ✅ Gerencia clientes e produtos
- ✅ Gerencia newsletter e comunicação
- ✅ Acessa relatórios completos
- ❌ **NÃO pode:** criar/editar/deletar usuários
- ❌ **NÃO pode:** deletar orçamentos permanentemente
- ❌ **NÃO pode:** alterar configurações críticas do sistema

---

### 3. **vendedor** (Equipe de Vendas)
**Quantidade sugerida:** 5-10 pessoas

**Responsabilidades:**
- Criação e gerenciamento de orçamentos
- Atendimento a clientes
- Envio de propostas comerciais
- Acompanhamento de vendas

**Acesso:**
- ✅ Cria e edita seus próprios orçamentos
- ✅ Visualiza orçamentos próprios
- ✅ Gerencia clientes
- ✅ Envia orçamentos por e-mail
- ✅ Exporta relatórios próprios
- ❌ **NÃO pode:** deletar orçamentos
- ❌ **NÃO pode:** ver orçamentos de outros vendedores (opcional)
- ❌ **NÃO pode:** acessar configurações
- ❌ **NÃO pode:** gerenciar produtos

---

### 4. **financeiro** (Setor Financeiro)
**Quantidade sugerida:** 1-3 pessoas

**Responsabilidades:**
- Análise financeira
- Geração de relatórios
- Acompanhamento de faturamento
- Auditoria de orçamentos

**Acesso:**
- ✅ Visualiza todos os orçamentos (somente leitura)
- ✅ Exporta relatórios financeiros
- ✅ Acessa informações de clientes (básico)
- ✅ Visualiza produtos e preços
- ❌ **NÃO pode:** criar/editar orçamentos
- ❌ **NÃO pode:** deletar nada
- ❌ **NÃO pode:** enviar e-mails
- ❌ **NÃO pode:** acessar inbox

---

### 5. **atendimento** (Suporte/Recepção)
**Quantidade sugerida:** 2-5 pessoas

**Responsabilidades:**
- Primeiro contato com clientes
- Cadastro de clientes
- Gerenciamento de inbox do site
- Suporte básico

**Acesso:**
- ✅ Visualiza orçamentos (informações básicas)
- ✅ Cria e gerencia clientes
- ✅ Gerencia e-mails do inbox
- ✅ Visualiza e edita status de newsletter
- ❌ **NÃO pode:** criar orçamentos
- ❌ **NÃO pode:** ver valores sensíveis completos
- ❌ **NÃO pode:** deletar clientes
- ❌ **NÃO pode:** acessar configurações

---

## 🧩 Módulos do Sistema

### 1. **Gerenciamento de Usuários**
- Cadastro de usuários
- Atribuição de cargos
- Controle de acesso

### 2. **Orçamentos (Budget)**
- Criação de orçamentos
- Edição e acompanhamento
- Envio por e-mail com PDF
- Histórico de alterações
- Exportação

### 3. **Clientes (Customers)**
- Cadastro completo
- Gerenciamento de contatos
- Histórico de orçamentos

### 4. **Produtos**
- Catálogo de produtos
- Preços e opções
- Locais/Áreas de aplicação

### 5. **Comunicação**
- **Inbox:** E-mails recebidos e enviados
- **Newsletter:** Gerenciamento de assinantes
- **E-mails de Orçamento:** Envio automático de propostas

### 6. **Configurações**
- Configurações gerais
- Configurações de orçamento
- Configurações de e-mail
- Módulos do sistema

### 7. **Logs e Relatórios**
- Activity logs
- Relatórios de vendas
- Exportações

---

## 📊 Matriz de Permissões Completa

| Módulo / Ação | super_admin | admin | vendedor | financeiro | atendimento |
|---------------|-------------|-------|----------|------------|-------------|
| **👥 USUÁRIOS** |
| `user_view_any` - Ver lista | ✅ | ✅ | ❌ | ❌ | ❌ |
| `user_view` - Ver detalhes | ✅ | ✅ | ❌ | ❌ | ❌ |
| `user_create` - Criar | ✅ | ❌ | ❌ | ❌ | ❌ |
| `user_update` - Editar | ✅ | ❌ | ❌ | ❌ | ❌ |
| `user_delete` - Deletar | ✅ | ❌ | ❌ | ❌ | ❌ |
| `user_force_delete` - Deletar permanente | ✅ | ❌ | ❌ | ❌ | ❌ |
| **👔 CARGOS E PERMISSÕES** |
| `role_view_any` - Ver cargos | ✅ | ❌ | ❌ | ❌ | ❌ |
| `role_view` - Ver detalhes | ✅ | ❌ | ❌ | ❌ | ❌ |
| `role_create` - Criar cargo | ✅ | ❌ | ❌ | ❌ | ❌ |
| `role_update` - Editar cargo | ✅ | ❌ | ❌ | ❌ | ❌ |
| `role_delete` - Deletar cargo | ✅ | ❌ | ❌ | ❌ | ❌ |
| **💰 ORÇAMENTOS** |
| `budget_view_any` - Ver lista | ✅ | ✅ | ✅ Próprios | ✅ | ✅ Básico |
| `budget_view` - Ver detalhes | ✅ | ✅ | ✅ Próprios | ✅ | ✅ Básico |
| `budget_create` - Criar | ✅ | ✅ | ✅ | ❌ | ❌ |
| `budget_update` - Editar | ✅ | ✅ | ✅ Próprios | ❌ | ❌ |
| `budget_delete` - Arquivar | ✅ | ✅ | ✅ Próprios | ❌ | ❌ |
| `budget_force_delete` - Deletar permanente | ✅ | ❌ | ❌ | ❌ | ❌ |
| `budget_restore` - Restaurar | ✅ | ✅ | ✅ Próprios | ❌ | ❌ |
| `budget_export` - Exportar | ✅ | ✅ | ✅ Próprios | ✅ | ❌ |
| `budget_send_email` - Enviar por e-mail | ✅ | ✅ | ✅ Próprios | ❌ | ❌ |
| **👥 CLIENTES** |
| `customer_view_any` - Ver lista | ✅ | ✅ | ✅ | ✅ Básico | ✅ |
| `customer_view` - Ver detalhes | ✅ | ✅ | ✅ | ✅ Básico | ✅ |
| `customer_create` - Criar | ✅ | ✅ | ✅ | ❌ | ✅ |
| `customer_update` - Editar | ✅ | ✅ | ✅ | ❌ | ✅ |
| `customer_delete` - Deletar | ✅ | ✅ | ❌ | ❌ | ❌ |
| `customer_force_delete` - Deletar permanente | ✅ | ✅ | ❌ | ❌ | ❌ |
| `customer_restore` - Restaurar | ✅ | ✅ | ❌ | ❌ | ❌ |
| **🛍️ PRODUTOS** |
| `product_view_any` - Ver lista | ✅ | ✅ | ✅ | ✅ | ✅ |
| `product_view` - Ver detalhes | ✅ | ✅ | ✅ | ✅ | ✅ |
| `product_create` - Criar | ✅ | ✅ | ❌ | ❌ | ❌ |
| `product_update` - Editar | ✅ | ✅ | ❌ | ❌ | ❌ |
| `product_delete` - Deletar | ✅ | ✅ | ❌ | ❌ | ❌ |
| `product_force_delete` - Deletar permanente | ✅ | ✅ | ❌ | ❌ | ❌ |
| **📬 INBOX (E-MAILS)** |
| `mail_view_any` - Ver lista | ✅ | ✅ | ✅ Próprios | ❌ | ✅ Próprios |
| `mail_view` - Ver detalhes | ✅ | ✅ | ✅ Próprios | ❌ | ✅ Próprios |
| `mail_create` - Compor/Enviar | ✅ | ✅ | ✅ Limitado | ❌ | ✅ |
| `mail_update` - Editar (marcar lido) | ✅ | ✅ | ✅ Próprios | ❌ | ✅ Próprios |
| `mail_delete` - Deletar | ✅ | ✅ | ✅ Próprios | ❌ | ❌ |
| `mail_force_delete` - Deletar permanente | ✅ | ✅ | ❌ | ❌ | ❌ |
| `mail_restore` - Restaurar | ✅ | ✅ | ✅ Próprios | ❌ | ❌ |
| **📮 NEWSLETTER** |
| `newsletter_view_any` - Ver assinantes | ✅ | ✅ | ❌ | ❌ | ✅ |
| `newsletter_view` - Ver detalhes | ✅ | ✅ | ❌ | ❌ | ✅ |
| `newsletter_create` - Criar assinante | ✅ | ✅ | ❌ | ❌ | ✅ |
| `newsletter_update` - Editar status | ✅ | ✅ | ❌ | ❌ | ✅ |
| `newsletter_delete` - Deletar | ✅ | ✅ | ❌ | ❌ | ❌ |
| `newsletter_export` - Exportar lista | ✅ | ✅ | ❌ | ❌ | ❌ |
| `newsletter_send` - Enviar campanha | ✅ | ✅ | ❌ | ❌ | ❌ |
| **⚙️ CONFIGURAÇÕES** |
| `setting_view` - Ver configurações | ✅ | ✅ Parcial | ❌ | ❌ | ❌ |
| `setting_update` - Editar configurações | ✅ | ✅ Parcial | ❌ | ❌ | ❌ |
| `setting_budget` - Config. orçamento | ✅ | ✅ | ❌ | ❌ | ❌ |
| `setting_email` - Config. e-mail | ✅ | ❌ | ❌ | ❌ | ❌ |
| `setting_system` - Config. sistema | ✅ | ❌ | ❌ | ❌ | ❌ |
| **📊 LOGS E RELATÓRIOS** |
| `activity_log_view_any` - Ver logs | ✅ | ✅ Básico | ❌ | ❌ | ❌ |
| `activity_log_view` - Ver detalhes | ✅ | ✅ Básico | ❌ | ❌ | ❌ |
| `report_view` - Ver relatórios | ✅ | ✅ | ✅ Próprios | ✅ | ❌ |
| `report_export` - Exportar | ✅ | ✅ | ✅ Próprios | ✅ | ❌ |
| **📄 PÁGINAS ESPECIAIS** |
| `page_dashboard` - Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ |
| `page_fabricator` - Editor de páginas | ✅ | ✅ | ❌ | ❌ | ❌ |

---

## 🎯 Regras Específicas por Módulo

### 📧 **Sistema de E-mails**

#### **Inbox - Regras de Visibilidade:**

1. **super_admin e admin:**
   - Veem TODOS os e-mails (centralizados)
   - Podem gerenciar inbox completo
   - Acessam pastas: Recebidos, Enviados, Favoritos, Spam, Lixeira

2. **vendedor:**
   - Vê apenas e-mails relacionados aos seus orçamentos
   - Pode enviar e-mails para seus clientes
   - Acessa apenas suas mensagens

3. **atendimento:**
   - Vê e-mails recebidos do formulário do site
   - Pode compor respostas
   - Gerencia comunicação inicial com clientes

4. **financeiro:**
   - SEM acesso ao inbox (não necessita)

#### **Envio de Orçamentos:**
- Apenas quem pode **editar** o orçamento pode enviá-lo por e-mail
- O PDF é anexado automaticamente
- Registro no inbox quando enviado

#### **Newsletter:**
- super_admin e admin podem enviar campanhas
- atendimento pode gerenciar assinantes
- Outros cargos não têm acesso

---

### 💰 **Orçamentos - Regras de Propriedade**

#### **Campo `user_id` (Criador do Orçamento):**
- Todo orçamento registra quem criou (`user_id`)
- Vendedores veem apenas orçamentos onde `user_id = auth()->id()`
- admin e super_admin veem todos

#### **Edição:**
- super_admin: Edita qualquer orçamento
- admin: Edita qualquer orçamento
- vendedor: Edita apenas `WHERE user_id = auth()->id()`

#### **Exclusão:**
- super_admin: Pode fazer `force_delete` (permanente)
- admin: Pode arquivar (soft delete)
- vendedor: Pode arquivar apenas os próprios

#### **Histórico de Alterações:**
- Registrado na tabela `budget_histories`
- Vinculado ao usuário que fez a alteração
- Auditável pelo super_admin

---

### 👥 **Clientes - Regras de Acesso**

#### **Níveis de Visualização:**

1. **Completo** (super_admin, admin, vendedor, atendimento):
   - Nome, e-mail, telefone, endereço completo
   - Histórico de orçamentos
   - Notas e observações

2. **Básico** (financeiro):
   - Nome, e-mail, telefone
   - Cidade/Estado
   - SEM acesso a observações internas

#### **Criação/Edição:**
- Todos menos financeiro podem criar clientes
- Apenas admin pode deletar clientes

---

### 🛍️ **Produtos - Regras de Gerenciamento**

- **super_admin e admin:** Gerenciam catálogo completo
- **Demais cargos:** Somente visualização
- Preços visíveis para todos (necessário para orçamentos)

---

### ⚙️ **Configurações - Acesso Restrito**

#### **Níveis de Acesso:**

1. **super_admin:**
   - Configurações gerais do sistema
   - Configurações de e-mail (SMTP)
   - Configurações de orçamento
   - Módulos ativos/inativos
   - Integrações

2. **admin:**
   - Configurações de orçamento (parcial)
   - Configurações de comunicação
   - SEM acesso a: SMTP, sistema crítico

3. **Demais:**
   - SEM acesso a configurações

---

## 🔧 Implementação Técnica

### **Stack Utilizado:**
- **Laravel 11.x**
- **Filament 3.x**
- **Spatie Laravel Permission** (roles e permissões)
- **Filament Shield** (interface de gerenciamento)

### **Estrutura de Banco de Dados:**

#### **Tabelas do Spatie Permission:**
```sql
- roles (cargos)
- permissions (permissões)
- model_has_roles (usuário → cargo)
- model_has_permissions (usuário → permissão direta)
- role_has_permissions (cargo → permissões)
```

#### **Modificações Necessárias:**

1. **Tabela `users`:**
   - Já possui trait `HasRoles`
   - Sem modificações necessárias

2. **Tabela `budgets`:**
   ```sql
   ALTER TABLE budgets ADD COLUMN user_id BIGINT UNSIGNED;
   ALTER TABLE budgets ADD FOREIGN KEY (user_id) REFERENCES users(id);
   ```

3. **Tabela `mails`:**
   ```sql
   ALTER TABLE mails ADD COLUMN user_id BIGINT UNSIGNED;
   ALTER TABLE mails ADD FOREIGN KEY (user_id) REFERENCES users(id);
   ```

---

### **Arquivos a Serem Criados/Modificados:**

#### **1. Seeder - Cargos e Permissões:**
```
database/seeders/RolesAndPermissionsSeeder.php
```

#### **2. Policies:**
```
app/Policies/BudgetPolicy.php
app/Policies/UserPolicy.php
app/Policies/CustomerPolicy.php
app/Policies/ProductPolicy.php
app/Policies/MailPolicy.php
app/Policies/NewsletterPolicy.php
app/Policies/SettingPolicy.php
app/Policies/ActivityLogPolicy.php
```

#### **3. Migrations:**
```
database/migrations/xxxx_add_user_id_to_budgets_table.php
database/migrations/xxxx_add_user_id_to_mails_table.php
```

#### **4. Resources (Filament):**
Modificar para aplicar policies:
```
app/Filament/Resources/BudgetResource.php
app/Filament/Resources/CustomerResource.php
app/Filament/Resources/ProductResource.php
app/Filament/Resources/MailResource.php
app/Filament/Resources/NewsletterResource.php
```

#### **5. Scopes (Query Filters):**
```
app/Models/Scopes/UserBudgetScope.php
app/Models/Scopes/UserMailScope.php
```

---

### **Fluxo de Implementação:**

#### **Fase 1: Estrutura Base**
1. ✅ Criar migration para adicionar `user_id` em budgets e mails
2. ✅ Criar Seeder com cargos e permissões
3. ✅ Executar migrations e seeders
4. ✅ Testar criação de usuários com cargos

#### **Fase 2: Policies**
5. ✅ Criar Policies para cada Resource
6. ✅ Implementar métodos: viewAny, view, create, update, delete, forceDelete
7. ✅ Adicionar lógica de propriedade (user_id)
8. ✅ Registrar Policies no AuthServiceProvider

#### **Fase 3: Resources do Filament**
9. ✅ Aplicar Policies nos Resources
10. ✅ Adicionar Global Scopes para filtrar por user_id
11. ✅ Modificar queries para respeitar permissões
12. ✅ Adicionar campo user_id nos formulários (hidden)

#### **Fase 4: Interface**
13. ✅ Configurar Filament Shield Resource
14. ✅ Adicionar navegação para gerenciamento de cargos
15. ✅ Customizar dashboard por cargo
16. ✅ Ajustar widgets de estatísticas

#### **Fase 5: Testes e Validação**
17. ✅ Testar cada cargo individualmente
18. ✅ Validar visibilidade de orçamentos
19. ✅ Validar permissões de e-mail
20. ✅ Testar criação/edição/exclusão

---

## 📝 Notas Importantes

### **Segurança:**
- Todas as permissões são verificadas tanto no backend (Policies) quanto no frontend (Filament)
- Queries são filtradas automaticamente via Global Scopes
- Logs de auditoria registram todas as ações críticas

### **Escalabilidade:**
- Fácil adicionar novos cargos via interface
- Permissões granulares permitem ajustes finos
- Estrutura suporta crescimento da equipe

### **Manutenção:**
- Documentação inline em todos os arquivos
- Nomenclatura padronizada de permissões
- Testes automatizados para validação

---

## 🚀 Comandos Úteis

### **Gerar Permissões (Filament Shield):**
```bash
php artisan shield:generate
```

### **Criar Super Admin:**
```bash
php artisan shield:super-admin
```

### **Rodar Seeders:**
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### **Limpar Cache de Permissões:**
```bash
php artisan permission:cache-reset
```

### **Criar Policy:**
```bash
php artisan make:policy BudgetPolicy --model=Budget
```

---

## 📞 Suporte e Dúvidas

Para dúvidas sobre implementação ou ajustes na estrutura de permissões:
- Consultar documentação do [Spatie Permission](https://spatie.be/docs/laravel-permission)
- Consultar documentação do [Filament Shield](https://github.com/bezhanSalleh/filament-shield)

---

**Documento criado em:** 07/11/2025  
**Última atualização:** 07/11/2025  
**Status:** ✅ Aprovado para implementação
