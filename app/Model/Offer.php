<?php

App::uses('AppModel', 'Model');

class Offer extends AppModel {

    var $name = 'Offer';

    /* ------------------------------------------------
      Function name:saveItem()
      Description:To Save Item Information
      created:04/8/2015
      ----------------------------------------------------- */

    public function saveOffer($offerData = null) {
        if ($offerData) {
            if ($this->save($offerData)) {
                return true; //Success
            } else {
                return false; // Failure
            }
        }
    }

    /* ------------------------------------------------
      Function name:getOfferDetails()
      Description:To get offer Details
      created:11/8/2015
      ----------------------------------------------------- */

    public function getOfferDetails($offerid = null) {
        $offer = $this->find('first', array('conditions' => array('Offer.is_deleted' => 0, 'Offer.id' => $offerid), 'recursive' => 3));
        if ($offer) {
            return $offer;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:offerExistsOnItem()
      Description:To get offer Details
      created:11/8/2015
      ----------------------------------------------------- */

    public function offerExistsOnItem($itemId = null, $startDate = null, $endDate = null, $sizeid = 0, $unit = 1, $offerId = null) {

        $conditions = array('Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.item_id' => $itemId, 'Offer.size_id' => $sizeid, 'Offer.unit' => $unit);
        //if($startDate && $endDate){
        //    $conditions['Offer.offer_start_date >=']=$startDate;
        //    $conditions['Offer.offer_end_date <=']=$endDate;
        //}
        if ($offerId) {
            $conditions['Offer.id !='] = $offerId;
        }
        $offer = $this->find('first', array('conditions' => $conditions));
        if ($offer) {
            return $offer;
        } else {
            return false;
        }
    }

    public function offerOnItem($itemId = null) {
        $date = date('Y-m-d');
        $time = date('H:m:s');
        $conditions = array('Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.item_id' => $itemId);
        $offer = $this->find('first', array('conditions' => $conditions));
        if (!empty($offer)) {
            $offer['Offer']['count'] = $this->OfferDetail->find('count', array('conditions' => array('is_active' => 1, 'is_deleted' => 0, 'offer_id' => $offer['Offer']['id'])));
            if ($offer['Offer']['count'] <= 0) {
                $offer = array();
                return $offer;
            } else {
                if ($offer['Offer']['is_time'] == 1) {
                    if (($offer['Offer']['offer_start_date'] == "0000-00-00") || ($offer['Offer']['offer_start_date'] == "")) {
                        if (($offer['Offer']['offer_start_time'] <= $time) && ($offer['Offer']['offer_end_time'] >= $time)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    } else {
                        if (($offer['Offer']['offer_start_date'] <= $date) && ($offer['Offer']['offer_end_date'] >= $date) && ($offer['Offer']['offer_start_time'] <= $time) && ($offer['Offer']['offer_end_time'] >= $time)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    }
                } else {
                    if (($offer['Offer']['offer_start_date'] == "0000-00-00") || ($offer['Offer']['offer_start_date'] == "")) {
                        return $offer;
                    } else {
                        if (($offer['Offer']['offer_start_date'] <= $date) && ($offer['Offer']['offer_end_date'] >= $date)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    }
                }
            }
        } else {
            return $offer;
        }
    }

    public function offerOnItemSize($itemId = null, $sizeId = null) {
        $date = date('Y-m-d');
        $time = date('H:m:s');
        $conditions = array('Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.item_id' => $itemId, 'Offer.size_id' => $sizeId);
        $offer = $this->find('first', array('conditions' => $conditions));
        if (!empty($offer)) {
            $offer['Offer']['count'] = $this->OfferDetail->find('count', array('conditions' => array('is_active' => 1, 'is_deleted' => 0, 'offer_id' => $offer['Offer']['id'])));
            if ($offer['Offer']['count'] <= 0) {
                $offer = array();
                return $offer;
            } else {
                if ($offer['Offer']['is_time'] == 1) {
                    if (($offer['Offer']['offer_start_date'] == "0000-00-00") || ($offer['Offer']['offer_start_date'] == "")) {
                        if (($offer['Offer']['offer_start_time'] <= $time) && ($offer['Offer']['offer_end_time'] >= $time)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    } else {
                        if (($offer['Offer']['offer_start_date'] <= $date) && ($offer['Offer']['offer_end_date'] >= $date) && ($offer['Offer']['offer_start_time'] <= $time) && ($offer['Offer']['offer_end_time'] >= $time)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    }
                } else {
                    if (($offer['Offer']['offer_start_date'] == "0000-00-00") || ($offer['Offer']['offer_start_date'] == "")) {
                        return $offer;
                    } else {
                        if (($offer['Offer']['offer_start_date'] <= $date) && ($offer['Offer']['offer_end_date'] >= $date)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    }
                }
            }
        } else {
            return $offer;
        }
    }

    public function offerOnItemm($itemId = null, $unit = null) {
        $date = date('Y-m-d');
        $time = date('H:m:s');
        $conditions = array('Offer.unit' => $unit, 'Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.item_id' => $itemId);
        $offer = $this->find('first', array('conditions' => $conditions));
        if (!empty($offer)) {
            $offer['Offer']['count'] = $this->OfferDetail->find('count', array('conditions' => array('is_active' => 1, 'is_deleted' => 0, 'offer_id' => $offer['Offer']['id'])));
            if ($offer['Offer']['count'] <= 0) {
                $offer = array();
                return $offer;
            } else {
                if ($offer['Offer']['is_time'] == 1) {
                    if (($offer['Offer']['offer_start_date'] == "0000-00-00") || ($offer['Offer']['offer_start_date'] == "")) {
                        if (($offer['Offer']['offer_start_time'] <= $time) && ($offer['Offer']['offer_end_time'] >= $time)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    } else {
                        if (($offer['Offer']['offer_start_date'] <= $date) && ($offer['Offer']['offer_end_date'] >= $date) && ($offer['Offer']['offer_start_time'] <= $time) && ($offer['Offer']['offer_end_time'] >= $time)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    }
                } else {
                    if (($offer['Offer']['offer_start_date'] == "0000-00-00") || ($offer['Offer']['offer_start_date'] == "")) {
                        return $offer;
                    } else {
                        if (($offer['Offer']['offer_start_date'] <= $date) && ($offer['Offer']['offer_end_date'] >= $date)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    }
                }
            }
        } else {
            return $offer;
        }
    }

    public function offerOnItemSizee($itemId = null, $sizeId = null, $unit = null) {
        $date = date('Y-m-d');
        $time = date('H:m:s');
        $conditions = array('Offer.unit' => $unit, 'Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.item_id' => $itemId, 'Offer.size_id' => $sizeId);
        $offer = $this->find('first', array('conditions' => $conditions));
        if (!empty($offer)) {
            $offer['Offer']['count'] = $this->OfferDetail->find('count', array('conditions' => array('is_active' => 1, 'is_deleted' => 0, 'offer_id' => $offer['Offer']['id'])));
            if ($offer['Offer']['count'] <= 0) {
                $offer = array();
                return $offer;
            } else {
                if ($offer['Offer']['is_time'] == 1) {
                    if (($offer['Offer']['offer_start_date'] == "0000-00-00") || ($offer['Offer']['offer_start_date'] == "")) {
                        if (($offer['Offer']['offer_start_time'] <= $time) && ($offer['Offer']['offer_end_time'] >= $time)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    } else {
                        if (($offer['Offer']['offer_start_date'] <= $date) && ($offer['Offer']['offer_end_date'] >= $date) && ($offer['Offer']['offer_start_time'] <= $time) && ($offer['Offer']['offer_end_time'] >= $time)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    }
                } else {
                    if (($offer['Offer']['offer_start_date'] == "0000-00-00") || ($offer['Offer']['offer_start_date'] == "")) {
                        return $offer;
                    } else {
                        if (($offer['Offer']['offer_start_date'] <= $date) && ($offer['Offer']['offer_end_date'] >= $date)) {
                            return $offer;
                        } else {
                            $offer = array();
                            return $offer;
                        }
                    }
                }
            }
        } else {
            return $offer;
        }
    }

    public function allOfferOnItem($itemId = null,$desc=null) {
        $date = date('Y-m-d');
        $time = date('H:m:s');
        if(!empty($desc)){
            $descCheck='';
            $conditions = array('Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.item_id' => $itemId,'Offer.description !='=>$descCheck);
        }else{
            $conditions = array('Offer.is_active' => 1, 'Offer.is_deleted' => 0, 'Offer.item_id' => $itemId);
        }
        
        $offer = $this->find('all', array('conditions' => $conditions));
        $displayOffer = array();
        if (!empty($offer)) {
            $i = 0;
            foreach ($offer as $off) {
                $off['Offer']['count'] = $this->OfferDetail->find('count', array('conditions' => array('is_active' => 1, 'is_deleted' => 0, 'offer_id' => $off['Offer']['id'])));
                if ($off['Offer']['count'] <= 0) {

                } else {
                    if ($off['Offer']['is_time'] == 1) {
                        if (($off['Offer']['offer_start_date'] == "0000-00-00") || ($off['Offer']['offer_start_date'] == "")) {
                            if (($off['Offer']['offer_start_time'] <= $time) && ($off['Offer']['offer_end_time'] >= $time)) {
                                $displayOffer[$i] = $off['Offer']['description'];
                            }
                        } else {
                            if (($off['Offer']['offer_start_date'] <= $date) && ($off['Offer']['offer_end_date'] >= $date) && ($off['Offer']['offer_start_time'] <= $time) && ($off['Offer']['offer_end_time'] >= $time)) {
                                $displayOffer[$i] = $off['Offer']['description'];
                            }
                        }
                    } else {
                        if (($off['Offer']['offer_start_date'] == "0000-00-00") || ($off['Offer']['offer_start_date'] == "")) {
                            $displayOffer[$i] = $off['Offer']['description'];
                        } else {
                            if (($off['Offer']['offer_start_date'] <= $date) && ($off['Offer']['offer_end_date'] >= $date)) {
                                $displayOffer[$i] = $off['Offer']['description'];
                            }
                        }
                    }
                }
                $i++;
            }
            return $displayOffer;
        } else {
            return $offer;
        }
    }

    public function fetchOfferList($storeId = null) {
        $itemid = $this->find('all', array('recursive' => 2, 'conditions' => array('Offer.store_id' => $storeId, 'Offer.is_deleted' => 0)));
        if ($itemid) {
            return $itemid;
        } else {
            return false;
        }
    }

    public function fetchOfferListByMerchantId($merchantId = null) {
        $itemid = $this->find('all', array('recursive' => 2, 'conditions' => array('Offer.merchant_id' => $merchantId, 'Offer.is_deleted' => 0)));
        if ($itemid) {
            return $itemid;
        } else {
            return false;
        }
    }

    public function checkOfferWithId($offerId = null) {
        $conditions = array('Offer.id' => $offerId);
        $offer = $this->find('first', array('fields' => array('id', 'store_id'), 'conditions' => $conditions));
        return $offer;
    }

}
