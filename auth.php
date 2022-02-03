<?php

if(!empty($_SESSION['login_date'])){
  debug('ログイン済みユーザーです。');

  if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
    debug('ログイン有効期限オーバーです。');
    session_destroy();
    debug('ログインページへ遷移します。');
    header("Location:login.php");
  }else{
    debug('ログイン有効期限内です。');
    $_SESSION['login_date'] = time();
    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      debug('マイページへ遷移します。');
      header("Location:mypage.php");
    }
  }
}else{
  debug('未ログインユーザーです。');
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    debug('ログインページへ遷移します。');
    header("Location:login.php");
  }
}

?>
