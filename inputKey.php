<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 パスワード再発行 認証キー入力ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


if(empty($_SESSION['auth_key'])){
  header("Location:sendKey.php");
}

if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST, true));

  $auth_key = $_POST['token'];

  validRequired($auth_key, 'token');

  if(empty($err_msg)){
    debug('未入力チェックが完了しました。')

    validLength($auth_key, 'token');
    validHalf($auth_key, 'token');

    if(empty($err_msg)){
      debug('バリデーションチェックが完了しました。');

      if($auth_key !== $_SESSION['auth_key']){
        $err_msg['common'] = MSG15;
      }
      if(time() > $_SESSION['auth_key_limit']){
        $err_msg['common'] = MSG16;
      }

      if(empty($err_msg)){
        debug('認証キーの照合が完了しました。');

        $pass = makeRandKey();

        try{
          $dbh = dbConnect();
          $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
          $data = array(':email' => $_SESSION[auth_key], ':pass' => password_hash($pass, PASSWORD_DEFAULT));
          $stmt = queryPost($dbh, $sql, $data);

          if($stmt){
            debug('クエリに成功しました。');

            $from = 'info@jensensounds.com';
            $to = $_SESSION['auth_email'];
            $subject = '【パスワード再発行完了】| JENSEN SOUNDS';
            $comment = <<<EOT
パスワードの再発行が完了致しました。
下記URLにて新しいパスワードをご入力頂き、ログインください。

ログインページ：
再発行パスワード：{$pass}
※ログイン後、マイページにてパスワードの変更が可能です。

引続きのご利用よろしくお願い申し上げます。


////////////////////////////////////////
Jensen Sounds カスタマーサポート
URL
E-mail
////////////////////////////////////////
EOT;

            sendMail($from, $to, $subject, $comment);

            session_unset();
            $_SESSION['suc_msg'] = SUC03;

            debug('セッション変数内容：'.print_r($_SESSION, true));
            debug('ログインページへ遷移します。');
            header("Location:login.php");

          }else{
            debug('クエリに失敗しました。');
            $err_msg['common'] = MSG07;
          }

        }catch(Exception $e){
          error_log('エラー発生：'.$e->getMessage());
          $err_msg['common'] = MSG07;
        }
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
  <p id="js-show-msg" style="display:none;" class="slide_msg">
    <?php echo getSessionFlash('suc_msg'); ?>
  </p>
  <main id="main">
    <section class="form_container">
      <h2>パスワード再発行</h2>
      <form class="form" action="" method="post">
        <p style="font-size: 0.9rem">ご指定のメールアドレスへお送りした認証キーをご入力ください。</p>
        <div class="err_msg">
          <?php echo getErrMsg('common'); ?>
        </div>
        <label class="<?php echo addClassErr('token'); ?>">
          認証キー
          <input type="text" name="email" value="<?php echo getFormData('token'); ?>">
        </label>
        <div class="err_msg">
          <?php echo getErrMsg('token'); ?>
        </div>
        <div class="submit_button">
          <input type="submit" name="submit" value="再発行">
        </div>
      </form>
    </section>
  </main>
  <?php
  require('footer.php');
  ?>
