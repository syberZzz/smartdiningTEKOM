<?php
$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
$search = ['Lumière Dining', 'Lumière', 'Lumire', 'Lumire Dining'];
$replace = 'Zazil Bakery';
foreach ($dir as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $newContent = str_replace($search, $replace, $content);
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated: {$file->getPathname()}\n";
        }
    }
}
