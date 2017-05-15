<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="<?=base_url('asset/map')?>/assets/logo/logo.svg" sizes="16x16 32x32" type="image/png">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>WWSGO</title>


  <!-- Style -->
  <link href="<?=base_url('asset/map')?>/assets/css/style.css" rel="stylesheet">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body class="front login-form">
  <div id="page" class="page">
    <div class="logo">
      <a href="#"><img src="<?=base_url('asset/map')?>/assets/logo/logo.svg" width="300" height="72" alt="Back to Home"></a>
    </div>
    <div class="block-form">
      <form class="form-login" method="post" action="<?=base_url('fire/googlemap')?>">
        <div class="form-item">
          <input type="text" class="form-text" name="user" value="<?=$this->session->userdata('user');?>" placeholder="Tên Người Dùng" required>
        </div>
        <div class="form-item">
          <input type="text" class="form-text" name="adress" id="my-address" value="" placeholder="Nhập khu vực cần thiết lập..." required>
        </div>
        <div class="form-item form-type-checkbox">
          <!--<input type="checkbox" id="edit-persistent-login" name="check" value="1" class="form-checkbox" checked>
          <label class="option" for="edit-persistent-login" >Ghi nhớ đăng nhập </label>-->
        </div>
        <div class="group-action">
          <input class="form-submit" type="submit" value="Tìm Tọa độ">
        </div>
      </form>
    </div>
    <!-- END FOOTER -->
  </div>

  <!-- Javascript -->
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyDCWdLUnroyaD93J96bVYGc2RzpHpBkh80"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="<?=base_url('asset/map')?>/assets/includes/bootstrap/js/bootstrap.min.js"></script>
  <script src="<?=base_url('asset/map')?>/assets/js/mobile_menu.js"></script>
  <script src="<?=base_url('asset/map')?>/assets/js/script.js"></script>
  <script>
  function initialize() {
        var address = (document.getElementById('my-address'));
        var autocomplete = new google.maps.places.Autocomplete(address);
        autocomplete.setTypes(['geocode']);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }
        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
        }
      });
  }
google.maps.event.addDomListener(window, 'load', initialize);
  </script>
</body>
</html>