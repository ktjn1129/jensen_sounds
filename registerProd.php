<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 商品登録ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('adminAuth.php');


$dbCategoryData = getCategory();
debug('取得したカテゴリーデータ：'.print_r($dbCategoryData, true));


if(!empty($_POST)){
  debug('POST情報があります');
  debug('POST情報：'.print_r($_POST, true));
  debug('FILE情報：'.print_r($_POST, true));

  $title = $_POST['title'];
  $artist = $_POST['artist'];
  $label = $_POST['label'];
  $category = $_POST['category_id'];
  $format = $_POST['format'];
  $price = $_POST['price'];
  $caption = $_POST['caption'];
  $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'], 'pic1') : '';
  $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'], 'pic2') : '';
  $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'], 'pic3') : '';

  validRequired($title, 'title');
  validRequired($artist, 'artist');
  validRequired($label, 'label');
  validRequired($format, 'format');
  validRequired($price, 'price');

  if(!empty($err_msg)){
    debug('未入力チェックが完了しました。');

    validMaxLen($title, 'title');
    validMaxLen($artist, 'artist');
    validMaxLen($label, 'label');
    validSelect($category, 'category_id');
    validMaxLen($format, 'format');
    validNumber($price, 'price');
    validMaxLen($caption, 'caption');

    if(!empty($err_msg)){
      debug('バリデーションチェックが完了しました。');

      try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO products (title, artist, label, category_id, format, price, caption, pic1, pic2, pic3, created_date)
                VALUES (:title, :artist, :label, :category_id, :format, :price, :caption, :pic1, :pic2, :pic3, :created_date)';
        $data = array(':title' => $title, ':artist' => $artist, ':label' => $label, ':category_id' => $category, ':format' => $format, ':price' => $price,
                      ':caption' => $caption, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':created_date' => date('Y-m-d H:i:s'));
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
          $_SESSION['suc_msg'] = SUC04;
          debug('マイページへ遷移します。');
          header("Location:mypage.php");
        }
      }catch(Exception $e){
        debug('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
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
    <div class="container">
      <?php
      require('adminSide.php');
      ?>
      <section class="form_container">
        <h2>商品登録</h2>
        <form class="form" action="" method="post">
          <div class="err_msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <label class="<?php echo addClassErr('title'); ?>">
            曲名
            <input type="text" name="title" value="<?php echo getFormData('title'); ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('title'); ?>
          </div>
          <label class="<?php echo addClassErr('artist'); ?>">
            アーティスト名
            <input type="text" name="artist" value="<?php echo getFormData('artist'); ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('artist'); ?>
          </div>
          <label class="<?php echo addClassErr('label'); ?>">
            レーベル名
            <input type="text" name="label" value="<?php echo getFormData('label'); ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('label'); ?>
          </div>
          <label class="<?php echo addClassErr('category'); ?>">
            <span style="margin-right:10px;">ジャンル</span>
            <select name="category_id">
              <option value="0" <?php if(getFormData('category_id') == 0){ echo 'selected'; } ?>>
                選択してください
              </option>
              <?php foreach ($dbCategoryData as $key => $value) { ?>
                <option value="<?php echo $value['id'] ?>" <?php if(getFormData('category_id') == $value['id']){ echo 'selected'; } ?>>
                  <?php echo $value['name'] ?>
                </option>
              <?php } ?>
            </select>
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('category_id'); ?>
          </div>
          <label class="<?php echo addClassErr('format'); ?>">
            フォーマット
            <input type="text" name="format" value="<?php echo getFormData('format'); ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('label'); ?>
          </div>
          <label class="<?php echo addClassErr('price'); ?>">
            価格(円)
            <input type="text" name="price" value="<?php echo getFormData('price'); ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('price'); ?>
          </div>
          <label class="<?php echo addClassErr('pickup'); ?>">
            <span style="margin-right:10px;">ピックアップ</span>
            <select name="pickup">
              <option value="0" <?php if(getFormData('pickup') == 0){ echo 'selected'; } ?>>追加しない</option>
              <option value="1" <?php if(getFormData('pickup') == 1){ echo 'selected'; } ?>>追加する</option>
            </select>
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('pickup'); ?>
          </div>
          <label class="<?php echo addClassErr('caption'); ?>">
            <span>商品説明</span>
            <textarea name="caption" rows="10" cols="30" style="width:100%; height:150px;"><?php echo getFormData('caption'); ?></textarea>
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('caption'); ?>
          </div>
          <label class="<?php echo addClassErr('pic1'); ?>">
            メイン画像
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic1" class="input_file">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('pic1'); ?>
          </div>
          <label class="<?php echo addClassErr('pic2'); ?>">
            サブ画像
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic2" class="input_file">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('pic2'); ?>
          </div>
          <label class="<?php echo addClassErr('pic3'); ?>">
            サブ画像
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic3" class="input_file">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('pic3'); ?>
          </div>
          <div class="submit_button">
            <input type="submit" name="submit" value="登録">
          </div>
        </form>
      </section>
    </div>
  </main>
  <?php
  require('footer.php');
  ?>
