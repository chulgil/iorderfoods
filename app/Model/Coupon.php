<?php App::uses('AppModel','Model');  
    class Coupon extends AppModel {
      
                
        
         
     /*------------------------------------------------
     Function name:saveCoupon()
     Description:To Save Coupon Information
     created:08/8/2015
     -----------------------------------------------------*/	
    public function saveCoupon($couponData=null){
          if($couponData){
            if($this->save($couponData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
         /*------------------------------------------------
   Function name:getCouponDetail()
   Description:To find Detail of coupon from coupon table 
   created:08/8/2015
  -----------------------------------------------------*/
    public function getCouponDetail($couponId=null,$storeId=null){      
        $couponDetail =$this->find('first',array('conditions'=>array('Coupon.store_id'=>$storeId,'Coupon.id'=>$couponId)));     
        if($couponDetail){
            return $couponDetail;
         
        }
     }
     
          
      /*------------------------------------------------
        Function name:checkCouponUniqueName()
        Description:to check Coupon name is unique
        created:08/8/2015
        -----------------------------------------------------*/
     public function checkCouponUniqueName($couponName=null,$storeId=null,$couponId=null){
        $conditions = array('LOWER(Coupon.name)'=>strtolower($couponName),'Coupon.store_id'=>$storeId,'Coupon.is_deleted'=>0);
            if($couponId){
                $conditions['Coupon.id !=']=$couponId;
            }
            $coupon =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
            if($coupon){
                return 0;
            }else{
                return 1;
            }
        
           
     }
     
     /*------------------------------------------------
        Function name:checkCouponUniqueCode()
        Description:to check Coupon code is unique
        created:08/8/2015
        -----------------------------------------------------*/
     public function checkCouponUniqueCode($couponCode=null,$storeId=null,$couponId=null){
        
        $conditions = array('LOWER(Coupon.coupon_code)'=>strtolower($couponCode),'Coupon.store_id'=>$storeId,'Coupon.is_deleted'=>0);
            if($couponId){
                $conditions['Coupon.id !=']=$couponId;
            }
            $coupon =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
            if($coupon){
                return 0;
            }else{
                return 1;
            }
        
           
     }
     
    /*------------------------------------------------
        Function name:getValidCoupon()
        Description:To find Detail of coupon from coupon table 
        created:20/8/2015
    -----------------------------------------------------*/
    public function getValidCoupon($couponCode=null,$storeId=null,$date){  
        $couponDetail = $this->find('first',array('conditions'=>array('Coupon.store_id'=>$storeId,'Coupon.coupon_code'=>$couponCode,'Coupon.is_active'=>1,'Coupon.is_deleted'=>0, 'Coupon.start_date <= ' => $date, 'Coupon.end_date >= ' => $date)));     
        return $couponDetail;
        
     }
     
    public function fetchCouponList($storeId=null){            
        $itemid = $this->find('all',array('conditions'=>array('Coupon.store_id'=>$storeId,'Coupon.is_deleted'=>0)));            
        if($itemid){               
            return $itemid;
        }else{
           return false;
        }
    }
    public function fetchCouponListByMerchantId($merchantId=null){            
        $itemid = $this->find('all',array('conditions'=>array('Coupon.merchant_id'=>$merchantId,'Coupon.is_deleted'=>0)));            
        if($itemid){               
            return $itemid;
        }else{
           return false;
        }
    }
    
     public function checkCouponWithId($couponId = null) {
        $conditions = array('Coupon.id' => $couponId);
        $coupon = $this->find('first', array('fields' => array('id','store_id'), 'conditions' => $conditions));
        return $coupon;
    }

     
}