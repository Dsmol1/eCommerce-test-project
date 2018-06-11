<?php
$sql = "SELECT * FROM categories WHERE parent = 0";
$pquery = $db->query($sql);
?>

<nav class="navigation navbar navbar-expand-lg navbar">
  <div class="container">
    <a href="index.php" class="navbar-brand">Shauntas boutique</a>
    <ul class="nav navbar-nav">
      <?php while($parent = mysqli_fetch_assoc($pquery)) : ?>
        <?php
          $parent_id = $parent['id'];
          $sql2 = "SELECT * FROM categories WHERE parent = '$parent_id'";
          $cquery = $db->query($sql2);
         ?>
        <!-- Menu items -->
        <li class="dropdown mr-3">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= $parent['category']; ?><span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <?php while($child = mysqli_fetch_assoc($cquery)) : ?>
            <li><a href="category.php?cat=<?=$child['id'];?>"><?= $child['category'] ?></a></li>
            <?php endwhile; ?>
          </ul>
        </li>
    <?php endwhile; ?>
    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> My cart</a></li>
  </ul>
</div>
</nav>
