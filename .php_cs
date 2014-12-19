<?php
printf("Executing php_cs!!!\n\n");
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->ignoreDotFiles(true)
    ->filter(function (SplFileInfo $file) {
        $path = $file->getPathname();

        switch (true) {
            case (strrpos($path, '/vendor/')):
                return false;
            default:
                return true;
        }
    });

return Symfony\CS\Config\Config::create()
    ->finder($finder);
