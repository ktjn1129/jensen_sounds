<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 商品詳細ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$dbProductData = getProduct($p_id);
debug('取得した商品データ：'.print_r($dbProductData, true));

if(empty($dbProductData)){
  error_log('エラー発生：指定ページに不正な値が入りました。');
  header("Location:index.php");
}


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
    <section class="product_detail">
      <div class="img_container">
        <img src="<?php echo showImg(sanitize($dbProductData['pic1'])); ?>">
        <img src="<?php echo showImg(sanitize($dbProductData['pic2'])); ?>">
        <img src="<?php echo showImg(sanitize($dbProductData['pic3'])); ?>">
      </div>
      <div class="data_container">
        <ul>
          <li class="title"><?php echo sanitize($dbProductData['title']) ?></li>
          <li class="artist">Artist: <?php echo sanitize($dbProductData['artist']) ?></li>
          <li class="label">Label: <?php echo sanitize($dbProductData['label']) ?></li>
          <li class="category">Genre: <?php echo sanitize($dbProductData['category']) ?></li>
          <li class="format">Format: <?php echo sanitize($dbProductData['format']) ?></li>
          <li class="price">¥<?php echo sanitize(number_format($dbProductData['price'])) ?></li>
        </ul>
        <div class="button">
          <i class="fa fa-heart icn-like js-click-like <?php if(isLike($_SESSION['user_id'], $dbProductData['id'])) { echo 'active'; } ?>" data-productid="<?php echo sanitize($dbProductData['id']); ?>"></i>
          <a href=""><i class="fas fa-shopping-cart"></i></a>
        </div>
        <div class="caption">
          <?php echo sanitize($dbProductData['caption']) ?>
        </div>
      </div>
    </section>
  </main>
  <?php
  require('footer.php');
  ?>
