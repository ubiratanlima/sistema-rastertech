---
description: Padronização de listagens e tabelas para o Padrão Ouro RTECH
---

Sempre que o comando `\padrao-ouro` for invocado, aplique as seguintes regras de design e estrutura (Base: Listagem de Chips):

### 1. Cabeçalho da Tabela (`thead`)
- **Tipografia**: `font-size: 1rem`, `text-uppercase`, `font-weight-bold`.
- **Estilo**: `letter-spacing: 0.5px`, sem fundo colorido (limpo), com `border-bottom: 2px solid #eee`.
- **Interatividade**: Adicionar ícones de sort sutis (`fas fa-sort ml-1 opacity-50`) em todas as colunas de dados.
- **Cor**: `text-dark`.

### 2. Linhas e Colunas (`tbody`)
- **Animação**: Adicionar classe `animate__animated animate__fadeIn` em cada `<tr>`.
- **Espaçamento**: Padding vertical `py-3` em todos os `<td>` para um visual arejado.
- **ID (CÓDIGO)**: 
    - Primeira coluna, largura fixa de `~80px`.
    - Estilo: `text-center align-middle text-muted`, font-size `0.85rem`.
    - Formatação: Preencher com zeros à esquerda (`str_pad($id, 4, '0', STR_PAD_LEFT)`).
- **Nome Principal**:
    - Cor: `text-primary`.
    - Tipografia: `font-size: 1.05rem`, **SEM negrito**.
- **Destaque Tático (Cargo/Categoria)**:
    - Cor: `text-pink`.
    - Estilo: `font-weight-bold text-uppercase`, font-size `0.8rem`.

### 3. Status e Badges
- **Estilo**: Usar badges com `shadow-sm`, `px-2` ou `px-3`.
- **Semântica**: `bg-success` para Ativo/OK, `bg-danger` para Inativo/Erro, `bg-warning` para Pendente/Estoque.

### 4. Ações
- **Grupo**: `btn-group shadow-sm` com `border-radius: 8px; overflow: hidden;`.
- **Botões**: `btn-light btn-square border-right`.
- **Ícones**:
    - **Visualizar**: `fas fa-eye fa-lg text-info`.
    - **Editar**: `fas fa-tools fa-lg text-warning`.
    - **Inativar**: `fas fa-power-off fa-lg text-danger`.
    - **Reativar**: `fas fa-undo fa-lg text-success`.

### 5. Cabeçalho de Página (Page Header)
- **Grid**: `row col-12` (Ações agora ficam dentro do card-header, não no h1).
- **Título**: `h1` com ícone temático e legenda `text-muted`.
