<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
  $name = htmlspecialchars($_POST['full_name']);
  $email = htmlspecialchars($_POST['email']);
  $street = htmlspecialchars($_POST['street']);
  $street2 = htmlspecialchars($_POST['street2']);
  $city = htmlspecialchars($_POST['city']);
  $zip = htmlspecialchars($_POST['zip']);
  $country = htmlspecialchars($_POST['country']);

  $errors = array();
  $required = array(
    'full_name' => 'Full name',
    'email'     => 'Email',
    'street'    => 'Street address',
    'city'      => 'City',
    'zip'       => 'Zip',
    'country'   => 'Country',
  );

  // Check if all required fields are filled fann_descale_output
  //f = field, d = display
  foreach ($required as $f => $d) {
    if (empty($_POST[$f]) || $_POST[$f] == '') {
      $errors[] = $d.' is requierd.';
    }
  }

  // Check if valid email check_address
  if (!filter_var($email,FILTER_VALIDATE_EMAIL) && $email != '') {
    $errors[] = 'Please enter a valid email.';
  }

  if (!empty($errors)) {
    echo display_errors($errors);
  } else {
    echo 'passed';
  }
