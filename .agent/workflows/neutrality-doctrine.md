---
description: Doutrina da Neutralidade de Infraestrutura (Porta :8000)
---
# 🛡️ Fluxo de Trabalho: Neutralidade de Infraestrutura

Este workflow é uma DIRETIVA MANDATÓRIA para evitar que a navegação do sistema quebre em ambientes com portas personalizadas (ex: `:8000` no WSL).

## 🚀 Passos de Verificação OBRIGATÓRIOS:

1. **PROIBIÇÃO DO AUXILIAR ROUTE()**: 
   - Em arquivos `.blade.php`, nunca utilize `{{ route('...') }}` ou `{{ url('...') }}` para links internos ou formulários.
   - **Ação**: Utilize caminhos relativos diretos. Exemplo: `action="/sim-cards"` ou `href="/dispositivos"`.

// turbo
2. **FILTROS E TOGGLES RELATIVOS**:
   - Para links que apenas alteram parâmetros (como Ordenação ou Botão de Ativos/Inativos), use caminhos relativos ao query-string.
   - **Ação**: `href="?status=inactive"`.

// turbo
3. **PAGINAÇÃO COM PATH LOCALIZADO**:
   - Sempre que utilizar o método `paginate()` no Controller, anexe imediatamente o método `withPath`.
   - **Ação**: `->paginate(15)->withPath('/caminho-da-tela-relativo')`.

4. **CHECK DE PRÉ-SUBMISSÃO**:
   - Antes de declarar uma tarefa como concluída, inspecione todos os `<a>`, `<form>` e controladores alterados.
   - **Critério de Sucesso**: Nenhum link deve conter o domínio (host) gerado pelo servidor, apenas o caminho (path).

// turbo
5. **CUMPRIMENTO DA DOUTRINA**:
   - Verifique sempre o arquivo `docs/LOG_DE_ERROS_E_SOLUCOES.md` para novos padrões de estabilização.
