<?php
require './extras/oauth2/vendor/autoload.php';
session_start();

$redirectUri = isset($_SERVER['HTTPS'])?'https://':'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

//All Details Obtained by setting up APP in Google Developer Console.
//Set Redirect URI in Developer Console as [https/http]://<yourdomain>/<folder>/get_oauth_token.php
// eg: http://localhost/phpmail/get_oauth_token.php
$provider = new League\OAuth2\Client\Provider\Google ([
    'clientId' => 'RANDOMCHARS----p05gduv1n2.apps.googleusercontent.com',
    'clientSecret' => 'RANDOMCHARS----CWufYlGyjPcRtvP',
    'redirectUri' => $redirectUri,
    'scopes' => ['https://mail.google.com/'],
    'accessType' => 'offline'
 ]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->state;
    header('Location: ' . $authUrl);
    exit;
// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');
} else {

    $provider->accessType = 'offline';
    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);


// Use this to interact with an API on the users behalf
//    echo $token->accessToken.'<br>';

    // Use this to get a new access token if the old one expires
    echo 'Refresh Token: '.$token->refreshToken;

// Unix timestamp of when the token will expire, and need refreshing
//    echo $token->expires;
}
?>