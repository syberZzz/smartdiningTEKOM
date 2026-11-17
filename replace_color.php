<?php
$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
$search = ['#D32F2F', '#D32F2F'];
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
