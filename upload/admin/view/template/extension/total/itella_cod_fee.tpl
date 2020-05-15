<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><img src="view/image/itella_cod/logo.png" alt="Itella Logo"><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
      <span class="itella-version">v<?= $itella_cod_fee_version; ?></span>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-itella-cod-fee" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="itella_cod_fee_status" id="input-status" class="form-control">
                <?php if ($itella_cod_fee_status) { ?>
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
            <label class="col-sm-2 control-label" for="input-tax-class"><?php echo $entry_tax_class; ?></label>
            <div class="col-sm-10">
              <select name="itella_cod_fee_tax_class_id" id="input-tax-class" class="form-control">
                <option value="0"><?php echo $text_none; ?></option>
                <?php foreach ($tax_classes as $tax_class) { ?>
                <?php if ($tax_class['tax_class_id'] == $itella_cod_fee_tax_class_id) { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="itella_cod_fee_sort_order" value="<?php echo $itella_cod_fee_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><?php echo $entry_total; ?></label>
            <div class="col-sm-5">
              <div class="input-group">
                <span class="input-group-addon"><?php echo $entry_total_min; ?></span>
                <input type="text" class="form-control" value="<?php echo $itella_cod_fee_total_min; ?>" name="itella_cod_fee_total_min" placeholder="<?php echo $entry_total_min; ?>"> 
              </div>
            </div>
            <div class="col-sm-5">
              <div class="input-group">
                <span class="input-group-addon"><?php echo $entry_total_max; ?></span>
                <input type="text" class="form-control" value="<?php echo $itella_cod_fee_total_max; ?>" name="itella_cod_fee_total_max" placeholder="<?php echo $entry_total_max; ?>"> 
              </div>
            </div>
         </div>
         
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-fee-type"><?php echo $entry_fee_type; ?></label>
            <div class="col-sm-10">
              <select name="itella_cod_fee_fee_type" id="input-fee-type" class="form-control">
                <?php if (!$itella_cod_fee_fee_type) { ?>
                <option value="0" selected="selected"><?php echo $text_fee_flat; ?></option>
                <option value="1"><?php echo $text_fee_perc; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_fee_flat; ?></option>
                <option value="1" selected="selected"><?php echo $text_fee_perc; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-fee-flat"><?php echo $entry_fee; ?></label>
            <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><?php echo $entry_fee_flat; ?></span>
                <input type="text" name="itella_cod_fee_fee_flat" value="<?php echo $itella_cod_fee_fee_flat; ?>" placeholder="<?php echo $entry_fee_flat; ?>" id="input-fee-flat" class="form-control" />
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-fee-perc"><?php echo $entry_fee; ?></label>
            <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><?php echo $entry_fee_perc; ?></span>
                <input type="text" name="itella_cod_fee_fee_perc" value="<?php echo $itella_cod_fee_fee_perc; ?>" placeholder="<?php echo $entry_fee_perc; ?>" id="input-fee-perc" class="form-control" />
              </div>
            </div>
          </div>
          
        </form>
      </div>

      <div class="panel-footer clearfix">
        <div class="pull-right">
          <button type="submit" form="form-itella-cod-fee" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
          <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .page-header h1 img {
    height: 33px;
    margin-right: 1rem;
  }
</style>
<?php echo $footer; ?> 