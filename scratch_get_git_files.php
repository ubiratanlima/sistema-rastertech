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
    'app/Providers/AppServiceProvider.php' // I need to be careful with this, user said "sem mecher nos codigos" but this has the forceScheme. I will restore only infra files.
];

$outputDir = __DIR__ . '/scratch_git_extract';
if (!is_dir($outputDir)) mkdir($outputDir);

foreach ($files as $f) {
    $content = shell_exec("git show $commit:$f 2>&1");
    if (strpos($content, 'fatal:') === false) {
        file_put_contents("$outputDir/" . str_replace('/', '_', $f), $content);
    }
}
echo "Done.";
