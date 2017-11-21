<?php App::uses('AppModel','Model');  
    class Interval extends AppModel {
        var $name = 'Interval';
        
        public $hasMany =array(
            'IntervalDay'=>array(
                'className'=> 'IntervalDay',
                'foreignKey'=>'interval_id',
            )
        );
        
        
    /*------------------------------------------------
     Function name:saveInterval()
     Description:To Save Interval Information
     created:09/02/2016
     -----------------------------------------------------*/	
    public function saveInterval($intervalData=null){
          if($intervalData){
            if($this->save($intervalData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
    /*------------------------------------------------
        Function name:getAllIntervals()
        Description:to get all Interval
        created:9/2/2016
        -----------------------------------------------------*/
     public function getAllInvervals($storeId=null){            
            $intervalList = $this->find('all',array('conditions'=>array('Interval.store_id'=>$storeId),'order'=>array('Interval.name ASC')));            
            if($intervalList){               
                return $intervalList;
            }else{
               return false;
            }
     }
     
    /*------------------------------------------------
        Function name:getAllActiveInvervals()
        Description:to get all active Interval
        created:9/2/2016
        -----------------------------------------------------*/
     public function getAllActiveInvervals($storeId=null){            
            $intervalList = $this->find('all',array('conditions'=>array('Interval.store_id'=>$storeId,'Interval.is_active'=>1,'Interval.is_deleted'=>0),'order'=>array('Interval.start ASC')));            
            if($intervalList){               
                return $intervalList;
            }else{
               return false;
            }
     }
    
    /*------------------------------------------------
     Function name:getIntervalDetails()
     Description:To get offer Details
     created:11/8/2015
     -----------------------------------------------------*/	
    
    public function getIntervalDetail($intervalId=null){
        $intervalDetail = $this->find('first',array('conditions'=>array('Interval.is_deleted'=>0,'Interval.id'=>$intervalId),'recursive'=>3));
        if($intervalDetail){           
            return $intervalDetail;
        }else{
           return false;
        }
    }
    
    
    
    
    public function getIntervalList($storeId=null){            
        $intervalList = $this->find('list',array('fields'=>array('name'),'conditions'=>array('Interval.store_id'=>$storeId,'Interval.is_active'=>1,'Interval.is_deleted'=>0),'order'=>array('Interval.start ASC')));            
        if($intervalList){               
            return $intervalList;
        }else{
           return false;
        }
    }
    
    public function getIntervalName($intervalId=null){
        $intervalDetail = $this->find('first',array('conditions'=>array('Interval.is_deleted'=>0,'Interval.id'=>$intervalId),'fields'=>array('Interval.id','Interval.name')));
        if($intervalDetail){           
            return $intervalDetail['Interval']['name'];
        }else{
           return false;
        }
    }
    public function getIntervalListByMerchantId($merchant_id=null){
        if($merchant_id){
            $result = $this->find('list',array('fields'=>array('Interval.name','Interval.name'),'conditions'=>array('Interval.merchant_id'=>$merchant_id,'Interval.is_active'=>1,'Interval.is_deleted'=>0),'group'=>'name','recursive'=>-1));
            return $result;
        }else{
            return FALSE;
        }
    }
     
}