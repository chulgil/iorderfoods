<?php

App::uses('AppModel', 'Model');

class MerchantNewsletter extends AppModel {
    /* ------------------------------------------------
      Function name:saveNewsletter()
      Description:Save MerchantNewsletter
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
      Description:To find Detail of the MerchantNewsletter
      created:21/8/2015
      ----------------------------------------------------- */

    public function getNewsletterDetail($newsletterId = null, $merchantId = null) {
        $newsletterDetail = $this->find('first', array('conditions' => array('MerchantNewsletter.merchant_id' => $merchantId, 'MerchantNewsletter.id' => $newsletterId)));
        if ($newsletterDetail) {
            return $newsletterDetail;
        }
    }

    /* ------------------------------------------------
      Function name:checkNewsletterUniqueName()
      Description:to check MerchantNewsletter name is unique
      created:21/8/2015
      ----------------------------------------------------- */

    public function checkNewsletterUniqueName($newsletterName = null, $merchantId = null, $newsletterId = null) {
        $conditions = array('LOWER(MerchantNewsletter.name)' => strtolower($newsletterName), 'MerchantNewsletter.merchant_id' => $merchantId, 'MerchantNewsletter.is_deleted' => 0);
        if ($newsletterId) {
            $conditions['MerchantNewsletter.id !='] = $newsletterId;
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
      Description:to check MerchantNewsletter code is unique
      created:21/8/2015
      ----------------------------------------------------- */

    public function checkNewsletterUniqueCode($newsletterCode = null, $merchantId = null, $newsletterId = null) {

        $conditions = array('LOWER(MerchantNewsletter.content_key)' => strtolower($newsletterCode), 'MerchantNewsletter.merchant_id' => $merchantId, 'MerchantNewsletter.is_deleted' => 0);
        if ($newsletterId) {
            $conditions['MerchantNewsletter.id !='] = $newsletterId;
        }
        $newslettercode = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($newslettercode) {
            return 0;
        } else {
            return 1;
        }
    }
    
    public function getAllNewsLetter($merchantId=null){
        $newsletterDetail = $this->find('all',array('conditions'=>array('is_active'=>1,'is_deleted'=>0,'merchant_id'=>$merchantId)));
        if ($newsletterDetail) {
            return $newsletterDetail;
        }else{
            return false;
        }
    }

}
