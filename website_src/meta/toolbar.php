<nav class="container navbar navbar-expant-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">WorkAdventure Administration</a>
    <?php
        if(isLoggedIn()) {
    ?>
        <a class="navbar-brand" href="logout.php">Log out</a>
    <?php } else { ?>
        <a class="navbar-brand" href="login.php">Log in</a>
    <?php } ?>
  </div>
</nav>
