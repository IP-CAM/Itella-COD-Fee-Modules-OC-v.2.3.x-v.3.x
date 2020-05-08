<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><img src="view/image/itella_cod/logo.png" alt="Itella Logo"><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb): ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php endforeach; ?>
      </ul>
      <span class="itella-version">v<?= $itella_cod_version; ?></span>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning): ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php endif; ?>

    <div class="panel panel-default">
      
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>

      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-cod" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="itella_cod_status" id="input-status" class="form-control">
                <?php if ($itella_cod_status): ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                <?php else: ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php endif; ?>
              </select>
            </div>
          </div>
        
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="itella_cod_total" value="<?php echo $itella_cod_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
              <div class="itella-help"><?php echo $help_total; ?></div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
            <div class="col-sm-10">
              <select name="itella_cod_order_status_id" id="input-order-status" class="form-control">
                <?php foreach ($order_statuses as $order_status): ?>
                  <?php if ($order_status['order_status_id'] == $itella_cod_order_status_id): ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php else: ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-geo-zone"><span data-toggle="tooltip" title="<?php echo $help_geo_zone; ?>"><?php echo $entry_geo_zone; ?></span></label>
            <div class="col-sm-10">
              <select name="itella_cod_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone): ?>
                  <?php if ($geo_zone['geo_zone_id'] == $itella_cod_geo_zone_id): ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                  <?php else: ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
              <div class="itella-help"><?php echo $help_geo_zone; ?></div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="itella_cod_sort_order" value="<?php echo $itella_cod_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>

          <?php foreach($languages as $lng): ?>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-terms<?= $lng['language_id']; ?>"><?php echo $entry_terms; ?></label>
              <div class="col-sm-10">
                <div class="input-group">
                  <span class="input-group-addon"><img src="language/<?= $lng['code'] . '/' . $lng['code']; ?>.png" title="<?= $lng['name']; ?>"></span>
                  <input type="text" name="itella_cod_terms[<?= $lng['language_id']; ?>]" value="<?php echo $itella_cod_terms[$lng['language_id']]; ?>" placeholder="<?php echo $entry_terms; ?>" id="input-terms<?= $lng['language_id']; ?>" class="form-control">	
                </div>
              </div>
            </div>
          <?php endforeach; ?>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_shipping_options; ?></label>
            <div class="col-sm-10">
              <p class="help-block"><?= $text_shipping_options_help; ?></p>
              <div class="itella-checkboxes">
                <?php foreach ($shipping_options as $key => $shipping_name) : ?>
                  <div class="checkbox">
                    <input type="checkbox" name="itella_cod_shipping_options[]" id="shipping-option-<?= $key; ?>" value="<?= $key; ?>" <?= (in_array($key, $itella_cod_shipping_options) ? 'checked' : ''); ?>>
                    <label for="shipping-option-<?= $key; ?>"><?= $shipping_name; ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

        </form>
      </div>

      <div class="panel-footer clearfix">
        <div class="pull-right">
          <button type="submit" form="form-cod" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
          <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
        </div>
      </div>

    </div>
    <!-- PANEL END -->
  </div>
  <!-- CONTAINER END -->
</div>

<style>
  .page-header h1 img {
    height: 33px;
    margin-right: 1rem;
  }

  .itella-help {
    display: none;
  }

  @media only screen and (max-width: 768px) {
    span[data-toggle="tooltip"]::after {
      display: none;
    }

    .itella-help {
      display: block;
    }
  }
</style>

<?php echo $footer; ?> 