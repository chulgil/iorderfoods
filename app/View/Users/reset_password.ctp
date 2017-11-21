<div class="main-container ">
    <div class="inner-wrap profile reset-password-wrap">
        <?php //echo $this->Session->flash(); ?>       
        
        <?php
        if(!empty($record)){
            if (DESIGN == 1) {
                echo $this->element('design/common/reset_password');
            } elseif (DESIGN == 2) {
                echo $this->element('design/common/reset_password');
            } elseif (DESIGN == 3) {
                 echo $this->element('design/common/reset_password');
            } elseif (DESIGN == 4) {
                echo $this->element('design/oldlayout/innerpage/reset_password');
            }
        }else{            
            echo $this->element('admin/reset_password');
        }
        ?>
        
        
        
        <!-- ============================================================================= -->
        
    </div>
</div>
<script>
    $(document).ready(function () {
        jQuery.validator.addMethod("passw", function (pass, element) {
            pass = pass.replace(/\s+/g, "");
            return this.optional(element) || pass.length > 7 &&
                    pass.match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[A-Za-z\d$@$!%*#?& ]{8,}$/);
            //pass.match(/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?& ])[A-Za-z\d$@$!%*#?& ]{8,}$/);
        }, "Atleast one digit, one upper and one lower case letter");

        $("#UsersResetPasswordForm").validate({
            debug: false,
            errorClass: "error",
            errorElement: 'span',
            onkeyup: false,
            rules: {
                "data[User][newpassword]": {
                    required: true,
                    minlength: 8,
                    maxlength: 20,
                    passw: true,
                },
                "data[User][repassword]": {
                    required: true,
                    equalTo: "#UserNewpassword"
                },
            },
            messages: {
                "data[User][newpassword]": {
                    required: "Please enter your password",
                    minlength: "Password must be at least 8 characters",
                    maxlength: "Please enter no more than 20 characters",
                    passw: "Atleast one digit, one upper and lower case letter"
                },
                "data[User][repassword]": {
                    required: "Please enter your password again",
                    equalTo: "Password not matched",
                }

            }, highlight: function (element, errorClass) {
                $(element).removeClass(errorClass);
            }
        });
    });
</script>