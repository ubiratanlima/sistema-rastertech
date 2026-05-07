# 🛰️ Manual de Guerra: Deploy Portainer (Produção)

Este manual contém as soluções reais para os problemas de permissão, autenticação, segurança e sincronização enfrentados no ambiente Rastertech.

---

## 1. O Problema da Senha do GitHub (Token)
**Sintoma**: `Authentication failed` ou `Password authentication is not supported`.
**Solução**: O GitHub não aceita mais sua senha comum. Use um **Personal Access Token (PAT)**.
1.  Gere o Token no GitHub (Settings -> Developer Settings -> Tokens Classic).
2.  No Portainer, use seu usuário e cole o **Token** no campo de senha.

---

## 2. Aviso de "Conexão Não Segura" no Login (HTTPS)
**Sintoma**: Navegador avisa que "as informações serão enviadas por uma conexão não segura".
**Solução**: O Laravel está gerando links `http` em um site `https`.
1.  Garantir que `AppServiceProvider.php` tenha o comando `URL::forceScheme('https');`.
2.  Sempre rodar `php artisan optimize` após o deploy para atualizar o cache de links.

---

## 3. Erro 500 ao abrir páginas (Missing Column)
**Sintoma**: `column ... deleted_at does not exist`.
**Solução**: Algum modelo está usando `SoftDeletes` mas a tabela no banco não tem essa coluna.
1.  Remover `use SoftDeletes` do Modelo PHP correspondente.
2.  Ou rodar a migração para adicionar a coluna (se for desejado).
3.  **Importante**: Sempre rode `php artisan migrate --force` para garantir que a tabela de **Auditoria** existirá.

---

## 4. Loop de Redirecionamento (Portal vs Dashboard)
**Sintoma**: O usuário faz login e fica sendo jogado de uma página para outra sem carregar.
**Solução**: Geralmente o `CustomerPortalController` não encontra o `customer_id` do usuário.
1.  Verificar se o usuário logado tem um "Cliente" vinculado no cadastro.
2.  O `DashboardController` agora possui lógica para detectar o cargo e mandar para o lugar certo.

---

## 5. O Problema de Permissão (Permission Denied)
**Sintoma**: `failed to remove... Permission denied`.
**Solução**: Você precisa assumir o controle dos arquivos como ROOT.
1.  No Console do Portainer, selecione o usuário **`root`**.
2.  Rode: `chown -R ubiratanlima:ubiratanlima /var/www`.
3.  Rode: `git config --global --add safe.directory /var/www`.

---

## 6. O Reset Geral (Solução Atômica)
Se o Git der erro de conflito e nada funcionar, use o "Reset de Fábrica" do servidor:
```bash
git reset --hard origin/main
git clean -fd
git pull origin main
php artisan optimize
```

---
*Este documento foi forjado em combate. Siga os passos acima e o sistema sempre voltará ao ar.*
