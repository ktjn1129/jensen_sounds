<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 アカウント削除ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


if(!empty($_POST)){
  debug('POST送信があります。');

  try{
    $dbh = dbConnect();
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :id';
    $sql2 = 'UPDATE wishlist SET delete_flg = 1 WHERE id = :id';
    $data = array(':id' => $_SESSION['user_id']);

    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);

    if($stmt1){
      session_destroy();
      debug('セッション変数内容：'.print_r($_SESSION, true));
      debug('トップページへ遷移します。');
      debug("Location:index.php");
    }else{
      debug('クエリに失敗しました。');
      $err_msg['common'] = MSG07;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
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
        <h2>アカウント削除</h2>
        <form class="form" action="" method="post">
          <p style="font-size: 0.9rem">アカウント情報およびウィッシュリストを削除します。</p>
          <div class="err_msg">
            <?php echo getErrMsg('common'); ?>
          </div>
          <div class="submit_button">
            <input type="submit" name="submit" value="削除">
          </div>
        </form>
      </section>
    </div>
  </main>
  <?php
  require('footer.php');
  ?>
