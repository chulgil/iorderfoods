<form action="<?=$formURL?>" method="POST">
    <input type="hidden" id="ccexp" name="billing-cc-exp" value="1012" />
    <input type="hidden" id="nzcardtype" value=""/>

    <div class="field">
</div>
    <div class="field">
        <div class="lable_div">Credit Card Number</div>
        <div><input type="text" required name="billing-cc-number" value="" id="card_number" placeholder="Credit Card Number"></div>
    </div>

    <div class="field">
        <div class="lable_div">Expiration Date</div>
        <div id="month_div">
            <select class="expdate" required name="month" id="pay_month">
                <option value="" disabled selected hidden>Month</option>
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>
        </div>
        <div id="year_div">
            <select class="expdate" required name="year" id="pay_year">
                <option value="" disabled selected hidden>Year</option>
                <option value="01">2016</option>
                <option value="02">2017</option>
                <option value="03">2018</option>
                <option value="04">2019</option>
                <option value="05">2020</option>
                <option value="06">2021</option>
                <option value="07">2022</option>
                <option value="08">2023</option>
                <option value="09">2024</option>
                <option value="10">2025</option>
                <option value="11">2026</option>
                <option value="12">2027</option>
                <option value="01">2028</option>
                <option value="02">2029</option>
                <option value="03">2030</option>
                <option value="04">2031</option>
                <option value="05">2032</option>
                <option value="06">2033</option>
                <option value="07">2034</option>
                <option value="08">2035</option>
                <option value="09">2036</option>
            </select>
        </div>
    </div>

    <div class="field">
        <div class="lable_div">CVV</div>
        <div><input type="text" id="cvv" required class="longtext" placeholder="CVV" name="cvv" /></div>
    </div>


    <div><input type="submit" value="Check Out" id="continue_button" /></div>

</form>

<script>
$(document).ready(function () {

    $('#card_number').validateCreditCard(function (result) {
	var flag=true;
	if(result.card_type != null){
		$.ajax({
		    type: 'post',
		    async:false,
		    url: "<?php echo $this->Html->url(array('controller' => 'MBServices', 'action' => 'checkCardType')); ?>",
		    data: {'result': result.card_type.name},
		    success: function (res) {
			var res1 = $.parseJSON(res);
			if (res1.response==0) {
			   flag=false;
			}
		    }
		});
	}

	if(!flag){
	  result =null;	
	}

	
        $('.log').html('<strong>Card type: </strong>' + (result.card_type == null ? '-' : result.card_type.name)
         + '<br><strong>Valid: </strong>' + result.valid
         + '<br><strong>Length valid: </strong>' + result.length_valid
         + '<br><strong>Luhn valid: </strong>' + result.luhn_valid);
        if (result.card_type == null) {
            $('#card_number').removeClass();
        }
        else {
            $('#card_number').addClass(result.card_type.name);
        }

        if (!result.valid) {
            $('#card_number').removeClass("valid");
            $('#nzcardtype').val("");
        }
        else {
            $('#card_number').addClass("valid");
            $('#nzcardtype').val(result.card_type.name);
        }
    });

    $("form").submit(function(e){
        var card_type = $('#nzcardtype').val();
        var card_num = $('#card_number').val();
        var card_mask = card_num.substr(card_num.length - 4); // => "Tabs1"
        $.get( "nzgate_cardtype/"+card_type+"/"+card_mask, function( data ) {});
        var month = $("#pay_month :selected").text();
        var year = $("#pay_year :selected").text();
        var ccexp = year.slice(2,4);
        $("#ccexp").val(month+ccexp);
    })
});

</script>
