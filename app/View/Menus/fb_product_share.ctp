<?php ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <?php if(!empty($ItemDetails)){?>
    <meta property="og:url"           content="<?php echo $_SERVER['HTTP_HOST'];?>" />    
    <meta property="og:title"         content="<?php echo $ItemDetails['Item']['name'] ?>" />
    <meta property="og:description"   content="<?php echo $ItemDetails['Item']['description'] ?>" />
    <meta property="og:image"         content="<?php echo $ItemDetails['Item']['image'] ?>" />
    <?php }?>
</head>
<body>
    
</body>
</html>    