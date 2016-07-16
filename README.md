
# o-auth-connect
OAuthConnect is the easiest way (second the author) to connect to OAuth2 services using Zend Framework 2 (ZF2).

It's a tiny ZF2 module that you can require using composer. It does not install any tirdy part OAuth library automatically, so you are free to install only the OAuth librarys of the services you want to use and o-auth-connect will detect it.

For example. Lets say you want add Login With Facebook functionality to your site. In order to do that you just need to install o-auth-connect and then install [Facebook PHP SDK](https://developers.facebook.com/docs/reference/php/). That way you only install what you will be using.

## Installing
 1. Execute `composer require stavarengo/o-auth-connect:^0.0`
 2. Install all libraries of the OAuth Services you want to use. See [list of supported services](#supported-oauth-services).

## Using it
### First Things First: Concepts!

The simplier the better. With OAuthConnect you just have tow URLs to work with, instead of worry about all the URLs endpoint that each OAuth service expose to you interact with when asking for someone's authorization.
 1. `sta/oAuthConnect/ask/:oAuthService`: This route is to where you must send your users when asking for their authorizations. This route renders a URL like this: `/sta/o-auth-connect/facebook`.
 2. `sta/oAuthConnect/response`: This is where the OAuth Service will redirect the users autorization's response. Most of the OAuth Services ask for an *redirect callback URL*. This is an URL in your site that is prepared to receive the user's authorization response (if the user allow or deny your request). OAuthConnect take care of the response for your, so whenever you were setting up a OAuth Service account and it asks you something like "Whats your redirect URL?", you must set `<your_host>/sta/o-auth-connect/response`. Later int this file you will see how to listen to that response.
### Learn by Example
Lest see how it works with an example. In this example we are going to add "Login With Facebook" and "Login with Google" functionality to our site.

#### Learn by Example: Showing links to login
First you need to add the links that says "Login with Facebook" and "Login with Google". These links will point to the route `sta/oAuthConnect/ask/:oAuthService`. So, put the code bellow inside a Zend View (the `phtml` file) that will print out the links (Note: This code will fail! If you're in a hurry [skip to here](#executing-and-testing), otherwise, continue to read to see why it will fail).
```php
<?php
$routeToRedirectAfterResponse = [
    'route' => 'homePage',
];
$facebookHref                 = $this->url(
    'sta/oAuthConnect/ask',
    [
        'oAuthService' => 'facebook',
    ],
    [
        'query' => [
            'redirectAfterResponse' => $routeToRedirectAfterResponse,
        ],
    ]
);
$googleHref                   = $this->url(
    'sta/oAuthConnect/ask',
    [
        'oAuthService' => 'google',
    ],
    [
        'query' => [
            'scopes' => [
                \Google_Service_YouTube::YOUTUBE,
            ],
            'redirectAfterResponse' => $routeToRedirectAfterResponse,
        ],
    ]
);
?>
<a href="<?php echo $facebookHref ?>">Login with Facebook</a>
- <a href="<?php echo $googleHref ?>">Login with Goolge</a>
```
This will print out a link that send the user to the page where the OAuth Service (in this case, Facebook) will ask to the user if he agree to share his Facebook information with us. Note the parameter `oAuthService`. You must use this parameter to set the name of the service you want to use.

Lest understand a little bit more about what is going on in this `phtml` file.

 1. We are creating two URLs: One that allow your users to login with Facebook and another with Google.
 2. Both URLs point to the same route, each is `sta/oAuthConnect/ask`. It's important to note that you will always use this route to generate a link whenever you want to allow your user share their information using OAuth. 
 3. We are passing a query parameter called `redirectAfterResponse`. Use this parameter to inform where should we go after the user awser if they allow or not. The parameter `redirectAfterResponse` is an `array` an it receives the same parameters you would pass to the method `\Zend\Mvc\Controller\Plugin\Redirect::toRoute()`. So, in others words `redirectAfterResponse` is an array that can receives tree entries: `route` - route name (required); `params` - parameters of your route (eg: when your're using a Segment route); `options` - others route options (eg: query parameters).
 4. Google requires that you always specify what are the scopes you need (Facebook doesn't), because of this we passed a query parameter called `scopes` when creating the URL to login with Google. The parameter `scopes` is an `array` of `string` with with the scopes you need to ask.

> Note: Passing route informations instead of an URL to the `redirectAfterResponse` parameter makes your website safier, since it will avoid others maliciousus scripts to ack as if they were you asking for your users to share their information.

> Note: If your don't want to expose yours routes throgth an query param, [read it to learn how](#hidding-routes).

#### <a name="executing-and-testing"></a>Learn by Example: Executing and testing
If you execute this code now and click in one of the links you will get an error `Zend\ServiceManager\Exception\ServiceNotCreatedException` with some previous exceptions attached to it. Looking down throw the previous exceptions attached you will find the exception `Sta\OAuthConnect\Exception\MissingConfiguration` saying that the configuration `$config['sta']['o-auth-connect']['o-auth-services']['facebook']['appId']` wasn't found.

It happens because we need to configure our credentials of all the services we need to use. Each service has diferent ways to declare its credentials, but you does not need to worry about it: OAuthConnect will ways say what is missing and where exactly you should set it. So lets add our credentials to use both Google and Facebook OAuth Services.

> Note: You can see what configuration option each service accept in their source files. So checkout [the files here](https://github.com/stavarengo/o-auth-connect/tree/master/src/OAuthConnect/OAuthService/Service) if you want to learn more.

Add this to your `config/autoload/global.php` config file.
```php
'sta' => [
    'o-auth-connect' => [
        'o-auth-services' => [
            'facebook' => [
                'appId' => 'YOUR_APP_ID',
                'appSecret' => 'YOUR_APP_SECRET',
            ],
            'google' => [
                'clientId' => '...',
                'clientSecret' => '...',
            ],
        ],
    ],
],
```
#### Learn by Example: Done
You are now good to go. Access your site and click in one of the links that your `phtml` file printed out. You will be redirect to the OAuth Service website, where it will be asking for your authorization. Go ahead: Authorize it and you will be redirect back to your site. Note that you were redirected back to where you setted up in the parameter `redirectAfterResponse`. If you didn't set the parameter `redirectAfterResponse`, then you will see a message saying: "You can now close this window" (More information about this message [here](#asynch-authorization)).
 
To avoid the message "You can now close this window", you must tell to OAuthConnect where to go after a response came. So, change you 

### Listen to authorization responses



## <a name="supported-oauth-services"></a>Supported OAuth Services
See [list of supported library here](https://github.com/stavarengo/o-auth-connect/tree/master/src/OAuthConnect/OAuthService/Service). 
### <a name="adding-custom-services"></a>Adding support to others OAuth Libraries
If you need to use another OAuth service that is not supported by o-auth-connect yet, all you need to to is:

 1. Creating a class that implements the interface [\Sta\OAuthConnect\OAuthService\OAuthServiceInterface](https://github.com/stavarengo/o-auth-connect/blob/master/src/OAuthConnect/OAuthService/OAuthServiceInterface.php).
 2. Add this class to the OAuthConnect using the config file, as follow:   
```PHP
'sta' => [
    'o-auth-connect' => [
        'custom-services' => [
	        'Your\New\Facy\OAuthServiceClass'
        ],
    ],
];
```
The string `Your\New\Facy\OAuthServiceClass` could be ether a class name or a service factory name, that would be create troght ZF `[ServiceManager](zendframework.github.io/zend-servicemanager)`. OAuthConnect will automatically detect whether or not you meant to use ServiceManager.

## <a name="hidding-routes"></a>Hidding Routes from Query Params
***ToDo: Section still in under construction***

## <a name="asynch-authorization"></a>Asynchronous authorization: Asking without leaving your site
***ToDo: Section still in under construction***

 > Note: All strings in OAuthConnect are translated using Zend Translator. Read [here](#i18n) to learn more.

## <a name="i18n"></a>Translating messages
***ToDo: Section still in under construction***

OAuthConnect prints a few (really few) strings to your user. By default all strings are written in English. OAuthConnect also has theses strings translated to a few others languages. You can check if yours is already translated [here](https://github.com/stavarengo/o-auth-connect/tree/master/language). Even if it is not translated to your language yet, you can translate it your self by following theses bellow.

> Note: Help our project to grow by sending your translations via pull request.

