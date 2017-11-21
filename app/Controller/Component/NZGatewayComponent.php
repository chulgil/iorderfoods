<?php
/**
 * Created by Bankcardservices,INC.
 * User: CG.LEE
 * Date: 2016. 1. 27.
 * Time: 오전 10:54
 */


App::import('Vendor', 'NZGateway', array('file' => 'NZGateway'.DS.'NZGatewayDPI.php'));

/**
 * Exception class for NZGateway PHP SDK.
 *
 * @package NZGateway
 */
class NZGatewayComponent extends Component{

    private $gateway;

    function __construct() {
        $this->gateway = new NZGatewayDPI();
    }

    function __destruct() {
        $this->gateway = null;
    }

    function setLogin($loginid, $password) {
        $this->gateway->setLogin($loginid, $password);
    }


    function setOrder($orderid, $orderdescription, $tax, $shipping, $ponumber, $ipaddress)
    {
        $this->gateway->setOrder($orderid, $orderdescription, $tax, $shipping, $ponumber, $ipaddress);
    }

    function setBilling($firstname, $lastname, $company, $address, $address2,
                        $city, $state, $zipcode, $country, $phone,
                        $fax, $customer_email, $website){
        $this->gateway->setBilling(
            $firstname, $lastname, $company, $address, $address2,
            $city, $state, $zipcode, $country, $phone, $fax, $customer_email, $website);
    }

    function doSale($amount=0, $card_num, $exp_date, $cvv) {
        $this->gateway->doSale($amount, $card_num, $exp_date, $cvv);
        return $this->gateway->responses;
    }

    function initVault($id, $card_num, $exp_date ) {
        $this->gateway->initVault($id, $card_num, $exp_date, "");
        return $this->gateway->responses;
    }

    function useVault($id, $amount) {
        $this->gateway->useVault($id, $amount,"");
        return $this->gateway->responses;
    }

    function getVault($id) {
        $result = $this->gateway->vaultXmlQuery($id, '');
        return $result;
    }

    function delVault($id) {
        $this->gateway->delVault($id);
        return $this->gateway->responses;
    }

    function doRefund($id, $amount) {
        $this->gateway->doRefund($id, $amount);
        return $this->gateway->responses;
    }

    function doVoid($id) {
        $this->gateway->doVoid($id);
        return $this->gateway->responses;
    }
}