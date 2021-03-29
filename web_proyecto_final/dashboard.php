<?php
session_start();
$logged = $_SESSION['logged'];

if(!$logged){
  echo "Ingreso no autorizado";
  die();
}

$devices = $_SESSION['devices'];

//momento de conectarnos a db
$conn = mysqli_connect("localhost","admin_cursoiot","121212","admin_cursoiot");



$array = array();

foreach ($devices as $device ) {
  array_push($array,$device['devices_serie']);
}



$matches = implode(',', $array);


$query = "SELECT * FROM `traffic_devices` WHERE `devices_serie` IN($matches)";
$result = $conn->query($query);
$traffics = $result->fetch_all(MYSQLI_ASSOC);

//print_r($traffics);
//die();

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
  <link rel="apple-touch-icon" href="assets/images/logo.png">
  <meta name="apple-mobile-web-app-title" content="Flatkit">
  <!-- for Chrome on Android, multi-resolution icon of 196x196 -->
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="shortcut icon" sizes="196x196" href="assets/images/logo.png">

  <!-- style -->
  <link rel="stylesheet" href="assets/animate.css/animate.min.css" type="text/css" />
  <link rel="stylesheet" href="assets/glyphicons/glyphicons.css" type="text/css" />
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css" type="text/css" />
  <link rel="stylesheet" href="assets/material-design-icons/material-design-icons.css" type="text/css" />

  <link rel="stylesheet" href="assets/bootstrap/dist/css/bootstrap.min.css" type="text/css" />
  <!-- build:css assets/styles/app.min.css -->
  <link rel="stylesheet" href="assets/styles/app.css" type="text/css" />
  <!-- endbuild -->
  <link rel="stylesheet" href="assets/styles/font.css" type="text/css" />
