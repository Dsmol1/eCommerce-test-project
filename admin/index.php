<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  require_once '../core/init.php';
  if (!is_logged_in()) {
    header('Location: login.php');
  }
  include 'includes/head.php';
  include 'includes/navigation.php';
  ?>
  <!-- Orders to fill -->
  <?php
  // Transaction query
  $txnQuery = "SELECT t.id, t.cart_id, t.full_name, t.description, t.txn_date, t.grand_total, c.items, c.paid, c.shipped
  FROM transactions t
  LEFT JOIN cart c ON t.cart_id = c.id
  WHERE c.paid = 1 AND c.shipped = 0
  ORDER BY t.txn_date";
  ?>
  <div class="col-md-12">
    <h3 class="text-center">Orders to ship</h3>
    <table class="table table-condensed table-bordered table-striped">
      <thead>
        <th></th>
        <th>Name</th>
        <th>Description</th>
        <th>Total</th>
        <th>Date</th>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
    </table>
  </div>


  <?php include 'includes/footer.php'; ?>
