<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 アカウント情報編集ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザーデータ：'.print_r($dbFormData, true));

if(!empty($_POST)){
  debug('POST情報があります');
  debug('POST情報：'.print_r($_POST, true));

  $name = $_POST['name'];
  $zip = $_POST['zip'];
  $addr = $_POST['addr'];
  $tel = $_POST['tel'];
  $email = $_POST['email'];

  if($dbFormData['name'] !== $name){
    validMaxLen($name,'name');
  }
  if($dbFormData['zip'] !== $zip){
    validZip($zip, 'zip');
  }
  if($dbFormData['addr'] !== $addr){
    validMaxLen($addr, 'addr');
  }
  if($dbFormData['tel'] !== $tel){
    validTel($tel,'tel');
  }
  if($dbFormData['email'] !== $email){
    validRequired($email,'email');

    if(empty($err_msg['email'])){
      validEmail($email, 'email');
      validMaxLen($email, 'email');
      validEmailDup($email, 'email');
    }
  }
  if(empty($err_msg)){
    debug('バリデーションチェックが完了しました。');

    try{
      $dbh = dbConnect();
      $sql = 'UPDATE users SET name = :name, zip = :zip, addr = :addr, tel = :tel, email = :email';
      $data = array(':name' => $name, ':zip' => $zip, ':addr' => $addr, ':tel' => $tel, ':email' => $email);
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $_SESSION['suc_msg'] = SUC02;
        debug('マイぺージへ遷移します。');
        header("Location:mypage.php");
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
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
      require('sidebar.php');
      ?>
      <section class="form_container">
        <h2>アカウント情報変更</h2>
        <form class="form" action="" method="post">
          <div class="err_msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <label class="<?php echo addClassErr('name'); ?>">
            お名前
            <input type="text" name="name" value="<?php echo getFormData('name'); ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('name'); ?>
          </div>
          <label class="<?php echo addClassErr('zip'); ?>">
            郵便番号<span style="font-size: 0.75rem"> ※ハイフンなしでご入力ください</span>
            <input type="text" name="zip" value="<?php if( !empty(getFormData('zip')) ){ echo getFormData('zip'); } ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('zip'); ?>
          </div>
          <label class="<?php echo addClassErr('addr'); ?>">
            ご住所
            <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('addr'); ?>
          </div>
          <label class="<?php echo addClassErr('tel'); ?>">
            電話番号<span style="font-size: 0.75rem"> ※ハイフンなしでご入力ください</span>
            <input type="text" name="tel" value="<?php echo getFormData('tel'); ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('tel'); ?>
          </div>
          <label class="<?php echo addClassErr('email'); ?>">
            メールアドレス
            <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('email'); ?>
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
