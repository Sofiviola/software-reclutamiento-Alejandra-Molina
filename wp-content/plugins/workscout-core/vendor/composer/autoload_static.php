<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit566b3f770e7ffed19cebe31cc350668a
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
            'Sample\\' => 7,
        ),
        'P' => 
        array (
            'PaypalPayoutsSDK\\' => 17,
            'PayPalHttp\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
        'Sample\\' => 
        array (
            0 => __DIR__ . '/..' . '/paypal/paypal-payouts-sdk/samples',
        ),
        'PaypalPayoutsSDK\\' => 
        array (
            0 => __DIR__ . '/..' . '/paypal/paypal-payouts-sdk/lib/PaypalPayoutsSDK',
        ),
        'PayPalHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/paypal/paypalhttp/lib/PayPalHttp',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit566b3f770e7ffed19cebe31cc350668a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit566b3f770e7ffed19cebe31cc350668a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit566b3f770e7ffed19cebe31cc350668a::$classMap;

        }, null, ClassLoader::class);
    }
}