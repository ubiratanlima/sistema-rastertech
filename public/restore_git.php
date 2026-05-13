<?php
$files = [
    'docker-compose.yml' => 'scratch_docker-compose.yml',
    'docker-compose.local.yml' => 'scratch_docker-compose.local.yml',
    'Dockerfile' => 'scratch_Dockerfile',
    'docker/volumes/web/nginx.conf' => 'scratch_docker_volumes_web_nginx.conf',
    'kong.local.yml' => 'scratch_kong.local.yml'
];

$output = [];
chdir('/var/www');
foreach ($files as $dest => $src) {
    if (file_exists($src)) {
        $content = file_get_contents($src);
        file_put_contents($dest, $content);
        $output[] = "Restaurado: $dest";
    } else {
        $output[] = "Arquivo fonte não encontrado: $src";
    }
}
echo implode("<br>", $output);
