<?php App::uses('AppModel','Model');  
    class Module extends AppModel {
        var $name = 'Module';
        
        
    /*------------------------------------------------
     Function name:getUrlRoutes()
     Description:To Get Routes Details
     created:04/8/2015
     -----------------------------------------------------*/	
          
    public function getUrlRoutes($subdomain=null){
        if($subdomain){
            $subdomain=trim($subdomain);
            $routeData = $this->find('first',array('conditions'=>array('Module.subdomain'=>$subdomain,'Module.is_delete'=>0)));		
            if($routeData){
                   return $routeData;
            }else{
                   return false;
            } 
        }         
     }
     
     
     /*------------------------------------------------
        Function name:saveRouteData()
        Description:To Save Route data
        created:17/8/2015
       -----------------------------------------------------*/
    public function saveRouteData($RouteData=null){
          if($RouteData){
            if($this->save($RouteData)){		    
                    return true; //Success
               }else{			
                    return false;// Failure 
               }	       
          }         
    }
     
}