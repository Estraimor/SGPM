<?php

header('Content-Type: text/plain; charset=utf-8');

// ¿Dónde estoy?
echo "Este script está en: " . __FILE__ . "\n";

// ¿Cuál es el root del servidor?
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";

// Lista de .php en este directorio
$files = glob(__DIR__ . '/*.php');
echo "Archivos PHP en este dir:\n";
foreach($files as $f) {
    echo " - " . basename($f) . "\n";
}
