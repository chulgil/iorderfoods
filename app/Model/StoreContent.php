<?php App::uses('AppModel','Model');  
    class StoreContent extends AppModel {
      
                

   
     /*------------------------------------------------
     Function name:savePage()
     Description:To Save Pages
     created:21/8/2015
     -----------------------------------------------------*/	
    public function savePage($pageData=null){
          if($pageData){
            if($this->save($pageData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
    
         /*------------------------------------------------
   Function name:getPageDetail()
   Description:To find Detail of the Perticular page 
   created:21/8/2015
  -----------------------------------------------------*/
    public function getPageDetail($pageId=null,$storeId=null){      
        $pageDetail =$this->find('first',array('conditions'=>array('StoreContent.store_id'=>$storeId,'StoreContent.id'=>$pageId)));     
        if($pageDetail){
            return $pageDetail;
         
        }
     }
     
         
      /*------------------------------------------------
        Function name:checkPageUniqueName()
        Description:to check Page name is unique
        created:21/8/2015
        -----------------------------------------------------*/
     public function checkPageUniqueName($pageName=null,$storeId=null,$pageId=null){
        $conditions = array('LOWER(StoreContent.name)'=>strtolower($pageName),'StoreContent.store_id'=>$storeId,'StoreContent.is_deleted'=>0);
            if($pageId){
                $conditions['StoreContent.id !=']=$pageId;
            }
            $page =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
            if($page){
                return 0;
            }else{
                return 1;
            }
        
           
     }
     
     /*------------------------------------------------
        Function name:checkPageUniqueCode()
        Description:to check Page code is unique
        created:21/8/2015
        -----------------------------------------------------*/
     public function checkPageUniqueCode($pageCode=null,$storeId=null,$pageId=null){
          
        $conditions = array('LOWER(StoreContent.content_key)'=>strtolower($pageCode),'StoreContent.store_id'=>$storeId,'StoreContent.is_deleted'=>0);
            if($pageId){
                $conditions['StoreContent.id !=']=$pageId;
            }
            $pagecode =$this->find('first',array('fields'=>array('id'),'conditions'=>$conditions));            
            if($pagecode){
                return 0;
            }else{
                return 1;
            }
        
           
     }
}