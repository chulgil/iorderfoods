<!DOCTYPE html>
<html>
<head>
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="shortcut icon" href="/img/favicon.png">
	<title>Restaurant Online Ordering System</title>
        <?php 
        $ImageLink="";
        if($itemDetails['Item']['image']){
         $ImageLink= $itemDetails['Store']['store_url']."/MenuItem-Image/".$itemDetails['Item']['image'];
        }     
        
        ?>
        <meta name="url"   content="<?php echo $itemDetails['Store']['store_url']; ?>" />
        <meta name="type"  content="website" />
        <meta name="description" content="wild cod, beer battered or grilled, wild baby arugula, onion, pickle, chipotle tartar, avocado cilantro creme">
        <meta name="title" content="<?php echo $itemDetails['Item']['name']; ?>" />
        
        <meta property="og:url"           content="<?php echo $itemDetails['Store']['store_url']; ?>" />
        <meta property="og:type"          content="website" />
        <meta property="og:title"         content="<?php echo $itemDetails['Item']['name']; ?>" />
        <meta property="og:description"   content="<?php echo $itemDetails['Item']['description']; ?>" />
        <meta property="og:image"         content="<?php echo $ImageLink;?>" />
        
</head>
<body>
</body></html>