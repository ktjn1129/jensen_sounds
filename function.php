<?php

ini_set('log_errors','on');
ini_set('error_log','php.log');


$debug_flg = true;

function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION, true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
  }
}

session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime', 60*60*24*30);
session_start();
session_regenerate_id();


function isLogin(){
  if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです。');

    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
      debug('ログイン有効期限オーバーです。');
      session_destroy();
      return false;
    }else{
      debug('ログイン有効期限内です。');
      return true;
    }
  }else{
    debug('未ログインユーザーです。');
    return false;
  }
}

function dbConnect(){
  $dsn = 'mysql:dbname=heroku_86ffb9d8d046ab8;host=us-cdbr-east-05.cleardb.net;cahrset=utf8';
  $user = 'bb22d75c9a05be';
  $password = 'e237704d';
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}

function queryPost($dbh, $sql, $data){
  $stmt = $dbh->prepare($sql);

  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    debug('失敗したSQL：'.print_r($stmt,true));
    $err_msg['common'] = MSG07;
    return 0;
  }
  debug('クエリ成功。');
  return $stmt;
}


$err_msg = array();

define('MSG01','入力必須です');
define('MSG02','Emailの形式でご入力ください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上でご入力ください');
define('MSG06','255文字以内でご入力ください');
define('MSG07','予期せぬエラーが発生しました');
define('MSG08','既に登録済みのメールアドレスです');
define('MSG09','メールアドレスまたはパスワードが違います');
define('MSG10','電話番号の形式が違います');
define('MSG11','郵便番号の形式が違います');
define('MSG12','パスワードが違います');
define('MSG13','パスワードが以前のものと同じです');
define('MSG14','文字で入力してください');
define('MSG15','半角数字でご入力ください');
define('MSG16','カテゴリーの中から選択してください');
define('SUC01','パスワードの変更が完了しました。');
define('SUC02','プロフィールの変更が完了しました。');
define('SUC03','メールを送信しました。');
define('SUC04','商品の登録が完了しました。');


function validRequired($str, $key){
  if($str == null){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}

function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}

function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}

function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}

function validMaxLen($str, $key, $max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}

function validEmailDup($email){
  global $err_msg;
  try{
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}

function validTel($str, $key){
  if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $str)){
    global $err_msg;
    $err_mag[$key] = MSG10;
  }
}

function validZip($str, $key){
  if(!preg_match("/^\d{7}$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG11;
  }
}

function validLength($str, $key, $len = 8){
  if(mb_strlen($str) !== $len){
    global $err_msg;
    $err_msg[$key] = $len.MSG14;
  }
}

function validNumber($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG15;
  }
}

function validSelect($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG16;
  }
}

function validPass($str, $key){
  validHalf($str, $key);
  validMaxLen($str, $key);
  validMinLen($str, $key);
}

function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}

function addClassErr($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return 'error';
  }
}

function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}

function sanitize($str){
  return htmlspecialchars($str, ENT_QUOTES);
}

function getFormData($str, $flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }
  global $dbFormData;
  if(!empty($dbFormData)){
    if(!empty($err_msg[$str])){
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbFormData[$str]);
      }
    }else{
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbFormData[$str]);
      }
    }
  }else{
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}

function getUser($u_id){
  debug('ユーザー情報を取得します。');

  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getCategory(){
  debug('カテゴリー情報を取得します。');
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM category';
    $data = array();
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e){
    error.log('エラー発生：'.$e->getMessage());
  }
}

