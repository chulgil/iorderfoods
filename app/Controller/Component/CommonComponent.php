<?php
/**
 * Custom component
 * PHP 5
 * Created By         :Navdeep kaur
 * Date Of Creation   : 23 Oct 2013
 */
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * Custom component
 */
class CommonComponent extends Component {

    /**
     * This component uses the component
     *
     * @var array
     */
    var $components = array('Cookie', 'Session', 'Email', 'Upload', 'Categories.Easyphpthumbnail');

    /*
     * Function to generate the random password
     */

    public function getRandPass() {
        // Array Declaration
        $pass = array();
        // Variable declaration
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Upload Original Image
     * @author       Navdeep kaur
     * @copyright     smartData Enterprise Inc.
     * @method        image_upload
     * @param         $file, $path, $folder_name, $thumb, $multiple
     * @return        $filename or $err_type
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function upload_image($file, $path, $folder_name, $thumb = false, $multiple = array()) {
        // Variable containing File type
        $extType = $file['type'];
        // Variable containing extension in lowercase
        $ext = strtolower($extType);
        // Condition checking File extension
        if ($ext == 'image/jpg' || $ext == 'image/png' || $ext == 'image/jpeg' || $ext == 'image/gif') {
            // Condition checking File size
            if ($file['size'] <= 10485760) {
                // Filename
                $filename = time() . '_' . $file['name'];
                // Folder path
                $folder_url = APP . $path . '/' . $folder_name;
                // Condition checking File exist or not
                if (!file_exists($folder_url . '/' . $filename)) {
                    // create full filename
                    $full_url = $folder_url . '/' . $filename;
                    // upload the file
                    move_uploaded_file($file['tmp_name'], $full_url);
                    if ($thumb) {
                        // If multiple folder upload required then pass TRUE as last parameter
                        $this->upload_thumb_image($filename, $path, $folder_name, $multiple);
                    }
                    return $filename;
                } else {
                    return 'exist_error';
                }
            } else {
                return 'size_mb_error';
            }
        } else {
            return 'type_error';
        }
    }

    /**
     * Upload Thumb Image
     * @author        Anuj Kumar
     * @copyright     smartData Enterprise Inc.
     * @method        upload_thumb_image
     * @param         $filename, $path, $folder_name, $multiple
     * @return        void
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function upload_thumb_image($filename, $path, $folder_name, $multiple = array()) {
        // image path from where pic taken
        $dircover = str_replace(chr(92), chr(47), APP) . '/' . $path . '/' . $folder_name . '/' . $filename;
        if (!empty($multiple) && count($multiple) > 0) {
            foreach ($multiple as $result) {
                $this->Easyphpthumbnail->Thumblocation = str_replace(chr(92), chr(47), APP) . '/' . $path . '/' . $result['folder_name'] . '/';
                $this->Easyphpthumbnail->Thumbheight = $result['height'];
                $this->Easyphpthumbnail->Thumbwidth = $result['width'];
                $this->Easyphpthumbnail->Createthumb($dircover, 'file');
            }
        }
    }

    /**
     * Handle image errors
     * @author        Anuj Kumar
     * @copyright     smartData Enterprise Inc.
     * @method        is_image_error
     * @param         $image_name
     * @return        error msg
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function is_image_error($image_name = null) {
        $errmsg = '';
        switch ($image_name) {
            case 'exist_error':
                $errmsg = 'File already exist.';
                break;

            case 'size_mb_error':
                $errmsg = 'Only mb of file is allowed to upload.';
                break;

            case 'type_error':
                $errmsg = 'Only JPG, JPEG, PNG & GIF are allowed.';
                break;
            default:
                $errmsg = 'Some error occured.';
                break;
        }
        return $errmsg;
    }

    /**
     * Delete image
     * @author       Navdeep kaur
     * @copyright     smartData Enterprise Inc.
     * @method        delete_image
     * @param         $image_name, $path, $thumb_path
     * @return        void
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function delete_image($imagename = null, $path = null, $folder_name = null, $thumb = false, $multiple = array()) {
        if (!empty($path)) {
            $full_path = WWW_ROOT . $path . '/' . $folder_name . '/' . $imagename;
            if (file_exists($full_path)) {
                unlink($full_path);
            }
            if ($thumb && !empty($multiple) && count($multiple) > 0) {
                foreach ($multiple as $result) {
                    $full_thumb_path = WWW_ROOT . $path . '/' . $result['folder_name'] . '/' . $imagename;
                    if (file_exists($full_thumb_path)) {
                        unlink($full_thumb_path);
                    }
                }
            }
        }
    }

    /**
     * Upload Video
     * @author       Navdeep kaur
     * @copyright     smartData Enterprise Inc.
     * @method        upload_video
     * @param         $file, $path
     * @return        $filename or $err_type
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function upload_video($file, $path) {
        // Variable containing File type
        $extType = end(explode('.', $file['name']));
        // Variable containing extension in lowercase
        $ext = strtolower($extType);
        // Condition checking File extension
        $extArray = array('mov', 'avi', 'wmv', 'dat', 'mpeg', 'mpg', 'flv', 'mp4', 'mp2');
        if (in_array($ext, $extArray)) {
            // Condition checking File size
            if ($file['size'] <= 10485760) {
                // Array Declaration
                $arrVideo = array();
                // Filename without extension
                $filename_without_ext = preg_replace('/\.[a-z0-9]+$/i', '', $file['name']);
                // New filename
                $new_filename = time() . '_' . $filename_without_ext;
                // Filename
                $original_filename = $new_filename . '.' . $ext;
                $converted_filename = $new_filename . '.flv';
                $thumb_filename = $new_filename . '.jpg';
                // Folder path
                $path_original_video = WWW_ROOT . $path . '/' . $original_filename;
                $path_converted_video = WWW_ROOT . $path . '/' . $converted_filename;
                $path_converted_video_thumb = WWW_ROOT . $path . '/thumb/' . $thumb_filename;
                // Condition checking File exist or not
                if (!file_exists($path_original_video)) {
                    // create full filename
                    $full_url = $path_original_video;
                    // upload the file
                    if (move_uploaded_file($file['tmp_name'], $full_url)) {
                        // The first this we need to do is convert the video
                        $this->VideoEncoder->convert_video($path_original_video, $path_converted_video, 480, 360);
                        // Then we need to set the buffer on the converted video
                        $this->VideoEncoder->set_buffering($path_converted_video);
                        // We can also grab a screenshot from the video as a jpeg and store it for future use.
                        $this->VideoEncoder->grab_image($path_converted_video, $path_converted_video_thumb);
                        if ($ext != 'flv') {
                            // Finally we can delete the original video
                            $this->VideoEncoder->remove_uploaded_video($path_original_video);
                        }
                        $arrVideo = array('0' => $converted_filename, '1' => $thumb_filename);
                        return $arrVideo;
                    } else {
                        return 'some_error';
                    }
                } else {
                    return 'exist_error';
                }
            } else {
                return 'size_mb_error';
            }
        } else {
            return 'type_error';
        }
    }

    /**
     * Handle image errors
     * @author       Navdeep kaur
     * @copyright     smartData Enterprise Inc.
     * @method        is_video_error
     * @param         array()
     * @return        error msg
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function is_video_error($arr = array()) {
        $errmsg = '';
        if (!empty($arr) && count($arr) > 0) {
            switch ($arr[0]) {
                case 'some_error':
                    $errmsg = 'Some error occured while uploading video. Please try again.';
                    break;
                case 'exist_error':
                    $errmsg = 'File already exist.';
                    break;
                case 'size_mb_error':
                    $errmsg = 'Only mb of file is allowed to upload.';
                    break;
                case 'type_error':
                    $errmsg = 'Only JPG, JPEG, PNG & GIF are allowed.';
                    break;
                default:
                    $errmsg = 'Some error occured.';
                    break;
            }
        }
        return $errmsg;
    }

    /**
     * Upload Document
     * @author       Navdeep kaur
     * @copyright     smartData Enterprise Inc.
     * @method        upload_document
     * @param         $file, $path
     * @return        $filename or $err_type
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function upload_document($file, $path) {
        // Variable containing File type
        $extType = end(explode('.', $file['name']));

        // Variable containing extension in lowercase
        $ext = strtolower($extType);

        // Condition checking File extension
        $extArray = array('xls', 'doc', 'docx', 'pdf', 'txt');
        if (in_array($ext, $extArray)) {
            // Condition checking File size
            if ($file['size'] <= 10485760) {
                // Filename
                $filename = time() . '_' . $file['name'];
                // Folder path
                $folder_url = WWW_ROOT . $path . '/' . $filename;
                // Condition checking File exist or not
                if (!file_exists($folder_url)) {
                    // create full filename
                    $full_url = $folder_url;
                    // upload the file
                    if (move_uploaded_file($file['tmp_name'], $full_url)) {
                        return $filename;
                    } else {
                        return 'some_error';
                    }
                } else {
                    return 'exist_error';
                }
            } else {
                return 'size_mb_error';
            }
        } else {
            return 'type_error';
        }
    }

    /**
     * Handle image errors
     * @author       Navdeep kaur
     * @copyright     smartData Enterprise Inc.
     * @method        is_document_error
     * @param         $document_name
     * @return        error msg
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function is_document_error($document_name = null) {
        $errmsg = '';
        switch ($document_name) {
            case 'some_error':
                $errmsg = 'Some error occured while uploading document. Please try again.';
                break;
            case 'exist_error':
                $errmsg = 'File already exist.';
                break;

            case 'size_mb_error':
                $errmsg = 'Only 10 mb of file is allowed to upload.';
                break;

            case 'type_error':
                $errmsg = 'Only TXT, PDF, DOC, DOCX & XLS are allowed.';
                break;
            default:
                $errmsg = 'Some error occured.';
                break;
        }
        return $errmsg;
    }

    /**
     * Delete image
     * @author       Navdeep kaur
     * @copyright     smartData Enterprise Inc.
     * @method        delete_image
     * @param         $image_name, $path, $thumb_path
     * @return        void
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function delete_document($filename, $path) {
        if (!empty($filename) && !empty($path)) {
            $full_path = WWW_ROOT . $path . '/' . $filename;
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }
    }

    /**
     * Download file
     * @author       Navdeep kaur
     * @copyright     smartData Enterprise Inc.
     * @method        download_file
     * @param         $filename, $path
     * @return        void
     * @since         version 0.0.1
     * @version       0.0.1
     */
    public function download_file($filename, $path) {

        // Variable Declaration
        $fullPath = $path . '/' . $filename;
        if ($fd = fopen($fullPath, 'r')) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case 'xls':
                case 'doc':
                case 'docx':
                    // add here more headers for diff. extensions
                    header("Content-type: application/doc");
                    // use 'attachment' to force a download
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\"");
                    break;

                default;
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while (!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose($fd);
        exit;
    }

    /* Change the space into underscore */

    function stringConvertUscoreToSpace($getName = null) {
        $getName = strtolower(str_replace('_', ' ', $getName));
        return $getName;
    }

    /* Report Management */

    function getReport($getName = null) {
        $reportArray = array('1' => 'Order Master', '2' => 'Order Detail');
        return $reportArray;
    }

    /* Get State List */

    function getStateList() {
        $statelist = array("AL" => "Alabama", "AK" => "Alaska", "AZ" => "Arizona", "AR" => "Arkansas", "AS" => "American Samoa", "CA" => "California", "CO" => "Colorado", "CT" => "Connecticut", "DE" => "Delaware", "DC" => "District of Columbia", "FL" => "Florida", "GA" => "Georgia", "GU" => "Guam", "HI" => "Hawaii", "ID" => "Idaho", "IL" => "Illinois", "IN" => "Indiana", "IA" => "Iowa", "KS" => "Kansas", "KY" => "Kentucky", "LA" => "Louisiana", "ME" => "Maine", "MD" => "Maryland", "MA" => "Massachusetts", "MI" => "Michigan", "MN" => "Minnesota", "MS" => "Mississippi", "MO" => "Missouri", "MT" => "Montana", "NE" => "Nebraska", "NV" => "Nevada", "NH" => "New Hampshire", "NJ" => "New Jersey", "NM" => "New Mexico", "NY" => "New York", "NC" => "North Carolina", "ND" => "North Dakota", "MP" => "Northern Marianas Islands", "OH" => "Ohio", "OK" => "Oklahoma", "OR" => "Oregon", "PA" => "Pennsylvania", "PR" => "Puerto Rico", "RI" => "Rhode Island", "SC" => "South Carolina", "SD" => "South Dakota", "TN" => "Tennessee", "TX" => "Texas", "UT" => "Utah", "VT" => "Vermont", "VA" => "Virginia", "VI" => "Virgin Islands", "WA" => "Washington", "WV" => "West Virginia", "WI" => "Wisconsin", "WY" => "Wyoming");
        return $statelist;
    }

    /* ------------------------------------------------
      Function name:getStoreTimeAdmin()
      Description: return time range array for admin
      created:10/2/2016
      ----------------------------------------------------- */

    function getStoreTimeAdmin($startTime = null, $endTime = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('admin_store_id');
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate')));
        $tStart = strtotime($startTime);
        $tEnd = strtotime($endTime);
        $tNow = $tStart;
        $timeRange = array();
        while ($tNow <= $tEnd) {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $intervalTime = date("h:i a", $tNow);
            } else {
                $intervalTime = date("H:i", $tNow);
            }
            $intervalTimeforValue = date("H:i:s", $tNow);
            if ($intervalTime == "00:00" || $intervalTimeforValue == "00:00:00" || $intervalTime == "00:00:00") {
                $intervalTime = strtotime("00:00");
                if ($storeInfo['Store']['time_formate'] == 1) {
                    $intervalTime = date("h:i a", $intervalTime);
                } else {
                    $intervalTime = date("H:i", $intervalTime);
                }
                $intervalTimeforValue = "00:00:00";
            }
            $timeRange[$intervalTimeforValue] = $intervalTime;
            $tNow = strtotime('+15 minutes', $tNow);
        }
        if ($storeInfo['Store']['time_formate'] == 1) {
            $lastTime = '11:59 pm';
        } else {
            $lastTime = '23:59';
        }
        $timeRange['23:59:00'] = $lastTime;
        return $timeRange;
    }

