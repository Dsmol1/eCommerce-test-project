<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/tutorial/core/init.php';
include 'includes/head.php';

$email = ((isset($_POST['email']))?htmlspecialchars($_POST['email']):'');
$email = trim($email);
$password = ((isset($_POST['password']))?htmlspecialchars($_POST['password']):'');
$password = trim($password);
$errors = array();
?>
<style media="screen">
  body {
    background-image:url("/tutorial/images/aldiwan.jpg");
    background-repeat: no-repeat;
    background-size: 100vw 100vh;
    background-attachment: fixed;
  }
</style>
  <div class="container-fluid" id="login-form">
    <div>
      <?php
        if($_POST){
          // Form validation
          if(empty($_POST['email']) || empty($_POST['password'])){
            $errors[] = 'You must provide email and password.';
          }

          // Validate EMAIL
          if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'You must enter a valid email.';
          }

          // Password is more than 6 chars
          if (strlen($password) < 6) {
            $errors[] = 'Password must be atleast 6 characters.';
          }
          // Check if EMAIL exists in DB
          $query = $db->query("SELECT * FROM users WHERE email = '$email'");
          $user = mysqli_fetch_assoc($query);
          $userCount = mysqli_num_rows($query);
          if ($userCount < 1) {
            $errors[] = 'That email doesn\'t exist in our database.';
          }
          if (!password_verify($password, $user['password'])) {
            $errors[] = 'The password does not match our records. Please try again.';
          }

          // Check for $errors
          if (!empty($errors)) {
            echo display_errors($errors);
          } else {
            // Log user in
            $user_id = $user['id'];
            login($user_id);
          }
        }
       ?>
    </div>
    <h2 class="text-center">LOGIN</h2><hr>
    <form class="" action="login.php" method="post">
      <div class="form-group col-lg-8 col-md-8 col-sm 12 mx-auto">
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" class="form-control" value="<?=$email;?>">
      </div>
      <div class="form-group col-lg-8 col-md-8 col-sm 12 mx-auto">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" class="form-control " value="<?=$password;?>">
      </div>
      <div class="form-group text-center">
        <input class="btn btn-lg btn-primary" type="submit" value="Login">
      </div>
    </form>
    <p class="text-right"> <a href="/tutorial/index.php" alt="home">Visit site</a> </p>
  </div>

<?php include 'includes/footer.php' ?>
