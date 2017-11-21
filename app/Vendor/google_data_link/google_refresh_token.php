<?php
require_once 'sample/src/Google_Client.php';
require_once 'sample/src/contrib/Google_Oauth2Service.php';

function get_refresh_token($TOKEN) {


    $keyData = ClassRegistry::init('CommonSetting')->getSettingsForSync($TOKEN['GoogleToken']['is_new']);
    $google_client_id = $keyData['google_client_id'];
    $google_client_secret = $keyData['google_client_secret'];
    $google_redirect_uri = $keyData['google_redirect_uri'];


    $tokenArr = array();

    $client = new Google_Client();
    $client->setApplicationName("Schedule It!");

    $client->setClientId($google_client_id);
    $client->setClientSecret($google_client_secret);
    // $client->setRedirectUri(REDIRECTURI);

    $oauth2 = new Google_Oauth2Service($client);

    $tokenInfo['token'] = $TOKEN['GoogleToken']['response_form'];

    if (isset($tokenInfo['token'])) {
        $client->setAccessToken($tokenInfo['token']);
    }

    if ($client->getAccessToken()) {

        $jsonreq = json_decode($tokenInfo['token'], true);

        try {
            $resultrefresh = $client->refreshToken($jsonreq['refresh_token']);
            $requesttoken = $client->getAccessToken();
        } catch (Google_AuthException $e) {

            return false;
        }
        //$tokenInfo['token'] = $client->getAccessToken();
    } else {
        return true;
    }

    return $requesttoken;
}

?>