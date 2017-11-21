<div class="row">
            <div class="col-lg-6">
                <h3>Edit Sub Add-ons</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton">                
                        <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
                </div>
            </div>
    </div>   
    <div class="row">        
            <?php echo $this->Form->create('Topping', array('inputDefaults' => array('label' => false, 'div'=>false, 'required' => false, 'error' => false, 'legend' => false,'autocomplete' => 'off'),'id'=>'SizeAdd','enctype'=>'multipart/form-data'));?>
        <div class="col-lg-6">
		
		  <div class="form-group form_margin">		 
                <label>Sub Add-ons <span class="required"> * </span></label>               
              
		<?php echo $this->Form->input('Topping.name',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Sub Add-ons name','label'=>'','div'=>false));
                  echo $this->Form->input('Topping.id',array('type'=>'hidden','class'=>'toppingHiddeniD')) ;
		  echo $this->Form->error('Topping.name'); 
		  echo $this->Form->input('Topping.addon_id',array('type'=>'hidden','id'=>'hiddenAddonid')) ;
		  echo $this->Form->input('',array('type'=>'hidden','id'=>'hiddenitemid','value'=>$this->request->data['Topping']['item_id'])) ;
		 
?>		    
            </div>
		  
		  
	     <div class="form-group form_margin">
                <label>Category<span class="required"> * </span></label>  
                <?php 
                    echo $this->Form->input('Category.id',array('type'=>'select','class'=>'form-control valid','label'=>'','div'=>false,'required'=>true,'autocomplete' => 'off','options'=>$categoryList,'empty'=>'Select'));            
                ?>
            </div>		  
	<!--  
	    <div class="form-group form_margin">		 
                <label>Add-ons<span class="required"> * </span></label>               
              
	    <?php echo $this->Form->input('Topping.addon_id',array('type'=>'select','class'=>'form-control valid','label'=>false,'div'=>false,'autocomplete' => 'off','options'=>$addonList,'empty'=>'Select Add-ons')); 	
                  echo $this->Form->error('Topping.id'); ?>
            </div>
	-->
	
	
	 <?php if($addonpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}?>
	    <div class="form-group form_spacing" id="addonDiv" <?php echo $display;?> >
                <label>Add-ons<span class="required"> * </span></label>                
                <span id="addonBox" >
                <?php
                echo $this->Form->input('Topping.addon_id',array('type'=>'select','class'=>'form-control valid','label'=>'','div'=>false,'autocomplete' => 'off','multiple'=>false,'options'=>$addonList));            
                ?>
		</span>
		
            </div>
	    
	    <?php if($itempost){ $display="style='display:block;'";}else{$display="style='display:none;'";}?>
	    <div class="form-group form_spacing" id="ItemsDiv" <?php echo $display;?> >
                <label>Item<span class="required"> * </span></label>                
                <span id="ItemsBox" >
                <?php
                echo $this->Form->input('Topping.item_id',array('type'=>'select','class'=>'form-control valid','label'=>'','div'=>false,'autocomplete' => 'off','options'=>$Itemslist));            
                ?>
		</span>
            </div>
	    
	    
	    
	   <div class="form-group form_margin">		 
                <label>Price<span class="required"> * </span></label>               
              
		<?php echo $this->Form->input('Topping.price',array('type'=>'text','class'=>'form-control valid','placeholder'=>'Enter Price','label'=>'','div'=>false));
                  echo $this->Form->error('Topping.price'); ?>

            </div>
            <div class="form-group form_spacing">
	    <label>No Size applicable</label>
	    <?php
		$checked="";
		if($this->request->data['Topping']['no_size']==1){
		  $checked="checked";
		}                 
		echo $this->Form->checkbox('Topping.no_size',array('checked'=>$checked));
	    ?>
	    
	    <label>Default</label>
	    <?php
		if($this->request->data['ItemDefaultTopping']['id']){
			$checked="checked";
			echo $this->Form->input('ItemDefaultTopping.id',array('type'=>'hidden')) ;
		}     
		echo $this->Form->checkbox('Topping.defaultcheck',array('checked'=>$checked));
	    ?>
	    </div>
	    <div class="form-group form_margin">
                <label>Status<span class="required"> * </span></label>                
               &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                <?php    
                echo $this->Form->input('Topping.is_active', array(
  'type' => 'radio',
  'options' => array('1' => 'Active&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '0' => 'Inactive') ,
  'default'=>1
));
                echo $this->Form->error('Topping.is_active');
                   ?>
            </div>
            
	    

             <?php //if($seasonalpost){ $display="style='display:block;'";}else{$display="style='display:none;'";}?>

	       	       
 
	  
            <?php echo $this->Form->button('Save', array('type' => 'submit','class' => 'btn btn-default'));?>             
            <?php echo $this->Html->link('Cancel', "/toppings/listSubTopping/", array("class" => "btn btn-default",'escape' => false)); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->
    
    
     <script>
    $(document).ready(function() {
	$('#ToppingPrice').keyup(function () {
		this.value = this.value.replace(/[^0-9.,]/g,'');
	  });
	    $("#SizeAdd").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[Topping][name]": {
                    required: true, 
                },
                "data[Topping][id]": {
                    required: true, 
                },
		 "data[Topping][price]": {
                    required: true, 
                    number:true
                }
                
            },
            messages: {
                "data[Topping][name]": {
                    required: "Please enter sub add-ons name",
                },
                "data[Topping][id]": {
                    required: "Please select add-ons",
                },
		"data[Topping][price]": {
                    required: "Please enter price",
                },
                
            },highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            },
        });
           $('#ToppingName').change(function(){
      var str = $(this).val();
      if ($.trim(str) === '') {
         $(this).val('');
         $(this).css('border', '1px solid red');
         $(this).focus();
      }else{
         $(this).css('border', '');
      }
      });
	   
	    $('#ToppingPrice').change(function(){
      var str = $(this).val();
      if ($.trim(str) === '') {
         $(this).val('');
         $(this).css('border', '1px solid red');
         $(this).focus();
      }else{
         $(this).css('border', '');
      }
      });
	    
	    
	$("#ToppingAddonId").change(function(){
		var catgoryId=$("#ToppingAddonId").val();
		var subtoppingID=$(".toppingHiddeniD").val();
		var itemID= $("#hiddenitemid").val();
		if (catgoryId) {	
			$.ajax({url: "/toppings/getItemsByAddonCategoryId/"+catgoryId+'/'+subtoppingID+'/'+itemID, success: function(result){		
			    $("#ItemsDiv").show();
			    $("#ItemsBox").show();
			    $("#ItemsBox").html(result);			    
			}});
		}
	});
	
	$("#CategoryId").change(function(){
		var catgoryId=$("#CategoryId").val();
		var hiddenAddonid=$("#hiddenAddonid").val();
		
		if (catgoryId) {	
			$.ajax({url: "/toppings/addonByCategoryEdit/"+catgoryId+"/"+hiddenAddonid, success: function(result){		
			    $("#addonDiv").show();
			    $("#addonBox").show();
			    $("#addonBox").html(result);
			     $("#ItemsBox").html("Please select Add-on");	
			}});
		}
	});
	    
            
    });
</script>