<?php
$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
$search = ['#B71C1C', '#B71C1C'];
$replace = '#00aaffff';
foreach ($dir as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ['php', 'js', 'css', 'blade'])) {
        $content = file_get_contents($file->getPathname());
        $newContent = str_ireplace($search, $replace, $content);
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated: {$file->getPathname()}\n";
        }
    }
}
