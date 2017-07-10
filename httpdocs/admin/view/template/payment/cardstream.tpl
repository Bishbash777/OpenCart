<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="cardstream-settings-form" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <?php if (isset($error_warning)) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="cardstream-settings-form" class="form-horizontal">
                    <!-- cardstream module status on/off -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="cardstream_status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="cardstream_status" id="cardstream_status" class="form-control">
                                <?php if ($cardstream_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- cardstream integration type -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="cardstream_module_types"><?php echo $entry_module_type; ?></label>
                        <div class="col-sm-10">
                            <select name="cardstream_module_type" id="cardstream_module_type" class="form-control">
                                <option value="hosted" <? echo $cardstream_module_type == 'hosted' ? 'selected="selected"' : ''; ?> ><?php echo $entry_module_hosted; ?></option>
                                <option value="iframe" <? echo $cardstream_module_type == 'iframe' ? 'selected="selected"' : ''; ?>><?php echo $entry_module_iframe; ?></option>
                                <?php /*<option value="direct" <? echo $cardstream_module_type == 'direct' ? 'selected="selected"' : ''; ?>><?php echo $entry_module_direct; ?></option>*/ ?>
                            </select>
                        </div>
                    </div>


                    <!-- merchant id -->
                    <div class="form-group <?php if ( isset( $error_merchantid ) ) { ?> has-error has-feedback<?php } ?>" >
                        <label class="col-sm-2 control-label" for="cardstream_merchantid"><?php echo $entry_merchantid; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="cardstream_merchantid" value="<?php echo $cardstream_merchantid; ?>" placeholder="<?php echo $text_merchantid; ?>" id="cardstream_merchantid" class="form-control" />
                            <?php if ( isset( $error_merchantid ) ) { ?>
                            <p class="help-block"><?php echo $error_merchantid; ?></p>
                            <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- merchant secret pass -->
                    <div class="form-group <?php if ( isset( $error_merchantsecret ) ) { ?> has-error has-feedback<?php } ?>" >
                        <label class="col-sm-2 control-label" for="cardstream_merchantsecret"><?php echo $entry_merchantsecret; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="cardstream_merchantsecret" value="<?php echo $cardstream_merchantsecret; ?>" placeholder="<?php echo $text_merchantsecret; ?>" id="cardstream_merchantsecret" class="form-control" />
                            <?php if ( isset( $error_merchantsecret ) ) { ?>
                            <p class="help-block"><?php echo $error_merchantsecret; ?></p>
                            <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Currency Code -->
                    <div class="form-group <?php if ( isset( $error_currencycode ) ) { ?> has-error has-feedback<?php } ?>" >
                        <label class="col-sm-2 control-label" for="cardstream_currencycode"><?php echo $entry_currencycode; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="cardstream_currencycode" value="<?php echo $cardstream_currencycode; ?>" placeholder="<?php echo $text_currencycode; ?>" id="cardstream_currencycode" class="form-control" />
                            <?php if ( isset( $error_currencycode ) ) { ?>
                            <p class="help-block"><?php echo $error_currencycode; ?></p>
                            <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Country Code -->
                    <div class="form-group <?php if ( isset( $error_countrycode ) ) { ?> has-error has-feedback<?php } ?>" >
                        <label class="col-sm-2 control-label" for="cardstream_countrycode"><?php echo $entry_countrycode; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="cardstream_countrycode" value="<?php echo $cardstream_countrycode; ?>" placeholder="<?php echo $text_countrycode; ?>" id="cardstream_countrycode" class="form-control" />
                            <?php if ( isset( $error_countrycode ) ) { ?>
                            <p class="help-block"><?php echo $error_countrycode; ?></p>
                            <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- cardstream geo zone -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="cardstream_geo_zone_id"><?php echo $entry_geo_zone; ?></label>
                        <div class="col-sm-10">
                            <select name="cardstream_geo_zone_id" id="cardstream_geo_zone_id" class="form-control">
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

                     <!-- cardstream form responsive -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="cardstream_form_responsive"><?php echo $entry_form_responsive; ?></label>
                        <div class="col-sm-10">
                            <select name="cardstream_form_responsive" id="cardstream_form_responsive" class="form-control">
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>


                    <!-- Cardstream Order Status -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="cardstream_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $cardstream_order_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- cardstream sort order -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="cardstream_sort_order" value="<?php echo $cardstream_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>
<?php echo $footer; ?>
