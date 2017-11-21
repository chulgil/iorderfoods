<?php
/**
 * Created by Bankcardservices,INC.
 * User: CG.LEE
 * Date: 2016. 1. 27.
 * Time: 오전 10:54
 */

define("APPROVED", 1);
define("DECLINED", 2);
define("ERROR", 3);

class NZGatewayDPI
{

// Initial Setting Functions

    function setLogin($username, $password)
    {
        $this->login['username'] = $username;
        $this->login['password'] = $password;

        // initialization
        $this->setOrder();
        $this->setShipping();
    }

    function setOrder($orderid = '',
                      $orderdescription = '',
                      $tax = '',
                      $shipping = '',
                      $ponumber = '',
                      $ipaddress = '')
    {
        $this->order['orderid'] = $orderid;
        $this->order['order_description'] = $orderdescription;
        $this->order['tax'] = $tax;
        $this->order['shipping'] = $shipping;
        $this->order['ponumber'] = $ponumber;
        $this->order['ipaddress'] = $ipaddress;
    }

    function setBilling($firstname,
                        $lastname,
                        $company,
                        $address1,
                        $address2,
                        $city,
                        $state,
                        $zip,
                        $country,
                        $phone,
                        $fax,
                        $customer_email,
                        $website)
    {
        $this->billing['firstname'] = $firstname;
        $this->billing['lastname'] = $lastname;
        $this->billing['company'] = $company;
        $this->billing['address1'] = $address1;
        $this->billing['address2'] = $address2;
        $this->billing['city'] = $city;
        $this->billing['state'] = $state;
        $this->billing['zip'] = $zip;
        $this->billing['country'] = $country;
        $this->billing['phone'] = $phone;
        $this->billing['fax'] = $fax;
        $this->billing['email'] = $customer_email;
        $this->billing['website'] = $website;
    }

    function setShipping($firstname = '',
                         $lastname = '',
                         $company = '',
                         $address1 = '',
                         $address2 = '',
                         $city = '',
                         $state = '',
                         $zip = '',
                         $country = '',
                         $email = '')
    {
        $this->shipping['firstname'] = $firstname;
        $this->shipping['lastname'] = $lastname;
        $this->shipping['company'] = $company;
        $this->shipping['address1'] = $address1;
        $this->shipping['address2'] = $address2;
        $this->shipping['city'] = $city;
        $this->shipping['state'] = $state;
        $this->shipping['zip'] = $zip;
        $this->shipping['country'] = $country;
        $this->shipping['email'] = $email;
    }

