<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/tutorial/core/init.php';
if (!is_logged_in()) {
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';
?>

<?php
$sql = "SELECT * FROM products WHERE deleted = 1";
$presults = $db->query($sql);

// UnArchive products
if (isset($_GET['undo'])) {
  $id = htmlspecialchars($_GET['undo']);
  $db->query("UPDATE products SET deleted = '0' WHERE id = '$id'");
  header('Location: archives.php');
}
?>

<h2 class="text-center mt-5">Archives</h2>
<table class="table table-bordered table-condensed table-striped">
  <thead>
    <th></th>
    <th>Product</th>
    <th>Price</th>
    <th>Category</th>
    <th>Sold</th>
  </thead>
  <tbody>
    <?php while($product = mysqli_fetch_assoc($presults)):
      $childID = $product['categories'];
      $catSql = "SELECT * FROM categories WHERE id = $childID";
      $result = $db->query($catSql);
      $child = mysqli_fetch_assoc($result);
      $parentID = $child['parent'];
      $psQL = "SELECT * FROM categories WHERE id = $parentID";
      $presult = $db->query($psQL);
      $parent = mysqli_fetch_assoc($presult);
      $category = $parent['category'].'~'.$child['category'];
     ?>
      <tr>
        <td> <a href="archives.php?undo=<?= $product['id'];?>" class="btn btn-success"><i class="fas fa-redo"></i> Add back to products</a> </td>
        <td><?= $product['title'];?></td>
        <td><?= money($product['price']);?></td>
        <td><?= $category;?></td>
        <td>0</td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include 'includes/footer.php'; ?>
