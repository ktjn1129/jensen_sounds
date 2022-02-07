<?php

if(!empty($_SESSION['login_date'])){
  debug('ログイン済みの管理者です。');

  if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
    debug('ログイン有効期限オーバーです。');
    session_destroy();
    debug('管理者ログインページへ遷移します。');
    header("Location:adminLogin.php");
  }else{
    debug('ログイン有効期限内です。');
    $_SESSION['login_date'] = time();
    if(basename($_SERVER['PHP_SELF']) === 'adminLogin.php'){
      debug('管理者トップページへ遷移します。');
      header("Location:adminTop.php");
    }
  }
}else{
  debug('未ログインの管理者です。');
  if(basename($_SERVER['PHP_SELF']) !== 'adminLogin.php'){
    debug('管理者ログインページへ遷移します。');
    header("Location:adminLogin.php");
  }
}

?>
