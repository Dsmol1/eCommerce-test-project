<?php
require_once $_SERVER['DOCUMENT_ROOT']. '/tutorial/core/init.php';
if (!is_logged_in()) {
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

// Archive products
if (isset($_GET['archive'])) {
  $id = htmlspecialchars($_GET['archive']);
  $db->query("UPDATE products SET deleted = '1' WHERE id = '$id'");
  $_SESSION['error_flash'] = 'Product has been Archived!';
  header('Location: products.php');
}

// Delete products
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $id = htmlspecialchars($_GET['delete']);
  $db->query("DELETE FROM products WHERE id = '$id'");
  $_SESSION['error_flash'] = 'Product has been permanently deleted!';
  header('Location: products.php');
}

// Add prodcuts
$dbpath = '';
if (isset($_GET['add']) || isset($_GET['edit'])) {
$brandQuery = $db->query("SELECT * FROM brand ORDER BY brand");
$parentQuery = $db->query("SELECT * FROM categories WHERE parent = 0 ORDER BY category");
$title = ((isset($_POST['title']) && $_POST['title'] != '')?htmlspecialchars($_POST['title']):'');
$brand = ((isset($_POST['brand']) && !empty($_POST['brand']))?htmlspecialchars($_POST['brand']):'');
$parent = ((isset($_POST['parent']) && !empty($_POST['parent']))?htmlspecialchars($_POST['parent']):'');
$category = ((isset($_POST['child']) && !empty($_POST['child']))?htmlspecialchars($_POST['child']):'');
$price = ((isset($_POST['price']) && $_POST['price'] != '')?htmlspecialchars($_POST['price']):'');
$list_price = ((isset($_POST['list_price']) && $_POST['list_price'] != '')?htmlspecialchars($_POST['list_price']):'');
$description = ((isset($_POST['description']) && $_POST['description'] != '')?htmlspecialchars($_POST['description']):'');
$sizes = ((isset($_POST['sizes']) && $_POST['sizes'] != '')?htmlspecialchars($_POST['sizes']):'');
$sizes = rtrim($sizes, ',');
$saved_image = '';

  if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $productResults = $db->query("SELECT * FROM products WHERE id = '$edit_id'");
    $product = mysqli_fetch_assoc($productResults);
    if (isset($_GET['delete_image'])) {
      $imgInc = (int)$_GET['imgInc'] - 1;
      $images = explode(',', $product['image']);
      $image_url = $_SERVER['DOCUMENT_ROOT'].$images[$imgInc];
      unlink($image_url);
      unset($images[$imgInc]);
      $imageString = implode(',', $images);
      $db->query("UPDATE products SET image = '{$imageString}' WHERE id = '$edit_id'");
      header('Location: products.php?edit='.$edit_id);
    }
    $category = ((isset($_POST['child']) && $_POST['child'] != '')?htmlspecialchars($_POST['child']):$product['categories']);
    $title = ((isset($_POST['title']) && $_POST['title'] != '')?htmlspecialchars($_POST['title']):$product['title']);
    $brand = ((isset($_POST['brand']) && $_POST['brand'] != '')?htmlspecialchars($_POST['brand']):$product['brand']);
    $parentQ = $db->query("SELECT * FROM categories WHERE id = '$category'");
    $parentResult = mysqli_fetch_assoc($parentQ);
    $parent = ((isset($_POST['parent']) && $_POST['parent'] != '')?htmlspecialchars($_POST['parent']):$parentResult['parent']);
    $price = ((isset($_POST['price']) && $_POST['price'] != '')?htmlspecialchars($_POST['price']):$product['price']);
    $list_price = ((isset($_POST['list_price']))?htmlspecialchars($_POST['list_price']):$product['list_price']);
    $description = ((isset($_POST['description']))?htmlspecialchars($_POST['description']):$product['description']);
    $sizes = ((isset($_POST['sizes']) && $_POST['sizes'] != '')?htmlspecialchars($_POST['sizes']):$product['sizes']);
    $sizes = rtrim($sizes, ',');
    $saved_image = (($product['image'] != '')?$product['image']:'');
    $dbpath = $saved_image;
  }

  if (!empty($sizes)) {
    $sizeString = htmlspecialchars($sizes);
    $sizeString = rtrim($sizeString, ',');
    $sizesArray = explode(',', $sizeString);
    $sArray = array();
    $qArray = array();
    foreach($sizesArray as $ss){
      $s = explode(':', $ss);
      $sArray[] = $s[0];
      $qArray[] = $s[1];
    }
  } else {
    $sizesArray = array();
  }

