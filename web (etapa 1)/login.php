<?php
session_start();
$_SESSION['logged'] = false;

$msg="";
$email="";

if(isset($_POST['email']) && isset($_POST['password'])) {

  if ($_POST['email']==""){
    $msg.="Debe ingresar un email <br>";
  }else if ($_POST['password']=="") {
    $msg.="Debe ingresar la clave <br>";
  }else {
    $email = strip_tags($_POST['email']);
    $password= sha1(strip_tags($_POST['password']));

    //momento de conectarnos a db
    $conn = mysqli_connect("localhost","admin_cursoiot","121212","admin_cursoiot");


    if ($conn==false){
      echo "Hubo un problema al conectarse a María DB";
      die();
    }

    $result = $conn->query("SELECT * FROM `users` WHERE `users_email` = '".$email."' AND  `users_password` = '".$password."' ");
    $users = $result->fetch_all(MYSQLI_ASSOC);


    //cuento cuantos elementos tiene $tabla,
    $count = count($users);

    if ($count == 1){

      //cargo datos del usuario en variables de sesión
      $_SESSION['user_id'] = $users[0]['users_id'];
      $_SESSION['users_email'] = $users[0]['users_email'];

      $msg .= "Exito!!!";
      $_SESSION['logged'] = true;



      echo '<meta http-equiv="refresh" content="2; url=dashboard.php">';
    }else{
      $msg .= "Acceso denegado!!!";
      $_SESSION['logged'] = false;
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>IoT Masterclass</title>
  <meta name="description" content="Admin, Dashboard, Bootstrap, Bootstrap 4, Angular, AngularJS" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- for ios 7 style, multi-resolution icon of 152x152 -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-barstyle" content="black-translucent">
  <link rel="apple-touch-icon" href="../assets/images/logo.png">
  <meta name="apple-mobile-web-app-title" content="Flatkit">
  <!-- for Chrome on Android, multi-resolution icon of 196x196 -->
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="shortcut icon" sizes="196x196" href="../assets/images/logo.png">

  <!-- style -->
  <link rel="stylesheet" href="assets/animate.css/animate.min.css" type="text/css" />
  <link rel="stylesheet" href="assets/glyphicons/glyphicons.css" type="text/css" />
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="assets/material-design-icons/material-design-icons.css" type="text/css" />

  <link rel="stylesheet" href="assets/bootstrap/dist/css/bootstrap.min.css" type="text/css" />
  <!-- build:css ../assets/styles/app.min.css -->
  <link rel="stylesheet" href="assets/styles/app.css" type="text/css" />
  <!-- endbuild -->
  <link rel="stylesheet" href="assets/styles/font.css" type="text/css" />
</head>
<body>
  <div class="app" id="app">

<!-- ############ LAYOUT START-->
  <div class="center-block w-xxl w-auto-xs p-y-md">
    <div class="navbar">
      <div class="pull-center">
        <div ui-include="'../views/blocks/navbar.brand.html'"></div>
      </div>
    </div>
    <div class="p-a-md box-color r box-shadow-z1 text-color m-a">
      <div class="m-b text-sm">
        Sign in with your IoT Account
      </div>



      <form target="" method="post" name="form">
        <div class="md-form-group float-label">
          <input name="email" type="email" class="md-input" value="<?php echo $email ?>" ng-model="user.email" required >
          <label>Email</label>
        </div>
        <div  class="md-form-group float-label">
          <input name="password" type="password" class="md-input" ng-model="user.password" required >
          <label>Password</label>
        </div>
        <button type="submit" class="btn primary btn-block p-x-md">Sign in</button>
      </form>

      <div style="color:red" class="">
        <?php echo $msg ?>
      </div>





    </div>

    <div class="p-v-lg text-center">
      <div class="m-b"><a ui-sref="access.forgot-password" href="forgot-password.html" class="text-primary _600">Forgot password?</a></div>
      <div>Do not have an account? <a ui-sref="access.signup" href="register.php" class="text-primary _600">Sign up</a></div>
    </div>
  </div>

<!-- ############ LAYOUT END-->

  </div>
<!-- build:js scripts/app.html.js -->
<!-- jQuery -->
  <script src="libs/jquery/jquery/dist/jquery.js"></script>
<!-- Bootstrap -->
  <script src="libs/jquery/tether/dist/js/tether.min.js"></script>
  <script src="libs/jquery/bootstrap/dist/js/bootstrap.js"></script>
<!-- core -->
  <script src="libs/jquery/underscore/underscore-min.js"></script>
  <script src="libs/jquery/jQuery-Storage-API/jquery.storageapi.min.js"></script>
  <script src="libs/jquery/PACE/pace.min.js"></script>

  <script src="html/scripts/config.lazyload.js"></script>

  <script src="html/scripts/palette.js"></script>
  <script src="html/scripts/ui-load.js"></script>
  <script src="html/scripts/ui-jp.js"></script>
  <script src="html/scripts/ui-include.js"></script>
  <script src="html/scripts/ui-device.js"></script>
  <script src="html/scripts/ui-form.js"></script>
  <script src="html/scripts/ui-nav.js"></script>
  <script src="html/scripts/ui-screenfull.js"></script>
  <script src="html/scripts/ui-scroll-to.js"></script>
  <script src="html/scripts/ui-toggle-class.js"></script>

  <script src="html/scripts/app.js"></script>

  <!-- ajax -->
  <script src="libs/jquery/jquery-pjax/jquery.pjax.js"></script>
  <script src="html/scripts/ajax.js"></script>




<!-- endbuild -->
</body>
</html>
