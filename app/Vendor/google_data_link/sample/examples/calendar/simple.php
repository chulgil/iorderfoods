<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once '../../src/Google_Client.php';
require_once '../../src/contrib/Google_CalendarService.php';
session_start();

$client = new Google_Client();
$client->setApplicationName("Google Calendar PHP Starter Application");

// Visit https://code.google.com/apis/console?api=calendar to generate your
// client id, client secret, and to register your redirect uri.
 $client->setClientId('531104926444-6vocsh1lbrvoac9r9erqlqu5a3ovb11k@developer.gserviceaccount.com');
 $client->setClientSecret('a726q5lNtq3Z1wpTZ2xBYecw');
 $client->setRedirectUri('http://www.vimbli.com/beta/sample/examples/calendar/simple.php');
// $client->setDeveloperKey('insert_your_developer_key');
$cal = new Google_CalendarService($client);
if (isset($_GET['logout'])) {
  unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  
  //echo '<pre>'; print_r(json_decode($_SESSION['token'], true));
  //echo "<br>=======================================<br>"; die;
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $calList = $cal->calendarList->listCalendarList();
  //print "<h1>Calendar List</h1><pre>" . print_r($calList, true) . "</pre>";
  //echo '<br>****************************************<br>'; 
	$events = $cal->events->listEvents($calList['items'][2]['id']); //Passing email dynamically
	print "<h1>Calendar Events</h1><pre>" . print_r($events, true) . "</pre>";


$_SESSION['token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
}