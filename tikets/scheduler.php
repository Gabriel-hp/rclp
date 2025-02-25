<?php

while (true) {
    echo "[" . date('Y-m-d H:i:s') . "] Executando schedule:run...\n";
    shell_exec('php artisan schedule:run');
    sleep(120); // Aguarda 120 segundos antes de rodar novamente
}
