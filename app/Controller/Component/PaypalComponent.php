<?php

//
//  Paypal.php
//  CakePHP 2.0 component for paypal website payments pro
//  PayPal Express and Direct Payments
//  Created by Rob Mcvey on 2011-12-03.
//  Copyright 2011 Rob Mcvey. All rights reserved.
//
App::uses('HttpSocket', 'Network/Http');

class PaypalComponent extends Component {

    var $components = array('Cookie', 'Session');
    // Live v Sandbox mode !important
    public $sandboxMode = true;
    // Live paypal API config
    public $config = array(
        'webscr' => 'https://www.paypal.com/webscr/',
        'endpoint' => 'https://api-3t.paypal.com/nvp/',
        'password' => '',
        'email' => '',
        'signature' => ''
    );
    // Sandbox paypal API config
    public $sandboxConfig = array(
        'webscr' => 'https://www.sandbox.paypal.com/webscr/',
        'endpoint' => 'https://api-3t.sandbox.paypal.com/nvp/',
        'password' => '1367320435',
        'email' => 'eddypawan-seller1_api1.gmail.com',
        'signature' => 'Af7b-k2jGmW.ajyumu9Q5FofHQ72AewpUfsstbWjRIszM-j0ih4AYbbb'
    );
    // API version
    public $apiVersion = '53.0';
    // Return URL for express payments
    public $returnUrl = '';
    // Cancel URL for Express payments cancelled
    public $cancelUrl = '';
    // Default Currency code
    public $currencyCode = 'USD';
    //The amount of the transaction For example, EUR 2.000,00 must be specified as 2000.00 or 2,000.00.
    public $amount = null;
    // Customise Express checkout with a description (api version > 53)
    public $itemName = '';
    // Customise Express checkout with a description (api version > 53)
    public $orderDesc = '';
    // optional quantity
    public $quantity = 1;
    // The token returned from payapl and used in subsequesnt reuqest
    public $token = null;
    // The payers paypal ID
    public $payerId = null;
    // Credit card details
    public $creditCardNumber = '4046849339086033';
    public $creditCardType = 'Visa';
    public $creditCardExpires = '042018';
    public $creditCardCvv = '962';
    // Customer details
    public $customerSalutation = '';
    public $customerFirstName = '';
    public $customerMiddleName = '';
    public $customerLastName = '';
    public $customerSuffix = '';
    // Billing details
    public $billingAddress1 = '';
    public $billingAddress2 = '';
    public $billingCity = '';
    public $billingState = '';
    public $billingCountryCode = '';
    public $billingZip = '';
    // Users IP address
    public $ipAddress = '';
    // controller reference
    protected $_controller = null;

    /**
     * Start up, gets an instance on the controller class (needed for redirect) sets
     * the config (live or sandbox) and sets the users IP
     *
     * @return void
     * @author Rob Mcvey
     * */
    public function initialize(Controller $controller) {
        $this->_controller = $controller;
        $this->ipAddress = $_SERVER['REMOTE_ADDR'];
        App::import('Model', 'Store');
        $this->Store = new Store();
        $store_id = $this->Session->read('store_id');
        $storeInfo = $this->Store->fetchStorePaypalDetail($store_id);
        if (empty($storeInfo['Store']['paypal_mode'])) {
            $this->sandboxMode = true;
        } else {
            $this->sandboxMode = false;
        }
        if ($this->sandboxMode) {
            $this->config = $this->sandboxConfig;
        }
    }

    /**
     * The DoDirectPayment API Operation enables you to process a credit card payment.
     *
     * @return array $parsed An array of fields with the payment info, or throws and exception
     * @author Rob Mcvey
     * */
    public function doDirectPayment($email = null, $pwd = null, $sign = null) {
        // Build the NVPs (Named value pairs)
        $doDirectPaymentNvp = array(
            'METHOD' => 'DoDirectPayment',
            'PAYMENTACTION' => 'SALE',
            'VERSION' => $this->apiVersion,
            'AMT' => $this->amount,
            'CURRENCYCODE' => $this->currencyCode,
            'IPADDRESS' => $this->ipAddress,
            'USER' => $email, //$this->config['email'],
            'PWD' => $pwd, //$this->config['password'],
            'SIGNATURE' => $sign, //$this->config['signature'],
            // Credit Card Details
            'CREDITCARDTYPE' => $this->creditCardType,
            'ACCT' => $this->creditCardNumber,
            'EXPDATE' => $this->creditCardExpires,
            'CVV2' => $this->creditCardCvv,
            // Customer Details
            'SALUTATION' => $this->customerSalutation,
            'FIRSTNAME' => $this->customerFirstName,
            'MIDDLENAME' => $this->customerMiddleName,
            'LASTNAME' => $this->customerLastName,
            'SUFFIX' => $this->customerSuffix,
            // Billing Address
            'STREET' => $this->billingAddress1,
            'STREET2' => $this->billingAddress2,
            'CITY' => $this->billingCity,
            'STATE' => $this->billingState,
            'COUNTRYCODE' => $this->billingCountryCode,
            'ZIP' => $this->billingZip,
        );

        // HTTPSocket class
        $httpSocket = new HttpSocket();

        // Post the NVPs to the relevent endpoint
        $response = $httpSocket->post($this->config['endpoint'], $doDirectPaymentNvp);
        // Handle the response
        return $this->handleResponse($response);
    }

