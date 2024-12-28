<!-- Listings Loader -->
<div class="listings-loader">
  <div class="spinner">
    <div class="double-bounce1"></div>
    <div class="double-bounce2"></div>
  </div>
</div>
<?php
if(isset($style)) {
  $resume_style = $style;
} else {
  $resume_style = 'grid';
}
?>
<?php $layout = Kirki::get_option('workscout', 'pp_resume_old_layout', false);  ?>

<!-- <ul class="resumes <?php if (!$layout) { ?>alternative<?php } ?>"> -->
  <ul class="resumes freelancers-container freelancers-<?php echo $resume_style; ?>-layout <?php if($resume_style =="list") echo "compact-list"; ?> margin-top-35">