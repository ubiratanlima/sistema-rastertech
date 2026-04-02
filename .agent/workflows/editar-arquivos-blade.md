# REGRAS OBRIGATORIAS ANTES DE QUALQUER EDICAO

## REGRA 1: Sempre leia o arquivo antes de editar
- Use grep_search para localizar a linha
- Use view_file para confirmar o conteudo exato
- Copie o TargetContent IDENTICO ao arquivo

## REGRA 2: Formularios dentro de JavaScript (SweetAlert)
Em index.blade.php os formularios ficam dentro de Swal.fire({ html: ... })
Atributos HTML dentro de JS usam aspas simples para nao conflitar com JS.

## REGRA 3: Comandos sempre via Docker
docker exec -it rastertech-app php artisan migrate
docker exec -it rastertech-app php artisan tinker --execute='...'

## REGRA 4: Ferramentas que funcionam
Escrita: write_to_file, replace_file_content, multi_replace_file_content
Nao funciona: run_command com Cwd apontando para wsl.localhost

## LICAO APRENDIDA 02/04/2026
Fluxo correto:
1. grep_search para achar a linha
2. view_file para copiar conteudo exato
3. Editar com TargetContent identico ao arquivo
