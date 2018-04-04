<?php defined('_JEXEC') or die('Restricted access'); ?>

<form name="adminForm" id="adminForm" action="" method="post">

  <div class="errorvectortable">

    <div>
      <?= $this->lists["select_showtargets"] ?>
    </div>
    <br>

    <?= $this->errorvector_content ?>

    <div>
      <?= $this->lists["select_showtargets2"] ?>
    </div>
    <br>

  </div>

  <input type="submit" name="submit1" id="submit1" style="display: none;">
  <input type="submit" name="submit2" id="submit2" style="display: none;">
</form>

<script>
  jQuery(function ($) {
    
    $("#showtargets").change(function () {
      $("#submit1").click();
    });
    
    $("#showtargets2").change(function () {
      $("#submit2").click();
    });
    
  });
</script>