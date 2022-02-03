<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 商品一覧ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';

$listSpan = 20;
$currentMinNum = (($currentPageNum - 1) * $listSpan);

$dbProductList = getProductList($category, $currentMinNum);
$dbCategoryData = getCategory();


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
    <div class="page_nav">
      <div class="record_count">
        <span><?php echo (!empty($dbProductList['data'])) ? $currentMinNum+1 : 0; ?></span> - <span><?php echo $currentMinNum+count($dbProductList['data']); ?></span>件 / <span><?php echo sanitize($dbProductList['total']); ?></span>件中
      </div>
      <?php pagination($currentPageNum, $dbProductList['total_page']); ?>
    </div>
    <div class="panel_list">
      <?php
        foreach ($dbProductList['data'] as $key => $value):
      ?>
        <a class="panel" href="prodDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$value['id'] : '?p_id='.$value['id']; ?>">
          <div class="panel_head">
            <img src="<?php echo sanitize($value['pic1']); ?>" alt="<?php echo sanitize($value['title']); ?>">
          </div>
          <div class="panel_body">
            <ul>
              <li class="title"><?php echo sanitize($value['title']); ?></li>
              <li class="artist"><?php echo sanitize($value['artist']); ?></li>
              <li class="label"><?php echo sanitize($value['label']); ?></li>
              <li class="format"><?php echo sanitize($value['format']); ?></li>
              <li class="price">¥<?php echo sanitize(number_format($value['price'])); ?></li>
            </ul>
          </div>
        </a>
      <?php
        endforeach;
      ?>
    </div>
  </main>
  <?php
  require('footer.php');
  ?>