</head>
<body>
  <div class="app" id="app">

    <!-- ############ LAYOUT START-->

    <!-- BARRA IZQUIERDA -->
    <!-- aside -->
    <div id="aside" class="app-aside modal nav-dropdown">
      <!-- fluid app aside -->
      <div class="left navside dark dk" data-layout="column">
        <div class="navbar no-radius">
          <!-- brand -->
          <a class="navbar-brand">
            <div ui-include="'assets/images/logo.svg'"></div>
            <img src="assets/images/logo.png" alt="." class="hide">
            <span class="hidden-folded inline">IoT</span>
          </a>
          <!-- / brand -->
        </div>
        <div class="hide-scroll" data-flex>
          <nav class="scroll nav-light">

            <ul class="nav" ui-nav>
              <li class="nav-header hidden-folded">
                <small class="text-muted">Main</small>
              </li>

              <li>
                <a href="dashboard.php" >
                  <span class="nav-icon">
                    <i class="fa fa-building-o"></i>
                  </span>
                  <span class="nav-text">Principal</span>
                </a>
              </li>

              <li>
                <a href="devices.php" >
                  <span class="nav-icon">
                    <i class="fa fa-cogs"></i>
                  </span>
                  <span class="nav-text">Dispositivos</span>
                </a>
              </li>



            </ul>
          </nav>
        </div>
        <div class="b-t">
          <div class="nav-fold">
            <a href="profile.html">
              <span class="pull-left">
                <img src="assets/images/a0.jpg" alt="..." class="w-40 img-circle">
              </span>
              <span class="clear hidden-folded p-x">
                <span class="block _500">Jean Reyes</span>
                <small class="block text-muted"><i class="fa fa-circle text-success m-r-sm"></i>online</small>
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- / -->

    <!-- content -->
    <div id="content" class="app-content box-shadow-z0" role="main">
      <div class="app-header white box-shadow">
        <div class="navbar navbar-toggleable-sm flex-row align-items-center">
          <!-- Open side - Naviation on mobile -->
          <a data-toggle="modal" data-target="#aside" class="hidden-lg-up mr-3">
            <i class="material-icons">&#xe5d2;</i>
          </a>
          <!-- / -->
          <div class="">
            <b id="display_new_access">  </b>
          </div>
          <!-- Page title - Bind to $state's title -->
          <div class="mb-0 h5 no-wrap" ng-bind="$state.current.data.title" id="pageTitle"></div>

          <!-- navbar collapse -->
          <div class="collapse navbar-collapse" id="collapse">
            <!-- link and dropdown -->
            <ul class="nav navbar-nav mr-auto">
              <li class="nav-item dropdown">
                <a  class="nav-link" href data-toggle="dropdown">

                </a>
                <div ui-include="'views/blocks/dropdown.new.html'"></div>
              </li>
            </ul>

            <div ui-include="'views/blocks/navbar.form.html'"></div>
            <!-- / -->
          </div>
          <!-- / navbar collapse -->

          <!-- BARRA DE LA DERECHA -->
          <ul class="nav navbar-nav ml-auto flex-row">
            <li class="nav-item dropdown pos-stc-xs">
              <a class="nav-link mr-2" href data-toggle="dropdown">
                <i class="material-icons">&#xe7f5;</i>
                <span class="label label-sm up warn">3</span>
              </a>
              <div ui-include="'views/blocks/dropdown.notification.html'"></div>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link p-0 clear" href="#" data-toggle="dropdown">
                <span class="avatar w-32">
                  <img src="assets/images/a0.jpg" alt="...">
                  <i class="on b-white bottom"></i>
                </span>
              </a>
              <div ui-include="'views/blocks/dropdown.user.html'"></div>
            </li>
            <li class="nav-item hidden-md-up">
              <a class="nav-link pl-2" data-toggle="collapse" data-target="#collapse">
                <i class="material-icons">&#xe5d4;</i>
              </a>
            </li>
          </ul>
          <!-- / navbar right -->
        </div>
      </div>


      <!-- PIE DE PAGINA -->
      <div class="app-footer">
        <div class="p-2 text-xs">
          <div class="pull-right text-muted py-1">
            &copy; Copyright <strong>Flatkit</strong> <span class="hidden-xs-down">- Built with Love v1.1.3</span>
            <a ui-scroll-to="content"><i class="fa fa-long-arrow-up p-x-sm"></i></a>
          </div>
          <div class="nav">
            <a class="nav-link" href="">About</a>
          </div>
        </div>
      </div>

      <div ui-view class="app-body" id="view">


        <!-- SECCION CENTRAL -->
        <div class="padding">

          <!-- SWItCH1 y 2 -->
          <div class="row">
            <!-- SWItCH1 -->
            <div class="col-xs-12 col-sm-9">
              <div class="box p-a">
                <div class="form-group row">

                  <button onclick="command('open')" class="md-btn md-raised m-b-sm w-xs green" style="margin-left:25px">Abrir</button>
                  <button onclick="command('close')" class="md-btn md-raised m-b-sm w-xs red"  style="margin-left:25px" >Cerrar</button>

                  <div class="form-group" style="margin-left:25px">
                    <select  id="device_id" class="form-control select2" ui-jp="select2" ui-options="{theme: 'bootstrap'}">
                      <?php foreach ($devices as $device ) { ?>
                        <option value="<?php echo  $device['devices_serie']?>">---><?php echo $device['devices_alias'] ?><---</option>
                      <?php } ?>
                    </select>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-xs-12 col-sm-3">
              <div class="box p-a">
                <div class="pull-left m-r">
                  <span class="w-48 rounded  accent">
                    <i class="fa fa-sun-o"></i>
                  </span>
                </div>
                <div class="clear">
                  <h4 class="m-0 text-lg _300"><b id="display_temp1">-- </b><span class="text-sm"> C</span></h4>
                  <small class="text-muted">Promedio: 17 C</small>
                </div>
              </div>
            </div>


          </div>

          <!-- VALORES EN TIEMPO REAL -->
          <div class="row">
            <div class="col-sm-6">
                  <div class="box">
                    <div class="box-header">
                      <h2>Accesos</h2>
                      <small>
                        Encuentre los accessos a los portales del usuario <?php echo $_SESSION['users_email'] ?>.
                      </small>
                    </div>
                    <table class="table table-striped b-t">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Fecha</th>
                          <th>Vecino</th>
                          <th>Portal</th>
                        </tr>
                      </thead>
                      <tbody>

                        <?php foreach ($traffics as $traffic ): ?>
                          <tr>
                            <td><?php echo $traffic['traffic_id'] ?></td>
                            <td><?php echo $traffic['traffic_date'] ?></td>
                            <td><?php echo $traffic['hab_name'] ?></td>
                            <td><?php echo $traffic['devices_alias'] ?></td>
                          </tr>
                        <?php endforeach; ?>

                      </tbody>
                    </table>
                  </div>
                </div>
          </div>




        </div>

        <!-- ############ PAGE END-->

      </div>

    </div>
    <!-- / -->

    <!-- SELECTOR DE TEMAS -->
    <div id="switcher">
      <div class="switcher box-color dark-white text-color" id="sw-theme">
        <a href ui-toggle-class="active" target="#sw-theme" class="box-color dark-white text-color sw-btn">
          <i class="fa fa-gear"></i>
        </a>
        <div class="box-header">
          <a href="https://themeforest.net/item/flatkit-app-ui-kit/13231484?ref=flatfull" class="btn btn-xs rounded danger pull-right">BUY</a>
          <h2>Theme Switcher</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
          <p class="hidden-md-down">
            <label class="md-check m-y-xs"  data-target="folded">
              <input type="checkbox">
              <i class="green"></i>
              <span class="hidden-folded">Folded Aside</span>
            </label>
            <label class="md-check m-y-xs" data-target="boxed">
              <input type="checkbox">
              <i class="green"></i>
              <span class="hidden-folded">Boxed Layout</span>
            </label>
            <label class="m-y-xs pointer" ui-fullscreen>
              <span class="fa fa-expand fa-fw m-r-xs"></span>
              <span>Fullscreen Mode</span>
            </label>
          </p>
          <p>Colors:</p>
          <p data-target="themeID">
            <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'primary', accent:'accent', warn:'warn'}">
              <input type="radio" name="color" value="1">
              <i class="primary"></i>
            </label>
            <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'accent', accent:'cyan', warn:'warn'}">
              <input type="radio" name="color" value="2">
              <i class="accent"></i>
            </label>
            <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'warn', accent:'light-blue', warn:'warning'}">
              <input type="radio" name="color" value="3">
              <i class="warn"></i>
            </label>
            <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'success', accent:'teal', warn:'lime'}">
              <input type="radio" name="color" value="4">
              <i class="success"></i>
            </label>
            <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'info', accent:'light-blue', warn:'success'}">
              <input type="radio" name="color" value="5">
              <i class="info"></i>
            </label>
            <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'blue', accent:'indigo', warn:'primary'}">
              <input type="radio" name="color" value="6">
              <i class="blue"></i>
            </label>
            <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'warning', accent:'grey-100', warn:'success'}">
              <input type="radio" name="color" value="7">
              <i class="warning"></i>
            </label>
            <label class="radio radio-inline m-0 ui-check ui-check-color ui-check-md" data-value="{primary:'danger', accent:'grey-100', warn:'grey-300'}">
              <input type="radio" name="color" value="8">
              <i class="danger"></i>
            </label>
          </p>
          <p>Themes:</p>
          <div data-target="bg" class="row no-gutter text-u-c text-center _600 clearfix">
            <label class="p-a col-sm-6 light pointer m-0">
              <input type="radio" name="theme" value="" hidden>
              Light
            </label>
            <label class="p-a col-sm-6 grey pointer m-0">
              <input type="radio" name="theme" value="grey" hidden>
              Grey
            </label>
            <label class="p-a col-sm-6 dark pointer m-0">
              <input type="radio" name="theme" value="dark" hidden>
              Dark
            </label>
            <label class="p-a col-sm-6 black pointer m-0">
              <input type="radio" name="theme" value="black" hidden>
              Black
            </label>
          </div>
        </div>
      </div>

      <div class="switcher box-color black lt" id="sw-demo">
        <a href ui-toggle-class="active" target="#sw-demo" class="box-color black lt text-color sw-btn">
          <i class="fa fa-list text-white"></i>
        </a>
        <div class="box-header">
          <h2>Demos</h2>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
          <div class="row no-gutter text-u-c text-center _600 clearfix">
            <a href="dashboard.html"
            class="p-a col-sm-6 primary">
            <span class="text-white">Default</span>
          </a>
          <a href="dashboard.0.html"
          class="p-a col-sm-6 success">
          <span class="text-white">Zero</span>
        </a>
        <a href="dashboard.1.html"
        class="p-a col-sm-6 blue">
        <span class="text-white">One</span>
      </a>
      <a href="dashboard.2.html"
      class="p-a col-sm-6 warn">
      <span class="text-white">Two</span>
    </a>
    <a href="dashboard.3.html"
    class="p-a col-sm-6 danger">
    <span class="text-white">Three</span>
  </a>
  <a href="dashboard.4.html"
  class="p-a col-sm-6 green">
  <span class="text-white">Four</span>