// Checks for POST and uses JAVASCRIPT function in footer.php to remove last comma(,).
if ($_POST) {

  $errors = array();

  $required = array('title', 'brand', 'price', 'parent', 'child', 'sizes');
  $allowed = array('png', 'jpg', 'jpeg', 'gif');
  $uploadPath = array();
  $tmpLoc = array();
  foreach ($required as $field) {
    if ($_POST[$field] == '') {
      $errors[] = 'All fields with * are required.';
      break;
    }
  }

  $photoCount = count($_FILES['photo']['name']);
   if ($photoCount > 0) {
     for($i = 0; $i < $photoCount; $i++){
       $name = $_FILES['photo']['name'][$i];
       $nameArray = explode('.', $name);
       $fileName = $nameArray[0];
       $fileExt = $nameArray[1];
       $mime = explode('/', $_FILES['photo']['type'][$i]);
       $mimeType = $mime[0];
       $mimeExt = $mime[1];
       $tmpLoc[] = $_FILES['photo']['tmp_name'][$i];
       $fileSize = $_FILES['photo']['size'][$i];
       $uploadName = md5(microtime().$i).'.'.$fileExt;
       $uploadPath[] = BASEURL.'images/'.$uploadName;
       if ($i != 0) {
         $dbpath .= ',';
       }
       $dbpath .= '/tutorial/images/'.$uploadName;
      if ($mimeType != 'image') {
        $errors[] = 'The file must be an image.';
      }
      if (!in_array($fileExt, $allowed)) {
        $errors[] = 'File extension must be a PNG, JPG, JPEG, or GIF.';
      }
      if ($fileSize > 15000000) {
        $errors [] = 'The file size must be under 15MB.';
      }
      if ($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt != 'jpg')) {
        $erriors [] = 'File extension does not match the file.';
      }
    }
  }
  if (!empty($errors)) {
    echo display_errors($errors);
  } else {
    if ($photoCount > 0) {
      // Upload file and insert into database
      for ($i=0; $i < $photoCount ; $i++) {
        move_uploaded_file($tmpLoc[$i],$uploadPath[$i]);
      }
    }

    $insertSql = "INSERT INTO products (title, price, list_price, brand, categories, image, description, featured, sizes, deleted) VALUES ('$title', '$price', '$list_price', '$brand', '$category', '$dbpath', '$description', '0', '$sizes', '0')";

    if(isset($_GET['edit'])){
      $insertSql = "UPDATE products SET title = '$title', price = '$price', list_price = '$list_price', brand = '$brand', categories = '$category', image = '$dbpath', description = '$description', featured = '1', sizes = '$sizes', deleted = '0' WHERE id = '$edit_id'";
    }
    $db->query($insertSql);

    // $_SESSION['success_flash'] = 'Product has been added!';
    header('Location: products.php');
  }
}
?>
  <!-- Add a new product -->
  <h2 class="text-center"><?=((isset($_GET['edit']))?'Edit':'Add a new');?> product</h2><hr>
  <form class="" action="products.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1');?>" method="POST" enctype ="multipart/form-data">
    <!-- Row 1 -->
    <div class="row">

    <!-- Title -->
      <div class="form-group col-md-3">
        <label for="title">Title*:</label>
        <input type="text" name="title" value="<?=$title;?>" class="form-control" id="title">
      </div>

      <!--  Brand  -->
      <div class="form-group col-md-3">
        <label for="brand">Brand*:</label>
        <select class="form-control" id="brand" name="brand">
          <option value=""<?=(($brand == '')?' selected':'');?>></option>
          <?php while($b = mysqli_fetch_assoc($brandQuery)): ?>
            <option value="<?=$b['id'];?>"<?=(($brand == $b['id'])?' selected':'');?>><?=$b['brand'];?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Parent Category -->
      <div class="form-group col-md-3">
        <label for="parent">Parent category*:</label>
        <select class="form-control" id="parent" name="parent">
          <option value=""<?=(($parent == '')?' selected':'');?>></option>
          <?php while($p = mysqli_fetch_assoc($parentQuery)): ?>
            <option value="<?=$p['id'];?>"<?=(($parent == $p['id'])?' selected':'');?>><?=$p['category'];?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Child category -->
      <div class="form-group col-md-3">
        <label for="child">Child category*:</label>
        <select id="child" class="form-control" name="child">
        </select>
      </div>
    </div>
    <!-- Row 2 -->
    <div class="row">

      <!-- Price -->
      <div class="form-group col-md-3">
        <label for="price">Price*:</label>
        <input type="text" name="price" value="<?=$price;?>" class="form-control" id="price">
      </div>


      <!-- List_Price -->
      <div class="form-group col-md-3">
        <label for="list_price">List price:</label>
        <input type="text" name="list_price" value="<?=$list_price;?>" class="form-control" id="list_price">
      </div>

      <!-- Quantity and Sizes -->
      <div class="form-group col-md-3 text-center">
        <label>Quantity & Sizes</label>
        <button class="btn btn-info form-control" type="button" name="button" onclick="$('#sizesModal').modal('toggle');return false;">Quantity & Sizes</button>
      </div>

      <!-- Sizes and Qty preview -->
      <div class="form-group col-md-3">
        <label for="sizes">Sizes & Qty preview</label>
        <input type="text" name="sizes" class="form-control" id="sizes" value="<?=$sizes;?>" readonly>
      </div>

      <!-- Upload photo -->
      <div class="form-group col-md-6 p-0">
        <?php if($saved_image != ''): ?>
          <?php
            $imgInc = 1;
            $images = explode(',', $saved_image);?>
            <?php foreach($images as $image): ?>
          <div class="saved-image col-lg-3 col-md-5 col-sm-3 p-0">
            <img src="<?=$image;?>" alt="saved image" class="img-fluid"><br>
            <a href="products.php?delete_image=1&edit=<?=$edit_id;?>&imgInc=<?=$imgInc?>" class="btn btn-danger">Delete image</a>
          </div>
        <?php
          $imgInc++;
        endforeach; ?>
        <?php else: ?>
          <label for="photo">Product photo:</label>
          <input type="file" name="photo[]" id="photo" class="form-control" multiple>
        <?php endif; ?>
      </div>

      <!-- Description -->
      <div class="form-group col-md-6">
        <label for="description">Description</label>
        <textarea id="description" class="form-control" name="description" rows="8"><?=$description;?></textarea>
      </div>
    </div>
    <div class="col-md-3 float-right text-center">
      <a href="products.php" class=" btn btn-danger">Cancel</a>
      <input class=" btn btn-success" type="submit" name="" value="<?=((isset($_GET['edit']))?'Edit':'Add');?> product">
    </div>
    <div class="clearfix"></div>
  </form>

  <!-- Modal -->
