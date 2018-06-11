<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/tutorial/core/init.php';
if (!is_logged_in()) {
  login_error_redirect();
}
include 'includes/head.php';
$hashed = $user_data['password'];

$old_password = ((isset($_POST['old_password']))?htmlspecialchars($_POST['old_password']):'');
$old_password = trim($old_password);

$password = ((isset($_POST['password']))?htmlspecialchars($_POST['password']):'');
$password = trim($password);

$confirm = ((isset($_POST['confirm']))?htmlspecialchars($_POST['confirm']):'');
$confirm = trim($confirm);

$new_hashed = password_hash($password, PASSWORD_DEFAULT);
$user_id = $user_data['id'];

$errors = array();
?>
  <div class="container-fluid" id="login-form">
    <div>
      <?php
        if($_POST){
          // Form validation
          if(empty($_POST['old_password']) || empty($_POST['password']) || empty($_POST['confirm'])){
            $errors[] = 'You must fill out all fields.';
          }

          // Password is more than 6 chars
          if (strlen($password) < 6) {
            $errors[] = 'Password must be atleast 6 characters.';
          }

          // If new password matches confirm
          if ($password != $confirm) {
            $errors[] = 'The new password and confirm password does not match.';
          }

          if (!password_verify($old_password, $hashed)) {
            $errors[] = 'Your old password does not match our records.';
          }

          // Check for $errors
          if (!empty($errors)) {
            echo display_errors($errors);
          } else {
            // Change password
            $db->query("UPDATE users SET password = '$new_hashed' WHERE ID = '$user_id'");
            $_SESSION['success_flash'] = 'Your password has been updated!';
            header('Location: index.php');
          }
        }
       ?>
    </div>
    <h2 class="text-center">Change password</h2><hr>
    <form class="" action="change_password.php" method="post">
      <div class="form-group col-lg-8 col-md-8 col-sm 12 mx-auto">
        <label for="old_password">Old password:</label>
        <input type="password" name="old_password" id="old_password" class="form-control" value="<?=$old_password;?>">
      </div>

      <div class="form-group col-lg-8 col-md-8 col-sm 12 mx-auto">
        <label for="password">New password:</label>
        <input type="password" name="password" id="password" class="form-control " value="<?=$password;?>">
      </div>

      <div class="form-group col-lg-8 col-md-8 col-sm 12 mx-auto">
        <label for="confirm">Confirm new password:</label>
        <input type="password" name="confirm" id="confirm" class="form-control " value="<?=$confirm;?>">
      </div>

      <div class="form-group text-center">
        <a href="javascript:history.back()" class="btn btn-lg btn-danger" value="Cancel">Cancel</a>

      <div class="form-group text-center">
        <a href="index.php" class="btn btn-default"></a>
        <input class="btn btn-lg btn-primary" type="submit" value="Login">
      </div>
    </form>
    <p class="text-right"> <a href="/tutorial/index.php" alt="home">Visit site</a> </p>
  </div>

<?php include 'includes/footer.php' ?>
