<?php

declare(strict_types=1);

namespace App\View;

use App\Exception\ViewException;
use Smarty;

final class SmartyFactory
{
    public static function create(): Smarty
    {
        $basePath = dirname(__DIR__, 2);

        $compileDir = $basePath . '/var/templates_c';
        $cacheDir = $basePath . '/var/cache';

        self::ensureWritableDirectory($compileDir);
        self::ensureWritableDirectory($cacheDir);

        $smarty = new Smarty();
        $smarty->setTemplateDir($basePath . '/templates');
        $smarty->setCompileDir($compileDir);
        $smarty->setCacheDir($cacheDir);
        $smarty->caching = Smarty::CACHING_OFF;

        return $smarty;
    }

    private static function ensureWritableDirectory(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0775, true) && !is_dir($path)) {
            throw ViewException::directoryNotCreatable($path);
        }

        if (!is_writable($path)) {
            throw ViewException::directoryNotWritable($path);
        }
    }
}
