<div class="content">
    <div class="container">
        <?php echo $this->Session->flash();?>   
        <div class="row">
            <div class="col-md-8" style=" margin-left: 15%;">
                <div class="well well-sm">
                    <?php echo $this->Form->create('ContactUs', array('url' => array('controller' => 'hqusers', 'action' => 'contact_us'), 'inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'))); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">
                                    Name</label>
                                <?php echo $this->Form->input('name', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter name', 'label' => false, 'div' => false, 'required' => "required")); ?>
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    Email Address</label>
                                    <?php echo $this->Form->input('email', array('type' => 'email', 'class' => 'form-control', 'placeholder' => 'Enter Email', 'label' => false, 'div' => false, 'required' => "required")); ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="Phone">
                                    Phone Number</label>
                               <?php echo $this->Form->input('phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control inbox phone-number', 'placeholder' => 'Enter Number', 'label' => false, 'div' => false, 'required' => true));
                            echo $this->Form->error('User.phone'); ?>
                                <span>(eg. 111-111-1111)</span>
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                            
                            
                            <div class="form-group">
                                <label for="subject">
                                    Subject</label>
                                <?php echo $this->Form->input('subject', array('type' => 'text', 'class' => 'form-control', 'placeholder' => 'Enter subject', 'label' => false, 'div' => false, 'required' => "required")); ?>
                            </div>
                            
                            
                            
                            <div class="form-group">
                                <label for="name">
                                    Message</label>
                                <?php echo $this->Form->input('message', array('type' => 'textarea', 'class' => 'form-control', 'placeholder' => 'Enter Message', 'label' => false, 'div' => false, 'required' => "required", "rows" => "4", "cols" => "25")); ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <?php echo $this->Form->button('Send Message', array('type' => 'submit', 'class' => 'btn btn-primary pull-right', "id" => "btnContactUs")); ?>             
                        </div>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
<!--            <div class="col-md-4">
                <form>
                    <legend><span class="glyphicon glyphicon-globe"></span> Our office</legend>
                    <address>
                        <strong>Twitter, Inc.</strong><br>
                        795 Folsom Ave, Suite 600<br>
                        San Francisco, CA 94107<br>
                        <abbr title="Phone">
                            P:</abbr>
                        (123) 456-7890
                    </address>
                    <address>
                        <strong>Full Name</strong><br>
                        <a href="mailto:#">first.last@example.com</a>
                    </address>
                </form>
            </div>-->
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#ContactUsContactUsForm").validate({
            rules: {
                "data[ContactUs][name]": {
                    required: true,
                },
                "data[ContactUs][email]": {
                    required: true,
                    email:true
                },
                "data[ContactUs][subject]": {
                    required: true,
                },
                "data[ContactUs][message]": {
                    required: true,
                }
            },
            messages: {
                "data[ContactUs][name]": {
                    required: 'Please enter name.',
                },
                "data[ContactUs][email]": {
                    required: 'Please enter email.',
                },
                "data[ContactUs][subject]": {
                    required: 'Please enter subject.',
                },
                "data[ContactUs][message]": {
                    required: 'Please enter message.',
                }
            }
        });
         
        $(".phone-number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
        $("[data-mask='mobileNo']").mask("(999) 999-9999");
    });
</script>
