<div class="box">
  <div class="top" style="background: url('catalog/view/image/icon_brands.png') 8px 8px no-repeat; padding-left: 30px;"><?php echo $heading_title; ?></div>
  <div class="middle" style="text-align: center;">
    <select onchange="location=this.value">
      <option value=""><?php echo $text_select; ?></option>
      <?php foreach ($manufacturers as $manufacturer) { ?>
      <?php if ($manufacturer['manufacturer_id'] == $manufacturer_id) { ?>
      <option value="<?php echo $manufacturer['href']; ?>" selected="selected"><?php echo $manufacturer['name']; ?></option>
      <?php } else { ?>
      <option value="<?php echo $manufacturer['href']; ?>"><?php echo $manufacturer['name']; ?></option>
      <?php } ?>
      <?php } ?>
    </select>
  </div>
  <div class="bottom"></div>
</div>
