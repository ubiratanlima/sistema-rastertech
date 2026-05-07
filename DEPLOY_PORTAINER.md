# 🛰️ Manual de Guerra: Deploy Portainer (Produção)

Este manual contém as soluções reais para os problemas de permissão, autenticação e sincronização enfrentados no ambiente Rastertech.

---

## 1. O Problema da Senha do GitHub (Token)
**Sintoma**: `Authentication failed` ou `Password authentication is not supported`.
**Solução**: O GitHub não aceita mais sua senha. Você deve usar um **Personal Access Token (PAT)**.
1.  Gere o Token no GitHub (Settings -> Developer Settings -> Tokens Classic).
2.  No Portainer (Stack ou Build), use seu usuário e cole o **Token** no campo de senha.

---

## 2. O Problema da Imagem "Congelada"
**Sintoma**: Você dá o "Update" na Stack, mas o site online não muda.
**Solução**: O Portainer não reconstruiu o código. Force o Build:
1.  Vá em **Images** -> **Build a new image**.
2.  Nome: `sistema-rastertech-app:latest`.
3.  Método: **Git Repository** (ou Web Editor se o Git falhar).
4.  Após o Build, volte na Stack e dê **Update** marcando **"Re-pull images"**.

---

## 3. O Problema de Permissão (Permission Denied)
**Sintoma**: `failed to remove... Permission denied` ou `unable to unlink`.
**Solução**: Você está tentando mexer em arquivos como um usuário comum.
1.  Ao abrir o **Console** no Portainer, mude o campo **User** de `default` para **`root`**.
2.  Lá dentro, assuma o controle da pasta:
    ```bash
    chown -R ubiratanlima:ubiratanlima /var/www
    ```

---

## 4. O Problema do Git "Dubious Ownership"
**Sintoma**: `fatal: detected dubious ownership in repository`.
**Solução**: O Git está desconfiado da pasta. Autorize-a:
```bash
git config --global --add safe.directory /var/www
```

---

## 5. O Problema do Site não Atualizar (Reset Total)
**Sintoma**: O `git pull` dá erro de conflito ou arquivos locais.
**Solução**: Forçar o servidor a ignorar tudo e copiar o GitHub exatamente:
```bash
# Entre como ROOT no console
git reset --hard origin/main
git clean -fd
git pull origin main
```

---

## 6. Finalização Pós-Atualização
Sempre que atualizar o código, rode estes comandos para o Laravel "acordar":
```bash
php artisan migrate --force   # Aplica tabelas novas (como Auditoria)
php artisan optimize          # Limpa caches e atualiza classes
```

---
*Este documento foi forjado em combate. Siga os passos acima e o sistema sempre voltará ao ar.*
