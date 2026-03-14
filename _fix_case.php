<?php
// Script temporal para corregir case-sensitivity sin romper UTF-8
$files = [
    'controllers/cinventario.php',
    'views/admin/Vainventario.php',
    'views/admin/Vainventario_new.php',
    'assets/js/inventario.js',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (!file_exists($path)) {
        echo "NOT FOUND: $file\n";
        continue;
    }
    $original = file_get_contents($path);
    $modified = str_replace('ctrl=Cinventario', 'ctrl=cinventario', $original);
    if ($original !== $modified) {
        file_put_contents($path, $modified);
        $count = substr_count($modified, 'ctrl=cinventario') - substr_count($original, 'ctrl=cinventario');
        echo "FIXED ($count replacements): $file\n";
    } else {
        echo "NO CHANGE: $file\n";
    }
}
echo "Done.\n";