    /**
     * Handle PayPal response. Returns parsed array or throws exception
     * @author robmcvey
     * @param string $response
     * @return array
     */
    public function handleResponse($response) {
        // Parse the guff that comes back from paypal
        parse_str($response, $parsed);

        // Return the token, or throw a human readable error
        if (array_key_exists('ACK', $parsed) && $parsed['ACK'] == 'Success') {
            return $parsed;
        }
        // Long message present
        elseif (array_key_exists('ACK', $parsed) && array_key_exists('L_LONGMESSAGE0', $parsed) && $parsed['ACK'] != 'Success') {
            throw new Exception($parsed['ACK'] . ' : ' . $parsed['L_LONGMESSAGE0']);
        }
        // Error code present
        elseif (array_key_exists('ACK', $parsed) && array_key_exists('L_ERRORCODE0', $parsed) && $parsed['ACK'] != 'Success') {
            throw new Exception($parsed['ACK'] . ' : ' . $parsed['L_ERRORCODE0']);
        }
        // Some other combination is not possible AFAIK
        else {
            throw new Exception(__('There is a problem processing your payment. Please try again later'));
        }
    }

    /**
     * Generated a fresh token and redirects the use to the paypal page
     *
     * @return void
     * @author Rob Mcvey
     * */
    public function expressCheckout() {

        // We dont have a valid amount
        if (!isset($this->amount) || empty($this->amount) || !is_numeric($this->amount)) {
            throw new Exception(__('Invalid amount - must be numeric in the format 1234.00'));
        }

        // Call the SetExpressCheckout method to get a fresh token
        $token = $this->setExpressCheckout();

        // We have a token, redirect to paypals web server (not the URL is different to the API endpoint)
        if ($token) {
            $this->_controller->redirect($this->config['webscr'] . '?cmd=_express-checkout&token=' . $token);
        } else {
            $this->log($token, 'paypal');
            throw new Exception(__('The was a problem with the payment gateway'));
        }
    }

    /**
     * To obtain details about an Express Checkout transaction, you can invoke the
     * GetExpressCheckoutDetails API operation.
     *
     * @return array $parsed An array of fields with the customers details, or throws and exception
     * @author Rob Mcvey
     * */
    public function getExpressCheckoutDetails() {

        // Build the NVPs (Named value pairs)
        $getExpressCheckoutDetailsNvp = array(
            'METHOD' => 'GetExpressCheckoutDetails',
            'TOKEN' => $this->token,
            'VERSION' => $this->apiVersion,
            'USER' => $this->config['email'],
            'PWD' => $this->config['password'],
            'SIGNATURE' => $this->config['signature'],
        );

        // HTTPSocket class
        $httpSocket = new HttpSocket();

        // Post the NVPs to the relevent endpoint
        $response = $httpSocket->post($this->config['endpoint'], $getExpressCheckoutDetailsNvp);

        // Parse the guff that comes back from paypal
        parse_str($response, $parsed);
        // Return the token, or throw a human readable error
        if (array_key_exists('TOKEN', $parsed) && array_key_exists('ACK', $parsed) && $parsed['ACK'] == 'Success') {
            return $parsed;
        } elseif (array_key_exists('ACK', $parsed) && array_key_exists('L_LONGMESSAGE0', $parsed) && $parsed['ACK'] != 'Success') {
            $this->log($parsed, 'paypal');
            throw new Exception($parsed['ACK'] . ' : ' . $parsed['L_LONGMESSAGE0']);
        } elseif (array_key_exists('ACK', $parsed) && array_key_exists('L_ERRORCODE0', $parsed) && $parsed['ACK'] != 'Success') {
            $this->log($parsed, 'paypal');
            throw new Exception($parsed['ACK'] . ' : ' . $parsed['L_ERRORCODE0']);
        } else {
            $this->log($parsed, 'paypal');
            throw new Exception(__('There is a problem with the payment gateway. Please try again later.'));
        }
    }

