<?php
/**
 * @author Colao Stefano < lordcoste@gmail.com >
 */

const BUNDLE_NAME = 'analytics-s2s';

Autoloader::map(array(
    'Analytics'               => Bundle::path(BUNDLE_NAME).'Analytics.php',
    'AnalyticsService'        => Bundle::path(BUNDLE_NAME).'AnalyticsService.php',
    'Google_Client'           => Bundle::path(BUNDLE_NAME).'google-api'.DS.'Google_Client.php',
    'Google_AnalyticsService' => Bundle::path(BUNDLE_NAME).'google-api'.DS.'contrib'.DS.'Google_AnalyticsService.php',
));

Laravel\IoC::singleton('google-analytics', function()
{
    $prefix = Bundle::prefix(BUNDLE_NAME);

    if(!File::exists(Config::get($prefix.'google.certificate_path')))
    {
        throw new Exception("Can't find the .p12 certificate in: " . Config::get($prefix.'google.certificate_path'));
    }

    $config = array(
        'oauth2_client_id' => Config::get($prefix.'google.client_id'),
        'use_objects' => Config::get($prefix.'google.use_objects'),
    );

    $google = new Google_Client($config);

    $google->setAccessType('offline');

    $google->setAssertionCredentials(
        new Google_AssertionCredentials(
            Config::get($prefix.'google.service_email'),
            array('https://www.googleapis.com/auth/analytics.readonly'),
            file_get_contents(Config::get($prefix.'google.certificate_path'))
        )
    );

    return new AnalyticsService($google);
});

Analytics::init(IoC::resolve('google-analytics'));