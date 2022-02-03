<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 パスワード再発行 認証キー送信ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST, true));

  $email = $_POST['email'];

  validRequired($email, 'email');
  validEmail($email, 'email');
  validMaxLen($email, 'email');

  if(!empty($err_msg)){
    debug('バリデーションチェックが完了しました。');

    try{
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      $stmt = queryPost($dbh, $sql, $data, $stmt);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if($stmt && array_shift($result)){
        debug('DBにユーザーの登録が確認できました。');
        $_SESSION['suc_msg'] = SUC03;

        $auth_key = makeRandKey();

        $from = 'info@jensensounds.com';
        $to = $email;
        $subject = '【パスワード再発行用認証キー】| JENSEN SOUNDS';
        $content = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記URLにて認証キーをご入力頂くと新しいパスワードが発行されます。

パスワード再発行用認証キー入力ページ：http://
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
http://


////////////////////////////////////////
Jensen Sounds カスタマーサポート
URL http://jensensounds.com/
E-mail info@jensensounds.com
////////////////////////////////////////
EOT;

        sendMail($from, $to, $subject, $content);

        $_SESSION['auth_key'] = $auth_key;
        $_SESSION['auth_email'] = $email;
        $_SESSION['auth_key_limit'] = time() + (60 * 30);

        debug('セッション変数内容：'.print_r($_SESSION, true));
        header("Location:inputKey.php");

      }else{
        debug('クエリに失敗したか、データベースに登録のないアドレスです。');
        $err_msg['common'] = MSG07;
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
    <section class="reissue_pass_form">
      <h2>パスワード再発行</h2>
      <form class="form" action="" method="post">
        <p style="font-size: 0.9rem">ご指定のメールアドレスへパスワード再発行用のURLと認証キーを送信します。</p>
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
        <div class="submit_button">
          <input type="submit" name="submit" value="送信">
        </div>
      </form>
    </section>
  </main>
  <?php
  require('footer.php');
  ?>