    /**
     * To complete an Express Checkout transaction, you must invoke the
     * DoExpressCheckoutPayment API operation.
     *
     * @return array $parsed An array of fields with the payment info, or throws and exception
     * @author Rob Mcvey
     * */
    public function doExpressCheckoutPayment() {

        // Build the NVPs (Named value pairs)
        $doExpressCheckoutPaymentNvp = array(
            'METHOD' => 'DoExpressCheckoutPayment',
            'USER' => $this->config['email'],
            'PWD' => $this->config['password'],
            'SIGNATURE' => $this->config['signature'],
            'VERSION' => $this->apiVersion,
            'TOKEN' => $this->token,
            'PAYERID' => $this->payerId,
            'PAYMENTACTION' => 'Sale',
            'CURRENCYCODE' => $this->currencyCode,
            'AMT' => $this->amount
        );

        // HTTPSocket class
        $httpSocket = new HttpSocket();

        // Post the NVPs to the relevent endpoint
        $response = $httpSocket->post($this->config['endpoint'], $doExpressCheckoutPaymentNvp);

        // Parse the guff that comes back from paypal
        parse_str($response, $parsed);

        // Return the token, or throw a human readable error
        if (array_key_exists('TOKEN', $parsed) && array_key_exists('ACK', $parsed) && $parsed['ACK'] == 'Success') {
            return $parsed;
        } elseif (array_key_exists('ACK', $parsed) && array_key_exists('L_LONGMESSAGE0', $parsed) && $parsed['ACK'] != 'Success') {
            $this->log($parsed, 'paypal');
            throw new Exception($parsed['ACK'] . ' : ' . $parsed['L_LONGMESSAGE0']);
        } elseif (array_key_exists('ACK', $parsed) && array_key_exists('L_ERRORCODE0', $parsed) && $parsed['ACK'] != 'Success') {
            $this->log($parsed, 'paypal');
            throw new Exception($parsed['ACK'] . ' : ' . $parsed['L_ERRORCODE0']);
        } else {
            $this->log($parsed, 'paypal');
            throw new Exception(__('There is a problem with the payment gateway. Please try again later.'));
        }
    }

    /**
     * To set up an Express Checkout transaction, you must invoke the SetExpressCheckout API
     * operation to provide sufficient information to initiate the payment flow and redirect
     * to PayPal if the operation was successful with the token sent back from Paypal
     *
     * @return string $token A token to be used when redirecting the user to PayPal
     * @author Rob Mcvey
     * */
    public function setExpressCheckout() {

        // Build the NVPs (Named value pairs)
        $setExpressCheckoutNvp = array(
            'METHOD' => 'SetExpressCheckout',
            'VERSION' => $this->apiVersion,
            'USER' => $this->config['email'],
            'PWD' => $this->config['password'],
            'SIGNATURE' => $this->config['signature'],
            'CURRENCYCODE' => $this->currencyCode,
            'RETURNURL' => $this->returnUrl,
            'CANCELURL' => $this->cancelUrl,
            'PAYMENTACTION' => 'ORDER',
            'PAGESTYLE' => 'Copify',
            'AMT' => $this->amount,
            'L_NAME0' => $this->itemName,
            'L_DESC0' => $this->orderDesc,
            'L_AMT0' => $this->amount,
            'L_QTY0' => $this->quantity,
        );

        // HTTPSocket class
        $httpSocket = new HttpSocket();
        // Post the NVPs to the relevent endpoint
        $response = $httpSocket->post($this->config['endpoint'], $setExpressCheckoutNvp);

        // Parse the guff that comes back from paypal
        parse_str($response->body, $parsed);

        // Return the token, or throw a human readable error
        if (array_key_exists('TOKEN', $parsed) && array_key_exists('ACK', $parsed) && $parsed['ACK'] == 'Success') {
            return $parsed['TOKEN'];
        } elseif (array_key_exists('ACK', $parsed) && array_key_exists('L_LONGMESSAGE0', $parsed) && $parsed['ACK'] != 'Success') {
            $this->log($parsed, 'paypal');
            throw new Exception($parsed['ACK'] . ' : ' . $parsed['L_LONGMESSAGE0']);
        } elseif (array_key_exists('ACK', $parsed) && array_key_exists('L_ERRORCODE0', $parsed) && $parsed['ACK'] != 'Success') {
            $this->log($parsed, 'paypal');
            throw new Exception($parsed['ACK'] . ' : ' . $parsed['L_ERRORCODE0']);
        } else {
            $this->log($parsed, 'paypal');
            throw new Exception(__('There is a problem with the payment gateway. Please try again later.'));
        }
    }

    /**
     * Send HTTP POST Request
     *
     * @param	string	The API method name
     * @param	string	The POST Message fields in &name=value pair format
     * @return	array	Parsed HTTP Response body
     */
    function PPHttpPost($environment = "sandbox", $methodName_, $nvpStr_, $PAYPAL_USER, $PAYPAL_PASS, $PAYPAL_SIGNATURE, $version = '54.0') {
        // Set up your API credentials, PayPal end point, and API version.
        $API_UserName = urlencode($PAYPAL_USER);
        $API_Password = urlencode($PAYPAL_PASS);
        $API_Signature = urlencode($PAYPAL_SIGNATURE);
        $API_Endpoint = "https://api-3t.paypal.com/nvp";
        if ("sandbox" === $environment || "beta-sandbox" === $environment) {
            $API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
        }
        $version = urlencode($version);

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
        // Get response from the server.
        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            return array('status' => 0, 'response' => "$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            return array('status' => 0, 'response' => "Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

        foreach ($httpParsedResponseAr as $k => $v) {
            $httpParsedResponseAr[$k] = urldecode($v);
        }

        return array('status' => 1, 'response' => $httpParsedResponseAr);
    }

}

?>