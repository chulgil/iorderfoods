<?php echo $this->Html->script('ckeditor/ckeditor');?>
    <div class="row">
            <div class="col-lg-6">
                <h3>Edit Template</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('Template', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'TemplateAdd'));?>
        <div class="col-lg-6">            
	    
	    
	   <div class="form-group form_margin">		 
                <label>Subject<span class="required"> * </span></label>               
              
		<?php echo $this->Form->input('DefaultTemplate.template_subject',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Type','label'=>'','div'=>false));
                  echo $this->Form->error('DefaultTemplate.template_subject');
	          echo $this->Form->input('DefaultTemplate.id',array('type'=>'hidden'));

		  ?>
            </div>
             
	    <div class="form-group form_spacing">
                <label>Email Body</label> 
                <?php echo $this->Form->textarea('DefaultTemplate.template_message',array('class'=>'ckeditor'));
		echo $this->Form->error('DefaultTemplate.template_message');
		?>
                 </div>
	    <?php if($this->request->data['DefaultTemplate']['sms_template']){?>
	     <div class="form-group form_spacing">
                <label>SMS Text</label> 
                <?php echo $this->Form->input('DefaultTemplate.sms_template',array('type'=>'textarea','class'=>'form-control valid','placeholder'=>'Enter Message','label'=>'','div'=>false));
		echo $this->Form->error('DefaultTemplate.sms_template');
		?>
		
            </div>
	     <?php } ?>
	    <div class="form-group form_margin">
                <label>Status<span class="required"> * </span></label>                
               &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                <?php    
                echo $this->Form->input('DefaultTemplate.is_active', array(
  'type' => 'radio',
  'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive') ,
  'default'=>1
));
                echo $this->Form->error('DefaultTemplate.is_active');
                   ?>
            </div>
            
	    

             <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}?>

	       	       
 
	  
            <?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default'));?>             
            <?php echo $this->Html->link('Cancel', "/super/DefaultTemplate/", array("class" => "btn btn-default",'escape' => false)); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->
    
    
    <script>
    $(document).ready(function() {
	
	    $("#TemplateAdd").validate({
            
            rules: {
                "data[DefaultTemplate][template_subject]": {
                    required: true, 
                }
                
            },
            messages: {
                "data[DefaultTemplate][template_subject]": {
                    required: "Please enter subject.",
                }
            }
        });
          $('#EmailTemplateTemplateSubject').change(function(){
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