<?php App::uses('AppModel','Model');  
    class CountryCode extends AppModel {
      
 
    public function fetchCountryCode(){      
        $countryCode = $this->find('first');     
        return $countryCode;
     }
     
     public function fetchAllCountryCode(){      
        $countryCode = $this->find('list',array('fields'=>array('id','code')));     
        return $countryCode;
     }
     
     public function fetchCountryCodeId($id = null){      
        $countryCode = $this->find('first',array('conditions'=>array('CountryCode.id'=>$id)));     
        return $countryCode;
     }
     
}