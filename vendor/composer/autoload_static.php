<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb5508fbf7150a79e290b9721e0a32a48
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'B' => 
        array (
            'Bigcommerce\\Test\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Bigcommerce\\Test\\' => 
        array (
            0 => __DIR__ . '/..' . '/bigcommerce/api/test',
        ),
    );

    public static $prefixesPsr0 = array (
        'B' => 
        array (
            'Bigcommerce' => 
            array (
                0 => __DIR__ . '/..' . '/bigcommerce/api/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb5508fbf7150a79e290b9721e0a32a48::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb5508fbf7150a79e290b9721e0a32a48::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitb5508fbf7150a79e290b9721e0a32a48::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
