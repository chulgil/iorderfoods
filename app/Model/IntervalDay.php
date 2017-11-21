<?php App::uses('AppModel','Model');  
class IntervalDay extends AppModel {
    var $name = 'IntervalDay';
    
    public $belongsTo =array(
            'WeekDay'=>array(
                'className'=> 'WeekDay',
                'foreignKey'=>'week_day_id',
                'fields'=>array('WeekDay.id','WeekDay.name'),
            )
        );
    
    /*------------------------------------------------
     Function name:saveIntervalDay()
     Description:To Save Interval days Information
     created:09/02/2016
     -----------------------------------------------------*/	
    public function saveIntervalDay($intervalDayData=null){
          if($intervalDayData){
            if($this->save($intervalDayData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
        
        
        
        
}