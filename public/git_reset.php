<?php
chdir('/var/www');
$output = shell_exec('git reset --hard 1804bd1c9d9d01f44f42bedcf80aa64ff59c73a9 2>&1');
echo "<pre>$output</pre>";
