<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit45a226313bd899b0cf9b60d134f85e4c
{
    public static $prefixLengthsPsr4 = array (
        'w' => 
        array (
            'webpage\\' => 8,
        ),
        's' => 
        array (
            'server\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'webpage\\' => 
        array (
            0 => __DIR__ . '/../..' . '/demo/live_stream/webpage',
        ),
        'server\\' => 
        array (
            0 => __DIR__ . '/../..' . '/demo/live_stream/server',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit45a226313bd899b0cf9b60d134f85e4c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit45a226313bd899b0cf9b60d134f85e4c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit45a226313bd899b0cf9b60d134f85e4c::$classMap;

        }, null, ClassLoader::class);
    }
}