<div class="modal fade" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sizesModalLabel">Size & Quantity</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="row">
        <?php for($i=1; $i <= 12; $i++): ?>
          <div class="form-group col-md-4">
            <label for="size<?=$i;?>">Size:</label>
            <input type="text" name="size<?=$i;?>" id="size<?=$i;?>" value="<?=((!empty($sArray[$i-1]))?$sArray[$i-1]:'');?>" class="form-control">
          </div>

          <div class="form-group col-md-2">
            <label for="qty<?=$i;?>">Quantity:</label>
            <input type="number" name="qty<?=$i;?>" id="qty<?=$i;?>" value="<?=((!empty($qArray[$i-1]))?$qArray[$i-1]:'');?>" min="0" class="form-control">
          </div>
        <?php endfor; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="updateSizes();$('#sizesModal').modal('toggle');return false;">Save changes</button>
      </div>
    </div>
  </div>
</div>

<?php } else {
$sql = "SELECT * FROM products WHERE deleted = 0";
$presults = $db->query($sql);
if (isset($_GET['featured'])) {
  $id = (int)$_GET['id'];
  $featured = (int)$_GET['featured'];
  $featuredsql = "UPDATE products SET featured = '$featured' WHERE id = '$id'";
  $db->query($featuredsql);
  header('Location: products.php');
}
?>

<h2 class="text-center mt-5">Products</h2>
<div id="add-product-btn">
  <a href="products.php?add=1" class="btn btn-success float-right">Add product</a><div class="clearfix"></div>
</div>
<hr>
<table class="table table-bordered table-condensed table-striped">
  <thead>
    <th></th>
    <th>Product</th>
    <th>Price</th>
    <th>Category</th>
    <th>Featured</th>
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
        <td>
          <a href="products.php?edit=<?= $product['id'];?>" class="btn btn-sm btn-basic"><i class="fas fa-pencil-alt"></i></a>
          <a href="products.php?archive=<?= $product['id'];?>" class="btn btn-sm btn-warning">Archive</a>
          <a href="products.php?delete=<?= $product['id'];?>" class="btn btn-sm btn-danger delete">Delete permanently</a>
        </td>
        <td><?= $product['title'];?></td>
        <td><?= money($product['price']);?></td>
        <td><?= $category;?></td>
        <td>
          <a href="products.php?featured=<?=(($product['featured'] == 0)?'1':'0');?>&id=<?=$product['id'];?>" class="btn btn-sm btn-<?=(($product['featured'] == 1)?'danger':'success');?>"><i class="fas fa-<?=(($product['featured'] == 1)?'minus':'plus');?>"></i>
          </a>&nbsp <?=(($product['featured'] == 1)?'Hide from shop':'Add back to shop');?>
        </td>
        <td>0</td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php } include 'includes/footer.php'; ?>
<script type="text/javascript">
  $('document').ready(function(){
    get_child_options('<?=$category;?>');
  });
</script>
