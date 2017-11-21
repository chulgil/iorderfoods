<?php App::uses('AppModel','Model');  
    class CronJob extends AppModel {
        var $name = 'CronJob';
        
      /*------------------------------------------------
        Function name:checkCronCurrentStatus()
        Description:Return current Status of cron job 
        created:10/12/2015
        -----------------------------------------------------*/
    public function checkCronCurrentStatus($cronType){
        $cronStatus =$this->find('first',array('fields'=>array('is_active'),'conditions'=>array('cron_type'=>$cronType)));            
        return $cronStatus['CronJob']['is_active'];
    }
    
      /*------------------------------------------------
        Function name:checkCronCurrentStatus()
        Description:Return current Status of cron job 
        created:10/12/2015
        -----------------------------------------------------*/
    public function activateCron($cronType){
        $this->updateAll(array('is_active'=>1),array('cron_type'=>$cronType));            
    }
    
    public function deActivateCron($cronType){
        $this->updateAll(array('is_active'=>0),array('cron_type'=>$cronType));            
    }
}