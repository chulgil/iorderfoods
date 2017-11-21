
<div class="container-mid contactus">
   <h3>Manage Staff</h3>
            <div class="login-panel">

            
            <?php echo $this->Session->flash();	    
                  echo $this->Form->create('Stores', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'ImageUpload','enctype'=>'multipart/form-data'));
                  echo $this->Form->input('StoreGallery.merchant_id',array('type'=>'hidden','value'=>$merchantId));
                  echo $this->Form->input('StoreGallery.store_id',array('type'=>'hidden','value'=>$storeId));                 
                  echo $this->Form->input('StoreGallery.image',array('type'=>'file','label'=>'Image','div'=>false));                  echo $this->Form->error('StoreGallery.image'); 
                  echo $this->Form->textarea('StoreGallery.description',array('rows' => '5', 'cols' => '5','label'=>'Description'));		   
                  echo $this->Form->submit('Upload File');		  
                  echo $this->Form->end();?>
            </div>
	    
	    <div>
	       
	       <?php
	       if(isset($sliderImages)){
		  echo "<pre>";
		  print_r($sliderImages);
		  
	       }	       
	       ?>
	       
	    </div>
        
    </div>
<script>
    $(document).ready(function() {	
	    $("#ImageUpload").validate({
            rules: {
                "data[StoreGallery][image]": {
                    required: true,                   
                },                  
            },
            messages: {
                "data[StoreGallery][image]": {
                    required: "Please Select image to upload",
                },               
            }
        });
    });
</script>
    <!--Mid Section Ends