    /* ------------------------------------------------
      Function name:getStoreTime()
      Description : return time range array for admin
      created:10/2/2016
      ----------------------------------------------------- */

    function getStoreTime($startTime = null, $endTime = null, $ordertype = null, $store_data = null, $storeBT = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('store_id');
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate', 'Store.delivery_delay', 'Store.pick_up_delay', 'Store.cutoff_time')));
        if ($ordertype) {
            if ($ordertype == 2) {
                $interval = ($storeInfo['Store']['pick_up_delay'] * 60);
            } elseif ($ordertype == 3) {
                $interval = ($storeInfo['Store']['delivery_delay'] * 60);
            } else {
                $interval = 900;
            }
        } else {
            $interval = 900;
        }

        $tStart = strtotime($startTime);

        $tEnd = strtotime($endTime);
        if (!empty($store_data) && !empty($storeBT)) {
            $BStart1 = strtotime($storeBT['StoreBreak']['break1_start_time']);
            $BEnd1 = strtotime($storeBT['StoreBreak']['break1_end_time']);
            $BStart2 = strtotime($storeBT['StoreBreak']['break2_start_time']);
            $BEnd2 = strtotime($storeBT['StoreBreak']['break2_end_time']);
        }
        $tNow = $tStart;
        $timeRange = array();
        $i = 0;
        $reachEndTime = false;
        while ($tNow <= $tEnd) {
            if (!empty($store_data) && !empty($storeBT)) {
                if (!empty($store_data['Store']['is_break_time']) && $store_data['Store']['is_break1'] == 1 && $tNow > $BStart1 && $tNow < $BEnd1) {
                    $tNow = $BEnd1;
                }
                if (!empty($store_data['Store']['is_break_time']) && $store_data['Store']['is_break2'] == 1 && $tNow > $BStart2 && $tNow < $BEnd2) {
                    $tNow = $BEnd2;
                }
            }

            if (!empty($storeInfo['Store']['time_formate'])) {
                $intervalTime = date("h:i a", $tNow);
            } else {
                $intervalTime = date("H:i", $tNow);
            }
            $intervalTimeforValue = date("H:i:s", $tNow);
            if ($intervalTime == "00:00" || $intervalTimeforValue == "00:00:00" || $intervalTime == "00:00:00") {
                $intervalTime = strtotime("23:59");
                if ($storeInfo['Store']['time_formate'] == 1) {
                    $intervalTime = date("h:i a", $intervalTime);
                } else {
                    $intervalTime = date("H:i", $intervalTime);
                }
                $reachEndTime = "23:59:00";
                $reachEndTimeInterval = $intervalTime;
            } else {
                $timeRange[$intervalTimeforValue] = $intervalTime;
            }
            $tNow = $tNow + 300;
            $i++;
        }
        if ($reachEndTime) {
            $timeRange[$reachEndTime] = $reachEndTimeInterval;
        }
        return $timeRange;
    }

    /* ------------------------------------------------
     * Function name:getDeliveryInterval()
     * Description : return start time for store
     * Parameter : $today(1=>Today, 0=>another Day), $orderType(3=>delivery, 2=>Pick-up), $preOrder(1=>preOrder, 0=>Now)
      created:10/2/2016
      ----------------------------------------------------- */

    function getStartTime($startTime = null, $today = null, $orderType = null, $preOrder = null, $endTime = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('store_id');
        $store_data = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.delivery_interval', 'Store.delivery_delay', 'Store.pick_up_delay')));
        $poststartTime = $startTime;
        //Check Order Type (i.e. Delivery/Pick Up)
        $deliveryInterval = 0;
        if ($orderType == 3) {
            $deliveryInterval = !empty($store_data['Store']['delivery_delay']) ? $store_data['Store']['delivery_delay'] : 0;
        } else if ($orderType == 2) {
            $deliveryInterval = !empty($store_data['Store']['pick_up_delay']) ? $store_data['Store']['pick_up_delay'] : 0;
        } else if ($orderType == 1) {
            $deliveryInterval = 0;
        }
        //Check Day (i.e. Today/ another)
        if ($today) {
            $nowDate = date("Y-m-d H:i:s");
            $minute = date("i");
            if ($minute % 5 != 0) {
                $mod = $minute % 5;
                $rem = 5 - $mod;
                $nowDate = date("Y-m-d H:i:s", strtotime((date("Y-m-d H:") . $minute . ":00")) + ($rem * 60));
            }

            $currentStoreactual = date("H:i:s", strtotime($this->storeTimeZoneUser('', $nowDate)));
            $currentStoreTime = date("H:i:s", strtotime($currentStoreactual) + ($deliveryInterval * 60));
            $var1 = explode(":", $currentStoreactual);
            $hours = $var1[0];
            $minutes = $var1[1];
            $TotalCurrentminutes = $hours * 60 + $minutes;
            $actualminutes = $TotalCurrentminutes + ($deliveryInterval);
            $var2 = explode(":", $endTime);
            $Ehours = $var2[0];
            $Eminutes = $var2[1];
            $TotalEndminutes = $Ehours * 60 + $Eminutes;
            if (!empty($endTime)) {
                if (strtotime($currentStoreactual) > strtotime($endTime)) {
                    return 0;
                }
                if ($actualminutes >= 1440 || $actualminutes >= $TotalEndminutes) {
                    return 0;
                }
            }
            $currentStoreTime = strtotime($currentStoreTime);
            $startTime = strtotime($startTime);
            //Check Day (i.e. Now/ Predor)
            $currentTime = 0;
            $currentTime = date("H:i:s", ($currentStoreTime));
            $currentTimestr = strtotime($currentTime);
            $hour24 = array('24:00:00', '24:00', '00:00:00', '00:00');
            //store start time is greater than or not to current time
            if ($startTime > $currentTimestr && (!in_array($poststartTime, $hour24))) {
                $currentTime = date("H:i:s", $startTime + ($deliveryInterval * 60));
            }
        } else {
            $currentTime = date("H:i:s", (strtotime($startTime) + ($deliveryInterval * 60)));
        }

        return $currentTime;
    }

    /* ------------------------------------------------
     * Function name:getDeliveryInterval()
     * Description : return start time for store
     * Parameter : $today(1=>Today, 0=>another Day), $orderType(3=>delivery, 2=>Pick-up), $preOrder(1=>preOrder, 0=>Now)
      created:10/2/2016
      ----------------------------------------------------- */

    function getNowDelayTime($orderType = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('store_id');
        $store_data = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.delivery_interval', 'Store.delivery_delay', 'Store.pick_up_delay')));
        //Check Order Type (i.e. Delivery/Pick Up)
        $deliveryInterval = 0;
        if ($orderType == 2) {
            $deliveryInterval = !empty($store_data['Store']['pick_up_delay']) ? $store_data['Store']['pick_up_delay'] : 0;
        } else {
            //$deliveryInterval = 60;
            $deliveryInterval = 0;
        }
        $nowDate = date("Y-m-d H:i:s");
        $minute = date("i");
        if ($minute % 5 != 0) {
            $mod = $minute % 5;
            $rem = 5 - $mod;
            $nowDate = date("Y-m-d H:i:s", strtotime((date("Y-m-d H:") . $minute . ":00")) + ($rem * 60));
        }
        $currentTime = date("H:i:s", (strtotime($this->storeTimeZoneUser('', $nowDate)) + ($deliveryInterval * 60)));
        $currentTime = $this->storeTimeFormateUser($currentTime);
        return $currentTime;
    }

    function getCurlData($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
        $curlData = curl_exec($curl);
        curl_close($curl);
        return $curlData;
    }

    /* ---------------------------------------------
      Function name:checcheckStoreAvalibilitykAddress
      Description:To check store timing
      ----------------------------------------------- */

