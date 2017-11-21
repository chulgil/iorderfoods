<?php ?>
<div class="row">
            <div class="col-lg-6">
                <h3>Upload Excel</h3> 
                <?php echo $this->Session->flash();?>   
            </div> 
            <div class="col-lg-6">                        
                <div class="addbutton"> 
                    <?php echo $this->Form->button('Sample Download', array('type' => 'button','onclick'=>"window.location.href='/SubPreferences/download'",'class' => 'btn btn-default')); ?>
<!--<a href="/file_upload_format/Preference_Sample_excel.xls">Sample Download</a>-->
                </div>
            </div>
    </div>   
<div class="row">        
            <?php echo $this->Form->create('SubPreference', array('url'=>array('controller'=>'SubPreferences','action'=> 'uploadfile'),"id" => "addfrm",'enctype'=>'multipart/form-data'));  ?>
    <div class="col-lg-6">            
	    <div class="form-group form_margin">		 
                <label>Excel File<span class="required"> * </span></label>               
              
		<?php echo $this->Form->input('SubPreference.file', array('type'=>'file','div'=>false,'label'=>false));
                    echo $this->Form->error('SubPreference.file'); ?>
            </div>
	    
	
            <?php echo $this->Form->button('Upload', array('type' => 'submit','class' => 'btn btn-default'));?>             
            <?php echo $this->Html->link('Cancel', "/SubPreferences/index/", array("class" => "btn btn-default",'escape' => false)); ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div><!-- /.row -->

   <script type="text/javascript">
    $(document).ready(function() {
        jQuery("#addfrm").validate({
            rules: {
                 'data[SubPreference][file]': {
                    required: true
                }
            },
            messages: {
                'data[SubPreference][file]': {
                    required: 'Please Upload File.'
                }
            }
        });
    });
</script>   
          
    
    



