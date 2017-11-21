<?php

/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */

App::uses('ClassRegistry', 'Utility');
$module = ClassRegistry::init('Module');
$requri = $_SERVER['REQUEST_URI'];
$routes = $module->getUrlRoutes(_convSearchableUrl($_SERVER['HTTP_HOST']));
$routes = $routes['Module'];

if ($routes) {
    $aUrl = explode('.', $_SERVER['HTTP_HOST']);

//    if($aUrl[0] === 'api' && $_SERVER['REQUEST_SCHEME']==='https') {
//        $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
//        header('Location:' . 'http://' . env('HTTP_HOST').$requri);
//        exit;
//    }

    if($routes['is_www'] && $aUrl[0] !== 'www') {
        header('Location:' . 'http://www.' . env('HTTP_HOST').$requri);
        exit;
    }
    if(!$routes['is_www'] && $aUrl[0] === 'www') {
        header('Location:' . 'http://' . _deleteWWW(env('HTTP_HOST')).$requri);
        exit;
    }

    if($routes['is_ssl'] && $_SERVER['REQUEST_SCHEME']==='http') {
        header('Location:' . 'https://' . env('HTTP_HOST').$requri);
        exit;
    }
    if(!$routes['is_ssl'] && $_SERVER['REQUEST_SCHEME']==='https') {
        header('Location:' . 'http://' . env('HTTP_HOST').$requri);
        exit;
    }
}

if ($_SERVER['HTTP_HOST'] && ($requri == '/' || $requri == "" || $requri == "/admin" || $requri == "/hq")) {

    $_SERVER['HTTP_HOST'] = _deleteWWW($_SERVER['HTTP_HOST']);
    $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];

    Router::connect($_SERVER['REQUEST_URI'], array('controller' => $routes['displayController'], 'action' => $routes['displayAction']));


    Router::connect($_SERVER['REQUEST_URI'], array('controller' => $routes['displayController'], 'action' => $routes['displayAction']));


    Router::connect($_SERVER['REQUEST_URI'], array('controller' => $routes['displayController'], 'action' => $routes['displayAction']));

//    switch($routes['type']) { // TODO
//        case "3" : return ""; //store admin
//        case "4" : return ""; //Store web site
//        case "5" : return "";
//        default : return "";
//    }
}

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**sudo pfctl -vnf /etc/pf.conf
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';



function _convSearchableUrl($domain){
    $_domain = _deleteWWW($domain);
    return $_domain . '/' . _convSiteUri($_SERVER['REQUEST_URI']);
}

function _convSiteUri($request_url){
    $_aTemp = explode('/', $request_url);
    if(count($_aTemp)>1) {
        $request_url = $_aTemp[1];
    }

    switch($request_url) {
        case "users" : return "";
        case "menus" : return "";
        case "pannels" : return "";
        case "stores" : return "admin";
        case "hq" : return "hq/";
        default : return $request_url;
    }
}

function _deleteWWW($domain) {
    $urlParts = parse_url($domain);
    return preg_replace('/^www\./', '', $urlParts['path']);
}