    // Transaction Functions
    //'response' => string '3' (length=1)
    //'responsetext' => string 'Duplicate transaction REFID:3174971726' (length=38)
    //'authcode' => string '' (length=0)
    //'transactionid' => string '' (length=0)
    //'avsresponse' => string '' (length=0)
    //'cvvresponse' => string '' (length=0)
    //'orderid' => string '' (length=0)
    //'type' => string 'sale' (length=4)
    //'response_code' => string '300' (length=3)
    function doSale($amount, $ccnumber, $ccexp, $cvv = "")
    {

        $query = "";
        // Login Information
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode($this->num_format($amount)) . "&";
        $query .= "cvv=" . urlencode($cvv) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['order_description']) . "&";
        $query .= "tax=" . urlencode($this->num_format($this->order['tax'])) . "&";
        $query .= "shipping=" . urlencode($this->num_format($this->order['shipping'])) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        $query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
        $query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
        $query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
        $query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
        $query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
        $query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
        $query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
        $query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
        $query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
        $query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
        $query .= "type=sale";
        return $this->_doPost($query);
    }

    function num_format($num)
    {
        if ($num) $num = number_format($num, 2, ".", "");
        return $num;
    }

    function doAuth($amount, $ccnumber, $ccexp, $cvv = "")
    {

        $query = "";
        // Login Information
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode($this->num_format($amount)) . "&";
        $query .= "cvv=" . urlencode($cvv) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['order_description']) . "&";
        $query .= "tax=" . urlencode($this->num_format($this->order['tax'])) . "&";
        $query .= "shipping=" . urlencode($this->num_format($this->order['shipping'])) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        $query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
        $query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
        $query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
        $query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
        $query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
        $query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
        $query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
        $query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
        $query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
        $query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
        $query .= "type=auth";
        return $this->_doPost($query);
    }

    function doCredit($amount, $ccnumber, $ccexp)
    {

        $query = "";
        // Login Information
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode($this->num_format($amount)) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['order_description']) . "&";
        $query .= "tax=" . urlencode($this->num_format($this->order['tax'])) . "&";
        $query .= "shipping=" . urlencode($this->num_format($this->order['shipping'])) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        $query .= "type=credit";
        return $this->_doPost($query);
    }

    function doOffline($authorizationcode, $amount, $ccnumber, $ccexp)
    {

        $query = "";
        // Login Information
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";
        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "amount=" . urlencode($this->num_format($amount)) . "&";
        $query .= "authorizationcode=" . urlencode($authorizationcode) . "&";
        // Order Information
        $query .= "ipaddress=" . urlencode($this->order['ipaddress']) . "&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "orderdescription=" . urlencode($this->order['order_description']) . "&";
        $query .= "tax=" . urlencode($this->num_format($this->order['tax'])) . "&";
        $query .= "shipping=" . urlencode($this->num_format($this->order['shipping'])) . "&";
        $query .= "ponumber=" . urlencode($this->order['ponumber']) . "&";
        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        $query .= "shipping_firstname=" . urlencode($this->shipping['firstname']) . "&";
        $query .= "shipping_lastname=" . urlencode($this->shipping['lastname']) . "&";
        $query .= "shipping_company=" . urlencode($this->shipping['company']) . "&";
        $query .= "shipping_address1=" . urlencode($this->shipping['address1']) . "&";
        $query .= "shipping_address2=" . urlencode($this->shipping['address2']) . "&";
        $query .= "shipping_city=" . urlencode($this->shipping['city']) . "&";
        $query .= "shipping_state=" . urlencode($this->shipping['state']) . "&";
        $query .= "shipping_zip=" . urlencode($this->shipping['zip']) . "&";
        $query .= "shipping_country=" . urlencode($this->shipping['country']) . "&";
        $query .= "shipping_email=" . urlencode($this->shipping['email']) . "&";
        $query .= "type=offline";
        return $this->_doPost($query);
    }

    function doCapture($transactionid, $amount = 0)
    {

        $query = "";
        // Login Information
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";
        // Transaction Information
        $query .= "transactionid=" . urlencode($transactionid) . "&";
        if ($amount > 0) {
            $query .= "amount=" . urlencode($this->num_format($amount)) . "&";
        }
        $query .= "type=capture";
        return $this->_doPost($query);
    }

    function doVoid($transactionid)
    {

        $query = "";
        // Login Information
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";
        // Transaction Information
        $query .= "transactionid=" . urlencode($transactionid) . "&";
        $query .= "type=void";
        return $this->_doPost($query);
    }

    function doRefund($transactionid, $amount = 0)
    {

        $query = "";
        // Login Information
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";
        // Transaction Information
        $query .= "transactionid=" . urlencode($transactionid) . "&";
        if ($amount > 0) {
            $query .= "amount=" . urlencode($this->num_format($amount)) . "&";
        }
        $query .= "type=refund";
        return $this->_doPost($query);
    }

    function _doPost($query, $url = 'https://secure.nzgateway.com/api/transact.php')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_POST, 1);

        if (!($data = curl_exec($ch))) {
            return ERROR;
        }
        curl_close($ch);
        unset($ch);
        print "\n$data\n";
        $data = explode("&", $data);
        for ($i = 0; $i < count($data); $i++) {
            $rdata = explode("=", $data[$i]);
            $this->responses[$rdata[0]] = $rdata[1];
        }
        return $this->responses['response'];
    }

    function vaultXmlQuery($customer_vault_id, $email="")
    {
        $transactionFields = array(
            'first_name',
            'last_name',
            'address_1',
            'address_2',
            'company',
            'city',
            'state',
            'postal_code',
            'country',
            'email',
            'phone',
            'fax',
            'cell_phone',
            'customertaxid',
            'website',

            'shipping_last_name',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_company',
            'shipping_city',
            'shipping_state',
            'shipping_postal_code',
            'shipping_country',
            'shipping_email',
            'shipping_carrier',
            'tracking_number',

            'cc_number',
            'cc_hash',
            'cc_exp',
            'cc_bin',
            'cc_issue_number',
            'check_account',
            'check_hash',
            'check_aba',
            'check_name',
            'account_holder_type',
            'account_type',
            'sec_code',
            'customer_vault_id',
            'processor_id');

        $postStr = "";
        $postStr .= "username=" . urlencode($this->login['username']) . "&";
        $postStr .= "password=" . urlencode($this->login['password']) . "&";
        if($email) {
            $postStr .= "&report_type=customer_vault&email=vncodenavi@gmail.com";
        } else {
            $postStr .= "&report_type=customer_vault&customer_vault_id=".$customer_vault_id;
        }

        $url = "https://secure.nzgateway.com/api/query.php?" . $postStr;

        $ch = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING => "",       // handle all encodings
            CURLOPT_USERAGENT => "BCS", // who am i
            CURLOPT_AUTOREFERER => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 30,      // timeout on connect
            CURLOPT_TIMEOUT => 30,      // timeout on response
            CURLOPT_MAXREDIRS => 10,       // stop after 10 redirects
        );
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        curl_close($ch);
        unset($ch);
        $response_xml = new SimpleXMLElement($data);
        if (!isset($response_xml->customer_vault)) {
            return 'No customer vault returned';
        }

        $result = [];
        foreach ($response_xml->customer_vault->customer as $transaction) {
            foreach ($transactionFields as $xmlField) {
                if (!isset($transaction->{$xmlField}[0])) {
                    return 'Error in field_id:' . $transaction->transaction_id[0] . ' id  Customer Vault tag is missing  field ' . $xmlField;
                } else {
                    $result[$xmlField] = trim((string)$transaction->{$xmlField});
                }
            }
        }
        return $result;
    }


    function initVault($id, $ccnumber, $ccexp, $payment = 'creditcard')
    {

        if ($id) $customer_vault = "update_customer";
        else $customer_vault = "add_customer";

        $billing_id = "";
        $customer_vault_id = $id;

        $query = "";
        $query .= "customer_vault=" . urlencode($customer_vault) . "&";
        $query .= "customer_vault_id=" . urlencode($customer_vault_id) . "&";
        $query .= "billing_id=" . urlencode($billing_id) . "&";

        // Login Information
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";

        // Sales Information
        $query .= "ccnumber=" . urlencode($ccnumber) . "&";
        $query .= "ccexp=" . urlencode($ccexp) . "&";
        $query .= "checkname=&";
        $query .= "checkaba=&";
        $query .= "checkaccount=&";
        $query .= "account_holder_type=&";
        $query .= "account_type=&";
        $query .= "sec_code=&";
        $query .= "payment=" . urlencode($payment) . "&";

        // Billing Information
        $query .= "firstname=" . urlencode($this->billing['firstname']) . "&";
        $query .= "lastname=" . urlencode($this->billing['lastname']) . "&";
        $query .= "company=" . urlencode($this->billing['company']) . "&";
        $query .= "address1=" . urlencode($this->billing['address1']) . "&";
        $query .= "address2=" . urlencode($this->billing['address2']) . "&";
        $query .= "city=" . urlencode($this->billing['city']) . "&";
        $query .= "state=" . urlencode($this->billing['state']) . "&";
        $query .= "zip=" . urlencode($this->billing['zip']) . "&";
        $query .= "country=" . urlencode($this->billing['country']) . "&";
        $query .= "phone=" . urlencode($this->billing['phone']) . "&";
        $query .= "fax=" . urlencode($this->billing['fax']) . "&";
        $query .= "email=" . urlencode($this->billing['email']) . "&";
        $query .= "website=" . urlencode($this->billing['website']) . "&";
        // Shipping Information
        return $this->_doPost($query);
    }

    function useVault($vault_id, $amount, $process_id)
    {
        $query = "";
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";
        $query .= "customer_vault_id=" . urlencode($vault_id) . "&";
        $query .= "amount=" . urlencode($this->num_format($amount)) . "&";
        $query .= "currency=&";
        $query .= "processor_id=" . urlencode($process_id) . "&";
        $query .= "descriptor=&";
        $query .= "descriptor_phone=&";
        $query .= "orderid=" . urlencode($this->order['orderid']) . "&";
        $query .= "order_description=" . urlencode($this->order['order_description']) . "&";
        return $this->_doPost($query);
    }

    function delVault($id)
    {
        $query = "customer_vault=delete_customer&";
        $query .= "customer_vault_id=" . urlencode($id) . "&";
        // Login Information
        $query .= "username=" . urlencode($this->login['username']) . "&";
        $query .= "password=" . urlencode($this->login['password']) . "&";
        return $this->_doPost($query);
    }

    function parseHttpGet($text)
    {
        $idArray = explode('&', $text);
        foreach ($idArray as $index => $avPair) {
            list($ignore, $value) = explode("=", $avPair);
            $id[$index] = urldecode($value);
        }
    }

}