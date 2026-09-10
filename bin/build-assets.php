#!/usr/bin/env php
<?php

declare(strict_types=1);

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

require dirname(__DIR__) . '/vendor/autoload.php';

$basePath = dirname(__DIR__);
$entryPoint = $basePath . '/scss/app.scss';
$outputPath = $basePath . '/public/css/app.css';

$source = file_get_contents($entryPoint);

if ($source === false) {
    fwrite(STDERR, sprintf('Unable to read "%s".', $entryPoint) . PHP_EOL);

    exit(1);
}

$compiler = new Compiler();
$compiler->setOutputStyle(OutputStyle::COMPRESSED);
$compiler->addImportPath($basePath . '/scss');

$css = $compiler->compileString($source, $entryPoint)->getCss();

file_put_contents($outputPath, $css);

fwrite(STDOUT, 'Compiled scss/app.scss -> public/css/app.css' . PHP_EOL);
