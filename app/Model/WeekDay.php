<?php App::uses('AppModel','Model');  
    class WeekDay extends AppModel {
        var $name = 'WeekDay';
        
    /*------------------------------------------------
        Function name:getAllIntervals()
        Description:to get all Interval
        created:9/2/2016
        -----------------------------------------------------*/
    public function getWeekDaysList(){            
            $weekDaysList = $this->find('list',array('fields'=>'name'));            
            if($weekDaysList){               
                return $weekDaysList;
            }else{
               return false;
            }
    }
     
}