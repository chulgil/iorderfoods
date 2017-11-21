<?php

App::uses('AppModel', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class EmailTemplate extends AppModel {
    /* ------------------------------------------------
      Function name:storeTemplates()
      Description:For fetching out the email templates related to the particulatr store
      created:22/7/2015
      ----------------------------------------------------- */

    public function storeTemplates($storeId = null, $mechantId = null, $template_type = null) {
        $condition['EmailTemplate.store_id'] = $storeId;
        $condition['EmailTemplate.merchant_id'] = $mechantId;
        $condition['EmailTemplate.is_deleted'] = 0;
        $condition['EmailTemplate.template_code'] = $template_type;
        $templateContent = $this->find('first', array('conditions' => $condition));
        if ($templateContent) {
            return $templateContent;
        } else {
            return false;
        }
    }

    /* ------------------------------------------------
      Function name:saveTemplate()
      Description:Save Template
      created:24/8/2015
      ----------------------------------------------------- */

    public function saveTemplate($templateData = null) {
        if ($templateData) {
            if ($this->save($templateData)) {
                return true; //Success
            } else {
                return false; // Failure 
            }
        }
    }

    /* ------------------------------------------------
      Function name:getTemplateDetail()
      Description:To find Detail of the Template
      created:24/8/2015
      ----------------------------------------------------- */

    public function getTemplateDetail($templateId = null, $storeId = null) {
        $templateDetail = $this->find('first', array('conditions' => array('EmailTemplate.store_id' => $storeId, 'EmailTemplate.id' => $templateId)));
        if ($templateDetail) {
            return $templateDetail;
        }
    }

    /* ------------------------------------------------
      Function name:checkTemplateUniqueName()
      Description:to check Template name is unique
      created:24/8/2015
      ----------------------------------------------------- */

    public function checkTemplateUniqueName($templateName = null, $storeId = null, $templateId = null, $merchant_id = null) {
        $conditions = array('LOWER(EmailTemplate.template_subject)' => strtolower($templateName), 'EmailTemplate.store_id' => $storeId, 'EmailTemplate.is_deleted' => 0);
        if (!empty($storeId)) {
            $conditions['EmailTemplate.store_id'] = $storeId;
        } else {
            $conditions['EmailTemplate.merchant_id'] = $merchant_id;
        }
        if ($templateId) {
            $conditions['EmailTemplate.id !='] = $templateId;
        }
        $template = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($template) {
            return 0;
        } else {
            return 1;
        }
    }

    /* ------------------------------------------------
      Function name:checkTemplateUniqueCode()
      Description:to check Template code is unique
      created:24/8/2015
      ----------------------------------------------------- */

    public function checkTemplateUniqueCode($templateCode = null, $storeId = null, $templateId = null) {
        $conditions = array('LOWER(EmailTemplate.template_code)' => strtolower($templateCode), 'EmailTemplate.store_id' => $storeId, 'EmailTemplate.is_deleted' => 0);
        if ($templateId) {
            $conditions['EmailTemplate.id !='] = $templateId;
        }
        $templatecode = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($templatecode) {
            return 0;
        } else {
            return 1;
        }
    }

    public function checkTemplate($templateCode = null, $merchantid = null) {
        $conditions = array('LOWER(EmailTemplate.template_code)' => strtolower($templateCode), 'EmailTemplate.merchant_id' => $merchantid, 'EmailTemplate.is_deleted' => 0);
        $templatecode = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($templatecode) {
            return 0;
        } else {
            return 1;
        }
    }

    public function checkStoreTemplate($templateCode = null, $storeid = null, $merchantid = null) {
        $conditions = array('LOWER(EmailTemplate.template_code)' => strtolower($templateCode), 'EmailTemplate.store_id' => $storeid, 'EmailTemplate.is_deleted' => 0, 'EmailTemplate.merchant_id' => $merchantid);
        $templatecode = $this->find('first', array('fields' => array('id'), 'conditions' => $conditions));
        if ($templatecode) {
            return 0;
        } else {
            return 1;
        }
    }

}
