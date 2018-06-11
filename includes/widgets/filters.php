<?php
$cat_id = ((isset($_REQUEST['cat']))?htmlspecialchars($_REQUEST['cat']):'');
$price_sort = ((isset($_REQUEST['price_sort']))?htmlspecialchars($_REQUEST['price_sort']):'');
$min_price  = ((isset($_REQUEST['min_price']))?htmlspecialchars($_REQUEST['min_price']):'');
$max_price  = ((isset($_REQUEST['max_price']))?htmlspecialchars($_REQUEST['max_price']):'');
$b = ((isset($_REQUEST['brand']))?htmlspecialchars($_REQUEST['brand']):'');
$brandQ = $db->query("SELECT * FROM brand ORDER BY brand");

 ?>
<h3 class="text-center">Search by:</h3>
<h4 class="text-center">Price</h4>

<form class="" action="search.php" method="post">
  <input type="hidden" name="cat" value="<?=$cat_id?>">
  <input type="hidden" name="price_sort" value="0">
  <label for="low">
  <input type="radio" name="price_sort" value="low" id="low"<?=(($price_sort == 'low')?' checked':'')?>>Low to high<br>
  </label><br>
  <label for="high">
  <input type="radio" name="price_sort" value="high" id="high"<?=(($price_sort == 'high')?' checked':'')?>>High to low<br><br>
  </label><br>
  <input type="text" name="min_price" class="price-range" placeholder="Min €" value="<?=$min_price?>"> to
  <input type="text" name="max_price" class="price-range" placeholder="Max €" value="<?=$max_price?>"><Br><br>
  <h4 class="text-center">Brand</h4>
  <input type="radio" name="brand" value=""<?=(($b == '')?' checked':'')?>>All <br>
  <?php while($brand = mysqli_fetch_assoc($brandQ)): ?>
    <label for="brand-id">
      <input type="radio" name="brand" value="<?=$brand['id']?>" id="brand-id"<?=(($b == $brand['id'])?' checked':'')?>><?=$brand['brand']?>
    </label><br>
  <?php endwhile; ?>
  <input type="submit" value="Search" class="btn btn-xs btn-primary">
</form>
