<!-- Details light box -->
<?php
require_once '../core/init.php';
$id = $_POST['id'];
$id = (int)$id;
$sql = "SELECT * FROM products WHERE id = '$id'";
$result = $db->query($sql);
$products = mysqli_fetch_assoc($result);
$brand_id = $products['brand'];
$sql = "SELECT brand FROM brand WHERE id = '$brand_id'";
$brand_query = $db->query($sql);
$brand = mysqli_fetch_assoc($brand_query);
$sizestring = $products['sizes'];
// $sizeString = rtrim($sizeString)
$size_array = explode(',', $sizestring);
?>

<?php ob_start(); ?>
<div class="modal fade details-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center"><?= $products['title'];?></h4>
        <button class="close" type="button" onclick="closeModal()" aria-label="close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <span id="modal_errors"></span>
          <div class="row">
            <div class="col-sm-6 fotorama">
              <?php $photos = explode(',', $products['image']);
              foreach($photos as $photo):?>
                  <img src="<?= $photo?>" alt="<?= $products['title'];?>" class="details img-fluid">
              <?php endforeach; ?>
            </div>
            <div class="col-md-6">
              <h4>Details</h4>
              <p><?= $products['description']?></p>
              <hr>
              <p>Price: $<?= $products['price']?></p>
              <p>Brand: <?= $brand['brand']?></p>

              <form class="" action="add_cart.php" method="post" id="add_product_form">
                <input type="hidden" name="product_id" value="<?=$id?>">
                <input type="hidden" name="available" id="available" value="">
                <div class="form-group">
                  <div class="col-sm-4 p-0">
                    <label for="quantity">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="" min="0">
                  </div>
                </div>
                <div class="form-group">
                  <label for="size">Sizes: </label>
                  <select class="form-control" name="size" id="size">
                    <option value=""></option>
                    <?php foreach($size_array as $string) {
                      $string_array = explode(':', $string);
                      $size = $string_array[0];
                      $available = $string_array[1];
                      echo '<option value="'.$size.'" data-available="'.$available.'">'.$size.' ('.$available.' Available)</option>';
                    } ?>
                  </select>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" onclick="closeModal()" type="button">Close</button>
        <button class="btn btn-warning" onclick="add_to_cart();return false;"><i class="fas fa-cart-plus"></i> Add To Cart</button>
      </div>
    </div>
  </div>
</div>

<script>
$('#size').change(function(){
  var available = $('#size option:selected').data("available");
  $('#available').val(available);
});

// Slideshow for products
$(function () {
  $('.fotorama').fotorama({'loop':true,'autoplay':false});
});

  function closeModal(){
    $('#details-modal').modal('hide');
    setTimeout(function(){
      $('#details-modal').remove();
      $('.modal-backdrop').remove();
    },500);
  }
</script>

<?php echo ob_get_clean(); ?>
