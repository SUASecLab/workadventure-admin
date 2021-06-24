<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <script src="js/bootstrap.min.js"></script>
  <title>Workadventure Administration</title>
</head>
<body>
  <? include 'meta/toolbar.php'; ?>
  
  <div class="container">
    <div class="row">
      <div class="col-sm-6">
        <div class="card" style="width: 18 rem;">
          <img src="..." class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">Room Management</h5>
            <p class="card-text">Here, you can adjust the room settings. Furthermore, you can restrict the access to the room</p>
            <a href="#" class="btn btn-primary">Go to Room Management</a>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="card" style="width: 18 rem;">
          <img src="..." class="card-img-top" alt="...">
          <div class="card-body">
            <h5 class="card-title">User Management</h5>
            <p class="card-text">Here, you can manage the user accounts. You can also set user tags here</p>
            <a href="user.php" class="btn btn-primary">User Management</a>
          </div>
        </div>
      </div>
        <div class="col-sm-6">
          <div class="card" style="width: 18 rem;">
            <img src="..." class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title">Manage Reports</h5>
              <p class="card-text">Here, you can see, which users have been reported. Furthermore, you can view the reports details and impose sanctions on non-behaving users</p>
              <a href="#" class="btn btn-primary">Reports Management</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