    function checkStoreAvalibility($strore_id = null, $selected_date = null, $booking_time = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        if (isset($strore_id)) {
            $storeHoliday = ClassRegistry::init('StoreHoliday');
            $storeAvailability = ClassRegistry::init('StoreAvailability');
            $store = ClassRegistry::init('Store');
            $storeBreak = ClassRegistry::init('StoreBreak');
            $store_data = $store->fetchStoreBreak($strore_id);
            if (!$selected_date) {
                $currentarr = explode(' ', $this->storeTimeZoneUser('', date('Y-m-d H:i:s')));
                $current_date = $currentarr[0];
            } else {
                $current_date = $selected_date;
            }
            $date = new DateTime($current_date);
            $current_day = $date->format('l');

            if (!$booking_time) {
                $current_time = date('H:i:s', strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s'))));
                $current_time = strtotime($current_time);
            } else {
                $current_time = strtotime($booking_time);
            }
            $start_time = "";
            $end_time = "";
            $description = "";

            $holidayList = $storeHoliday->getStoreHolidaylistDate($strore_id, $current_date);
            if (!empty($holidayList)) {
                $description = $holidayList['StoreHoliday']['description'];
                $store_status['status'] = "Holiday";
                $store_status['description'] = $description;
                return $store_status;
            }

            $storeAvailable = $storeAvailability->getStoreNotAvailableInfoDay($strore_id, $current_day);
            if (!empty($storeAvailable)) {
                if ($storeAvailable['StoreAvailability']['start_time'] == '24:00:00') {
                    $storeAvailable['StoreAvailability']['start_time'] = "00:00:00";
                }
                $start_time = strtotime($storeAvailable['StoreAvailability']['start_time']);
                $end_time = strtotime($storeAvailable['StoreAvailability']['end_time']);

                $StoreCutOff = $this->Store->fetchStoreCutOff($strore_id);
                $cutTime = '-' . $StoreCutOff['Store']['cutoff_time'] . ' minutes';
                $end_time = strtotime(date("H:i:s", strtotime("$cutTime", $end_time)));

                if (($current_time > $end_time) || ($current_time < $start_time)) {
                    $store_status['status'] = "Timeoff";
                    $store_status['start_time'] = $start_time;
                    $store_status['end_time'] = $end_time;
                    return $store_status;
                } else {
                    if ($store_data['Store']['is_break_time'] == 1) {
                        $store_break = $storeBreak->fetchStoreBreak($strore_id, $storeAvailable['StoreAvailability']['id']);
                        if ($store_break['StoreBreak']['break1_start_time'] == '24:00:00') {
                            $store_break['StoreBreak']['break1_start_time'] = "00:00:00";
                        }
                        if ($store_break['StoreBreak']['break2_start_time'] == '24:00:00') {
                            $store_break['StoreBreak']['break2_start_time'] = "00:00:00";
                        }
                        if (!empty($store_data['Store']['is_break_time']) && $store_data['Store']['is_break1'] == 1) {
                            $start_time = strtotime($store_break['StoreBreak']['break1_start_time']);
                            $end_time = strtotime($store_break['StoreBreak']['break1_end_time']);
                            if (($current_time < $end_time) && ($current_time > $start_time)) {
                                $store_status['status'] = "BreakTime";
                                $store_status['start_time'] = $start_time;
                                $store_status['end_time'] = $end_time;
                                return $store_status;
                            } else {
                                if (!empty($store_data['Store']['is_break_time']) && $store_data['Store']['is_break2'] == 1) {
                                    $start_time = strtotime($store_break['StoreBreak']['break2_start_time']);
                                    $end_time = strtotime($store_break['StoreBreak']['break2_end_time']);
                                    if (($current_time < $end_time) && ($current_time > $start_time)) {
                                        $store_status['status'] = "BreakTime";
                                        $store_status['start_time'] = $start_time;
                                        $store_status['end_time'] = $end_time;
                                        return $store_status;
                                    } else {
                                        return true;
                                    }
                                } else {
                                    return true;
                                }
                            }
                        } else if (!empty($store_data['Store']['is_break_time']) && $store_data['Store']['is_break2'] == 1) {
                            $start_time = strtotime($store_break['StoreBreak']['break2_start_time']);
                            $end_time = strtotime($store_break['StoreBreak']['break2_end_time']);
                            if (($current_time < $end_time) && ($current_time > $start_time)) {
                                $store_status['status'] = "BreakTime";
                                $store_status['start_time'] = $start_time;
                                $store_status['end_time'] = $end_time;
                                return $store_status;
                            }
                        } else {
                            return true;
                        }
                    } else {
                        return true;
                    }
                }
            } else {
                $store_status['status'] = "WeekDay";
                return $store_status;
            }
        }
    }

    /* ---------------------------------------------
      Function name:uploadMenuItemImages()
      Description:To upload Admin Images
      ----------------------------------------------- */

    public function uploadMenuItemImages($image = null, $path = null, $storeId = null, $newWidth = null, $newHeight = null, $folder_name = null, $newWidth1 = null, $newHeight1 = null) {
        if ($image['name'] != "") {
            $ImageStatus = "";
            $errormsg = '';
            $arr = pathinfo($image['name']);
            $fileextension = "";
            if (isset($arr['extension'])) {
                $fileextension = $arr['extension'];
            }
            if (!$this->checkImageExtension($fileextension)) {
                $errormsg = $errormsg . "Only jpg,gif,png type images are allowed<br />";
                $ImageStatus = "false";
            }
            $maxsize = 2097152; //In Byte
            $actualSize = $image['size'];
            if (($actualSize > $maxsize) || $image['error'] == "1") {
                $errormsg = $errormsg . "The image you are trying to upload is too large. Please limit the file size upto 2MB.";
                $ImageStatus = "false";
            }
            $target_dir = WWW_ROOT . $path;
            if (!file_exists($target_dir)) {
                (new Folder($target_dir, true, 0777));
            }
            $uniqueImageName = time() . rand() . '_' . $storeId . '.' . $fileextension;
            $target_file = $target_dir . $uniqueImageName;
            $response = array();
            $response['imagename'] = $uniqueImageName;
            if ($errormsg == "") {
                if (move_uploaded_file($image['tmp_name'], $target_file)) {
                    if (!empty($newHeight) && !empty($newWidth)) {
                        //Thumb Forlder path
                        $thumbDir = $target_dir . 'thumb';

                        //Check thumb folder exists or not
                        if (!file_exists($thumbDir)) {
                            (new Folder($thumbDir, true, 0777));
                        }

                        //Thumb Folder path
                        $thumb_folder_url = $target_dir . '/thumb';

                        // Condition checking thumb File exist or not
                        if (!file_exists($thumb_folder_url . '/' . $uniqueImageName)) {

                            // full path for thumb image
                            $full_thumb_url = $thumb_folder_url . '/' . $uniqueImageName;
                            list($width, $height, $type, $attr) = getimagesize($target_file);
                            if (empty($newHeight) && empty($newWidth)) {
                                $newHeight = 150;
                                $newWidth = 150;
                            }
                            $responseT = $this->getResize($height, $width, $newHeight, $newWidth, $target_file, $full_thumb_url);
                            if ($responseT) {
                                if (!empty($newHeight1) && !empty($newWidth1)) {
                                    //Thumb Forlder path
                                    $thumbDir1 = $target_dir . $folder_name;
                                    //Check thumb folder exists or not
                                    if (!file_exists($thumbDir1)) {
                                        (new Folder($thumbDir1, true, 0777));
                                    }
                                    //Thumb Folder path
                                    $thumb_folder_url1 = $target_dir . '/' . $folder_name;
                                    // Condition checking thumb File exist or not
                                    if (!file_exists($thumb_folder_url1 . '/' . $uniqueImageName)) {
                                        // full path for thumb image
                                        $full_thumb_url1 = $thumb_folder_url1 . '/' . $uniqueImageName;
                                        list($width, $height, $type, $attr) = getimagesize($target_file);
                                        $responseT = $this->getResize($height, $width, $newHeight1, $newWidth1, $target_file, $full_thumb_url1);
                                        if ($responseT) {
                                            $response['status'] = true;
                                        } else {
                                            $response['status'] = false;
                                            $response['errmsg'] = "Unable to upload image";
                                        }
                                    }
                                } else {
                                    $response['status'] = true;
                                }
                            } else {
                                $response['status'] = false;
                                $response['errmsg'] = "Unable to upload image";
                            }
                        }
                    } else {
                        $response['status'] = true;
                    }
                } else {
                    $response['status'] = false;
                    $response['errmsg'] = "Unable to upload image";
                }
            } else {
                $response['status'] = false;
            }
            $response['errmsg'] = $errormsg;
            return $response;
        } else {
            $response['imagename'] = '';
            $response['status'] = true;
            return $response;
        }
    }

    /* ---------------------------------------------
      Function name:checkExtension
      Description:To verify Image Extensions
      ----------------------------------------------- */

    function checkImageExtension($extension = null) {
        $extarr = array('jpg', 'gif', 'jpeg', 'png');
        $extension = strtolower($extension);
        if (in_array($extension, $extarr)) {
            return true;
        } else {
            return false;
        }
    }

    /* ---------------------------------------------
      Function name:RandomString
      Description:To generate unique number
      ----------------------------------------------- */

    function RandomString($storeID = null, $server_offset, $interface = 'W') {
        //(WEB/Mobile)(Unique_Merchant_id)-(date('YYMMDD'))(OrderNumber_on day)
        App::import('Model', 'Store');
        $this->Store = new Store();
        $merchantno = '';
        $merchantNumber = $this->Store->getMerchantNumber($storeID);
        if (!empty($merchantNumber)) {
            $merchantno = $merchantNumber['Store']['merchant_number'];
        }
        $date = $this->getcurrentTime($storeID, 2);
        $ordernumber = $this->getOrderCount($server_offset, $date, $interface, $merchantno);
        return $ordernumber;
    }

    function getOrderCount($server_offset, $date, $interface, $merchantno, $ordercount = 0) {
        App::import('Model', 'Order');
        $this->Order = new Order();
        if (empty($ordercount)) {
            $ordercount = $this->Order->getStoreOrdernumber($server_offset, $date);
        }
        $datestr = date_create($date);
        $finaldatestr = date_format($datestr, "mdy");
        $ordercount = $ordercount + 1;
        $finalOrderCount = str_pad($ordercount, count($ordercount) + 2, '0', STR_PAD_LEFT);
        $orderNumber = $interface . $merchantno . '-' . $finaldatestr . $finalOrderCount;
        if ($this->Order->checkorderNumber($orderNumber)) {
            $this->getOrderCount($server_offset, $date, $interface, $merchantno, $ordercount);
        }

        return $orderNumber;
    }

    /* ---------------------------------------------
      Function name:checkPermissionByaction
      Description:For permissions of Controllers and actions
      ----------------------------------------------- */

    function checkPermissionByaction($controller = null, $action = null, $userId = null) {
        if (!empty($userId)) {
            App::import('Model', 'User');
            $this->User = new User();
            $userdata = $this->User->findUserRole($userId);
            $roleID = $userdata['User']['role_id'];
        } else {
            $roleID = $this->Session->read('Auth.Admin.role_id');
            $userId = $this->Session->read('Auth.Admin.id');
        }
        if (!empty($controller)) {
            App::import('Model', 'Tab');
            $this->Tab = new Tab();
            $tabid = $this->Tab->getTabData(null, $controller, $action, $roleID);
            App::import('Model', 'Permission');
            $this->Permission = new Permission();
            $permissiondata = $this->Permission->getPermissionData($userId, $tabid);
            if (!empty($permissiondata)) {
                $permission = 1;
            } else {
                $permission = 0;
            }
            return $permission;
        }
    }

    /* ---------------------------------------------
      Function name:sendsmsNotification
      Description:For SMS Notification
      ----------------------------------------------- */

    function sendSmsNotification($toNumber = null, $message = null) {
        if ($toNumber) {
            App::import('Model', 'StoreSetting');
            $this->StoreSetting = new StoreSetting();
            $storeId = $storeId = $this->Session->read('admin_store_id');
            $storeSetting = $this->StoreSetting->findByStoreId($storeId);
            if (!empty($storeSetting['StoreSetting']['twilio_sms_allow'])) {
                App::import('Model', 'Store');
                $this->Store = new Store();
                $settings = $this->Store->fetchStoreDetail($storeId);
                if (!empty($settings['Store']['twilio_api_key']) && !empty($settings['Store']['twilio_api_token']) && !empty($settings['Store']['twilio_number'])) {
                    $tApikey = $settings['Store']['twilio_api_key'];
                    $tApiToken = $settings['Store']['twilio_api_token'];
                    $tApiNumber = $settings['Store']['twilio_number'];
                    App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
                    $client = new Services_Twilio($tApikey, $tApiToken);
                    $client->account->messages->create(array(
                        'To' => $toNumber,
                        'From' => $tApiNumber,
                        'Body' => $message,
                    ));
                }
            }
        }
    }

    function sendSmsNotificationFront($toNumber = null, $message = null, $storeId = null) {
        if ($toNumber) {
            App::import('Model', 'StoreSetting');
            $this->StoreSetting = new StoreSetting();
            if (empty($storeId)) {
                $storeId = $this->Session->read('store_id');
            }
            $storeSetting = $this->StoreSetting->findByStoreId($storeId);
            if (!empty($storeSetting['StoreSetting']['twilio_sms_allow'])) {
                App::import('Model', 'Store');
                $this->Store = new Store();
                $settings = $this->Store->fetchStoreDetail($storeId);
                if (!empty($settings['Store']['twilio_api_key']) && !empty($settings['Store']['twilio_api_token']) && !empty($settings['Store']['twilio_number'])) {
                    $tApikey = $settings['Store']['twilio_api_key'];
                    $tApiToken = $settings['Store']['twilio_api_token'];
                    $tApiNumber = $settings['Store']['twilio_number'];
                    App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
                    $client = new Services_Twilio($tApikey, $tApiToken);
                    $client->account->messages->create(array(
                        'To' => $toNumber,
                        'From' => $tApiNumber,
                        'Body' => $message,
                    ));
                }
            }
        }
    }

    function printdemo($printer_name = "smb://192.168.0.251/JetDirect") {
        App::import('Vendor', 'escpos', array('file' => 'escpos' . DS . 'Escpos.php'));
        $connector = new NetworkPrintConnector("192.168.0.251", 9100);
        $printer = new Escpos($connector);
        $printer->text("Hello World!\n Good Job");
        $printer->cut();
        $printer->close();
        die;
    }

    function PrintReceipt($printer_name = "smb://192.168.0.251/JetDirect") {
        App::import('Vendor', 'escpos', array('file' => 'escpos' . DS . 'Escpos.php'));
        App::import('Component', 'Item');
        /* Information for the receipt */
        $items = array(
            new Item("Example item #1", "4.00"),
            new Item("Another thing", "3.50"),
            new Item("Something else", "1.00"),
            new Item("A final item", "4.45"),
        );
        $subtotal = new Item('Subtotal', '12.95');
        $tax = new Item('A local tax', '1.30');
        $total = new Item('Total', '14.25', true);
        /* Print top logo */
        /* Name of shop */
        $printer->selectPrintMode(Escpos::MODE_DOUBLE_WIDTH);
        $printer->text("ExampleMart Ltd.\n");
        $printer->selectPrintMode();
        $printer->text("Shop No. 42.\n");
        $printer->feed();

        /* Title of receipt */
        $printer->setEmphasis(true);
        $printer->text("SALES INVOICE\n");
        $printer->setEmphasis(false);

        /* Items */
        $printer->setJustification(Escpos::JUSTIFY_LEFT);
        $printer->setEmphasis(true);
        $printer->text(new item('', '$'));
        $printer->setEmphasis(false);
        foreach ($items as $item) {
            $printer->text($item);
        }
        $printer->setEmphasis(true);
        $printer->text($subtotal);
        $printer->setEmphasis(false);
        $printer->feed();

        /* Tax and total */
        $printer->text($tax);
        $printer->selectPrintMode(Escpos::MODE_DOUBLE_WIDTH);
        $printer->text($total);
        $printer->selectPrintMode();

        /* Footer */
        $printer->feed(2);
        $printer->setJustification(Escpos::JUSTIFY_CENTER);
        $printer->text("Thank you for shopping at ExampleMart\n");
        $printer->text("For trading hours, please visit example.com\n");
        $printer->feed(2);
        $printer->text(date('l jS \of F Y h:i:s A') . "\n");

        /* Cut the receipt and open the cash drawer */
        $printer->cut();
        $printer->close();
    }

    /* ---------------------------------------------
      Function name:emailToMerchant
      Description:For Email to Merchant
      ----------------------------------------------- */

    public function emailToMerchant($email = null, $name = null, $roleId = null, $pwd = null) {
        if (!empty($email)) {
            App::import('Model', 'MainSiteSetting');
            $this->MainSiteSetting = new MainSiteSetting();
            $superEmail = $this->MainSiteSetting->getSiteSettings();
            App::import('Model', 'EmailTemplate');
            $this->EmailTemplate = new EmailTemplate();
            $template_type = 'new_merchant';
            $emailTemplate = $this->EmailTemplate->storeAdminTemplates($roleId, $template_type);
            $randomCode = $pwd;
            if ($emailTemplate) {
                if ($name) {
                    $fullName1 = $name;
                    $fullName = ucfirst($fullName1);
                }
                $userName = $email;
                $emailData = $emailTemplate['EmailTemplate']['template_message'];
                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                $emailData = str_replace('{USERNAME}', $userName, $emailData);
                $emailData = str_replace('{PASSWORD}', $randomCode, $emailData);
                $activationLink = HTTP_ROOT . 'hq/login';
                $emailData = str_replace('{ACTIVE_LINK}', $activationLink, $emailData);
                $subject = ucwords(str_replace('_', ' ', $emailTemplate['EmailTemplate']['template_subject']));
                $this->Email->to = $email;
                $this->Email->subject = $subject;
                $this->Email->from = $superEmail['MainSiteSetting']['super_email'];
                $this->set('data', $emailData);
                $this->Email->template = 'template';
                $this->Email->smtpOptions = array(
                    'port' => "$this->smtp_port",
                    'timeout' => '30',
                    'host' => "$this->smtp_host",
                    'username' => "$this->smtp_username",
                    'password' => "$this->smtp_password"
                );
                $this->Email->sendAs = 'html'; // because we like to send pretty mail
                try {
                    $this->Email->send();
                } catch (Exception $e) {
                    
                }
            }
        }
    }

    /* ---------------------------------------------
      Function name:emailToStore
      Description:For Email to Store
      ----------------------------------------------- */

    public function emailToStore($email = null, $name = null, $roleId = null, $pwd = null, $url) {
        if (!empty($email)) {
            App::import('Model', 'MainSiteSetting');
            $this->MainSiteSetting = new MainSiteSetting();
            $superEmail = $this->MainSiteSetting->getSiteSettings();
            App::import('Model', 'EmailTemplate');
            $this->EmailTemplate = new EmailTemplate();
            $template_type = 'new_store';
            $emailTemplate = $this->EmailTemplate->storeAdminTemplates($roleId, $template_type);
            $randomCode = $pwd;
            if ($emailTemplate) {
                if ($name) {
                    $fullName1 = $name;
                    $fullName = ucfirst($fullName1);
                }
                $userName = $email;
                $emailData = $emailTemplate['EmailTemplate']['template_message'];
                $emailData = str_replace('{FULL_NAME}', $fullName, $emailData);
                $emailData = str_replace('{USERNAME}', $userName, $emailData);
                $emailData = str_replace('{PASSWORD}', $randomCode, $emailData);
                $activationLink = HTTP_ROOT . $url . '/admin';
                $emailData = str_replace('{ACTIVE_LINK}', $activationLink, $emailData);
                $subject = ucwords(str_replace('_', ' ', $emailTemplate['EmailTemplate']['template_subject']));
                $this->Email->to = $email;
                $this->Email->subject = $subject;
                $this->Email->from = $superEmail['MainSiteSetting']['super_email'];
                $this->set('data', $emailData);
                $this->Email->template = 'template';
                $this->Email->smtpOptions = array(
                    'port' => "$this->smtp_port",
                    'timeout' => '30',
                    'host' => "$this->smtp_host",
                    'username' => "$this->smtp_username",
                    'password' => "$this->smtp_password"
                );
                $this->Email->sendAs = 'html'; // because we like to send pretty mail
                try {
                    $this->Email->send();
                } catch (Exception $e) {
                    
                }
            }
        }
    }

    function storeTimezone($timezoneId = null, $dateToconvert = null, $type = null) {
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $storeId = $this->Session->read('admin_store_id');
        $timezone = date_default_timezone_get(); //get server time zone
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));
        if ($this->Session->check('admin_time_zone_id')) {
            $storeadmintimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $this->Session->read('admin_time_zone_id')), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));
            $servertime = date("d-m-Y h:i:s A");
            date_default_timezone_set("GMT");
            $gmtTime = date("d-m-Y h:i:s A");
            $diff1 = (strtotime($gmtTime) - strtotime($servertime));
            date_default_timezone_set($storeadmintimezone['TimeZone']['code']);
            $requiredTime = date("d-m-Y h:i:s A");
            $diff2 = (strtotime($requiredTime) - strtotime($gmtTime));
            date_default_timezone_set($timezone);
            $dateToconvert = str_replace('/', '-', $dateToconvert);
            $dateToconvert = date_format(new DateTime($dateToconvert), "d-m-Y h:i:s A");
            $add = ($diff1) + ($diff2);
            $var = strtotime($dateToconvert) + $add;
            if (!empty($type)) {
                $dateToconvert = date("H:i:s", $var);
            } else {
                $dateToconvert = date("Y-m-d H:i:s", $var);
            }
            if ($storeInfo['Store']['time_formate'] == 1 && $type != '') {
                $dateToconvert = date("h:i:s a", $var);
            } else if ($storeInfo['Store']['time_formate'] == 0 && $type != '') {
                $dateToconvert = date("H:i:s", $var);
            } else if ($storeInfo['Store']['time_formate'] == 1) {
                $dateToconvert = date("Y-m-d h:i:s a", $var);
            }
        }
        return $dateToconvert;
    }

    function storeTimeZoneUser($timezoneId = null, $dateToconvert = null) {
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $storeId = $this->Session->read('store_id');
        $timezone = date_default_timezone_get(); //get server time zone
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));
        if ($this->Session->check('front_time_zone_id')) {
            $storefronttimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $this->Session->read('front_time_zone_id')), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));
            $servertime = date("d-m-Y h:i:s A");
            date_default_timezone_set("GMT");
            $gmtTime = date("d-m-Y h:i:s A");
            $diff1 = (strtotime($gmtTime) - strtotime($servertime));
            date_default_timezone_set($storefronttimezone['TimeZone']['code']);
            $requiredTime = date("d-m-Y h:i:s A");

            $diff2 = (strtotime($requiredTime) - strtotime($gmtTime));
            date_default_timezone_set($timezone);

            $dateToconvert = str_replace('/', '-', $dateToconvert);
            $dateToconvert = date_format(new DateTime($dateToconvert), "Y-m-d h:i:s A");
            $add = ($diff1) + ($diff2);
            $var = strtotime($dateToconvert) + $add;
            $dateToconvert = date("Y-m-d H:i:s", $var);
            if ($storeInfo['Store']['time_formate'] == 1) {
                $dateToconvert = date("Y-m-d h:i:s a", $var);
            }
        }
        return $dateToconvert;
    }

    function storeTimeFormate($timeToconvert = null, $withDate = null) {
        if (!$this->Session->check('admin_store_id')) {
            return $timeToconvert;
        }
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = $this->Session->read('admin_store_id');
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate')));
        if ($withDate) {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("n/j/Y g:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("n/j/Y G:i", (strtotime($timeToconvert)));
            }
        } else {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("g:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("G:i", (strtotime($timeToconvert)));
            }
        }
        return $timeToconvert;
    }

    function storeTimeFormateUser($timeToconvert = null, $withDate = null, $storeId = null) {
        if (!$this->Session->check('store_id') && empty($storeId)) {
            return $timeToconvert;
        }
        App::import('Model', 'Store');
        $this->Store = new Store();
        $storeId = ($this->Session->read('store_id')) ? $this->Session->read('store_id') : $storeId;
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.time_formate')));
        if ($withDate) {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("n/j/Y g:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("n/j/Y G:i", (strtotime($timeToconvert)));
            }
        } else {
            if ($storeInfo['Store']['time_formate'] == 1) {
                $timeToconvert = date("g:i a", (strtotime($timeToconvert)));
            } else {
                $timeToconvert = date("G:i", (strtotime($timeToconvert)));
            }
        }

        return $timeToconvert;
    }

    function trimValue($beforeTrim = null) {
        if (!empty($beforeTrim)) {
            if (is_array($beforeTrim)) {
                foreach ($beforeTrim as $key => $value) {
                    if (is_array($value)) {
                        $beforeTrim[$key] = $this->trimValue($value);
                    } else {
                        $beforeTrim[$key] = trim($value);
                    }
                }
            } else {
                $beforeTrim = trim($beforeTrim);
            }
            return $beforeTrim;
        } else {
            return $beforeTrim;
        }
    }

    function spaceToHtml($text) {
        if ($text)
            return $text;
        else
            return '&nbsp;';
    }

    function storeToServerTimeZone($timezoneId = null, $dateToconvert = null, $type = null) {
        if (!$this->Session->check('Auth.Admin.role_id')) {
            return $dateToconvert;
        }
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();
        $storeId = $this->Session->read('admin_store_id');
        $timezone = date_default_timezone_get(); //get server time zone
        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));
        if (!empty($timezoneId)) {
            //$serverTimezoneInfo = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $timezoneId), 'fields' => array('TimeZone.difference_in_seconds'), 'recursive' => -1));
            $storeadmintimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $this->Session->read('admin_time_zone_id')), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));
            date_default_timezone_set("GMT");
            $gmtTime = date("d-m-Y h:i:s A");
            $diff1 = strtotime($gmtTime) + $storeadmintimezone['TimeZone']['difference_in_seconds'];
            $StoreCurrentTime = date("d-m-Y h:i:s A", $diff1);
            $diff1 = (strtotime($gmtTime) - strtotime($StoreCurrentTime));

            date_default_timezone_set($storeadmintimezone['TimeZone']['code']);
            $requiredTime = date("d-m-Y h:i:s A");
            $diff2 = (strtotime($requiredTime) - strtotime($gmtTime));

            date_default_timezone_set($timezone);
            $dateToconvert = str_replace('/', '-', $dateToconvert);
            $dateToconvert = date_format(new DateTime($dateToconvert), "d-m-Y h:i:s A");
            $add = ($diff1) + ($diff2);
            $var = strtotime($dateToconvert) + $add;

            if ($storeInfo['Store']['dst'] == 1) {
                $dateToconvert = date("H:i:s", $var + 3600);
            } else {
                $dateToconvert = date("H:i:s", $var);
            }
        }
        return $dateToconvert;
    }

    function getNextDayTimeRange($currentdate = null, $today = 0, $orderType = 1, $withinfunc = false, $merchant = null) {
        App::import('Model', 'Store');
        $this->Store = new Store();
        if (!empty($merchant)) {
            $decrypt_storeId = $merchant['store_id'];
            $decrypt_merchantId = $merchant['merchant_id'];
        } else {
            $decrypt_storeId = $this->Session->read('store_id');
            $decrypt_merchantId = $this->Session->read('merchant_id');
        }

        $checkStoreDays = $this->checkStoreDays($decrypt_storeId);
        if (!$checkStoreDays) {
            return FALSE;
        }

        $date = new DateTime($currentdate);
        $current_day = $date->format('l');
        $this->Store->bindModel(
                array(
                    'hasMany' => array(
                        'StoreAvailability' => array(
                            'className' => 'StoreAvailability',
                            'foreignKey' => 'store_id',
                            'conditions' => array('StoreAvailability.day_name' => $current_day, 'StoreAvailability.is_deleted' => 0, 'StoreAvailability.is_active' => 1, 'is_closed' => 0),
                            'fields' => array('id', 'start_time', 'end_time', 'day_name')
                        )
                    )
                )
        );
        $store_data = $this->Store->fetchStoreDetail($decrypt_storeId, $decrypt_merchantId);
        $daysdiff = 0;
        if ($withinfunc == false) {
            $currentTime = date("Y-m-d H:i:s", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
            $curTimearr = explode(' ', $currentTime);
            $currentDay = $curTimearr[0];
            $strCurTime = strtotime($currentTime);
            $storedelayTime = $this->Store->getDelayTime($decrypt_storeId);
            if ($orderType == 2) {
                $currentdelayTime = date('Y-m-d', strtotime('+' . $storedelayTime['Store']['pick_up_delay'] . 'minutes', $strCurTime));
            } elseif ($orderType == 3) {
                $currentdelayTime = date('Y-m-d', strtotime('+' . $storedelayTime['Store']['delivery_delay'] . 'minutes', $strCurTime));
            } else {
                $currentdelayTime = date('Y-m-d', $strCurTime);
            }

            //$datediff = strtotime($currentdelayTime) - strtotime($currentDay);
            $date1 = date_create($currentdelayTime);
            $date2 = date_create($currentDay);
            $diff = date_diff($date1, $date2);
            $daysdiff = $diff->days;
        }

        $storeHoliday = ClassRegistry::init('StoreHoliday');
        $holidayList = $storeHoliday->getStoreHolidaylistDate($decrypt_storeId, $currentdate);
        if (!empty($holidayList)) {
            $nextDate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
            $today = 0;
            $finaldata = $this->getNextDayTimeRange($nextDate, $today, $orderType, true);
            return $finaldata;
        }

        if (empty($store_data['StoreAvailability']) || $daysdiff) {
            $nextDate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
            $today = 0;
            $finaldata = $this->getNextDayTimeRange($nextDate, $today, $orderType, true);
            return $finaldata;
        } else {

            $current_array = array();
            $time_break = array();
            $storeBreak = array();
            $time_range = array();
            $finaldata = array();

            $start = $store_data['StoreAvailability'][0]['start_time'];
            $end = $store_data['StoreAvailability'][0]['end_time'];
            $cutTime = '-' . $store_data['Store']['cutoff_time'] . ' minutes';
            $end = date("H:i:s", strtotime("$cutTime", strtotime($end)));
            $start = $this->getStartTime($start, $today, $orderType, 1, $end);

            App::import('Model', 'StoreBreak');
            $this->StoreBreak = new StoreBreak();
            $store_break = $this->StoreBreak->fetchStoreBreak($store_data['Store']['id'], $store_data['StoreAvailability'][0]['id']);
            $storeBreakTime = $store_break;
            $start = $this->checkbreakTime($start, $store_data, $storeBreakTime, $orderType);

            if (!$start) {
                $currentdate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
                $today = 0;
                $finaldata = $this->getNextDayTimeRange($currentdate, $today, $orderType, true);
                return $finaldata;
            }


            $time_ranges = $this->getStoreTime($start, $end, $orderType, $store_data, $storeBreakTime); // calling Common Component

            $current_array = $time_ranges;
            if (empty($current_array) || !$start) {
                $currentdate = date('Y-m-d', strtotime('+1 day', strtotime($currentdate)));
                $today = 0;
                $finaldata = $this->getNextDayTimeRange($currentdate, $today, $orderType, true);
                return $finaldata;
            }
            $finaldata['currentdate'] = $currentdate;
            if ($store_data['Store']['is_break_time'] == 1) {
                $time_break1 = array();
                $time_break2 = array();
                if (!empty($store_data['Store']['is_break_time']) && $store_data['Store']['is_break1'] == 1) {
                    $break_start_time = $store_break['StoreBreak']['break1_start_time'];
                    $break_end_time = $store_break['StoreBreak']['break1_end_time'];
                    $storeBreak[0]['start'] = $store_break['StoreBreak']['break1_start_time'];
                    $storeBreak[0]['end'] = $store_break['StoreBreak']['break1_end_time'];
                    $time_break1 = $this->getStoreTime($break_start_time, $break_end_time);
                }
                if (!empty($store_data['Store']['is_break_time']) && $store_data['Store']['is_break2'] == 1) {
                    $break_start_time = $store_break['StoreBreak']['break2_start_time'];
                    $break_end_time = $store_break['StoreBreak']['break2_end_time'];
                    $storeBreak[1]['start'] = $store_break['StoreBreak']['break2_start_time'];
                    $storeBreak[1]['end'] = $store_break['StoreBreak']['break2_end_time'];
                    $time_break2 = $this->getStoreTime($break_start_time, $break_end_time);
                }
                $time_break = array_unique(array_merge($time_break1, $time_break2), SORT_REGULAR);
            }
            $time_range = array_diff($current_array, $time_break);
            $time_range = $current_array;
            $current_date = date("Y-m-d H:i:s", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
            $CurrentDateTocheck = explode(" ", $current_date);
            $avalibilty_status = $this->checkStoreAvalibility($decrypt_storeId, $CurrentDateTocheck[0], $CurrentDateTocheck[1]);
            if ($avalibilty_status != 1) {
                $setPre = 1;
            } else {
                $setPre = 0;
            }
            $finaldata['setPre'] = $setPre;
            $finaldata['time_break'] = $time_break;
            $finaldata['store_data'] = $store_data;
            $finaldata['storeBreak'] = $storeBreak;
            $finaldata['time_range'] = $time_range;
            return $finaldata;
        }
    }

    function getIntervalName($intervalid = null) {
        App::import('Model', 'Interval');
        $this->Interval = new Interval();
        $intervalName = $this->Interval->getIntervalName($intervalid);
        return $intervalName;
    }

    public function checkbreakTime($start, $store_data, $storeBT, $orderType) {
        //Check Order Type (i.e. Delivery/Pick Up)
        $deliveryInterval = 0;
        if ($orderType == 3) {
            $deliveryInterval = !empty($store_data['Store']['delivery_delay']) ? $store_data['Store']['delivery_delay'] : 0;
        } else if ($orderType == 2) {
            $deliveryInterval = !empty($store_data['Store']['pick_up_delay']) ? $store_data['Store']['pick_up_delay'] : 0;
        } else if ($orderType == 1) {
            $deliveryInterval = 00;
        }

        $currST = date("H:i", strtotime($start) - ($deliveryInterval * 60));
        if (!empty($store_data['Store']['is_break_time']) && $store_data['Store']['is_break1'] == 1) {
            if (strtotime($currST) > strtotime($storeBT['StoreBreak']['break1_start_time']) && strtotime($currST) < strtotime($storeBT['StoreBreak']['break1_end_time'])) {
                return $storeBT['StoreBreak']['break1_end_time'];
            }
        }
        if (!empty($store_data['Store']['is_break_time']) && $store_data['Store']['is_break2'] == 1) {
            if (strtotime($currST) > strtotime($storeBT['StoreBreak']['break2_start_time']) && strtotime($currST) < strtotime($storeBT['StoreBreak']['break2_end_time'])) {
                return $storeBT['StoreBreak']['break2_end_time'];
            }
        }
        return $start;
    }

    public function getaddonSize($sizeid = null) {
        App::import('Model', 'AddonSize');
        $this->AddonSize = new AddonSize();
        $storeId = $this->Session->read('store_id');
        $Sizedetail = $this->AddonSize->getAddonSizeDetail($sizeid, $storeId);
        return $Sizedetail;
    }

    /* ---------------------------------------------
      Function name:checkImageExtensionAndSize
      Description:To verify Image Extensions and size
      ----------------------------------------------- */

    function checkImageExtensionAndSize($datas) {
        $errorType = $errorSize = 0;
        if (!empty($datas)) {
            foreach ($datas['image'] as $image) {
                if (!empty($image['name'])) {
                    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                    $extarr = array('jpg', 'gif', 'jpeg', 'png');
                    $extension = strtolower($extension);
                    if (in_array($extension, $extarr)) {
                        $maxsize = 2097152;             //In Byte//2MB
                        $actualSize = $image['size'];
                        if (($actualSize > $maxsize) || $image['error'] == "1") {
                            $errorSize++;
                        }
                    } else {
                        $errorType++;
                    }
                }
            }
            
            if($errorType > 0)
            {
                $response['errmsg'] = "Only jpg,gif,jpeg,png type images are allowed";
                $response['status'] = false;
                return $response;
            }
            
            if($errorSize > 0)
            {
                $response['errmsg'] = "The image you are trying to upload is too large. Please limit the file size upto 2MB.";
                $response['status'] = false;
                return $response;
            }
            if($errorType == 0 && $errorSize == 0)
            {
                $response['status'] = true;
                return $response;
            }
        }
    }

    /* ------------------------------------------------
      Function name:getStoreTimeAdmin()
      Description: return time range array for admin
      created:10/2/2016
      ----------------------------------------------------- */

    function getStoreTimeForHq($startTime = null, $endTime = null) {
        $tStart = strtotime($startTime);
        $tEnd = strtotime($endTime);
        $tNow = $tStart;
        $timeRange = array();
        while ($tNow <= $tEnd) {
            $intervalTime = date("H:i", $tNow);
            $intervalTimeforValue = date("H:i:s", $tNow);
            if ($intervalTime == "00:00" || $intervalTimeforValue == "00:00:00" || $intervalTime == "00:00:00") {
                $intervalTime = strtotime("23:59");
                $intervalTime = date("H:i", $intervalTime);
                $intervalTimeforValue = "23:59:00";
            }
            $timeRange[$intervalTimeforValue] = $intervalTime;
            $tNow = strtotime('+15 minutes', $tNow);
        }
        return $timeRange;
    }

    /* ------------------------------------------------
      Function name:getloatlong()
      Description: return lat long of address
      created:10/2/2016
      ----------------------------------------------------- */

    function getlatLong($address, $city, $state, $zipcode) {
        $latitude = "";
        $longitude = "";
        $dlocation = trim($address) . " " . trim($city) . " " . trim($state) . " " . trim($zipcode);
        $address2 = str_replace(' ', '+', $dlocation);
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.GOOGLE_GEOMAP_API_KEY.'&address=' . $address2 . '&sensor=false');
        $output = json_decode($geocode);
        if ($output->status == "ZERO_RESULTS" || $output->status != "OK") {
            
        } else {
            $latitude = @$output->results[0]->geometry->location->lat;
            $longitude = @$output->results[0]->geometry->location->lng;
        }
    }

    /* ------------------------------------------------
      Function name:checkpolygon()
      Description: Check pint exists in polygon or not
      created:10/2/2016
      ----------------------------------------------------- */

    function checkpolygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y) {
        $i = $j = $c = 0;
        for ($i = 0, $j = $points_polygon; $i < $points_polygon; $j = $i++) {
            if ((($vertices_y[$i] > $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
                    ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i])))
                $c = !$c;
        }
        return $c;
    }

    /* ------------------------------------------------
      Function name:checkpolygon()
      Description: Check pint exists in polygon or not
      created:10/2/2016
      ----------------------------------------------------- */

    function getzonescoords($storeId=null) {
        App::import('Model', 'Zone');
        $this->Zone = new Zone();
        if (empty($storeId)) {
            $storeId = $this->Session->read('store_id');
        }
        
        $zoneCoords = $this->Zone->getzones($storeId);
        return $zoneCoords;
    }

    function setZonefee($DelAddress = null, $storeID = null, $type = "web") {
        if (empty($storeID)) {
            $storeID = $this->Session->read('store_id');
        }
        if (!empty($DelAddress['DeliveryAddress']['longitude']) && $DelAddress['DeliveryAddress']['longitude'] != 0 && $DelAddress['DeliveryAddress']['latitude'] != 0) {
            $this->Session->delete('Zone');
            App::import('Model', 'Store');
            $this->Store = new Store();
            $storeLatlng = $this->Store->findById($storeID, array('latitude', 'logitude', 'delivery_zone_type'));
            if (!empty($storeLatlng) && $storeLatlng['Store']['delivery_zone_type'] == 1) {
                $zoneCords = $this->getzonescoords($storeID);
                if (!empty($zoneCords)) {
                    foreach ($zoneCords as $key => $cords) {
                        foreach ($cords['ZoneCoordinate'] as $zcords) {
                            $polygon[$key][] = array('lat' => $zcords['lat'], 'long' => $zcords['long']);
                        }

                        $checkcord = $this->pointInPolygon(array('lat' => $DelAddress['DeliveryAddress']['latitude'], 'long' => $DelAddress['DeliveryAddress']['longitude']), $polygon[$key]);

                        if ($checkcord) {
                            App::import('Model', 'Zone');
                            $this->Zone = new Zone();
                            $zoneInfo = $this->Zone->getzoneinfo($zcords['zone_id']);
                            if (!empty($zoneInfo)) {
                                if ($type == 'mob') {
                                    return $zoneInfo;
                                } else {
                                    $this->Session->write('Zone.id', $zoneInfo['Zone']['id']);
                                    $this->Session->write('Zone.name', $zoneInfo['Zone']['name']);
                                    $this->Session->write('Zone.fee', $zoneInfo['Zone']['fee']);
                                }
                            }
                            break;
                        }
                    }
                }
            } else if (!empty($storeLatlng['Store']['latitude']) && !empty($storeLatlng['Store']['logitude']) && $storeLatlng['Store']['delivery_zone_type'] == 2) {
                App::import('Model', 'Zone');
                $this->Zone = new Zone();

                //echo $DelAddress['DeliveryAddress']['latitude']."".$DelAddress['DeliveryAddress']['longitude']."".$storeLatlng['Store']['latitude']."".$storeLatlng['Store']['logitude'];
                $distance = $this->getDistance($DelAddress['DeliveryAddress']['latitude'], $DelAddress['DeliveryAddress']['longitude'], $storeLatlng['Store']['latitude'], $storeLatlng['Store']['logitude']);
                $zoneInfo = $this->Zone->find('all', array('conditions' => array('store_id' => $storeID, 'is_active' => 1, 'is_deleted' => 0, 'type' => 1), 'fields' => array('id', 'distance', 'fee', 'name'), 'order' => array('distance' => 'ASC')));
                foreach ($zoneInfo as $zone) {
                    $zoneDistance = $zone['Zone']['distance'] / 1000;
                    if ($distance <= $zoneDistance) {
                        if ($type == 'mob') {
                            return $zone;
                        } else {
                            $this->Session->write('Zone.id', $zone['Zone']['id']);
                            $this->Session->write('Zone.name', $zone['Zone']['name']);
                            $this->Session->write('Zone.fee', $zone['Zone']['fee']);
                        }


                        break;
                    }
                }
            }
        } else {
            $this->Session->delete('Zone');
        }
    }

    /*
     * 1 - Y-m-d
     * 2 - H:i:s
     * 3 - Y-m-d H:i:s
     */

    function gettodayDate($format = 1) {
        if ($format == 1) {
            $currTimevar = date("Y-m-d", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        } elseif ($format == 2) {
            $currTimevar = date("H:i:s", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        } elseif ($format == 3) {
            $currTimevar = date("Y-m-d H:i:s", (strtotime($this->storeTimeZoneUser('', date('Y-m-d H:i:s')))));
        }

        return $currTimevar;
    }

    function sa_gettodayDate($format = 1) {
        if ($format == 1) {
            $currTimevar = date("Y-m-d", (strtotime($this->storeTimezone('', date('Y-m-d H:i:s')))));
        } elseif ($format == 2) {
            $currTimevar = date("H:i:s", (strtotime($this->storeTimezone('', date('Y-m-d H:i:s')))));
        } elseif ($format == 3) {
            $currTimevar = date("Y-m-d H:i:s", (strtotime($this->storeTimezone('', date('Y-m-d H:i:s')))));
        }
        return $currTimevar;
    }

    function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {
        $earth_radius = 6371;
        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;
        return $d;
    }

    function pointInPolygon($p, $polygon) {
        $c = 0;
        $p1 = $polygon[0];
        $n = count($polygon);

        for ($i = 1; $i <= $n; $i++) {
            $p2 = $polygon[$i % $n];
            if ($p['long'] > min($p1['long'], $p2['long']) && $p['long'] <= max($p1['long'], $p2['long']) && $p['lat'] <= max($p1['lat'], $p2['lat']) && $p1['long'] != $p2['long']) {
                $xinters = ($p['long'] - $p1['long']) * ($p2['lat'] - $p1['lat']) / ($p2['long'] - $p1['long']) + $p1['lat'];
                if ($p1['lat'] == $p2['lat'] || $p['lat'] <= $xinters) {
                    $c++;
                }
            }
            $p1 = $p2;
        }
        return $c % 2 != 0;
    }

    function RandomStringMobile($storeID = null, $server_offset, $interface = 'W') {
        //(WEB/Mobile)(Unique_Merchant_id)-(date('YYMMDD'))(OrderNumber_on day)
        App::import('Model', 'Store');
        $this->Store = new Store();
        $merchantno = '';
        $merchantNumber = $this->Store->getMerchantNumber($storeID);
        if (!empty($merchantNumber)) {
            $merchantno = $merchantNumber['Store']['merchant_number'];
        }

        $date = $this->getcurrentTime($storeID, 2);
        $ordernumber = $this->getOrderCount($server_offset, $date, $interface, $merchantno);
        return $ordernumber;
    }

    function reformatDate($date, $from_format = 'm-d-Y', $to_format = 'Y-m-d') {
        $date_aux = date_create_from_format($from_format, $date);
        return date_format($date_aux, $to_format);
    }

    public function getcurrentTime($storeId = null, $returnType = 1) {
        /*
         * 1- "Y-m-d h:i:s"
         * 2- "Y-m-d"
         * 3- "h:i:s"
         */
        $returnTime = null;
        $storeTimezoneInfo = array();
        App::import('Model', 'TimeZone');
        App::import('Model', 'Store');
        $this->TimeZone = new TimeZone();
        $this->Store = new Store();

        $storeInfo = $this->Store->find('first', array('conditions' => array("Store.id" => $storeId), 'fields' => array('Store.dst', 'Store.time_zone_id', 'Store.time_formate')));
        if (!empty($storeInfo['Store']['time_zone_id'])) {
            $storeTimezoneInfo = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $storeInfo['Store']['time_zone_id']), 'fields' => array('TimeZone.code'), 'recursive' => -1));
            date_default_timezone_set($storeTimezoneInfo['TimeZone']['code']);
            if ($returnType == 1) {
                $returnTime = date("Y-m-d h:i:s");
            } elseif ($returnType == 2) {
                $returnTime = date("Y-m-d");
            } else {
                $returnTime = date("h:i:s");
            }
        }
        return $returnTime;
    }

    public function getResize($height, $width, $newHeight, $newWidth, $filenamePass, $uploadPath) {

        $extension = $this->getExtension($uploadPath);
        $extension = strtolower($extension);

        //Scale To Ratio
        //   if($width >= $height){
        //      $newWidth = $new_dimension_pass;
        //      $newHeight = ($height/$width)*$new_dimension_pass;
        //   }
        //   else{
        //      $newHeight = $new_dimension_pass;
        //      $newWidth = ($width/$height)*$new_dimension_pass;
        //   }

        $imagePas = imagecreatetruecolor($newWidth, $newHeight);
        if ($extension == "jpg" || $extension == "jpeg") {
            $image = imagecreatefromjpeg($filenamePass);
            imagecopyresampled($imagePas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagejpeg($imagePas, $uploadPath, 100);
            return 1;
        } else if ($extension == "png") {
            $image = imagecreatefrompng($filenamePass);
            //imagecopyresampled($imagePas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            //imagepng($imagePas,$uploadPath ,9);

            imagealphablending($imagePas, false);
            imagesavealpha($imagePas, true);
            $transparent = imagecolorallocatealpha($imagePas, 255, 255, 255, 127);
            imagefilledrectangle($imagePas, 0, 0, $newWidth, $newHeight, $transparent);
            imagecopyresampled($imagePas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagepng($imagePas, $uploadPath);
            imagedestroy($imagePas);
            imagedestroy($image);
            return 1;
        } else {
            $image = imagecreatefromgif($filenamePass);
            imagecopyresampled($imagePas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagegif($imagePas, $uploadPath);
            return 1;
        }
        return 0;
    }

    function getExtension($strPass) {
        $ioata = strrpos($strPass, ".");
        if (!$ioata) {
            return "";
        }
        $lens = strlen($strPass) - $ioata;
        $exten = substr($strPass, $ioata + 1, $lens);
        return $exten;
    }

    /*
      To get order Fax format
     */
    public function getOrderFaxFormat($orderId = null, $store_id = null, $merchant_id = null)
    {
        App::import('Model', 'Store');
        $this->Store = new Store();
        if (isset($store_id) && !empty($store_id)) {
            $storeID = $store_id;
        } else {
            $storeID = $this->Session->read('store_id');
        }
        if (isset($merchant_id) && !empty($merchant_id)) {
            $merchantId = $merchant_id;
        } else {
            $merchantId = $this->Session->read('merchant_id');
        }


        $storeInfo = $this->Store->fetchStoreDetail($storeID);
        if ($orderId) {
            App::import('Model', 'OrderOffer');
            $this->OrderOffer = new OrderOffer();
            App::import('Model', 'OrderItem');
            $this->OrderItem = new OrderItem();
            App::import('Model', 'OrderPayment');
            $this->OrderPayment = new OrderPayment();
            App::import('Model', 'Order');
            $this->Order = new Order();
            App::import('Model', 'OrderPreference');
            $this->OrderPreference = new OrderPreference();
            App::import('Model', 'OrderTopping');
            $this->OrderTopping = new OrderTopping();
            App::import('Model', 'OrderItemFree');
            $this->OrderItemFree = new OrderItemFree();
            App::import('Model', 'Item');
            $this->Item = new Item();
            App::import('Model', 'ItemPrice');
            $this->ItemPrice = new ItemPrice();
            
            $this->Store->unbindModel(array('hasOne' => array('SocialMedia'), 'belongsTo' => array('StoreTheme'), 'hasMany' => array('StoreGallery', 'StoreContent')));
            $this->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id', 'topType')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id','size','price'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')))), false);
            $this->OrderItem->OrderPreference->bindModel(array('belongsTo' => array('SubPreference' => array('fields' => array('name', 'price')))), false);
            $this->OrderItem->OrderTopping->bindModel(array('belongsTo' => array('Topping' => array('className' => 'Topping', 'foreignKey' => 'topping_id', 'fields' => array('id', 'name', 'price')))), false);
            $this->OrderItem->OrderOffer->bindModel(array('belongsTo' => array('Item' => array('foreignKey' => 'offered_item_id', 'fields' => array('name')), 'Size' => array('className' => 'Size', 'foreignKey' => 'offered_size_id', 'fields' => array('id', 'size')), 'Offer' => array('className' => 'Offer', 'foreignKey' => 'offer_id', 'fields' => array('id', 'is_fixed_price', 'offerprice')), 'OfferDetail' => array('className' => 'OfferDetail', 'foreignKey' => 'offer_id', 'fields' => array('id', 'discountAmt')))), false);
            $this->Order->bindModel(array('hasMany' => array('OrderItemFree' => array('fields' => array('Order_id', 'item_id', 'free_quantity', 'price')))));
            $this->Order->bindModel(array('hasMany' => array('OrderItem' => array('fields' => array('id', 'quantity', 'item_id', 'size_id', 'type_id', 'total_item_price', 'discount', 'total_item_price', 'tax_price', 'interval_id', 'item_price'))), 'belongsTo' => array('Store' => array('fields' => array('id', 'service_fee', 'delivery_fee', 'store_name', 'store_url', 'address')), 'Segment' => array('className' => 'Segment', 'foreignKey' => 'seqment_id', 'fields' => array('name')), 'DeliveryAddress' => array('fields' => array('name_on_bell', 'city', 'address', 'phone', 'email')), 'OrderStatus' => array('fields' => array('name')), 'User' => array('foreignKey' => 'user_id', 'fields' => array('email', 'fname', 'lname', 'phone')), 'OrderPayment' => array('className' => 'OrderPayment', 'foreignKey' => 'payment_id', 'fields' => array('id', 'transection_id', 'amount', 'payment_gateway', 'last_digit')))), false);

            $this->Order->OrderItem->bindModel(array('hasMany' => array('OrderTopping' => array('fields' => array('id', 'topping_id', 'addon_size_id', 'topType', 'price'), 'order' => array('OrderTopping.id' => 'asc')), 'OrderOffer' => array('fields' => array('id', 'offered_item_id', 'offered_size_id', 'quantity', 'offer_id')), 'OrderPreference' => array('fields' => array('id', 'sub_preference_id', 'order_item_id','size','price'), 'order' => array('OrderPreference.id' => 'asc'))), 'belongsTo' => array('Item' => array('foreignKey' => 'item_id', 'fields' => array('id', 'name', 'category_id')), 'Type' => array('foreignKey' => 'type_id', 'fields' => array('name')), 'Size' => array('foreignKey' => 'size_id', 'fields' => array('size')), 'Category' => array('foreignKey' => 'category_id', 'fields' => array('Category.id', 'Category.name')))), false);
            
            
            $orderDetails = $this->Order->getfirstOrder($merchantId, $storeID, $orderId);
            $amount = 0;
            $itemPriceWidth = "";
            $flag = true;
            foreach ($orderDetails['OrderItem'] as $order) {
                if ($flag) {
                    if (!empty($order['OrderTopping'])) {
                        foreach ($order['OrderTopping'] as $key => $toppingarr) {
                            if (!empty($toppingarr['Topping']['name'])) {
                                $itemPriceWidth = "";
                            }
                        }
                        $flag = false;
                    } else {
                        if (!empty($order['OrderPreference'])) {
                            foreach ($order['OrderPreference'] as $key => $prearr) {
                                if (!empty($prearr['SubPreference']['name'])) {
                                    $itemPriceWidth = "";
                                }
                            }
                        }
                        $flag = false;
                    }
                }
            }

            $orderItemByCategory = array();
            foreach ($orderDetails['OrderItem'] as $order) {
                $categoryId = (isset($order['Item']['category_id']) ? $order['Item']['category_id'] : 0);
                if(array_key_exists($categoryId, $orderItemByCategory))
                {
                    $orderItemByCategory[$categoryId][] = $order;
                } else {
                    $orderItemByCategory[$categoryId][] = $order;
                }
            }
            
            
            $categoryByData= array();
            foreach ($orderItemByCategory as $orderItemKey => $orderItem)
            {
                $itemss = array();
                $categoryData = '';
                if (!empty($orderItemKey)) {
                    App::import('Model', 'Category');
                    $this->Category = new Category();
                    $catName = $this->Category->findById($orderItemKey, array('name'));
                    $categoryData = ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;">&nbsp;</td>'
                                    . '<td style="padding:1px 4px;" colspan="2"><strong>' . strtoupper($catName['Category']['name']) . '</strong></td>'
                                . '</tr>';
                }
                
                foreach ($orderItem as $order) {
                    $itemPrice = (isset($order['item_price']) ? $order['item_price'] : 0);
                    if (empty($order['OrderOffer'])) {
                        if (!empty($order['Size']['size'])) {
                            $sizestring = $order['Size']['size'] . "&nbsp;";
                        } else {
                            $sizestring = "";
                        }
                        $Interval = "";
                        if (isset($order['interval_id'])) {
                            $intervalId = $order['interval_id'];
                            $Interval = ($this->getIntervalName($intervalId)) ? "(" . $this->getIntervalName($intervalId) . ")" : '';
                        }

                        $tempitem = ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;">' . $order['quantity'] . '</td>'
                                    . '<td style="padding:1px 4px; width: 290px; font-size: 14px;">' . $sizestring . $order['Item']['name'] . '&nbsp;' . $Interval . '&nbsp;x&nbsp;' . $this->amount_format($itemPrice) . '</td>'
                                    . '<td style="padding:1px 4px;">' . $this->amount_format($itemPrice * $order['quantity']) . '</td>'
                                . '</tr>';

                        $toppingstr = "";
                        
                        $toppingArr = array();
                        if (!empty($order['OrderTopping'])) {
                            foreach ($order['OrderTopping'] as $key => $toppingarr) {
                                if(array_key_exists($toppingarr['topType'], $toppingArr))
                                {
                                    $toppingArr[$toppingarr['topType']][] = $toppingarr;
                                } else {
                                    $toppingArr[$toppingarr['topType']][] = $toppingarr;
                                }
                            }
                            
                            foreach ($toppingArr as $toppingArrKey => $toppingArrVal)
                            {
                                foreach ($toppingArrVal as $key => $toppingarr) {
                                    if (!empty($toppingarr['Topping']['name'])) {
                                        if($toppingArrKey == 'defaultTop' && $toppingarr['addon_size_id'] == 1)
                                        {
                                            $toppingarr['price']= 0.00;
                                        }
                                        
                                        $toppingPrice = $toppingarr['price'] * $toppingarr['addon_size_id'];
                                        $toppingPriceValue = $singleToppingPriceString = '';
                                        if($toppingPrice > 0)
                                        {
                                            $toppingPriceValue = $this->amount_format($toppingPrice  * $order['quantity']);
                                            $singleToppingPriceString = '&nbsp;x&nbsp;' . $this->amount_format($toppingPrice);
                                        }
                                        $toppingstr .= ''
                                        . '<tr>'
                                            . '<td style="padding:1px 4px;"> </td>'
                                            . '<td style="padding:1px 4px; width: 290px; font-size: 14px;">' . (($toppingarr['addon_size_id'] > 1) ? '+' . $toppingarr['addon_size_id'] . ' ' : '+1 ') . $toppingarr['Topping']['name'] . $singleToppingPriceString . '</td>'
                                            . '<td style="padding:1px 4px;">' . $toppingPriceValue . '</td>'
                                        . '</tr>';
                                    }
                                }
                            }
                        }
                        $preferencetr = "";
                        if (!empty($order['OrderPreference'])) {
                            foreach ($order['OrderPreference'] as $key => $prearr) {
                                if (!empty($prearr['SubPreference']['name'])) {
                                        $preferenceStrNew = (($prearr['size'] > 1) ? '+' . $prearr['size'] . ' ' : '+1 ') . $prearr['SubPreference']['name'];
                                        
                                        $preferencePrice = $prearr['price'] * $prearr['size'];
                                        $preferencePriceValue = $singlePrefferencePriceString = '';
                                        if($preferencePrice > 0)
                                        {
                                            $preferencePriceValue = $this->amount_format($preferencePrice * $order['quantity']);
                                            $singlePrefferencePriceString = '&nbsp;x&nbsp;' . $this->amount_format($preferencePrice);
                                        }
                                        $preferencetr .= ''
                                        . '<tr>'
                                            . '<td style="padding:1px 4px;"> </td>'
                                            . '<td style="padding:1px 4px; width: 290px; font-size: 14px;">' . $preferenceStrNew . $singlePrefferencePriceString . '</td>'
                                            . '<td style="padding:1px 4px;">' . $preferencePriceValue . '</td>'
                                        . '</tr>';
                                }
                            }
                        }
                        $topping = "";
                        if (!empty($toppingstr)) {
                            $topping = $toppingstr;
                        }
                        $preference = "";
                        if (!empty($preferencetr)) {
                            $preference = $preferencetr;
                        }
                        
                        $tempfinalprice = ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;"> </td>'
                                    . '<td style="padding:1px 4px; width: 290px;"> </td>'
                                    . '<td style="padding:1px 4px;"><strong>' . $this->amount_format($order['total_item_price']) . '</strong></td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td style="padding: 3px;"> </td>'
                                    . '<td style="padding: 3px; width: 290px;"> </td>'
                                    . '<td style="padding: 3px;"> </td>'
                                . '</tr>';
                        
                        
                        $itemss[] = $tempitem . $preference . $topping . $tempfinalprice;
                    } else {
                        $offerItemNameString = ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;"> </td>'
                                    . '<td style="padding:1px 4px; width: 290px; font-size: 14px;">Promotional Offer: </td>'
                                    . '<td style="padding:1px 4px;"> </td>'
                                . '</tr>';
                        foreach ($order['OrderOffer'] as $off) {
                            if (!empty($off['Size']['size'])) {
                                $offsizestring = $off['Size']['size'] . "&nbsp;";
                            } else {
                                $offsizestring = "";
                            }
                            
                            $offerPrice = 0;
                            $offerItemName = '';
                            if ($off['Offer']['is_fixed_price'] == 1) {
                                $offerType = 1;
                                if ($off['Offer']['offerprice'] == 0) {
                                    $offerPrice = 0;
                                    $rate = 0;
                                } else {
                                    $offerPrice = $off['Offer']['offerprice'];
                                    $rate = 0;
                                }
                            } elseif ($off['Offer']['is_fixed_price'] == 0) {
                                $offerType = 0;
                                if (!isset($off['OfferDetail']['discountAmt'])) {
                                    $offerPrice = $offerPrice + 0;
                                    $rate = 0;
                                } else {
                                    if ($off['OfferDetail']['discountAmt'] == 0) {
                                        $offerPrice = $offerPrice + 0;
                                        $rate = 0;
                                    } else {
                                        $offerPrice = $offerPrice + $off['OfferDetail']['discountAmt'];
                                        $rate = $off['OfferDetail']['discountAmt'];
                                    }
                                }   
                            }
                            if ($rate == 0) {
                                if ($offerType == 1) {
                                    $offerItemName = $off['quantity'] . ' X ' . $offsizestring . $off['Item']['name'];
                                } else {
                                    $offerItemName = $off['quantity'] . ' X ' . $offsizestring . $off['Item']['name'] . ' @ Free';
                                }
                            } else {
                                $offerItemName = $off['quantity'] . ' X ' . $offsizestring . $off['Item']['name'] . ' @ ' . $this->amount_format($rate);
                            }
                            
                            $offerItemNameString .= ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;"> </td>'
                                    . '<td style="padding:1px 4px; width: 290px; font-size: 14px;">' . $offerItemName . '</td>'
                                    . '<td style="padding:1px 4px;"> </td>'
                                . '</tr>';
                            
                            // For Offer Price
                            if($off['Offer']['is_fixed_price'])
                            {
                                $itemPrice = (!empty($off['Offer']['offerprice'])) ? $off['Offer']['offerprice'] : $itemPrice;
                            }
                        }

                        $toppingstr = "";
                        if (!empty($order['OrderTopping'])) {
                            foreach ($order['OrderTopping'] as $key => $toppingarr) {
                                if (!empty($toppingarr['Topping']['name'])) {
                                    $toppingStrNew = (($toppingarr['addon_size_id'] > 1) ? '+' . $toppingarr['addon_size_id'] . ' ' : '+1 ') . $toppingarr['Topping']['name'];
                                    
                                    $toppingPrice = $toppingarr['price'] * $toppingarr['addon_size_id'];
                                    $singleToppingPriceString = $toppingPriceValue = '';
                                    if($toppingPrice > 0)
                                    {
                                        $toppingPriceValue = $this->amount_format($toppingPrice * $order['quantity']);
                                        $singleToppingPriceString = '&nbsp;x&nbsp;' . $this->amount_format($toppingPrice);
                                    }
                                    
                                    $toppingstr .= ''
                                    . '<tr>'
                                        . '<td style="padding:1px 4px;"> </td>'
                                        . '<td style="padding:1px 4px; width: 290px; font-size: 14px;">' . $toppingStrNew . $singleToppingPriceString . '</td>'
                                        . '<td style="padding:1px 4px;">' . $toppingPriceValue . '</td>'
                                    . '</tr>';
                                }
                            }
                        }

                        $preferencetr = "";
                        if (!empty($order['OrderPreference'])) {
                            foreach ($order['OrderPreference'] as $key => $prearr) {
                                if (!empty($prearr['SubPreference']['name'])) {
                                    
                                    $preferenceStrNew = (($prearr['size'] > 1) ? '+' . $prearr['size'] . ' ' : '+1 ') . $prearr['SubPreference']['name'];
                                    
                                    $preferencePrice = $prearr['price'] * $prearr['size'];
                                    $preferencePriceValue = $singlePrefferencePriceString = '';
                                    if($preferencePrice > 0)
                                    {
                                        $preferencePriceValue = $this->amount_format($preferencePrice * $order['quantity']);
                                        $singlePrefferencePriceString = '&nbsp;x&nbsp;' . $this->amount_format($preferencePrice);
                                    }
                                    $preferencetr .= ''
                                    . '<tr>'
                                        . '<td style="padding:1px 4px;"> </td>'
                                        . '<td style="padding:1px 4px; width: 290px; font-size: 14px;">' . $preferenceStrNew . $singlePrefferencePriceString . '</td>'
                                        . '<td style="padding:1px 4px;">' . $preferencePriceValue . '</td>'
                                    . '</tr>';
                                }
                            }
                        }
                        $topping = "";
                        if (!empty($toppingstr)) {
                            $topping = $toppingstr;
                        }
                        $preference = "";
                        if (!empty($preferencetr)) {
                            $preference = $preferencetr;
                        }
                        if (!empty($order['Size']['size'])) {
                            $osizestring = $order['Size']['size'] . "&nbsp;";
                        } else {
                            $osizestring = "";
                        }
                        
                        $Interval = "";
                        if (isset($order['interval_id'])) {
                            $intervalId = $order['interval_id'];
                            $Interval = ($this->getIntervalName($intervalId)) ? "(" . $this->getIntervalName($intervalId) . ")" : '';
                        }
                        $tempitem = ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;">' . $order['quantity'] . '</td>'
                                    . '<td style="padding:1px 4px; width: 290px; font-size: 14px;">' . $osizestring . $order['Item']['name'] . '&nbsp;' . $Interval . '&nbsp;x&nbsp;' . $this->amount_format($itemPrice) . '</td>'
                                    . '<td style="padding:1px 4px;">' . $this->amount_format($itemPrice * $order['quantity']) . '</td>'
                                . '</tr>';
                        
                        $tempfinalprice = ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;"> </td>'
                                    . '<td style="padding:1px 4px; width: 290px;"> </td>'
                                    . '<td style="padding:1px 4px;"><strong>' . $this->amount_format($order['total_item_price']) . '</strong></td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td style="padding: 3px;"> </td>'
                                    . '<td style="padding: 3px; width: 290px;"> </td>'
                                    . '<td style="padding: 3px;"> </td>'
                                . '</tr>';
                        $itemss[] = $tempitem . $preference . $topping . $offerItemNameString . $tempfinalprice;
                    }
                    $amount = $amount + $order['total_item_price'];
                }
                $categoryByData[$orderItemKey] = array('name' => $categoryData, 'items' => $itemss);
            }
            
            $printdata = ''
                . '<table style="width: 550px; border: none; margin :0 auto;">'
                    . '<tr>'
                        . '<td style="padding:1px 4px; width: 100%;">'
                            . '<table style="width: 100%; border: 1px solid #000000;">';
            
                            
            $createdTime = $this->storeTimeFormateUser($orderDetails['Order']['created'], true, $storeID);
            $eData = explode(" ", $createdTime);
            $date = $time = $am = '';
            if (!empty($eData[0])) {
                $date = $eData[0];
            }
            if (!empty($eData[1])) {
                $time = $eData[1];
            }
            if (!empty($eData[2])) {
                $am = $eData[2];
            }
            $printdata .= ''
                . '<tr>'
                    . '<td style="padding:1px 4px;" colspan="3">' . $date . '</td>'
                . '</tr>';
            

            //---- Start End-User info --------------------------
            if ($orderDetails['Order']['user_id'] == 0) {
                $enduser_name = $orderDetails['DeliveryAddress']['name_on_bell'];
                $enduser_phone = $orderDetails['DeliveryAddress']['phone'];
                $email = $orderDetails['DeliveryAddress']['email'];
            } else {
                $enduser_name = $orderDetails['User']['fname'] . ' ' . $orderDetails['User']['lname'];
                $enduser_phone = $orderDetails['User']['phone'];
                $email = $orderDetails['User']['email'];
            }
            
            $printdata .= ''
                . '<tr>'
                    . '<td style="padding:1px 4px;" colspan="3">' . $enduser_name . '</td>'
                . '</tr>';
            $printdata .= ''
                . '<tr>'
                    . '<td style="padding:1px 4px;" colspan="3"><a href="mailto:' . $email . '">' . $email . '</a></td>'
                . '</tr>';
            $printdata .= ''
                . '<tr>'
                    . '<td style="padding:1px 4px; width:268px;">' . $enduser_phone . '</td>'
                    . '<td style="padding:1px 4px; width:100px;"> </td>';
                            
            $address = '';
            if ($orderDetails['Order']['seqment_id'] != 2) {
                $printdata .='<td style="padding:1px 4px;">Delivery Date/Time</td>';
            } 
            else {
                $printdata .= '<td style="padding:1px 4px;">Pick up Date/Time:</td>';
            }
            $printdata .= ''
                . '</tr>';
            
            //---- End End-User info --------------------------------------

            $printdata.='<tr>'
                        . '<td style="padding:1px 4px; width:268px;"></br><strong>Online ' . $orderDetails['Segment']['name'] . '</strong></td>'
                        . '<td style="padding:1px 4px; width:100px;"> </td>'
                        . '<td style="padding:1px 4px;"></td>'
                    . '</tr>';
            if ($orderDetails['OrderPayment']['payment_gateway'] == 'COD') {
                if ($orderDetails['Order']['seqment_id'] == 3) {
                    $printdata .= ''
                            . '<tr>'
                                . '<td style="padding:1px 4px; width:268px;">Cash on Delivery - <b>UNPAID</b></td>';
                } else {
                    $printdata .= ''
                            . '<tr>'
                                .'<td style="padding:1px 4px; width:268px;">Cash on Pickup - <b>UNPAID</b></td>';
                }
            } else {
                $cardNumberString = '';
                if(isset($orderDetails['OrderPayment']['last_digit']) && !empty($orderDetails['OrderPayment']['last_digit']))
                {
                    $cardNumberString .= '<br/>Card Number: xxxxxxxxxxxx' . $orderDetails['OrderPayment']['last_digit'];
                }
                $printdata .= ''
                            . '<tr>'
                                .'<td style="padding:1px 4px; width:268px;"><b>PAID</b> by credit card (' . $orderDetails['OrderPayment']['payment_gateway'] . ')' . $cardNumberString . '</td>';
            }
            
            
            if ($orderDetails['Order']['seqment_id'] != 2) {
                
                $address = $orderDetails['DeliveryAddress']['address'] . " " . $orderDetails['DeliveryAddress']['city'];
                
                $pickuptime = '';
                $pickup_time = $this->storeTimeFormateUser($orderDetails['Order']['pickup_time'], true, $storeID);
                $pData = explode(" ", $pickup_time);
                $date = $time = $am = '';
                if (!empty($pData[0])) {
                    $date = $pData[0];
                }
                if (!empty($pData[1])) {
                    $time = $pData[1];
                }
                if (!empty($pData[2])) {
                    $am = $pData[2];
                }
                
                $printdata .= ''
                            . '<td style="padding:1px 4px; width:100px;"> </td>'
                            . '<td style="padding:1px 4px;">' . $date . '</td>'
                        . '</tr>'
                        . '<tr>'
                            . '<td style="padding:1px 4px; width:268px;">Order#: ' . $orderDetails['Order']['order_number'] . '</td>'
                            . '<td style="padding:1px 4px; width:100px;"> </td>'
                            . '<td style="padding:1px 4px;">' . $time . ' ' . $am .'</td>';
                
            } else {
                $pickuptime = $this->storeTimeFormateUser($orderDetails['Order']['pickup_time'], true, $storeID);
                $pData = explode(" ", $pickuptime);
                $date = $time = $am = '';
                if (!empty($pData[0])) {
                    $date = $pData[0];
                }
                if (!empty($pData[1])) {
                    $time = $pData[1];
                }
                if (!empty($pData[2])) {
                    $am = $pData[2];
                }
                $printdata .= ''
                            . '<td style="padding:1px 4px; width:100px;"> </td>'
                            . '<td style="padding:1px 4px;">' . $date . '</td>'
                        . '</tr>'
                        . '<tr>'
                            . '<td style="padding:1px 4px; width:268px;">Order#: ' . $orderDetails['Order']['order_number'] . '</td>'
                            . '<td style="padding:1px 4px; width:100px;"> </td>'
                            . '<td style="padding:1px 4px;">' . $time . ' ' . $am .'</td>';   
    }
            $printdata .= ''
                        . '</tr>';
            
            if($address != '')
            {
                $printdata .= ''
                        . '<tr>'
                            . '<td style="padding:1px 4px;" colspan="3">&nbsp;</td>'
                        . '</tr>'
                        . '<tr>'
                            . '<td style="padding:1px 4px;" colspan="3">' . $address . '</td>'
                        . '</tr>';
            }
                $printdata .= ''
                        . '</table>'
                    . '</td>'
                . '</tr>'
                . '<tr>'
                    . '<td style="padding:1px 4px; width: 100%;">'
                        . '<table style="width: 100%; border:1px solid #000000;" cellpadding="0" cellspacing="0">'
                        . '<tr>'
                            . '<td style="padding:1px 4px;" colspan="3"><strong>Order Detail</strong></td>'
                        . '</tr>'
                        . '<tr>'
                            . '<td style="padding:1px 4px; width: 94px;"><strong>Qty</strong></td>'
                            . '<td style="padding:1px 4px; width: 290px;"><strong>Item</strong></td>'
                            . '<td style="padding:1px 4px; width: 139px;"><strong>Price</strong></td>'
                        . '</tr>';
                foreach ($categoryByData as $categoryByDataVal)
                {
                    $printdata .= $categoryByDataVal['name'];
                    foreach ($categoryByDataVal['items'] as $dataItem) {    
                        $printdata .= $dataItem;
                    }
                    $printdata .= ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;" colspan="3">&nbsp;</td>'
                                . '</tr>';
                }
                $printdata .= ''
                            . '</table>'
                        . '</td>'
                    . '</tr>'
                    . '<tr>'
                        . '<td style="padding:1px 4px;">'
                            . '<table style="width:100%;border:1px solid #000000;">'
                                . '<tr>'
                                    . '<td style="padding:1px 4px; width:25%;">Sub-Total:</td>'
                                    . '<td style="padding:1px 4px; width: 246px;"> </td>'
                                    . '<td style="padding:1px 4px;">' . $this->amount_format($amount) . '</td>'
                                . '</tr>';
            if ($orderDetails['Order']['coupon_discount'] > 0) {
                $printdata .= ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px; width:25%;">Coupon Discount: ' . $orderDetails['Order']['coupon_code'] . '</td>'
                                    . '<td style="padding:1px 4px; width: 246px;"> </td>'
                                    . '<td style="padding:1px 4px;">' . $this->amount_format($orderDetails['Order']['coupon_discount']) . '</td>'
                                . '</tr>';
            }
            if (!empty($orderDetails['OrderItemFree'])) {
                $freeInt = 0;
                $freeItemArr = array();
                foreach ($orderDetails['OrderItemFree'] as $freeItem) {
                    $itemName = $this->Item->find('first', array('conditions' => array('Item.id' => $freeItem['item_id']), 'fields' => array('name')));
                    if (!empty($itemName['Item']['name'])) {
                        $printdata .= ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;" colspan="3">Free Item: ' . $freeItem['free_quantity'] . ' ' . $itemName['Item']['name'] . '</td>'
                                . '</tr>';
                    }
                }
            }
            if ($orderDetails['Order']['service_amount'] > 0) {
                $printdata .= ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px; width:25%;">Service Fee: </td>'
                                    . '<td style="padding:1px 4px; width: 246px;"> </td>'
                                    . '<td style="padding:1px 4px;">' . $this->amount_format($orderDetails['Order']['service_amount']) . '</td>'
                                . '</tr>';
            }
            if ($orderDetails['Order']['delivery_amount'] > 0) {
                $printdata .= ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px; width:25%;">Delivery Fee: </td>'
                                    . '<td style="padding:1px 4px; width: 246px;"> </td>'
                                    . '<td style="padding:1px 4px;">' . $this->amount_format($orderDetails['Order']['delivery_amount']) . '</td>'
                                . '</tr>';
            }
            if ($orderDetails['Order']['tax_price'] > 0) {
                $printdata .= ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px; width:25%;">Tax: </td>'
                                    . '<td style="padding:1px 4px; width: 246px;"> </td>'
                                    . '<td style="padding:1px 4px;">' . $this->amount_format($orderDetails['Order']['tax_price']) . '</td>'
                                . '</tr>';
            }
            $tipLabel = '';
            $tipAmount = '';
            if($orderDetails['Order']['tip_option'] == 0) {
                $tipLabel = 'No Tip';
                $tipAmount = '';
            } else if($orderDetails['Order']['tip_option'] == 1) {
                $tipLabel = 'Tip With Cash';
                $tipAmount = '';
            } else if($orderDetails['Order']['tip_option'] == 2) {
                $tipLabel = 'Tip With Card: ';
                $tipAmount = $this->amount_format($orderDetails['Order']['tip']);
            } else {
                $tipLabel = 'Tip % (' . $orderDetails['Order']['tip_percent'] . '%): ';
                $tipAmount = $this->amount_format($orderDetails['Order']['tip']);
            }
            $printdata .= ''
                            . '<tr>'
                                . '<td style="padding:1px 4px; width:25%;">' . $tipLabel . '</td>'
                                . '<td style="padding:1px 4px; width: 246px;"> </td>'
                                . '<td style="padding:1px 4px;">' . $tipAmount . '</td>'
                            . '</tr>';
            
            $printdata .= ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px; width:25%;"><strong>Total: </strong></td>'
                                    . '<td style="padding:1px 4px; width: 246px;"> </td>'
                                    . '<td style="padding:1px 4px;"><strong>' . $this->amount_format($orderDetails['Order']['amount'] - $orderDetails['Order']['coupon_discount']) . '</strong></td>'
                                . '</tr>';
            
            $printdata .= ''
                                . '<tr>'
                                    . '<td style="padding:1px 4px;" colspan="3">*Special Instruction*</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td style="padding:1px 4px;" colspan="3">&nbsp;</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td style="padding:1px 4px;" colspan="3">' . $orderDetails['Order']['order_comments'] . '</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td style="padding:1px 4px;" colspan="3">We kindly ask you not to reply to this e-mail but instead contact us via phone call.</td>'
                                . '</tr>'
                            . '</table>'
                        . '</td>'
                    . '</tr>'
                . '</table>';
            return $printdata;
        }
    }
    

    function currentStoreTime() {
        $storefronttimezone = $this->TimeZone->find('first', array('conditions' => array("TimeZone.id" => $this->Session->read('front_time_zone_id')), 'fields' => array('TimeZone.difference_in_seconds', 'TimeZone.code'), 'recursive' => -1));
        date_default_timezone_set($storefronttimezone['TimeZone']['code']);
        $currentTime = date_format(new DateTime(), "Y-m-d H:i:s");
        return $currentTime;
    }

    function addressInZone($DelAddress = null, $storeID = null, $type = null) {
        if (empty($storeID)) {
            $storeID = $this->Session->read('store_id');
        }
        if (!empty($DelAddress['DeliveryAddress']['longitude']) && $DelAddress['DeliveryAddress']['longitude'] != 0 && $DelAddress['DeliveryAddress']['latitude'] != 0) {
            $this->Session->delete('Zone');
            App::import('Model', 'Store');
            $this->Store = new Store();
            $storeLatlng = $this->Store->findById($storeID, array('latitude', 'logitude', 'delivery_zone_type'));
            if (!empty($storeLatlng) && $storeLatlng['Store']['delivery_zone_type'] == 1) {
                $zoneCords = $this->getzonescoords($storeID);
                if (!empty($zoneCords)) {
                    foreach ($zoneCords as $key => $cords) {
                        foreach ($cords['ZoneCoordinate'] as $zcords) {
                            $polygon[$key][] = array('lat' => $zcords['lat'], 'long' => $zcords['long']);
                        }

                        $checkcord = $this->pointInPolygon(array('lat' => $DelAddress['DeliveryAddress']['latitude'], 'long' => $DelAddress['DeliveryAddress']['longitude']), $polygon[$key]);

                        if ($checkcord) {
                            App::import('Model', 'Zone');
                            $this->Zone = new Zone();
                            $zoneInfo = $this->Zone->getzoneinfo($zcords['zone_id']);
                            if (!empty($zoneInfo)) {
                                if ($type == "mob") {
                                    return $zoneInfo;
                                }

                                return true;
                            }
                        }
                    }
                }
            } else if (!empty($storeLatlng['Store']['latitude']) && !empty($storeLatlng['Store']['logitude']) && $storeLatlng['Store']['delivery_zone_type'] == 2) {
                App::import('Model', 'Zone');
                $this->Zone = new Zone();
                $distance = $this->getDistance($DelAddress['DeliveryAddress']['latitude'], $DelAddress['DeliveryAddress']['longitude'], $storeLatlng['Store']['latitude'], $storeLatlng['Store']['logitude']);
                $zoneInfo = $this->Zone->find('all', array('conditions' => array('store_id' => $storeID, 'is_active' => 1, 'is_deleted' => 0, 'type' => 1), 'fields' => array('id', 'distance', 'fee', 'name'), 'order' => array('distance' => 'ASC')));
                if (!empty($zoneInfo)) {
                    foreach ($zoneInfo as $zone) {
                        $zoneDistance = $zone['Zone']['distance'] / 1000;
                        if ($distance <= $zoneDistance) {
                            if ($type == "mob") {
                                return $zone;
                            }
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    function getCustomerbyStateId($merchantID = null, $storeIds = array()) {
        App::import('Model', 'State');
        $this->State = new State();
        $this->State->bindModel(
                array(
            'hasMany' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'state_id',
                    'conditions' => array('User.merchant_id' => $merchantID, 'User.store_id' => $storeIds, 'User.role_id' => array(4, 5), 'User.is_deleted' => 0, 'User.is_active' => 1)
                )
            )
                ), false
        );
        $statesList = $this->State->find('all');
        $states = array();
        if (!empty($statesList)) {
            foreach ($statesList as $s => $listState) {
                if (!empty($listState['User'])) {
                    $states[$listState['State']['id']] = $listState['State']['name'];
                }
            }
        }
        return $states;
    }

    function getCustomerbyCityId($merchantID = null, $storeIds = array(), $stateId = null) {
        App::import('Model', 'City');
        $this->City = new City();
        $this->City->bindModel(
                array(
            'hasMany' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'city_id',
                    'conditions' => array('User.merchant_id' => $merchantID, 'User.store_id' => $storeIds, 'User.state_id' => $stateId, 'User.role_id' => array(4, 5), 'User.is_deleted' => 0, 'User.is_active' => 1)
                )
            )
                ), false
        );
        $cityList = $this->City->find('all', array('conditions' => array('City.state_id' => $stateId)));
        $city = array();
        if (!empty($cityList)) {
            foreach ($cityList as $c => $listCity) {
                if (!empty($listCity['User'])) {
                    $city[$listCity['City']['id']] = $listCity['City']['name'];
                }
            }
        }

        return $city;
    }

    function getCustomerbyZipId($merchantID = null, $storeIds = null, $stateId = array(), $cityId = null) {
        App::import('Model', 'Zip');
        $this->Zip = new Zip();
        $this->Zip->bindModel(
                array(
            'hasMany' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'zip_id',
                    'conditions' => array('User.merchant_id' => $merchantID, 'User.store_id' => $storeIds, 'User.state_id' => $stateId, 'User.city_id' => $cityId, 'User.role_id' => array(4, 5), 'User.is_deleted' => 0, 'User.is_active' => 1)
                )
            )
                ), false
        );
        $zipList = $this->Zip->find('all', array('fields' => array('id', 'zipcode'), 'conditions' => array('Zip.state_id' => $stateId, 'Zip.city_id' => $cityId)));
        $zip = array();
        if (!empty($zipList)) {
            foreach ($zipList as $z => $listZip) {
                if (!empty($listZip['User'])) {
                    $zip[$listZip['Zip']['id']] = $listZip['Zip']['zipcode'];
                }
            }
        }
        return $zip;
    }

    function checkStoreDays($storeIds = null) {
        App::import('Model', 'StoreAvailability');
        $this->StoreAvailability = new StoreAvailability();
        if (empty($storeIds)) {
            return FALSE;
        }
        $checkStoreDays = $this->StoreAvailability->find('all', array('conditions' => array('StoreAvailability.store_id' => $storeIds, 'StoreAvailability.is_active' => 1, 'StoreAvailability.is_closed' => 0, 'StoreAvailability.is_deleted' => 0)));
        if (!empty($checkStoreDays)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /* ------------------------------------------------
      Function name:storeHolidayCheck()
      Description:This function is used to check is store has Holiday or not on booking Date
      created:14/10/2016
      ----------------------------------------------------- */

    function storeHolidayCheck($storeId = null, $time = null, $date = null) {
        App::import('Model', 'StoreHoliday');
        $this->StoreHoliday = new StoreHoliday();
        $dateformat = explode("-", $date);
        $result = 0;
        $dateInt = $dateformat[0] . "-" . $dateformat[1] . "-" . $dateformat[2]; // date foramate Y-M-D
        $StoreHolidayTime = $this->StoreHoliday->find('count', array('conditions' => array('StoreHoliday.store_id' => $storeId, 'StoreHoliday.is_deleted' => 0, 'StoreHoliday.holiday_date' => $dateInt, 'StoreHoliday.is_active' => 1)));
        if ($StoreHolidayTime == 1) {
            $result = 1;
        } else {
            $result = 0;
        }
        return $result;
    }
    
    
    function deleteMultipleRecords($ids = null, $model = null) {
        if (!empty($ids) && !empty($model)) {
            App::import('Model', $model);
            $this->$model->recursive = -1;
            $this->$model = new $model();
            if ($this->$model->updateAll(array('is_deleted' => 1), array('id' => $ids))) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    
    //get HQ stores

    function getHQStores($merchantId = null) {
        if ($merchantId) {
            App::import('Model', 'Store');
            $this->Store = new Store();
            $merchantList = $this->Store->getMerchantStores($merchantId);
            return $merchantList;
        }
    }
    
    
    
    //change amount in 4 digits i.e. .9 = $00.90
    function amount_format($amount = null, $symbol = null) {
        if (strstr($amount, ',')) {
            $amount = str_replace(',', '', $amount);
        }
        $amount = number_format($amount, 2, '.', '');
        if ($amount > 0 && $amount < 1) {
            $amount = explode('.', $amount);
            $amount = '0.' . $amount[1];
        }
        if ($amount >= 1 && $amount <= 9) {
            $amount = $amount;
        }
        if (empty($symbol)) {
            $amount = '$' . $amount;
        }
        return $amount;
    }
    
    function orderItemDetail($orderId = null) {
        if ($orderId) {
            App::import('Model', 'OrderItem');
            $this->OrderItem = new OrderItem();
            $orderDetail = $this->OrderItem->find('all', array('conditions' => array('OrderItem.order_id' => $orderId)));
            return $orderDetail;
        }
    }

    function usedOfferDetailCount($orderId = null) {
        if ($orderId) {
            App::import('Model', 'OrderOffer');
            $this->OrderOffer = new OrderOffer();
            $orderOfferDetail = $this->OrderOffer->find('count', array('conditions' => array('OrderOffer.order_id' => $orderId)));
            return $orderOfferDetail;
        }
    }

    function usedItemOfferDetailCount($orderId = null) {
        if ($orderId) {
            App::import('Model', 'OrderItemFree');
            $this->OrderItemFree = new OrderItemFree();
            $orderItemFreeDetail = $this->OrderItemFree->find('count', array('conditions' => array('OrderItemFree.order_id' => $orderId)));
            return $orderItemFreeDetail;
        }
    }
    
    
    function getStartAndEndDate($week, $year)
    {
        $time = strtotime("1 January $year", time());
        $day = date('w', $time);
        $time += ((7*$week)+1-$day)*24*3600;
        $return = date('d', $time);
        return $return;
    }

}
