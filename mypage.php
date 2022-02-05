<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 マイページ（ウィッシュリスト） ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


$u_id = $_SESSION['user_id'];
$dbWishlist = getWishlist($u_id);
debug('取得したウィッシュリストデータ：'.print_r($dbWishlist, true));


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
      require('sidebar.php');
      ?>
      <section class="form_container">
        <h2>Wishlist</h2>
        <div class="panel_list">
          <?php
            if(!empty($dbWishist)):
            foreach ($dbWishlist as $key => $value):
          ?>
            <a href="prodDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.val['id'] : '?p_id='.val['id']; ?>" class="panel">
              <div class="panel_head">
                <img src="<?php echo sanitize($value['pic1']); ?>" alt="<?php echo sanitize($value['title']); ?>">
              </div>
              <div class="panel_body">
                <ul>
                  <li class="artist"><? php echo sanitize($value['artist']); ?></li>
                  <li class="title"><? php echo sanitize($value['title']); ?></li>
                  <li class="label"><? php echo sanitize($value['label']); ?></li>
                  <li class="format"><? php echo sanitize($value['format']); ?></li>
                  <li class="price">¥<? php echo sanitize(number_format($value['price'])); ?></li>
                </ul>
              </div>
            </a>
          <?php
            endforeach;
            endif;
          ?>
        </div>
      </section>
    </div>
  </main>
  <?php
  require('footer.php');
  ?>
