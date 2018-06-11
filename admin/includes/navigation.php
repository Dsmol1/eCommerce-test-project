<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a href="index.php" class="navbar-brand text-success font-weight-bold">eCommerce project Admin</a>
    <ul class="navbar-nav">
        <li class="pr-3"><a href="brands.php" class="nav-link text-info">Brands</a> </li>
        <li class="pr-3"><a href="categories.php" class="nav-link text-info">Categories</a> </li>
        <li class="pr-3"><a href="products.php" class="nav-link text-info">Products</a> </li>
        <li class="pr-3"><a href="archives.php" class="nav-link text-info">Archived</a> </li>
        <?php if(has_permission('admin')): ?>
          <li class="pr-3"><a href="users.php" class="nav-link text-info">Users</a> </li>
        <?php endif; ?>
        <div class="dropdown">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Hello <?=$user_data['first'];?>!</button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="change_password.php">Change password</a>
            <a class="dropdown-item" href="logout.php">Log out</a>
          </div>
        </div>
      </ul>

  </div>
</nav>