function getProduct($p_id){
  debug('商品情報を取得します。');
  debug('商品ID：'.$p_id);

  try{
    $dbh = dbConnect();
    $sql = 'SELECT p.id, p.title, p.artist, p.label, p.format, p.price, p.caption, p.pic1, p.pic2, p.pic3, c.name AS category
            FROM products AS p LEFT JOIN category AS c ON p.category_id = c.id WHERE p.id = :p_id AND p.delete_flg = 0 AND c.delete_flg = 0';
    $data = array(':p_id' => $p_id);
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getProductList($category, $currentMinNum = 1, $span = 20){
  debug('商品リストを取得します。');

  try{
    $dbh = dbConnect();
    $sql = 'SELECT id FROM products';
    if(!empty($category)){
      $sql .= ' WHERE category_id = '.$category;
    }
    $data = array();
    $stmt = queryPost($dbh, $sql, $data);

    $rst['total'] = $stmt->rowCount();
    $rst['total_page'] = ceil($rst['total']/$span);

    if(!$stmt){
      return false;
    }

    $sql = 'SELECT * FROM products';
    if(!empty($category)){
      $sql .= ' WHERE category_id = '.$category;
    }
    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
    $data = array();
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getWishlist($u_id){
  debug('ウィッシュリストを取得します。');
  debug('ユーザーID：'.$u_id);

  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM wishlist AS w LEFT JOIN product AS p ON w.product_id = p.id WHERE w.user_id = :u_id';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function isLike($u_id, $p_id){
  debug('商品のウィッシュリストへの登録有無を確認します。');
  debug('ユーザーID：'.$u_id);
  debug('商品ID：'.$p_id);

  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM wishlist WHERE product_id = :p_id AND user_id = :u_id';
    $data = array(':u_id' => $u_id, ':p_id' => $p_id);
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt->rowCount()){
      debug('ウィッシュリスト登録済みです。');
      return true;
    }else{
      debug('ウィッシュリスト未登録です。');
      return false;
    }
  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function pagination($currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){

  if($currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;
  }elseif($currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;
  }elseif($currentPageNum == 2 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;
  }elseif($currentPageNum == 1 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum;
    $maxPageNum = 5;
  }elseif($totalPageNum < $pageColNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  }else{
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
      if($currentPageNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
      }
      for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i ){ echo 'active'; }
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
      }
      if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
      }
    echo '</ul>';
  echo '</div>';
}

function appendGetParam($arr_del_key = array()){
  if(!empty($_GET)){
    $str = '?';
    foreach($_GET as $key => $val){
      if(!in_array($key,$arr_del_key,true)){
        $str .= $key.'='.$val.'&';
      }
    }
    $str = mb_substr($str, 0, -1, "UTF-8");
    return $str;
  }
}

function sendMail($from, $to, $subject, $content){
  if(!empty($to) && !empty($subject) && !empty($content)){
    mb_language("Japanese");
    mb_internal_encording("UTF-8");

    $result = mb_send_mail($to, $subject, $content, "From: ".$from);
    if($result){
      debug('メールの送信に成功しました。');
    }else{
      debug('メールの送信に失敗しました。');
    }
  }
}

function makeRandKey($length = 8){
  static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
  $str = '';
  for($i = 0; $i < $length; ++$i){
    $str .= $chars[mt_rand(0,61)];
  }
  return $str;
}

function uploadImg($file, $key){
  debug('画像のアップロードを開始します。');
  debug('FILE情報：'.print_r($file, true));

  if(isset($file['error']) && is_int($file['error'])){
    try{
      switch($file['error']){
        case UPLOAD_ERR_OK:
          break;
        case UPLOAD_ERR_NO_FILE:
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
          throw new RuntimeException('ファイルサイズが大きすぎます');
        default:
          throw new RuntimeException('その他のエラーが発生しました');
      }
      $type = @exif_imagetype($file['tmp_name']);
      if(!in_array(@type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)){
        throw new RuntimeException('画像のファイル形式が未対応です');
      }
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extention($type);
      if(!move_uploaded_file($file[tmp_name], $path)){
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }
      chmod($path, 0644);
      debug('ファイルが正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;

    }catch(RuntimeException $e){
      debug('エラー発生：'.$e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}

function showImg($path){
  if(empty($path)){
    return 'img/record_sample.jpg';
  }else{
    return $path;
  }
}

?>
