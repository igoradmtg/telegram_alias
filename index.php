<?php
  require(__DIR__ . '/inc/str.php');
  $fname_log = 'log.txt';
  if (!isset($_REQUEST['uri'])) {
    exit;
  }
  //add_log('Req: '.print_r($_REQUEST,true));
  //add_log('Post: '.print_r($_POST,true));
  //add_log('Files: '.print_r($_FILES,true));
  $params = file_get_contents('php://input');
  //add_log('Params: '.$params);
  $ar_params = json_decode($params,true);
  $is_file = false;
  if (isset($_FILES['document']['name'])) {
    $is_file = true;
    $fname = basename($_FILES['document']['name']);
    $uploaddir = '/tmp/';
    $uploadfile = $uploaddir . $fname;
    if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadfile)) {
      //add_log('Move file');
    } else {
      add_log('Error move file');
    }
  }
  $uri = $_REQUEST['uri'];
  if ($is_file) {
    $pos = strpos($uri,'/chat_id');
    $chat_id = substr($uri,$pos);
    $chat_id = str_replace('/chat_id=','',$chat_id);
    $chat_id = str_replace('/chat_id','',$chat_id);
    $uri = substr($uri,0,$pos);
    $cfile = new CURLFile($uploadfile,'',$fname);
    $ar_params['document'] = $cfile;
    $ar_params['chat_id'] = $chat_id;
    // ,'chat_id'=>1280714896); 
  }
  //phpinfo();
  $website="https://api.telegram.org".$uri;
  //add_log($website);
  $ch = curl_init($website);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $ar_params);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $result = curl_exec($ch);
  curl_close($ch);  
  //add_log($result);
  echo $result;