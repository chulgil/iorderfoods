
    <div class="row">
            <div class="col-lg-6">
                <h3>Add Size</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php echo $this->Form->button('Upload Size', array('type' => 'button','onclick'=>"window.location.href='/sizes/uploadfile'",'class' => 'btn btn-default')); ?>  
		   </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('Sizes', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'SizeAdd','enctype'=>'multipart/form-data'));?>
        <div class="col-lg-6">            
	    <div class="form-group form_margin">		 
                <label>Category<span class="required"> * </span></label>               
              
	    <?php echo $this->Form->input('Size.category_id',array('type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$categoryList,'empty'=>'Select Category')); 	
                  echo $this->Form->error('Sizes.category_id'); ?>
            </div>
	    
	   <div class="form-group form_margin">		 
                <label>Size<span class="required"> * </span></label>               
              
		<?php echo $this->Form->input('Size.size',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Size','label'=>'','div'=>false));
                  echo $this->Form->error('Sizes.size'); ?>
           <span class="blue">(Please enter comma for Multiple sizes.)</span>

            </div>
             <br>
	    <div class="form-group form_margin">
                <label>Status<span class="required"> * </span></label>                
               &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                <?php    
                echo $this->Form->input('Size.is_active', array(
  'type' => 'radio',
  'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive') ,
  'default'=>1
));
                echo $this->Form->error('Size.is_active');
                   ?>
            </div>
            
	    

             <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}?>

	       	       
 
	  
            <?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default'));?>             
            <?php echo $this->Html->link('Cancel', "/sizes/index/", array("class" => "btn btn-default",'escape' => false)); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->
    
    
    <script>
    $(document).ready(function() {
	
	    $("#SizeAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Size][category_id]": {
                    required: true, 
                },
                "data[Size][size]": {
                    required: true, 
                }
                
            },
            messages: {
                "data[Size][category_id]": {
                    required: "Please select category name",
                },
                "data[Size][size]": {
                    required: "Please enter size",
                },
                
            },highlight: function (element, errorClass) {
 $(element).removeClass(errorClass);
 },
        });
           $('#SizeSize').change(function(){
      var str = $(this).val();
      if ($.trim(str) === '') {
         $(this).val('');
         $(this).css('border', '1px solid red');
         $(this).focus();
      }else{
         $(this).css('border', '');
      }
      });  
            
    });
</script>