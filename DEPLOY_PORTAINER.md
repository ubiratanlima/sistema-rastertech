# 🛰️ Manual de Guerra: Deploy Portainer (Produção)

Este manual contém as soluções reais para os problemas de permissão, autenticação, segurança e sincronização enfrentados no ambiente Rastertech.

---

## 1. Sincronização de Horário (Timezone Brasil)
**Sintoma**: Horários na Auditoria ou Missões aparecem 3 horas à frente (UTC).
**Solução**: O Laravel e o Banco de Dados devem estar em sintonia com o horário de Brasília.
1.  No arquivo `config/app.php`, o timezone deve ser: `'timezone' => 'America/Sao_Paulo'`.
2.  No arquivo `config/database.php`, dentro da conexão `mysql`, adicione: `'timezone' => '-03:00'`.
3.  No Modelo `AuditLog.php`, certifique-se de que `$timestamps = true` para que o Laravel gerencie o tempo.
4.  Rode `php artisan optimize` para aplicar.

---

## 2. Limpeza de Dados da Auditoria
**Sintoma**: Tabela de logs crescendo muito ou necessidade de apagar registros antigos.
**Solução**:
- **Limpeza Automática (Configurada)**: O sistema apaga automaticamente registros com mais de **180 dias**.
- **Limpeza Manual Total**: Para apagar tudo e começar do zero, rode:
    ```bash
    php artisan tinker --execute="App\Models\AuditLog::truncate()"
    ```
- **Limpeza Manual de "Ruído"**: Para apagar apenas os logs de um período específico:
    ```bash
    php artisan tinker --execute="App\Models\AuditLog::where('created_at', '<', now()->subDays(7))->delete()"
    ```

---

## 3. Aviso de "Conexão Não Segura" (HTTPS)
**Sintoma**: Navegador avisa que "as informações serão enviadas por uma conexão não segura".
**Solução**:
1.  `AppServiceProvider.php` deve ter o comando `URL::forceScheme('https');` no topo do método `boot()`.
2.  Rode `php artisan optimize` para atualizar o cache de links.

---

## 4. O Problema da Senha do GitHub (Token)
**Sintoma**: `Authentication failed` no Portainer ao tentar atualizar.
**Solução**: Use um **Personal Access Token (PAT)** do GitHub no lugar da senha.

---

## 5. O Problema de Permissão (Permission Denied)
**Sintoma**: `failed to remove... Permission denied`.
**Solução**:
1.  No Console do Portainer, entre como usuário **`root`**.
2.  Rode: `chown -R ubiratanlima:ubiratanlima /var/www`.
3.  Rode: `git config --global --add safe.directory /var/www`.

---
*Este documento é o seu escudo operacional. Siga os passos acima para manter o sistema Rastertech em perfeito estado.*
