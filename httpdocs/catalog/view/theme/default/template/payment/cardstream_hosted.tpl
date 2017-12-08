<?php if (isset($loadIframe) && $loadIframe == true) { ?>
	<iframe id="paymentgatewayframe" name="paymentgatewayframe" frameBorder="0" seamless='seamless' style="width:699px; height:1073px;margin: 0 auto;display:block;"></iframe>
<?php } ?>

<form id="paymentgatewaymoduleform" action="<?=$form_hosted_url?>" method="post" <?php if (isset($loadIframe) && $loadIframe == true) { ?> target="paymentgatewayframe" <?php } ?>>
	<?php foreach ($formdata as $key=>$value) { ?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>"/>
	<?php } ?>

	<?php if (empty(@$loadIframe)) { ?>
	<div class="buttons">
		<div class="pull-right">
			<input type="submit" value="<?=$button_confirm?>" id="button-confirm" class="btn btn-primary" />
		</div>
	</div>
	<?php } ?>
</form>

<?php if (isset($loadIframe) && $loadIframe == true) { ?>
<script>
	// detects if jquery is loaded and adjusts the form for mobile devices
	if (window.jQuery) {
		$(function(){
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
				$('form#paymentgatewaymoduleform').append('<input type="hidden" name="formResponsive"  value="Y"/>');
				$('#paymentgatewayframe').css({ height : '1300px', width : '50%'});
			}
		});
	}
	document.getElementById('paymentgatewaymoduleform').submit();
</script>
<?php } ?>