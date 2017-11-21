<!DOCTYPE html>
<html>

<head>
    <?php //echo $this->Html->charset(); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" href="/img/favicon.png">
    <title><?php echo $this->Session->read('storeName');//$this->fetch('title'); ?> </title>

<?php
    echo $this->Html->css('mobile/payment');
    echo $this->Html->script('jquery.min');
    echo $this->Html->script('validation/jquery.validate.min');
    echo $this->Html->script('validation/jquery.creditCardValidator');
?>
  </head>


<body>

<?php echo $this->fetch('content'); ?>

</body>
<script>
    $("form").validate({});
</script>
</html>