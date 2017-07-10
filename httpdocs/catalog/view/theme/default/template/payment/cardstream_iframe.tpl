<?php

	$formdata = array(
		"merchantID"        => $merchantid,
		"amount"            => $amount,
		"action"            => "SALE",
		"type"              => 1,
		"countryCode"       => $countrycode,
		"currencyCode"      => $currencycode,
		"transactionUnique" => $trans_id,
		"orderRef"          => "Order " . $trans_id,
		"redirectURL"       => $callback,
		"callbackURL" 		=> $callback,
		"customerName"      => $bill_name,
		"formResponsive"	=> $form_responsive,
		"customerAddress"   => $bill_addr,
		"customerPostCode"  => $bill_post_code,
		"customerEmail"     => $bill_email,
		"customerPhone"     => $bill_tel,
		"item1Description"  => "Order " . $trans_id,
		"item1Quantity"     => 1,
		"item1GrossValue"   => $amount
);
ksort( $formdata );

$signature = http_build_query( $formdata, '', '&' ) . $merchantsecret;

$formdata['signature'] = hash( 'SHA512', $signature ).'|'.implode(',', array_keys($formdata));

?>
<iframe id="cardstreamframe" name="cardstreamframe" frameBorder="0" seamless='seamless' style="width:100%; height:100%;margin: 0 auto;display:block;border:0"></iframe>

<form id="cardstreamPaymentForm" action="https://gateway.cardstream.com/hosted/" method="post" target="cardstreamframe">
    <?php
		foreach ( $formdata as $key => $value ) {

    echo "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\" />";

    }
    ?>


</form>
<script>
    // Adapt to mobile
    if (window.jQuery) {
    	$(window).resize(function() {
    		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                $('#cardstreamframe').css({ height : '1300px', width : '100%'});
            } else {
                $('#cardstreamframe').css({ height : '1073px', width : '699px'});
            }
    	});
    	$(window).trigger('resize');
    }
    document.getElementById('cardstreamPaymentForm').submit();
</script>