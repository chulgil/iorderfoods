<?php

App::uses('AppModel', 'Model');

class CommonSetting extends AppModel {
    ##function to getting site's common settings

    function getSiteSettings() {
        $result = $this->find('first');
        return $result;
    }

    //function to get google map api key, passing type for further multiple keys
    function getMapKey($key_type = null) {
        $resultSet = $this->find('first', array('fields' => 'CommonSetting.google_map_key'));
        return $resultSet['CommonSetting']['google_map_key'];
    }

    //function is used to run old and new sync even after changing domain
    function getSettingsForSync($is_new = null) {

        $restult = array();
        $resultSet = $this->find('first', array('fields' => 'CommonSetting.google_client_id,CommonSetting.google_client_secret,CommonSetting.google_redirect_uri'));

        if ($is_new == 'old') {
            $restult['google_client_id'] = $resultSet['CommonSetting']['google_client_id_old'];
            $restult['google_client_secret'] = $resultSet['CommonSetting']['google_client_secret_old'];
        } else {
            $restult['google_client_id'] = $resultSet['CommonSetting']['google_client_id'];
            $restult['google_client_secret'] = $resultSet['CommonSetting']['google_client_secret'];
        }

        //added in constant.php and here
        $protocol = "http://";
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $protocol = "https://";
        }

        //$uriredirect = $protocol . $_SERVER['HTTP_HOST'] . $resultSet['CommonSetting']['google_redirect_uri'] . '&client_id=' . $restult['google_client_id'];
        $uriredirect = $protocol . "myeatapp.com" . $resultSet['CommonSetting']['google_redirect_uri'] . '&client_id=' . $restult['google_client_id'];
        $restult['google_redirect_uri_withclient'] = $uriredirect;
        $restult['google_redirect_uri'] = $protocol . $_SERVER['HTTP_HOST'] . $resultSet['CommonSetting']['google_redirect_uri'];

        return $restult;
    }

}
