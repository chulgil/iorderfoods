<?php

App::uses('AppModel', 'Model');

class Newsletter extends AppModel {
    /* ------------------------------------------------
      Function name:saveNewsletter()
      Description:Save Newsletter
      created:21/8/2015
      ----------------------------------------------------- */

    public function saveNewsletter($newsletterData = null) {
        if ($newsletterData) {
            if ($this->save($newsletterData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:getNewsletterDetail()
      Description:To find Detail of the Newsletter
      created:21/8/2015
      ----------------------------------------------------- */

    public function getNewsletterDetail($newsletterId = null, $storeId = null) {
        $newsletterDetail = $this->find('first', array('conditions' => array('Newsletter.store_id' => $storeId, 'Newsletter.id' => $newsletterId)));
        if ($newsletterDetail) {
            return $newsletterDetail;
        }
    }

    /* ------------------------------------------------
      Function name:checkNewsletterUniqueName()
      Description:to check Newsletter name is unique
      created:21/8/2015
      ----------------------------------------------------- */

    public function checkNewsletterUniqueName($newsletterName = null, $storeId = null, $newsletterId = null) {
        $conditions = array('LOWER(Newsletter.name)' => strtolower($newsletterName), 'Newsletter.store_id' => $storeId, 'Newsletter.is_deleted' => 0);
        if ($newsletterId) {
            $conditions['Newsletter.id !='] = $newsletterId;
        }
        $newsletter = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($newsletter) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ------------------------------------------------
      Function name:checkNewsletterUniqueCode()
      Description:to check Newsletter code is unique
      created:21/8/2015
      ----------------------------------------------------- */

    public function checkNewsletterUniqueCode($newsletterCode = null, $storeId = null, $newsletterId = null) {

        $conditions = array('LOWER(Newsletter.content_key)' => strtolower($newsletterCode), 'Newsletter.store_id' => $storeId, 'Newsletter.is_deleted' => 0);
        if ($newsletterId) {
            $conditions['Newsletter.id !='] = $newsletterId;
        }
        $newslettercode = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($newslettercode) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ------------------------------------------------
      Function name:getNewsletterDetail()
      Description:To find Detail of the Newsletter
      created:21/8/2015
      ----------------------------------------------------- */

    public function getNewsletterDetailByMerchantId($newsletterId = null, $merchantId = null) {
        $newsletterDetail = $this->find('first', array('conditions' => array('Newsletter.merchant_id' => $merchantId, 'Newsletter.id' => $newsletterId)));
        if ($newsletterDetail) {
            return $newsletterDetail;
        }
    }

    /* ------------------------------------------------
      Function name:checkNewsletterUniqueName()
      Description:to check Newsletter name is unique
      created:21/8/2015
      ----------------------------------------------------- */

    public function checkNewsletterUniqueNameByMerchantId($newsletterName = null, $merchantId = null, $newsletterId = null) {
        $conditions = array('LOWER(Newsletter.name)' => strtolower($newsletterName), 'Newsletter.merchant_id' => $merchantId, 'Newsletter.is_deleted' => 0);
        if ($newsletterId) {
            $conditions['Newsletter.id !='] = $newsletterId;
        }
        $newsletter = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($newsletter) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ------------------------------------------------
      Function name:checkNewsletterUniqueCode()
      Description:to check Newsletter code is unique
      created:21/8/2015
      ----------------------------------------------------- */

    public function checkNewsletterUniqueCodeByMerchantId($newsletterCode = null, $merchantId = null, $newsletterId = null) {

        $conditions = array('LOWER(Newsletter.content_key)' => strtolower($newsletterCode), 'Newsletter.merchant_id' => $merchantId, 'Newsletter.is_deleted' => 0);
        if ($newsletterId) {
            $conditions['Newsletter.id !='] = $newsletterId;
        }
        $newslettercode = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($newslettercode) {
            return 0;
        } else {
            return 1;
        }
    }

}
