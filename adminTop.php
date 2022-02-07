<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 管理者用トップページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('adminAuth.php');


debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
require('head.php');
?>
<body>
  <?php
  require('header.php')
  ?>
  <p id="js-show-msg" style="display: none;" class="slide_msg">
    <?php echo getSessionFlash('suc_msg'); ?>
  </p>
  <main id="main">
    <div class="container">
      <?php
      require('adminSide.php');
      ?>
      <section class="form_container">
      </section>
    </div>
  </main>
  <?php
  require('footer.php');
  ?>
