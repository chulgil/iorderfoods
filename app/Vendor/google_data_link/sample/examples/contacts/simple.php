<?php
/*
 * Copyright 2012 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../../src/Google_Client.php';
session_start();

$client = new Google_Client();
$client->setApplicationName('Google Contacts PHP Sample');
$client->setScopes("http://www.google.com/m8/feeds/");

// Documentation: http://code.google.com/apis/gdata/docs/2.0/basics.html
// Visit https://code.google.com/apis/console?api=contacts to generate your
// oauth2_client_id, oauth2_client_secret, and register your oauth2_redirect_uri.

$client->setClientId('531104926444-2299mie5h111l4mg9fu6923r8ekg1v39.apps.googleusercontent.com');
 $client->setClientSecret('Tp2z_t2ee0VhG_HCls0vADiK');
 $client->setRedirectUri('http://www.vimbli.com/beta/sample/examples/contacts/simple.php');

// $client->setDeveloperKey('insert_your_developer_key');

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
 $client->setAccessToken($_SESSION['token']);
}

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['token']);
  $client->revokeToken();
}

if ($client->getAccessToken()) {
  $req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full");
  
      

$val = $client->getIo()->authenticatedRequest($req);
$response = json_encode(simplexml_load_string($val->getResponseBody()));

  print "<pre>" . print_r(json_decode($response, true), true) . "</pre>";

  // The access token may have been updated lazily.
  $_SESSION['token'] = $client->getAccessToken();
} else {
  $auth = $client->createAuthUrl();
}

if (isset($auth)) {
    print "<a class=login href='$auth'>Connect Me!</a>";
  } else {
    print "<a class=logout href='?logout'>Logout</a>";
}


     
?>    