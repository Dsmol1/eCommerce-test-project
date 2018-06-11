<?php
  require_once 'core/init.php';
  include 'includes/head.php';
  include 'includes/navigation.php';
  include 'includes/headerpartial.php';

  if ($cart_id != '') {
    $cartQ = $db->query("SELECT * from cart WHERE id = '{$cart_id}'");
    $result = mysqli_fetch_assoc($cartQ);
    $items = json_decode($result['items'],true);
    $i = 1;
    $sub_total = 0;
    $item_count = 0;
  }
?>

<div class="row">
  <div class="col-md-12">
    <h2 class="text-center">My shopping cart</h2><hr>
    <?php if ($cart_id == ''): ?>
      <div class="bg-danger">
        <p class="text-center">
          Your shopping cart is empty!
        </p>
      </div>
    <?php else: ?>
      <table class="table table-bordered table-condensed table-striped">
        <thead>
          <th>#</th>
          <th>Item</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Size</th>
          <th>Sub total</th>
        </thead>
        <tbody>
          <?php
            foreach ($items as $item) {
              $product_id = $item['id'];
              $productQ = $db->query("SELECT * FROM products WHERE id = '{$product_id}'");
              $product = mysqli_fetch_assoc($productQ);
              $sArray = explode(',',$product['sizes']);
              foreach ($sArray as $sizeString) {
                $s = explode(':',$sizeString);
                if ($s[0] == $item['size']) {
                  $available = $s[1];
                }
              }
              ?>
              <tr>
                <td><?=$i?></td>
                <td><?=$product['title']?></td>
                <td><?=money($product['price'])?></td>
                <td>
                  <button class="btn btn-xs btn-default" onclick="update_cart('removeone','<?=$product['id']?>','<?=$item['size']?>');" type="button" name="button">-</button>

                  <?=$item['quantity']?>

                  <?php if($item['quantity'] < $available): ?>
                    <button class="btn btn-xs btn-default" onclick="update_cart('addone','<?=$product['id']?>','<?=$item['size']?>');" type="button" name="button">+</button>
                <?php else: ?>
                  <span class="text-danger">Max</span>
                <?php endif; ?>

                </td>
                <td><?=$item['size']?></td>
                <td><?=money($item['quantity'] * $product['price'])?>
              <?php
              $i++;
              $item_count += $item['quantity'];
              $sub_total += ($product['price'] * $item['quantity']);
            }
            // $tax = TAXRATE * $sub_total;
            // $tax = number_format($tax,2);
            // $grand_total = $tax + $sub_total;
            $grand_total = $sub_total;
             ?>
        </tbody>
      </table>

      <table class="table table-bordered table-condensed">
        <legend class="text-center">Totals</legend><hr>
        <thead class="text-center">
          <th>Total items</th>
          <th>Sub total</th>
          <!-- <th>Tax</th> -->
          <th>Grand total</th>
        </thead>
          <tr class="text-right">
            <td><?=$item_count?></td>
            <td><?=money($sub_total)?></td>
            <td class="bg-success"><?=money($grand_total)?></td>
          </tr>
        <tbody>

        </tbody>
      </table>
      <!-- Check out button -->
      <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#checkoutModal">
        <i class="fas fa-shopping-cart"></i> Check out >>
      </button>
      <div class="clearfix"></div>

      <!-- Modal -->
      <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="checkoutModalLabel">Shipping address</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

              <div class="row">
                <form class="form-control" action="thankYou.php" method="post" id="payment-form">
                  <span class="bg-danger text-white" id="payment_errors"></span>

                  <!-- Shipping details -->
                  <div id="step1" class="align-items-center">
                    <div class="form-group col-md-6">
                      <label for="full_name">Full Name:</label>
                      <input class="form-control" id="full_name" type="text" name="full_name">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="email">Email:</label>
                      <input class="form-control" id="email" type="email" name="email">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="street">Street address:</label>
                      <input class="form-control" id="street" type="text" name="street">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="street2">Street address 2:</label>
                      <input class="form-control" id="street2" type="text" name="street2">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="city">City:</label>
                      <input class="form-control" id="city" type="text" name="city">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="zip">Zip:</label>
                      <input class="form-control" id="zip" type="text" name="zip">
                    </div>

                    <div class="form-group col-md-6">
                      <label for="country">Country</label>
                      <input class="form-control" id="country" type="text" name="country">
                    </div>

                  </div>

                  <div id="step2">
                    <!-- Payment process -->
                    <div class="form-group col-md-3">
                      <label for="name">Name on card:</label>
                      <input type="text" id="name" class="form-control">
                    </div>

                    <div class="form-group col-md-3">
                      <label for="number">Card number:</label>
                      <input type="text" id="number" class="form-control">
                    </div>

                    <div class="form-group col-md-2">
                      <label for="cvc">CVC:</label>
                      <input type="text" id="cvc" class="form-control">
                    </div>

                    <div class="form-group col-md-2">
                      <label for="exp-month">Expire month:</label>
                      <select id="exp-month" class="form-control">
                        <option value=""></option>
                        <?php for($i = 1; $i < 13; $i++): ?>
                          <option value="<?=$i?>"><?=$i?></option>
                        <?php endfor; ?>
                      </select>
                    </div>

                    <div class="form-group col-md-3">
                      <label for="exp-year">Expire year:</label>
                      <select id="exp-year" class="form-control">
                        <option value=""></option>
                        <?php $yr = date("Y"); ?>
                        <?php for($i = 0; $i < 11; $i++): ?>
                          <option value="<?=$yr + $i?>"><?=$yr + $i?></option>
                        <?php endfor; ?>
                      </select>
                    </div>
                  </div>

              </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="check_address();" id="next_button">Next >></button>
              <button type="button" class="btn btn-primary" onclick="back_address();" id="back_button"><< Back</button>
              <button type="submit" class="btn btn-primary" id="checkout_button">Check out >></button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script type="text/javascript">

  function back_address(){
    $('#payment_errors').html("");
    $('#step1').css("display","block");
    $('#step2').css("display","none");
    $('#next_button').css("display","inline-block");
    $('#back_button').css("display","none");
    $('#checkout_button').css("display","none");
    $('#checkoutModalLabel').html("Shipping address");
  }

  function check_address(){
    var data = {
    'full_name' : $('#full_name').val(),
    'email'     : $('#email').val(),
    'street'    : $('#street').val(),
    'street2'   : $('#street2').val(),
    'city'      : $('#city').val(),
    'zip'       : $('#zip').val(),
    'country'   : $('#country').val(),
  };

  $.ajax({
    url : '/tutorial/admin/parsers/check_address.php',
    method : 'post',
    data : data,
    success : function(data){
      if (data != 'passed') {
        $('#payment_errors').html(data);
      }
      if (data.trim() == 'passed') {
        $('#payment_errors').html("");
        $('#step1').css("display","none");
        $('#step2').css("display","block");
        $('#next_button').css("display","none");
        $('#back_button').css("display","inline-block");
        $('#checkout_button').css("display","inline-block");
        $('#checkoutModalLabel').html("Enter your card details");
      }
    },
    error : function(){alert("Something went wrong");},
  });
  }
</script>

<?php
  include 'includes/footer.php';
?>
