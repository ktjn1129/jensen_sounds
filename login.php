<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 ログインページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


if(!empty($_POST)){
  debug('POST送信があります。');

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $savepass = (!empty($_POST['savepass'])) ? true : false ;

  validRequired($email, 'email');
  validRequired($pass, 'pass');

  validEmail($email, 'email');
  validMaxLen($email, 'email');

  validHalf($pass, 'pass');
  validMaxLen($pass, 'pass');
  validMinLen($pass, 'pass');

  if(empty($err_msg)){
    debug('バリデーションチェックが完了しました。');

    try{
      $dbh = dbConnect();
      $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      $stmt = queryPost($dbh, $sql, $data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      debug('クエリ結果内容：'.print_r($result, true));

      if(!empty($result) && password_verify($pass, array_shift($result))){
        debug('パスワードがマッチしました。');
        $sesLimit = 60 * 60;

        if($savepass){
          debug('ログイン保持にチェックがあります。');
          $_SESSION['login_limit'] = $sesLimit * 24 * 30;
        }else{
          debug('ログイン保持にチェックはありません。');
          $_SESSION['login_limit'] = $sesLimit;
        }

        $_SESSION['user_id'] = $result['id'];
        $_SESSION['login_date'] = time();

        debug('セッション変数内容：'.print_r($_SESSION, true));
        debug('マイページへ遷移します。');
        header("Location:mypage.php");

      }else{
        debug('パスワードがマッチしませんでした。');
        $err_msg['common'] = MSG09;
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
  <p id="js-show-msg" style="display:none;" class="slide_msg">
    <?php echo getSessionFlash('suc_msg'); ?>
  </p>
  <main id="main">
    <section class="form_container">
      <h2>ログイン</h2>
      <form class="form" action="" method="post">
        <div class="err_msg">
          <?php echo getErrMsg('common'); ?>
        </div>
        <label class="<?php echo addClassErr('email'); ?>">
          メールアドレス
          <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
        </label>
        <div class="err_msg">
          <?php echo getErrMsg('email'); ?>
        </div>
        <label class="<?php echo addClassErr('email'); ?>">
          パスワード
          <input type="password" name="pass" value="">
        </label>
        <div class="err_msg">
          <?php echo getErrMsg('pass'); ?>
        </div>
        <label>
          <input type="checkbox" name="savepass" value="">
          <span class="savepass">次回以降ログインを省略する</span>
        </label>
        <div class="submit_button">
          <input type="submit" name="submit" value="ログイン">
        </div>
        <div class="login_msg">
          <p>パスワードを忘れた方は<a href="sendKey.php">こちら</a></p>
          <p>アカウントの新規登録は<a href="signup.php">こちら</a></p>
        </div>
      </form>
    </section>
  </main>
  <?php
  require('footer.php');
  ?>