</a>
<a href="dashboard.5.html"
class="p-a col-sm-6 info">
<span class="text-white">Five</span>
</a>
<div
class="p-a col-sm-6 lter">
<span class="text">...</span>
</div>
</div>
</div>
</div>
</div>
<!-- / -->

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

<script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
<script type="text/javascript">

/*
******************************
****** PROCESOS  *************
******************************
*/


function command(action){
  var device_serie = $( "#device_id" ).val();
  console.log(device_serie);
  if(action == "open"){
    client.publish(device_serie + "/command", 'open', (error) => {
      console.log(error || 'Abriendo!!!')
    });
  }else{
    client.publish(device_serie + "/command", 'close', (error) => {
      console.log(error || 'Cerrando!!!')
    });
  }
}

var audio = new Audio('audio.mp3');
function process_msg(topic, message){
  var msg = message.toString();
  var splitted_topic = topic.split("/");
  var serial_number = splitted_topic[0];
  var query = splitted_topic[1];

  if (query == "temp"){
    $("#display_temp1").html(msg);
  }

  if (query == "access_query"){
    $("#display_new_access").html("Nuevo acceso: " + msg);
    audio.play();

    setTimeout(function(){
      $("#display_new_access").html("");
    }, 3000);

  }





}

function process_led1(){
  if ($('#input_led1').is(":checked")){
    console.log("Encendido");

    client.publish('led1', 'on', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }else{
    console.log("Apagado");
    client.publish('led1', 'off', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }
}

function process_led2(){
  if ($('#input_led2').is(":checked")){
    console.log("Encendido");

    client.publish('led2', 'on', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }else{
    console.log("Apagado");
    client.publish('led2', 'off', (error) => {
      console.log(error || 'Mensaje enviado!!!')
    })
  }
}





/*
******************************
****** CONEXION  *************
******************************
*/

// connect options
const options = {
      connectTimeout: 4000,
      // Authentication
      clientId: 'iotmc',
      username: 'web_client',
      password: '121212',
      keepalive: 60,
      clean: true,
}

var connected = false;

// WebSocket connect url
const WebSocket_URL = 'wss://cursoiot.ga:8094/mqtt'

const client = mqtt.connect(WebSocket_URL, options)


client.on('connect', () => {
    console.log('Mqtt conectado por WS! Exito!')

    <?php foreach ($devices as $device) { ?>
      client.subscribe('<?php echo $device['devices_serie'] ?>/access_query', { qos: 0 }, (error) => {})
      client.subscribe('<?php echo $device['devices_serie'] ?>/temp', { qos: 0 }, (error) => {})
    <?php } ?>

    // publish(topic, payload, options/callback)
    client.publish('fabrica', 'esto es un verdadero éxito', (error) => {
      console.log(error || 'Mensaje enviado!!!');
    })
})

client.on('message', (topic, message) => {
  console.log('Mensaje recibido bajo tópico: ', topic, ' -> ', message.toString());
  process_msg(topic, message);
})

client.on('reconnect', (error) => {
    console.log('Error al reconectar', error)
})

client.on('error', (error) => {
    console.log('Error de conexión:', error)
})







</script>

<!-- endbuild -->
</body>
</html>
