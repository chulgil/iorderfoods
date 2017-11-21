<!-- HEADER -->
<?php
if (DESIGN == 1) {
    echo $this->element('design/aaron/header');
} elseif (DESIGN == 2) {
    echo $this->element('design/chloe/header');
} elseif (DESIGN == 3) {
    echo $this->element('design/dasol/header');
}



//echo $this->element('design/dasol/header');
?>




<script>

    $(document).ready(function () {
        $('.rgtTopMenu').on('click', function () {
            $('.welcome-user-menu').toggleClass("menu-drop");
        });
    });


    $("#UserLogin").validate({
        rules: {
            "data[User][email]": {
                required: true,
            },
            "data[User][password]": {
                required: true,
            }
        },
        messages: {
            "data[User][email]": {
                required: "Please enter your email",
            },
            "data[User][password]": {
                required: "Please enter your password",
            }
        }
    });
</script>