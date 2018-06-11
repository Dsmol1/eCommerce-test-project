
<?php
  DEFINE('DB_USERNAME', 'root');
  DEFINE('DB_PASSWORD', 'acer1111');
  DEFINE('DB_HOST', '127.0.0.1');
  DEFINE('DB_DATABASE', 'tutorial');

  $db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
  session_start();
  if (mysqli_connect_error()) {
    die('Connect Error ('.mysqli_connect_errno().') '.mysqli_connect_error());
  }
  // echo 'Connected successfully.';

  // DEFINE('BASEURL', '/tutorial/');
  require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/config.php';
  // require_once '/var/www/html/tutorial/config.php';
  require_once BASEURL.'helpers/helpers.php';

  $cart_id = '';
  if(isset($_COOKIE[CART_COOKIE])){
    $cart_id = htmlspecialchars($_COOKIE[CART_COOKIE]);
  }

  if (isset($_SESSION['SBUser'])) {
    $user_id = $_SESSION['SBUser'];
    $query = $db->query("SELECT * FROM users WHERE id = '$user_id'");
    $user_data = mysqli_fetch_assoc($query);
    $fn = explode(' ', $user_data['full_name']);
    $user_data['first'] = $fn[0];
    $user_data['last'] = $fn[1];
  }

  // LOG IN SUCCESS
  if (isset($_SESSION['success_flash'])) {
    echo '<div class="bg-success"><p class="text-center">'.$_SESSION['success_flash'].'</p></div>';
    unset($_SESSION['success_flash']);
  }
  // LOG IN FAILURE
  if (isset($_SESSION['error_flash'])) {
    echo '<div class="bg-danger"><p class="text-center">'.$_SESSION['error_flash'].'</p></div>';
    unset($_SESSION['error_flash']);
  }
