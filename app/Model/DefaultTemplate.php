<?php

App::uses('AppModel', 'Model');

class DefaultTemplate extends AppModel {

    public function getAllDetail() {
        $templateContent = $this->find('all', array('conditions' => array('is_default' => 0)));
        return $templateContent;
    }

    public function adminTemplates($template_type = null) {
        $templateContent = $this->find('first', array('conditions' => array('is_default' => 1, 'template_code' => $template_type)));
        if ($templateContent) {
            return $templateContent;
        } else {
            return false;
        }
    }

    public function getAllDefaultTemplate() {
        $templateContent = $this->find('all', array('conditions' => array('is_default' => 2)));
        return $templateContent;
    }

    public function getDefaultTemplate($template_code = null, $is_default = null) {
        $templateContent = '';
        if (!empty($template_code)) {
            $templateContent = $this->find('all', array('conditions' => array('template_code' => $template_code, 'is_default' => $is_default)));
        }
        return $templateContent;
    }

}
