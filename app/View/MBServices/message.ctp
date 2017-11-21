<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<input type="hidden" id="token" value="<?=$token?>"/>
<input type="hidden" id="error" value="<?=$message?>"/>

<?php  if($token) { ?>
<div id="error_wrap">
    <div id="checkbox"><i id="checkicon1" class="fa fa-refresh" aria-hidden="true"></i></div>
    <div id="large_message1"><div style="margin-top:3%;">Processing</div></div>
    <div id="small_message1">Your request is being processed successfully. </div>
    <div id="button"><button id="button_ok" onclick="getTokenFromJS()">CONTINUE</button></div>
</div>

<?php } else { ?>
<div id="error_wrap">
    <div id="checkbox"><i id="checkicon" class="fa fa-exclamation-circle" aria-hidden="true"></i></div>
    <div id="large_message"><div id="error">Error!</div></div>
    <div id="small_message"><?=$message?></div>
    <div id="small_message">Please try again.</div>
    <div id="button"><button id="button_ok">OK</button></div>
</div>
<?php }?>

<script>
<?php if($isiOS){  ?>
    var getTokenFromJS = function() {
        window.location = "iofApp:getTokenFromJS:"+$("#token").val();
    }
    var errorFromJS = function() {
        window.location = "iofApp:errorFromJS:1";
    }
<?php } else if($isAndroidOS){  ?>
    function getTokenFromJS(){
        window.JSInterface.getTokenFromJS($("#token").val());
    }
    function errorFormJS(){
        window.JSInterface.errorFormJS(1);
    }
<?php } else { ?>
    function getTokenFromJS() {
        alert("mobile only work.");
    }
<?php }?>
</script>
