<?php
$commit = '1804bd1c9d9d01f44f42bedcf80aa64ff59c73a9';
$files = [
    'docker-compose.yml',
    'docker-compose.local.yml',
    'Dockerfile',
    'Dockerfile.dev',
    'docker/volumes/web/nginx.conf',
    'docker/volumes/web/nginx.local.conf',
    'kong.local.yml',
    'app/Providers/AppServiceProvider.php'
];

$output = [];
foreach ($files as $f) {
    // shell_exec executa na raiz do projeto porque o script PHP no web server
    // normalmente tem o diretório atual do script (public), 
    // mas vamos garantir o chdir
    chdir('/var/www');
    $content = shell_exec("git show $commit:$f 2>&1");
    if (strpos($content, 'fatal:') === false) {
        file_put_contents("/var/www/scratch_" . str_replace('/', '_', $f), $content);
        $output[] = "Salvo: $f";
    } else {
        $output[] = "Erro ao buscar: $f - $content";
    }
}
echo implode("<br>", $output);
