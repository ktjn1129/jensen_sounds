<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 トップページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


$dbPickupData = getPickup();

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
require('head.php');
?>
<body>
  <?php
  require('header.php')
  ?>
  <main id="main">
    <h2>Pick Up</h2>
    <section class="record_shelf">
      <?php
        foreach($dbPickupData as $key => $value):
      ?>
      <a class="pickup" href="prodDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$value['id'] : '?p_id='.$value['id']; ?>">
        <img src="<?php echo sanitize($value['pic1']); ?>" alt="<?php echo sanitize($value['title']); ?>">
      </a>
      <?php
        endforeach;
      ?>
    </section>
  </main>
  <?php
  require('footer.php');
  ?>
