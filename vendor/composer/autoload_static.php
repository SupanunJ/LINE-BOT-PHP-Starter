<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8c6b99653623c7152e0fa72f84ec6abb
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LINE\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LINE\\' => 
        array (
            0 => __DIR__ . '/..' . '/linecorp/line-bot-sdk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8c6b99653623c7152e0fa72f84ec6abb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8c6b99653623c7152e0fa72f84ec6abb::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}