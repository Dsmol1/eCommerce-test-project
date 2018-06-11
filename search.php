<?php

require_once 'core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/headerfull.php';
include 'includes/leftbar.php';

$sql = "SELECT * FROM products";
$cat_id = (($_POST['cat'] != '')?htmlspecialchars($_POST['cat']):'');
if ($cat_id == '') {
  $sql .= " WHERE deleted = 0";
} else {
  $sql .= " WHERE categories = '{$cat_id}' AND deleted = 0";
}
$price_sort = (($_POST['price_sort'] != '')?htmlspecialchars($_POST['price_sort']):'');
$min_price = (($_POST['min_price'] != '')?htmlspecialchars($_POST['min_price']):'');
$max_price = (($_POST['max_price'] != '')?htmlspecialchars($_POST['max_price']):'');
$brand = (($_POST['brand'] != '')?htmlspecialchars($_POST['brand']):'');

if ($min_price != '') {
  $sql .= " AND price >= '{$min_price}'";
}
if ($max_price != '') {
  $sql .= " AND price <= '{$max_price}'";
}
if ($brand != '') {
  $sql .= " AND brand = '{$brand}'";
}
if ($price_sort == 'low') {
  $sql .= " ORDER BY price";
}
if ($price_sort == 'high') {
  $sql .= " ORDER BY price DESC";
}

$productQ = $db->query($sql);
$category = get_category($cat_id);

?>
<!-- Main content -->
<div class="col-md-8">
  <!-- <h2 class="text-center">eCommerce</h2> -->
  <h2 class="mx-auto text-center"><?=$category['parent']. ' ' .$category['child']?></h2>
  <div class="row">
    <?php if($cat_id != ''): ?>
    <?php else: ?>
    <?php endif; ?>
    <?php while($product = mysqli_fetch_assoc($productQ)) : ?>
      <div class="col-md-3 text-center">
        <h4><?= $product['title']; ?></h4>
        <?php $photos = explode(',', $product['image']); ?>
        <img src="<?= $photos[0]; ?>" alt="<?= $product['title']; ?>" class="img-thumb img-fluid">
        <p class="list-price text-danger">List Price: <s>$<?= $product['list_price']; ?>.</s></p>
        <p class="price">Our price: $<?= $product['price']; ?></p>
        <button type="button" class="btn btn-sm btn-success" onclick="detailsmodal(<?= $product['id']; ?>)">Details</button>
      </div>
    <?php endwhile; ?>
  </div>
</div>


<?php
include 'includes/rightbar.php';
include 'includes/footer.php';
?>
