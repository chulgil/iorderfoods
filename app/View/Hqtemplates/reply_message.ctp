<style>
    .inquiryrepmsg {
    height: 152px;
    width: 272px;
    margin-top: 6px;
     margin-bottom: 6px;
}
.inquiryrep {
    margin-top: 6px;
    width: 271px;
}
</style>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<div class="row">
    <div class="col-lg-6">
        <h3>Reply Message</h3> 
        <hr>
        <?php echo $this->Session->flash(); ?>   
    </div> 
</div>   
    <?php echo $this->Form->create('ContactUs', array('url'=>array('controller'=>'hqtemplates','action'=>'replyMessage',$this->Encryption->encode($contactUsDetail['ContactUs']['id'])),'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'))); ?>
   <?php //echo $this->Form->textarea('template_message', array('class' => 'ckeditor','required'=>'required'));?>
         
<table><tr class="inquiryreptr">
        <td><label>Customer Name :- </label></td>
            <td><?php echo $this->Form->input('ContactUs.name',array('type'=>'text','value'=>$contactUsDetail['ContactUs']['name'],'class'=>'inquiryrep',' disabled'=>' disabled'));?></td>
        </tr>    
        <tr class="inquiryreptr">
            <td><label>Email :- </label></td>
            <td><?php echo $this->Form->input('ContactUs.email',array('type'=>'text','value'=>$contactUsDetail['ContactUs']['email'],'class'=>'inquiryrep',' disabled'=>' disabled'));?></td>
        </tr>
        <tr class="inquiryreptr">
            <td><label>Subject :- </label></td>
            <td><?php echo $this->Form->input('ContactUs.subject',array('type'=>'text','value'=>$contactUsDetail['ContactUs']['subject'],'class'=>'inquiryrep',' disabled'=>' disabled'));?></td>
         </tr>
        <tr class="inquiryreptr">   
           <td> <label>Inquiry Message :- </label></td>
           <td>  <?php echo $this->Form->input('ContactUs.message',array('type'=>'textarea','value'=>$contactUsDetail['ContactUs']['message'],'class'=>'inquiryrep',' disabled'=>' disabled'));?></td>
         </tr>
        <tr class="inquiryreptr">   
            <td><label>Reply to<br>Customer :- </label>
            <td><?php echo $this->Form->textarea('template_message', array('required'=>'required','class'=>'inquiryrepmsg'));
             echo $this->Form->error('template_message');
            ?></td>
         </tr>
         <tr class="inquiryreptr">
         <td>&nbsp;</td>      
         <td>
         <?php 
         echo $this->Form->button('Send', array('type' => 'submit', 'class' => 'btn btn-default')); echo "&nbsp;&nbsp;&nbsp;";
         echo $this->Html->link('Cancel', "/hqtemplates/enquiryMessages/", array("class" => "btn btn-default", 'escape' => false)); ?>
         </td>
             </tr>    
    </table>
    <?php echo $this->Form->end(); ?>
</div><!-- /.row -->


<script>
    $(document).ready(function () {

        $("#ContactUsReplyMessageForm").validate({
            rules: {
                "data[ContactUs][template_message]": {
                    required: true,
                }

            },
            messages: {
                "data[ContactUs][template_message]": {
                    required: "Please enter message.",
                }
            }
        });
    });
</script>