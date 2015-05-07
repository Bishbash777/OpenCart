<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
	      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
	          <div class="form-group">
	            <label class="col-sm-2 control-label"><?php echo $entry_module_type; ?></label>
	            <div class="col-sm-10">
	            	<select name="cardstream_module_type" class="form-control">
	                  <option value="hosted" <? echo $cardstream_module_type == 'hosted' ? 'selected="selected"' : ''; ?> ><?php echo $entry_module_hosted; ?></option>
	                  <option value="iframe" <? echo $cardstream_module_type == 'iframe' ? 'selected="selected"' : ''; ?>><?php echo $entry_module_iframe; ?></option>
	              </select>
	             </div>
	          </div>
	          <div class="form-group">
	            <label class="col-sm-2 control-label"><span class="required">*</span> <?php echo $entry_merchantid; ?></label>
	            <div class="col-sm-10">
	            	<input type="text" name="cardstream_merchantid" class="form-control"
	                   value="<?php if ( isset( $cardstream_merchantid ) ) {
	                     echo $cardstream_merchantid;
	                   } else {
	                     echo '100001';
	                   } ?>"/>
	              <?php if ( isset( $error_merchantid ) ) { ?>
	                <span class="error"><?php echo $error_merchantid; ?></span>
	              <?php } ?>
	             </div>
	          </div>
	          <div class="form-group">
	            <label class="col-sm-2 control-label"><span class="required">*</span> <?php echo $entry_merchantsecret; ?></label>
	            <div class="col-sm-10">
	            	<input type="text" name="cardstream_merchantsecret" class="form-control"
	                   value="<?php if ( isset( $cardstream_merchantsecret ) ) {
	                     echo $cardstream_merchantsecret;
	                   } else {
	                     echo 'Circle4Take40Idea';
	                   } ?>"/>
	              <?php if ( isset( $error_merchantsecret ) ) { ?>
	                <span class="error"><?php echo $error_merchantsecret; ?></span>
	              <?php } ?>
	             </div>
	          </div>
	          <div class="form-group">
	            <label class="col-sm-2 control-label"><span class="required">*</span> <?php echo $entry_currencycode; ?></label>
	            <div class="col-sm-10">
	            	<input type="text" name="cardstream_currencycode" class="form-control"
	                   value="<?php if ( isset( $cardstream_currencycode ) ) {
	                     echo $cardstream_currencycode;
	                   } else {
	                     echo 826;
	                   } ?>"/>
	              <?php if ( isset( $error_currencycode ) ) { ?>
	                <span class="error"><?php echo $error_currencycode; ?></span>
	              <?php } ?>
	             </div>
	          </div>
	          <div class="form-group">
	            <label class="col-sm-2 control-label"><span class="required">*</span> <?php echo $entry_countrycode; ?></label>
	            <div class="col-sm-10">
	            	<input type="text" name="cardstream_countrycode" class="form-control"
	                   value="<?php if ( isset( $cardstream_countrycode ) ) {
	                     echo $cardstream_countrycode;
	                   } else {
	                     echo 826;
	                   } ?>"/>
	              <?php if ( isset( $error_countrycode ) ) { ?>
	                <span class="error"><?php echo $error_countrycode; ?></span>
	              <?php } ?>
	             </div>
	          </div>
	          <div class="form-group">
	            <label class="col-sm-2 control-label"><?php echo $entry_order_status; ?></label>
	            <div class="col-sm-10">
	            	<select name="cardstream_order_status_id" class="form-control">
	                <?php foreach ( $order_statuses as $order_status ) { ?>
	                  <?php if ( $order_status['order_status_id'] == $cardstream_order_status_id
	                  ) {
	                    ?>
	                    <option value="<?php echo $order_status['order_status_id']; ?>"
	                        selected="selected"><?php echo $order_status['name']; ?></option>
	                  <?php } else { ?>
	                    <option
	                      value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
	                  <?php } ?>
	                <?php } ?>
	              </select>
	             </div>
	          </div>
	          <div class="form-group">
	            <label class="col-sm-2 control-label"><?php echo $entry_geo_zone; ?></label>
	            <div class="col-sm-10">
	            	<select name="cardstream_geo_zone_id" class="form-control">
	                <option value="0"><?php echo $text_all_zones; ?></option>
	                <?php foreach ( $geo_zones as $geo_zone ) { ?>
	                  <?php if ( $geo_zone['geo_zone_id'] == $cardstream_geo_zone_id ) { ?>
	                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
	                        selected="selected"><?php echo $geo_zone['name']; ?></option>
	                  <?php } else { ?>
	                    <option
	                      value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
	                  <?php } ?>
	                <?php } ?>
	              </select>
	             </div>
	          </div>
	          <div class="form-group">
	            <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
	            <div class="col-sm-10">
	            	<select name="cardstream_status" class="form-control">
	                <?php if ( $cardstream_status ) { ?>
	                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
	                  <option value="0"><?php echo $text_disabled; ?></option>
	                <?php } else { ?>
	                  <option value="1"><?php echo $text_enabled; ?></option>
	                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
	                <?php } ?>
	              </select>
	             </div>
	          </div>
	          <div class="form-group">
	            <label class="col-sm-2 control-label"><?php echo $entry_sort_order; ?></label>
	            <div class="col-sm-10">
	            	<input type="number" name="cardstream_sort_order" value="<?php echo $cardstream_sort_order; ?>" size="1" class="form-control" /> 
	             </div>
	          </div>
	      </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 