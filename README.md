Google Analytics API as Service Account (Laravel Bundle)
======================================
This bundle is based on the svn trunk of Google API PHP Client hosted at
<http://google-api-php-client.googlecode.com/svn/trunk/> (Revision 508)

    The Google API Client Library enables you to work with Google APIs.

    The revision available on the downloads page does not support service accounts.
    This is way this bundle is based on the svn trunk.

Overview
------------
The Google OAuth 2.0 Authorization Server supports *server-to-server* interactions, like those between a web application and Google Analytics.
The requesting application has to prove its own identity to gain access to an API, and an end-user doesn't have to be involved.

Basically this bundle let you access to *one* configured Analytics account *without* asking the authorization of the end-user.
Once the configuration is done you don't have to worry about refresh token etc.

Installation
------------
### Artisan

    php artisan bundle:install analytics-s2s

### Bundle Registration
Add the following to your application/bundles.php file:

```php
'analytics-s2s' => array('auto' => true),
```

### Configuration
Edit the following in your bundles/google-analytics-service/config/google.php file:

```php
return array(
  'client_id'        => 'xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com',
  'service_email'    => 'xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx@developer.gserviceaccount.com',

  // make sure is in a private folder
  'certificate_path' => path('base') . 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-privatekey.p12',
);
```

How to obtain Client id, Service Account Name and the p12 certificate
-------------
(1) Visit https://code.google.com/apis/console and login with your Analytics Amministrative Account.

(2) In the *Services* tab turn on Analytics API.

(3) In the API Access tab, click Create an OAuth2.0 Client ID
  - enter your name, upload a logo, and click Next
  - select the *Service account* option and press Create client ID
  - download your certificate key

(4) Now you're back on the API Access page. You'll see a section called Service account with a *Client ID* and *Email address*
  - copy the Client ID and paste it in bundles/google-analytics-service/config/google.php as 'client_id':

```php
'client_id' => 'xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.apps.googleusercontent.com',
```

  - copy the Email address and paste it in bundles/google-analytics-service/config/google.php as 'service_email':

```php
'service_email' => 'xxxxxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx@developer.gserviceaccount.com',
```

  - upload the .p12 certificate in a private folder of your server and insert the path in bundles/google-analytics-service/config/google.php:

```php
'certificate_path' => path('base') . 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-privatekey.p12',
```

(5) Visit https://www.google.com/analytics/web/#management/Accounts/
  - Add the 'service_email' as a user to the properties of each account that you would like authorize access

    This step is mandatory, otherwise you'll not get access.


Usage
-------------

### Static wrapper class
The bundle provide you the Analytics static class with few helper methods.

```php
/**
 * @param $url String
 * @return array(array('url1' => 'ga:1'), array('url2' => 'ga:2'))
 */
Analytics::getAllSitesIds()
```

```php
/**
 * @return String ga:xxxxxxx
 */
Analytics::getSiteIdByUrl($url)
```

```php
Analytics::query($id, $start_date, $end_date, $metrics, $others = array()) // $analytics->data_ga->get($id, $start_date, $end_date, $metrics, $others)
```

```php
Analytics::segments() // $analytics->management_segments
```

```php
Analytics::accounts() // $analytics->management_accounts
```

```php
Analytics::goals() // $analytics->management_goals
```

```php
Analytics::profiles() // $analytics->management_profiles
```

```php
Analytics::webproperties() // $analytics->management_webproperties
```

You can chain these with the methods provided by Google Analytics API v3.

### Old way usage
If you prefer to use the classic old usage just ignore the provided static class and use the bundle like this:

```php
$analytics = IoC::resolve('google-analytics');
```

Then you can use all the methods of the Google Analytics API v3 plus two additionals methods:

```php
$analytics->getAllSitesIds()
$analytics->getSiteIdByUrl($url)
```

Usage Example
-------------

List all Webproperties:

```php
Route::get('/list', function()
{
  return print_r(Analytics::webproperties()->listManagementWebproperties("~all"));
});
```

Get last week visits for each site:

```php
Route::get('/last_week', function()
{
  $results = array();

  foreach(Analytics::getAllSitesIds() as $url => $id)
  {
    $results[] = array(
      'site'   => $url,
      Analytics::query(
          $id,
          date('Y-m-d', strtotime('-1 week')),
          date('Y-m-d'),
          'ga:visits'
        )->totalsForAllResults['ga:visits'],
    );
  }

  return print_r($results);
});

/* This will return:

Array
(
    [0] => Array
        (
            [site] => http://www.first.com
            [visits] => 1250
        )

    [1] => Array
        (
            [site] => http://www.second.com
            [visits] => 1530
        )

)
*/
```
