<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  require_once '../core/init.php';
  if (!is_logged_in()) {
    login_error_redirect('admin');
  }
  if (!has_permission()) {
    permission_error_redirect('index.php');
  }
  include 'includes/head.php';
  include 'includes/navigation.php';

  // Delete user
  if (isset($_GET['delete'])){
    $delete_id = htmlspecialchars($_GET['delete']);
    $db->query("DELETE FROM users WHERE id = '$delete_id'");
    $_SESSION['error_flash'] = 'User has been deleted!';
    header('Location: users.php');
  }

  // Add user or edit user
  if (isset($_GET['add']) || isset($_GET['edit'])) {

      $name = ((isset($_POST['name']))?htmlspecialchars($_POST['name']):'');
      $email = ((isset($_POST['email']))?htmlspecialchars($_POST['email']):'');
      $password = ((isset($_POST['password']))?htmlspecialchars($_POST['password']):'');
      $confirm = ((isset($_POST['confirm']))?htmlspecialchars($_POST['confirm']):'');
      $permissions = ((isset($_POST['permissions']))?htmlspecialchars($_POST['permissions']):'');
      $errors = array();

      if (isset($_GET['edit'])) {
      $edit_id = (int)$_GET['edit'];
      $editResults = $db->query("SELECT * FROM users WHERE id = '$edit_id'");
      $editUser = mysqli_fetch_assoc($editResults);
      $name = ((isset($_POST['name']) && $_POST['name'] != '')?htmlspecialchars($_POST['name']):$editUser['full_name']);
      $email = ((isset($_POST['email']) && $_POST['email'] != '')?htmlspecialchars($_POST['email']):$editUser['email']);
      $password = ((isset($_POST['password']) && $_POST['password'] != '')?htmlspecialchars($_POST['password']):$editUser['password']);
      $join_date = ((isset($_POST['join_date']) && $_POST['join_date'] != '')?htmlspecialchars($_POST['join_date']):$editUser['join_date']);
      $last_login = ((isset($_POST['last_login']) && $_POST['last_login'] != '')?htmlspecialchars($_POST['last_login']):$editUser['last_login']);
      $permissions = ((isset($_POST['permissions']) && $_POST['permissions'] != '')?htmlspecialchars($_POST['permissions']):$editUser['permissions']);
      }

    if ($_POST) {
      // $edit_id = (int)$_GET['edit'];
      // Post validation
      $emailQuery = $db->query("SELECT * FROM users WHERE email = '$email'");
      $emailCount = mysqli_num_rows($emailQuery);


      if (isset($_GET['add'])) {
        if ($emailCount != 0) {
          $errors[] = 'That email already exists in our database.';
        }
      }

      if (isset($_GET['edit'])) {
        if ($editUser['email'] == $email) {
          echo "hello";
        } else {
          if ($emailCount !=0) {
            $errors[] = 'That email already exists in our database.';
          }
        }
      }

      $required = array('name', 'email', 'password', 'confirm', 'permissions');
      foreach ($required as $field) {
        if (empty($_POST[$field])) {
          $errors[] = 'You must fill out all fields.';
          break;
        }
      }
      if (strlen($password) < 6) {
        $errors[] = 'Your password must be at least 6 characters.';
      }

      if ($password != $confirm) {
        $errors[] = 'Your password do not match.';
      }

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'You must enter a valid email.';
      }

      if (!empty($errors)) {
        echo display_errors($errors);
      } else {
        if (isset($_GET['add'])) {
          $hashed = password_hash($password, PASSWORD_DEFAULT);
          $db->query("INSERT INTO users (id, full_name, email, password, join_date, last_login, permissions) VALUES (NULL, '$name', '$email', '$hashed', CURRENT_TIMESTAMP, '2018-04-17 00:00:00', '$permissions')");
          $_SESSION['success_flash'] = 'User has been added!';
          header('Location: users.php');
        } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $db->query("UPDATE users SET id = '$edit_id', full_name = '$name', email = '$email', password = '$hashed', join_date = '$join_date', last_login = '$last_login', permissions = '$permissions' WHERE id = '$edit_id'");
        $_SESSION['success_flash'] = 'User has been edited!';
        header('Location: users.php');
      }
      }
    }
    ?>
    <!-- Add new user -->
    <h2 class="text-center"><?=((isset($_GET['add']))?'Add a new':'Edit')?> user</h2><hr>
    <div class="row">
    <!-- <form class="col-md-8 mx-auto" action="users.php?add=1" method="post"> -->
      <form class="col-md-8 mx-auto" action="users.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1');?>" method="post">
      <div class="form-group col-md-8 mx-auto">
        <label for="name">Full name:</label>
        <input type="text" name="name" id="name" class="form-control" value="<?=$name?>">
      </div>

      <!-- Email -->
      <div class="form-group col-md-8 mx-auto">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" class="form-control" value="<?=$email?>">
      </div>

      <!-- Password -->
      <div class="form-group col-md-8 mx-auto">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" class="form-control" value="">
      </div>

      <!-- Confirm password -->
      <div class="form-group col-md-8 mx-auto">
        <label for="confirm">Confirm password:</label>
        <input type="password" name="confirm" id="confirm" class="form-control" value="">
      </div>

      <!-- Permissions -->
      <div class="form-group col-md-8 mx-auto">
        <label>Permissions:</label>
        <select class="form-control" name="permissions">
          <option value=""<?=(($permissions == '')?' selected':'');?>></option>
          <option value="editor"<?=(($permissions == 'editor')?' selected':'');?>>Editor</option>
          <option value="admin,editor"<?=(($permissions == 'admin,editor')?' selected':'');?>>Admin</option>
        </select>
      </div>

      <div class="form-group col-md-8 mx-auto text-right">
        <a href="users.php" class="btn btn-danger">Cancel</a>
        <input type="submit" name="" value="<?=((isset($_GET['add']))?'Add':'Edit');?> user" class="btn btn-primary">
      </div>

    </form>
  </div>
    <?php

  } else {
  $userQuery = $db->query("SELECT * FROM users ORDER BY full_name");
?>
  <h2>Users</h2><hr>
  <a href="users.php?add=1" class="btn btn-success mb-3 float-right">Add new user</a>
  <div class="clearfix"></div>

  <table class="table table-bordered table-striped table-condensed">
    <thead>
      <th></th>
      <th>Name</th>
      <th>Email</th>
      <th>Join date</th>
      <th>Last Login</th>
      <th>Permissions</th>
    </thead>
    <tbody>
      <?php while($user = mysqli_fetch_assoc($userQuery)): ?>
      <tr>
        <td>
          <a href="users.php?edit=<?=$user['id'];?>" class="btn btn-default"><i class="fas fa-pencil-alt"></i></a>
          <?php if($user['id'] != $user_data['id']): ?>
            <a href="users.php?delete=<?=$user['id'];?>" class="btn btn-default delete"><i class="fas fa-times"></i></a>
          <?php endif; ?>
        </td>
        <td><?=$user['full_name'];?></td>
        <td><?=$user['email'];?></td>
        <td><?=pretty_date($user['join_date']);?></td>
        <td><?=(($user['last_login'] == '2018-04-17 00:00:00')?'Never':pretty_date($user['last_login']));?></td>
        <td><?=$user['permissions'];?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  <?php } include 'includes/footer.php'; ?>
