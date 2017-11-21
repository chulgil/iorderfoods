<style>
    .new-chkbx-wrap { float:left;padding:5px;width:25%;margin-bottom:10px;}
    .new-chkbx-wrap > input {
        float: left;
        margin-right: 5px;
        position: relative;
        top: -3px;
    }
</style>
<div class="row">
    <div class="col-lg-6">
        <?php
        if (!isset($this->request->data['User']['id'])) {
            $title = "Add New Staff";
        } else {
            $title = "Edit Staff Information";
        }
        ?>
        <h3><?php echo $title; ?></h3>
        <hr>
        <?php echo $this->Session->flash(); ?>   
    </div> 
    <div class="col-lg-6">                        
        <div class="addbutton">                
            <?php //echo $this->Html->link('Back','/admin/admins/dashboard/',array('title' => 'Back'));?>
        </div>
    </div>
</div>   
<?php echo $this->Form->create('Super', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'id' => 'UsersRegistration')); ?>
<div class="row">        
    <div class="col-lg-6">
        <div class="form-group">
            <?php
            echo $this->Form->input('User.role_id', array('type' => 'hidden', 'value' => $roleId));
            echo $this->Form->input('User.store_id', array('type' => 'hidden'));
            echo $this->Form->input('User.id', array('type' => 'hidden'));
            ?>
            <label>Salutation<span class="required"> * </span></label>                
            <?php echo $this->Form->input('User.salutation', array('type' => 'select', 'options' => array('Mr.' => 'Mr.', 'Ms.' => 'Ms.', 'Mrs.' => 'Mrs.'), 'class' => 'form-control valid', 'label' => '', 'div' => false)); ?>
        </div>
        <div class="form-group">
            <label>First Name<span class="required"> * </span></label>                
            <?php
            echo $this->Form->input('User.fname', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Your First Name', 'label' => false, 'div' => false));
            echo $this->Form->error('User.fname');
            ?>
        </div>
        <div class="form-group">
            <label>Last Name</label>                
            <?php
            echo $this->Form->input('User.lname', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Your Last Name', 'label' => '', 'div' => false));
            echo $this->Form->error('User.lname');
            ?>
        </div>
        <div class="form-group">
            <label>Email<span class="required"> * </span></label>                
            <?php
            $readonly = '';
            if (isset($this->request->data['User']['id']) && $this->request->data['User']['id']) {
                $readonly = true;
            }
            echo $this->Form->input('User.email', array('type' => 'text', 'class' => 'form-control valid', 'placeholder' => 'Enter Your Email', 'label' => '', 'div' => false, 'required' => true, 'autocomplete' => 'off', 'readonly' => $readonly));
            echo $this->Form->error('User.email');
            ?>
        </div>
        <?php
        $userID = '';
        if (isset($this->request->data['User']['id'])) {
            $userID = $this->request->data['User']['id'];
        }
        if ($userID == '') {
            ?>
            <div class="form-group">
                <label>Password<span class="required"> * </span></label>
                <?php
                echo $this->Form->input('User.password', array('type' => 'password', 'class' => 'form-control valid', 'placeholder' => 'Enter Your password', 'label' => '', 'div' => false, 'required' => true));
                echo $this->Form->error('User.password');
                ?>
            </div>
            <div class="form-group">
                <label>Confirm Password<span class="required"> * </span></label>
                <?php
                echo $this->Form->input('User.password_match', array('type' => 'password', 'class' => 'form-control valid', 'placeholder' => 'Enter Confirm Password', 'label' => '', 'div' => false, 'required' => true));
                echo $this->Form->error('User.password_match');
                ?>
            </div>
            <?php
        }
        ?>
        <div class="form-group">
            <label>Mobile Phone<span class="required"> * </span></label>
            <?php
            echo $this->Form->input('User.phone', array('data-mask' => 'mobileNo', 'type' => 'text', 'class' => 'form-control valid phone_number', 'placeholder' => 'Enter Mobile Phone', 'label' => '', 'div' => false, 'required' => true));
            echo $this->Form->error('User.phone');
            ?>
            <span class="blue">(eg. 111-111-1111)</span> 
        </div>         
    </div>
</div><!-- /.row -->
<div class="row">
    <div class="col-lg-9">
        <h4><strong>Permissions</strong></h4>
        <h5><strong>&nbsp;&nbsp;<input type="checkbox" id="selectall"/>&nbsp;Check All</strong></h5>
        <?php
        foreach ($Tabs as $key => $data) {
            $checked = "";
            if (isset($data['Permission']) && isset($data['Permission'][0]['tab_id'])) {
                $checked = true;
            }
            echo "<div class='new-chkbx-wrap'>";
            echo "<label>" . $data['Tab']['tab_name'] . "</label>";
            echo $this->Form->checkbox('Permission.tab_id.' . $key, array('class' => 'case', 'value' => $data['Tab']['id'], 'checked' => $checked));
            echo "</div>";
        }
        ?>
        <div style="clear:both;"></div>
        <?php echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'btn btn-default')); ?>             
        <?php
        if (!empty($this->request->data['User']['id'])) {
            echo $this->Html->link('Cancel', "/super/manageStaff", array("class" => "btn btn-default", 'escape' => false));
        }
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<?php if (empty($this->request->data['User']['id'])) { ?>
    <hr>
    <div class="row">
        <div class="col-lg-12">
            <h3>Staff Listing</h3>
            <hr>
            <div class="table-responsive">   
                <?php echo $this->Form->create('Store', array('url' => array('controller' => 'super', 'action' => 'manageStaff'), 'id' => 'AdminId', 'type' => 'get')); ?>
                <div class="row padding_btm_20">
                    <div class="col-lg-3">   
                        <?php echo $this->Form->input('keyword', array('value' => @$keyword, 'label' => false, 'div' => false, 'placeholder' => 'Keyword Search', 'class' => 'form-control', 'maxlength' => 55)); ?>
                        <span class="blue">(<b>Search by:</b>First Name, Last Name, Email)</span>
                    </div>
                    <div class="col-lg-4">                        
                        <?php echo $this->Form->button('Search', array('type' => 'submit', 'class' => 'btn btn-default')); ?>
                        <?php echo $this->Html->link('Clear', array('controller' => 'super', 'action' => 'manageStaff'), array('class' => 'btn btn-default')); ?>
                    </div>
                    <!--                <div class="col-lg-4">    
                                        <div class="addbutton">                
                    <?php echo $this->Form->button('Add Staff', array('type' => 'button', 'onclick' => "window.location.href='/super/manageStaff'", 'class' => 'btn btn-default')); ?>  
                                        </div>
                                    </div>-->
                </div>
                <?php echo $this->Form->end(); ?>
                <?php
                if (!empty($list)) {
                    echo @$this->element('show_pagination_count');
                }
                ?>
                <table class="table table-bordered table-hover table-striped tablesorter">
                    <thead>
                        <tr>	    
                            <th  class="th_checkbox"><?php echo @$this->Paginator->sort('User.fname', 'First name'); ?></th>
                            <th  class="th_checkbox"><?php echo @$this->Paginator->sort('User.lname', 'Last name'); ?></th> 
                            <th  class="th_checkbox"><?php echo @$this->Paginator->sort('User.email', 'Email'); ?></th>
                            <th  class="th_checkbox"><?php echo @$this->Paginator->sort('User.created', 'Created'); ?></th>
                            <th  class="th_checkbox">Status : <?php echo $this->Html->image("store_admin/active.png"); ?> /
                                <?php echo $this->Html->image("store_admin/inactive.png"); ?></th>			
                            <th  class="th_checkbox">Action</th>
                        </tr>
                    </thead>
                    <tbody class="dyntable">
                        <?php
                        $i = 0;
                        if (!empty($list)) {
                            foreach ($list as $key => $data) {
                                $class = ($i % 2 == 0) ? ' class="active"' : '';
                                $EncryptStoreID = $this->Encryption->encode($data['User']['id']);
                                ?>
                                <tr <?php echo $class; ?>>	    
                                    <td><?php echo $data['User']['fname']; ?></td>
                                    <td><?php echo $data['User']['lname']; ?></td> 
                                    <td><?php echo $data['User']['email']; ?></td>
                                    <td><?php echo $this->Dateform->us_format($data['User']['created']); ?></td>
                                    <td>
                                        <?php
                                        if ($data['User']['is_active']) {
                                            echo $this->Html->link($this->Html->image("store_admin/active.png", array("alt" => "Active", "title" => "Active")), array('controller' => 'super', 'action' => 'activateStaff', $EncryptStoreID, 0), array('confirm' => 'Are you sure to Deactivate Record?', 'escape' => false));
                                        } else {
                                            echo $this->Html->link($this->Html->image("store_admin/inactive.png", array("alt" => "Inactive", "title" => "Inactive")), array('controller' => 'super', 'action' => 'activateStaff', $EncryptStoreID, 1), array('confirm' => 'Are you sure to Activate Record?', 'escape' => false));
                                        }
                                        ?>
                                    </td>


                                    <td>
                                        <?php echo $this->Html->link($this->Html->image("store_admin/edit.png", array("alt" => "Edit", "title" => "Edit")), array('controller' => 'super', 'action' => 'manageStaff', $EncryptStoreID), array('escape' => false)); ?>
                                        <?php echo " | "; ?>
                                        <?php echo $this->Html->link($this->Html->image("store_admin/delete.png", array("alt" => "Delete", "title" => "Delete")), array('controller' => 'super', 'action' => 'deleteStaff', $EncryptStoreID), array('confirm' => 'Are you sure to delete Record?', 'escape' => false)); ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">
                                    No record available
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>  
                <?php
                if (!empty($list)) {
                    echo $this->element('pagination');
                }
                ?>
                <div class="row padding_btm_20" style="padding-top:10px">
                    <div class="col-lg-1">   
                        LEGENDS:                        
                    </div>
                    <div class="col-lg-1" style="white-space: nowrap;"><?php echo $this->Html->image("store_admin/delete.png") . " Delete &nbsp;"; ?></div>
                    <div class="col-lg-1" style="white-space: nowrap;"> <?php echo $this->Html->image("store_admin/edit.png") . " Edit"; ?> </div>
                    <div class="col-lg-1" style="white-space: nowrap;"> <?php echo $this->Html->image("store_admin/active.png") . " Active"; ?> </div>
                    <div class="col-lg-1" style="white-space: nowrap;"> <?php echo $this->Html->image("store_admin/inactive.png") . " Inactive"; ?> </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    $(document).ready(function() {

    $(".phone_number").keypress(function (e) {
    //if the letter is not digit then display error and don't type anything
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    return false;
    }
    });
    $("[data-mask='mobileNo']").mask("(999) 999-9999");
    jQuery.validator.addMethod("passw", function (pass, element) {
    pass = pass.replace(/\s+/g, "");
    return this.optional(element) || pass.length > 7 &&
            pass.match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[A-Za-z\d$@$!%*#?& ]{8,}$/);
    //pass.match(/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?& ])[A-Za-z\d$@$!%*#?& ]{8,}$/);
    }, "Atleast one digit, one upper and one lower case letter");
    if ($('.case:checked').length == $('.case').length)
    {
    $("#selectall").attr("checked", "checked");
    }

    $('.case').click(function(){

    if ($('.case:checked').length == $('.case').length)
    {
    $("#selectall").prop("checked", true); }

    });
    $('.date_select').datepicker({

    dateFormat: 'mm-dd-yy',
    });
    $("#UsersRegistration").validate({
    debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
            "data[User][fname]": {
            required: true,
                    lettersonly: true,
            },
                    "data[User][lname]": {
                    required: false,
                            lettersonly: true,
                    },
                    "data[User][email]": {
                    required: true,
                            email: true,
<?php
if (!isset($this->request->data['User']['id'])) {
    ?>

                        remote: "/super/checkSuperEmail/<?php echo $roleId; ?>/"

    <?php
}
?>

                    },
                    "data[User][password]": {
                    required: true,
                            minlength:8,
                            maxlength:20,
                            passw:true,
                    },
                    "data[User][password_match]": {
                    required: true,
                            equalTo: "#UserPassword"
                    },
                    "data[User][phone]": {
                    required: true,
                    },
            },
            messages: {
            "data[User][fname]": {
            required: "Please enter your first name",
                    lettersonly:"Only alphabates allowed",
            },
                    "data[User][lname]": {
                    required: "Please enter your last name",
                            lettersonly:"Only alphabates Allowed",
                    },
                    "data[User][email]": {
                    required: "Please enter your email",
                            email:"Please enter valid email",
                            remote:"Email Already exist",
                    },
                    "data[User][password]": {
                    required: "Please enter your password",
                            minlength: "Password must be at least 8 characters",
                            maxlength: "Please enter no more than 20 characters",
                            passw: "Atleast one digit, one upper and one lower case letter"
                    },
                    "data[User][password_match]": {
                    required: "Please enter your password again.",
                            equalTo:"Password not matched"
                    },
                    "data[User][phone]": {
                    required: "Contact number required",
                    },
            },
            highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
            },
    });
    $("#selectall").click(function(){

    var st = $("#selectall").prop('checked');
    $('.case').prop('checked', st);
    });
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
    $(".case").click(function(){
    if ($(".case").length == $(".case:checked").length) {
    $("#selectall").attr("checked", "checked");
    } else {
    $("#selectall").removeAttr("checked");
    }

    });
    });
</script>