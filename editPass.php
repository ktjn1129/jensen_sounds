<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 パスワード変更ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


$dbUserData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($dbUserData, true));

if(!empty($_POST)){
  debug('POST情報があります。');
  debug('POST情報：'.print_r($_POST, true));

  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');

  if(!empty($err_msg)){
    debug('未入力チェックが完了しました。');

    validPass($pass_old, 'pass_old');
    validPass($pass_new, 'pass_new');

    if(!password_verify($pass_old, $useData['password'])){
      $err_msg['pass_old'] = MSG12;
    }

    if($pass_old === $pass_new){
      $err_msg['pass_new'] = MSG13;
    }

    validMatch($pass_new, $pass_new_re);

    if(empty($err_msg)){
      debug('バリデーションチェックが完了しました。');

      try{
        $dbh = dbConnect();
        $sql = 'UPDATE users SET password = :pass WHERE id = :id';
        $data = array(':id' => $_SESSION['user_id'], ':pass' => passwordhash($pass_new, PASSWORD_DEFAULT));
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
          $_SESSION['suc_msg'] = SUC01;

          $name = ($dbUserData['name'])? $dbUserData['name'] : 'NoName';
          $from = '';
          $to = $dbUserData['email'];
          $subject = '【パスワード変更完了】| JENSEN SOUNDS';
          $coment = <<<EOT
{$name} 様

パスワードの変更が完了致しました。
引続きのご利用よろしくお願い申し上げます。


////////////////////////////////////////
Jensen Sounds カスタマーサポート
URL
E-mail
////////////////////////////////////////
EOT;
          sendMail($from, $to, $subject, $comment);

          debug('マイページへ遷移します。');
          header("Location:mypage.php");
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
    <div class="container">
      <?php
      require('sidebar.php');
      ?>
      <section class="form_container">
        <h2>パスワード変更</h2>
        <form class="form" action="" method="post">
          <div class="err_msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <label class="<?php echo addClassErr('pass_old'); ?>">
            現在のパスワード
            <input type="password" name="pass_old" value="">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('pass_old'); ?>
          </div>
          <label class="<?php echo addClassErr('pass_new'); ?>">
            新しいパスワード<span style="font-size: 0.75rem"> ※英数字6文字以上</span>
            <input type="password" name="pass_new" value="">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('pass_new'); ?>
          </div>
          <label class="<?php echo addClassErr('pass_new_re'); ?>">
            新しいパスワード<span style="font-size: 0.9rem"> (再入力)</span>
            <input type="password" name="pass_new_re" value="">
          </label>
          <div class="err_msg">
            <?php echo getErrMsg('pass_new_re'); ?>
          </div>
          <div class="submit_button">
            <input type="submit" name="submit" value="変更">
          </div>
        </form>
      </section>
    </div>
  </main>
  <?php
  require('footer.php');
  ?>
