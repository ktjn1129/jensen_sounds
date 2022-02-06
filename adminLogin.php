<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 管理者用ログインページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


if(!empty($_POST)){
  debug('POST送信があります。');

  $admin_id = $_POST['id'];
  $pass = $_POST['pass'];

  validRequired($admin_id, 'id');
  validRequired($pass, 'pass');

  if(empty($err_msg)){
    debug('未入力チェックが完了しました。');

    validMaxLen($admin_id, 'id');
    validPass($pass, 'pass');

    if(empty($err_msg)){
      debug('バリデーションチェックが完了しました。');

      try{
        $dbh = dbConnect();
        $sql = 'SELECT password FROM admin WHERE id = :admin_id';
        $data = array(':admin_id' => $admin_id);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty($result) && password_verify($pass, array_shift($result))){
          debug('パスワードがマッチしました。');
          $sesLimit = 60 * 60;

          $_SESSION['admin_id'] = $result['id'];
          $_SESSION['login_date'] = time();

          debug('セッション変数内容：'.print_r($_SESSION, true));
          debug('管理者ページへ遷移します。');
          header("Location:adminTop.php");

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
    <section class="form_container">
      <h2>管理者ログイン</h2>
      <form class="form" action="" method="post">
        <div class="err_msg">
          <?php echo getErrMsg('common'); ?>
        </div>
        <label class="<?php echo addClassErr('id'); ?>">
          管理者ID
          <input type="text" name="id" value="<?php echo getFormData('id'); ?>">
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
        <div class="submit_button">
          <input type="submit" name="submit" value="ログイン">
        </div>
      </form>
    </section>
  </main>
  <?php
  require('footer.php');
  ?>
