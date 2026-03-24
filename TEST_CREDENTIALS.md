# 🔐 Sistema RasterTech - Credenciais de Teste

## ✅ Autenticação Implementada

O sistema agora inclui autenticação com 4 perfis de usuário com role-based access control (RBAC).

### 📋 Usuários Disponíveis

| Perfil | Email | Senha | Função |
|--------|-------|-------|--------|
| **Técnico** | `joao@instalador.com` | `joao123` | Instalador de Campo |
| **Operador** | `carlos@operador.com` | `carlos123` | Operador de Matriz |
| **Motorista** | `pedro@motorista.com` | `pedro123` | Motorista de Frota |
| **Admin** | `camila@cadastradora.com` | `camila123` | Cadastradora/Admin |

## 🧪 Como Testar

### 1. **Abrir a Aplicação**
- Navegue para: http://localhost:8000
- A aplicação deve mostrar o modal de autenticação automaticamente

### 2. **Selecionar um Usuário**
- O modal exibe 4 cartões com os perfis de usuário
- Clique em um dos cartões para selecionar o perfil desejado
- O email será preenchido automaticamente

### 3. **Fazer Login**
- Observe que o email já está preenchido com base no perfil selecionado
- Digite a senha correspondente (confira a tabela acima)
- Clique em "Entrar"

### 4. **Verificar Autenticação**
Após o login bem-sucedido, você verá:
- ✅ Mensagem "Bem-vindo, [Nome do Usuário]!"
- ✅ Nome e perfil do usuário exibidos no topo direito
- ✅ Botão "Sair" aparecendo no header
- ✅ Dashboard carregado com dados

### 5. **Testar Permissões por Perfil**

#### Técnico (João Silva)
- ✅ Pode ver Dashboard
- ✅ Pode acessar Ativos
- ✅ Pode criar Ordens de Serviço
- ❌ Não pode acessar Configurações

#### Operador (Carlos Santos)
- ✅ Pode ver Dashboard
- ✅ Pode acessar Ativos
- ✅ Pode criar Ordens de Serviço
- ❌ Não pode acessar Configurações

#### Motorista (Pedro Oliveira)
- ✅ Pode ver Dashboard
- ✅ Pode acessar Ativos (apenas leitura)
- ✅ Pode ver Ordens de Serviço
- ❌ Não pode criar Ordens de Serviço
- ❌ Não pode acessar Configurações

#### Admin (Camila Costa)
- ✅ Pode ver Dashboard
- ✅ Pode acessar Ativos
- ✅ Pode criar Ordens de Serviço
- ✅ Pode acessar Configurações

## 🔍 Debug - Verificar Console

Abra o Developer Console (F12) para ver logs detalhados:

```
🚀 Inicializando aplicação Rastertech...
✅ UI inicializada
✅ Event listeners configurados
🗄️ Inicializando sistema de dados...
🏗️ Criando modal de autenticação...
✅ Modal criado no DOM
✅ Event listeners configurados
👥 Usuários mockados carregados: 4
📋 Credenciais de teste:
  - Técnico: joao@instalador.com / joao123
  - Operador: carlos@operador.com / carlos123
  - Motorista: pedro@motorista.com / pedro123
  - Admin: camila@cadastradora.com / camila123
```

Ao fazer login:
```
🎭 Criando modal de autenticação...
🔧 Instanciando AuthModal...
✅ AuthModal criado
📤 Chamando authModal.show com callback...
👤 Selecionando usuário com role: technician
📧 Preenchendo email: joao@instalador.com
🎯 AuthModal handleLogin chamado com: {email, password}
📤 Chamando callback de login...
🔐 Tentando autenticar: joao@instalador.com
🔐 Hashing "joao123" -> "[hash da senha]"
✅ Usuário autenticado: João Silva
🔐 Login realizado com sucesso: João Silva (technician)
```

## ❌ Resolvendo Problemas

### Erro: "Usuário ou senha incorretos"
1. Verifique se o email e senha estão corretos (veja tabela acima)
2. Certifique-se de que você está usando exatamente a senha fornecida
3. Abra o console do navegador (F12) e procure por:
   - Se o hash está sendo calculado
   - Se o usuário foi encontrado no banco de dados

### Erro: "Erro durante a autenticação"
1. Verifique o console do navegador para mais detalhes
2. Procure por mensagens com 💥 (erro crítico)
3. Verifique se todos os componentes foram carregados corretamente

### Modal não aparece
1. Atualize a página (F5)
2. Limpe o cache do navegador (Ctrl+Shift+Delete)
3. Verifique se há erros no console do navegador

## 🔄 Logout

Para fazer logout:
1. Clique no botão "Sair" no topo direito
2. O modal de autenticação aparecerá novamente
3. Você pode fazer login com outro usuário

## 📝 Notas
- As credenciais são case-sensitive para email
- O sistema usa localStorage para manter a sessão do usuário logado
- Ao fechar e reabrir a página, o usuário permanece logado
- Setar novo usuário remove automaticamente o anterior

---

**Data de Atualização:** 24 de Março de 2026
**Status:** ✅ Autenticação Funcional
