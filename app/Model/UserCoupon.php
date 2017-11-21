<?php

App::uses('AppModel', 'Model');


class UserCoupon extends AppModel {
    var $name = 'UserCoupon';

    
     /*------------------------------------------------
     Function name:saveUserCoupon()
     Description:To Save User coupon Information
     created:12/8/2015
     -----------------------------------------------------*/	
    public function saveUserCoupon($couponData=null){
          if($couponData){
            if($this->save($couponData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
       /*------------------------------------------------
        Function name:checkUserCouponData()
        Description:to check User coupon data
        created:12/8/2015
        -----------------------------------------------------*/
     public function checkUserCouponData($userId=null,$couponCode=null,$storeId=null,$CouponId=null){
        
        $conditions = array('LOWER(UserCoupon.coupon_code)'=>strtolower($couponCode),'UserCoupon.coupon_id'=>$CouponId,'UserCoupon.store_id'=>$storeId,'UserCoupon.user_id'=>$userId,'UserCoupon.is_deleted'=>0);
            
            $data =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
            if($data){
                return 0;
            }else{
                return 1;
            }
        
           
     }
     
     /*------------------------------------------------
     Function name:getCouponDetails()
     Description:To get list of coupons
     created:12/8/2015
     -----------------------------------------------------*/	
    
    public function getCouponDetails($decrypt_merchantId=null,$decrypt_storeId=null,$decrypt_userId=null){
        //$myCoupons = $this->find('all',array('conditions'=>array('UserCoupon.merchant_id'=>$decrypt_merchantId,'UserCoupon.user_id'=>$decrypt_userId,'UserCoupon.store_id'=>$decrypt_storeId,'UserCoupon.is_active'=>1,'UserCoupon.is_deleted'=>0,'Coupon.is_active'=>1,'Coupon.is_deleted'=>0)));
        $myCoupons = $this->find('all',array('conditions'=>array('UserCoupon.merchant_id'=>$decrypt_merchantId,'UserCoupon.user_id'=>$decrypt_userId,'UserCoupon.is_active'=>1,'UserCoupon.is_deleted'=>0,'Coupon.is_active'=>1,'Coupon.is_deleted'=>0)));
        return $myCoupons;
    }
    
}