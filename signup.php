<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 アカウント新規登録ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


if(!empty($_POST)){

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  if(empty($err_msg)){

    validEmail($email, 'email');
    validMaxLen($email, 'email');
    validEmailDup($email);

    validHalf($pass, 'pass');
    validMaxLen($pass, 'pass');
    validMinLen($pass, 'pass');

    validMaxLen($pass_re, 'pass_re');
    validMinLen($pass_re, 'pass_re');
    validMatch($pass, $pass_re, 'pass_re');

    if(empty($err_msg)){

      try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO users (email, pass, created_date) VALUES (:email, :pass, :created_date)';
        $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                      ':created_date' => date('Y-m-d H:i:s'));
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
          $sesLimit = 60*60;
          $_SESSION['login_date'] = time();
          $_SESSION['login_limit'] = $sesLimit;
          $_SESSION['user_id'] = $dbh->lastInsertId();

          debug('セッション変数内容：'.print_r($_SESSION, true));
          debug('マイページへ遷移します。');
          header("Location:mypage.php");
        }
      } catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
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
    <section class="signup_form">
      <h2>アカウント新規登録</h2>
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
        <label class="<?php echo addClassErr('pass'); ?>">
          パスワード<span style="font-size: 0.75rem"> ※英数字6文字以上</span>
          <input type="password" name="pass" value="">
        </label>
        <div class="err_msg">
          <?php echo getErrMsg('pass'); ?>
        </div>
        <label class="<?php echo addClassErr('pass_re'); ?>">
          パスワード<span style="font-size: 0.9rem"> (再入力)</span>
          <input type="password" name="pass_re" value="">
        </label>
        <div class="err_msg">
          <?php echo getErrMsg('pass_re'); ?>
        </div>
        <div class="submit_button">
          <input type="submit" name="submit" value="登録">
        </div>
      </form>
    </section>
  </main>
  <?php
  require('footer.php');
  ?>
