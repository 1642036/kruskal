<!DOCTYPE html>
<html>
    <head>
        <title>WWSGO</title>
        <link href="<?=base_url('asset/map')?>/assets/css/style.css" rel="stylesheet">
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyDCWdLUnroyaD93J96bVYGc2RzpHpBkh80"></script>
        <script src="<?=base_url()?>assets/grocery_crud/js/jquery-1.11.1.min.js"></script>
        <script src="<?=base_url()?>asset/js/underscore.js"></script>
        <script src="<?=base_url('asset/map')?>/assets/includes/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?=base_url('asset/map')?>/assets/js/mobile_menu.js"></script>
        <script type="text/javascript">
function codeAddress(check) {
    geocoder = new google.maps.Geocoder();
    var address = "<?=$adress?>";
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        // neu thanh cong
        var lat = results[0].geometry.location.lat();
        var lng = results[0].geometry.location.lng();
        hienbandonhatnghe(lat,lng,check);
      }
      else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
  function showmap(obj) {
    var lat = obj.trungtam.lat;
    var lng = obj.trungtam.lng;
    hienbando(lat,lng,obj);
  }
google.maps.event.addDomListener(window, 'load');
        </script>
        <script>
        var kruskal_arr = [];
        var bd1;
        var isPaused = false;
        var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var labelIndex = 0;
        var arr_marker = [];
        var arr_poly = [];
        var arr_kruskal_mar = [];
        var arr_kruskal_poly = [];
                Object.size = function(obj) {
            var size = 0, key;
            for (key in obj) {
                if (obj.hasOwnProperty(key)) size++;
            }
            return size;
        };

        function hienbandonhatnghe(lat,lng,check) {
            var opt = {
              center: new google.maps.LatLng(lat,lng),
              zoom: 14,
              mapTypeId: google.maps.MapTypeId.ROADMAP //ROADMAP/SATELLITE/HYBRID/TERRAIN
            };
            bd1 = new google.maps.Map(document.getElementById("map_canvas"), opt);
            // tao poly
            poly = new google.maps.Polyline({
              strokeColor: '#000000',
              strokeOpacity: 1.0,
              strokeWeight: 3
            });
            poly.setMap(bd1);
            //tạo maker, infowindow
            var m1 = new google.maps.Marker({
                position: new google.maps.LatLng(lat,lng),
                map: bd1,
        	  title:'trung tâm'
            });
            UpLatLng(lat,lng,"-1");// thêm toạ độ trung tâm
            var dem = 0;
            google.maps.event.addListener(bd1, "click", function (e) {
                var lat = e.latLng.lat();
                var lng = e.latLng.lng();
                
                if(UpLatLng(lat,lng,dem)) console.log('thêm thành công');
                dem = dem + 1;
                addMarker(lat,lng,bd1);
            });
        }
        function hienbando(lat,lng,obj) {
            var opt = {
              center: new google.maps.LatLng(lat,lng),
              zoom: 14,
              mapTypeId: google.maps.MapTypeId.ROADMAP //ROADMAP/SATELLITE/HYBRID/TERRAIN
            };
            bd1 = new google.maps.Map(document.getElementById("map_canvas"), opt);
            // tao poly
            /*poly = new google.maps.Polyline({
              strokeColor: '#000000',
              strokeOpacity: 1.0,
              strokWeight: 3
            });
            poly.setMap(bd1);*/
            //tạo maker, infowindow
            var m1 = new google.maps.Marker({
                position: new google.maps.LatLng(lat,lng),
                map: bd1,
        	  title:'trung tâm'
            });
            //addMarker(lat,lng,bd1);
            var path = [];
            var tmp = []; // ds các đỉnh với key  và value = tên đỉnh
            //obj.data = removeA(obj.data , 0); // cần kiểm tra lại có nên xoá obj 0 hay ko
            //console.log(obj.data); 
            var chieudai = obj.data.length;
            for (i = 0; i < chieudai; i++) {
                var poly = obj.data[i];
                var tmp_path = [];
                for (j = 0;j < 2 ; j++){
                    var str = poly[j];
                    var res = str.split(","); // lat:0 , lng:1 , ten:2
                    if(tmp.indexOf(res[2])==-1){
                        tmp.push(res[2]);
                    }
                    path.push({ lat:parseFloat(res[0]) , lng:parseFloat(res[1]) });
                    tmp_path.push({ lat:parseFloat(res[0]) , lng:parseFloat(res[1]) });
                    
                    var objlatlng = new google.maps.LatLng(res[0],res[1]);
                        var marker1 =  new google.maps.Marker({
                            position: objlatlng,
                            //map: bd1,
                            animation: google.maps.Animation.DROP,
                            label: res[2], 
                          });
                    arr_marker.push(marker1);
                    //themmarker(res[0],res[1],res[2],bd1,i*200);
                }
                var line = new google.maps.Polyline({
                        path: tmp_path,
                        strokeColor: "#FF0000",
                        strokeOpacity: 1.0,
                        strokeWeight: 1,
                        geodesic: false,
                        animation: google.maps.Animation.DROP,
                        //map: bd1
                    });
                arr_poly.push(line);
                //thempath(tmp_path,bd1,i*200,1);// path khung
            }
            console.log('data ban đầu');
            console.log(obj.data);
            console.log('ds Đỉnh');
            console.log(tmp);
            kruskal_arr = kruskal(tmp,obj.data);
            console.log('data sau khi kruskal');
            console.log(kruskal_arr);
            $("#tutorial").html("<span>Đã tính xong kruskal</span>Mời bạn nhấp lựa chọn để hiện khung");
        }
        function kruskal(nodes, edges) {
            //node ds đỉnh , edges ds cạnh
            var mangtrave = [];
            var dsDiem = _.map(nodes, function(node) { return [node]; }); // trả về ds đỉnh nằm trong mảng nodes
            var mangCanh = _.sortBy(edges, function(edge) { return -edge[2]; }); // sắp xếp ds cạnh từ bé đến lớn
            while(dsDiem.length > 1) { // khi điểm vẫn còn 
                var edge = mangCanh.pop(); // lấy cạnh ra 
                var str1 = edge[0].split(","),
                    str2 = edge[1].split(",");
                var n1 = str1[2] , n2 = str2[2]; //n1 , n2 => tên đỉnh
                var t1 = _.filter(dsDiem, function(tree) { // lặp qua dsDiem , tra về arr thoả include (tree la value)
                    return _.include(tree, n1); // trả về true nếu n1 có trong array tree
                });
                var t2 = _.filter(dsDiem, function(tree) {
                    return _.include(tree, n2);
                });
                //console.log( "t1 = "+ t1 + " t2= " + t2);
                if (!_.isEqual(t1, t2)) {
                    dsDiem = _.without(dsDiem, t1[0], t2[0]); // trả về mảng ko có t1[0] , t2[0]
                    dsDiem.push(_.union(t1[0], t2[0])); // push vào t1[0] , t2[0] nếu trùng sẽ ghi đè lên chứ ko tạo mới
                    mangtrave.push(edge);
                }
            }
            return mangtrave;
        }
        function toObject(arr) {
              var rv = {};
              for (var i = 0; i < arr.length; ++i)
                if (arr[i] !== undefined) rv[i] = arr[i];
              return rv;
        }
        function removeA(arr,search) {
            var  ax;
            var i = 0;
            while (i<arr.length) {
                if( arr[i].indexOf(search) !== -1 ) {
                    arr.splice(i, 1);
                }
                i++;
            }
            return arr;
        }
        function initBando(map,ori,des,time) {
            window.setTimeout(function() {
                var directionsDisplay = new google.maps.DirectionsRenderer;
                var directionsService = new google.maps.DirectionsService;
                directionsDisplay.setMap(map);
                calculateAndDisplayRoute(directionsService, directionsDisplay,ori,des);
            }, time);
        }
        function calculateAndDisplayRoute(directionsService, directionsDisplay,ori,des) {
            var selectedMode = 'WALKING';
            directionsService.route({
              origin: ori,  // Haight.
              destination: des,  // Ocean Beach.
              travelMode: google.maps.TravelMode[selectedMode]
            }, function(response, status) {
              if (status == 'OK'){
                directionsDisplay.setDirections(response);
              } else {
                window.alert('Directions request failed due to ' + status);
              }
            });
       }
       function hienkruskal(){
            $("#hienkruskal").attr('onclick','xoakruskal()');
            $("#hienkruskal").html('Xoá Khung kruskal');
            var tmp = []; // ds các đỉnh với key  và value = tên đỉnh
            var chieudai = kruskal_arr.length;
            for (i = 0; i < chieudai; i++) {
                var poly = kruskal_arr[i];
                var path = [];
                for (j = 0;j < 2 ; j++){
                    var str = poly[j];
                    var res = str.split(","); // lat:0 , lng:1 , ten:2
                        tmp.push(res[2]);
                        path.push( { lat:parseFloat(res[0]) , lng:parseFloat(res[1]) } );
                        themmarker(res[0],res[1],res[2],bd1 , i*200 );
                }
                thempath(path,bd1,i*200,1,"#4248f4",1);
                //initBando(bd1,path[0],path[1],i*2000);           
            }
       }
       function hienmapkruskal(){
            $("#loadingpopup").attr('style','display:block;');
            var tmp = []; // ds các đỉnh với key  và value = tên đỉnh
            var chieudai = kruskal_arr.length;
            setTimeout(function(){ $("#loadingpopup").attr('style',''); }, chieudai*1000);
            for (i = 0; i < chieudai; i++) {
                var poly = kruskal_arr[i];
                var path = [];
                for (j = 0;j < 2 ; j++){
                    var str = poly[j];
                    var res = str.split(","); // lat:0 , lng:1 , ten:2
                        tmp.push(res[2]);
                        path.push( { lat:parseFloat(res[0]) , lng:parseFloat(res[1]) } );
                        //themmarker(res[0],res[1],res[2],bd1 , i*200 );
                }
                //thempath(path,bd1,i*200,1,"#4248f4",1);
                initBando(bd1,path[0],path[1],i*1000);           
            }
            
       }
       function xoakruskal(map){
            $("#hienkruskal").attr('onclick','hienkruskal()');
            $("#hienkruskal").html('Hiện Khung kruskal');
            for (var i = 0; i < arr_kruskal_mar.length; i++) {
                arr_kruskal_mar[i].setMap(null);
            }
            for (var i = 0; i < arr_kruskal_poly.length; i++) {
                arr_kruskal_poly[i].setMap(null);
            }
       }
        function hien(map){
            for (var i = 0; i < arr_marker.length; i++) {
                arr_marker[i].setMap(map);
            }
            for (var i = 0; i < arr_poly.length; i++) {
                arr_poly[i].setMap(map);
            }
        }
        function clearmap(){
            $("#hienmapra").attr('onclick','hienmapra()');
            $("#hienmapra").html('Hiện Khung Ban Đầu');
            hien(null);
        }
        function hienmapra(){
            $("#hienmapra").attr('onclick','clearmap()');
            $("#hienmapra").html('Xoa Khung Ban Đầu');
            hien(bd1);
        }
        function thempath(path,map,time,opacity,color,weight){
            if(!opacity) opacity = 1;
            if (!color) color = "#FF0000";
            if (!time) time = 0;
            if(!weight) weight = 1;
            window.setTimeout(function() {
                var line = new google.maps.Polyline({
                        path: path,
                        strokeColor: color,
                        strokeOpacity: opacity,
                        strokeWeight: weight,
                        geodesic: false,
                        animation: google.maps.Animation.DROP,
                        map: map
                    });
                    arr_kruskal_poly.push(line);
            }, time);
        }
        function themmarker(lat,lng,ten,map,time){
            var objlatlng = new google.maps.LatLng(lat,lng);
            window.setTimeout(function() {
                var marker1 =  new google.maps.Marker({
                    position: objlatlng,
                    map: map,
                    animation: google.maps.Animation.DROP,
                    label: ten, 
                  });
                  arr_kruskal_mar.push(marker1);
            }, time);
        }
        function addMarker(lat,lng, map) {
            // Add the marker at the clicked location, and add the next-available label
            // from the array of alphabetical characters.
            var objlatlng = new google.maps.LatLng(lat,lng);
            var marker = new google.maps.Marker({
              position: objlatlng,
              label: labels[labelIndex++ % labels.length],
              map: map
            });
        }
        function UpLatLng(lat,lng,ten){
            $.ajax({
                url:'<?=base_url()?>api/uplatlng/',
                type: "post",cache:false,data:'lat='+lat+'&lng='+lng+'&ten='+ten,
                success:function(d){
                    if(d.error===1){
                        alert(d.reason);
                        return false;
                    }
                    else return true;
                }
            });
        }
        function checktontai(){
            var kq = 2;
            $.ajax({
                url:'<?=base_url()?>api/checkkruskal/',
                type: "GET",cache:false,async: false,
                success:function(d){
                    if(d.error===1) kq = 1 ;
                    else kq = 0 ;
                }
            });
            //console.log(kq);
            return kq;
        }
        
        </script>

        <script>
            $('document').ready(function(e){
                //$("#loadingpopup").attr('style','');
                    if(!checktontai()){
                        //alert("bắt đầu thêm mới vành đai quận");
                        $("#tutorial").append("<span>bắt đầu thêm mới toạ độ</span>");
                        $(".access").fadeIn('slow');
                        codeAddress(0);// hàm này sẽ gọi hàm lưu toạ độ vào db
                        $("#loadingpopup").attr('style','');
                        $("#tutorial").fadeIn(3000);
                    }else{
                        //codeAddress(1);
                        $.ajax({
                            url:'<?=base_url()?>fire/showdata/',type:"GET",cache:false,
                            success:function(d){
                                //console.log(d);
                                var obj = JSON.parse(d);
                                if(obj.error == 0){
                                    $(".button").fadeIn('slow');
                                    $("#loadlai").fadeIn('slow');
                                    $("#buttonsubmit").fadeOut();
                                    showmap(obj);
                                    $("#loadingpopup").attr('style','');
                                    $("#tutorial").html("Mời bạn nhấp lựa chọn để hiện khung");
                                    $("#tutorial").fadeIn("300");
                                }else{
                                    $.ajax({
                                    url:'<?=base_url()?>api/xoatotaltoado/',type:"POST",cache:false,
                                    data:"key=toilalam",
                                    success:function(kq){
                                    }
                                   });
                                   location.reload();
                                }
                            }
                           });
                    }
                $("#xoatoado").click(function(e){
                    var r = confirm("Bạn có muốn xoá , lưu ý sau khi xoá bạn phải chọn lại toạ độ");
                   if(r == true){
                        $.ajax({
                        url:'<?=base_url()?>api/xoatotaltoado/',type:"POST",cache:false,
                        data:"key=toilalam",
                        success:function(xoatd){
                            if(xoatd.error == 0){
                                //alert(xoatd.reason);
                                $("#tutorial").html(xoatd.reason);
                                location.reload();
                            }else{
                                //alert(xoatd.reason);
                                $("#tutorial").html(xoatd.reason);
                                
                            }
                        }
                       });
                   }
                });
                $("#loadlai").click(function(e){
                    $(".button").fadeIn('slow');
                    $("#tutorial").html("<span>Đã tính xong kruskal</span>Mời bạn nhấp lựa chọn để hiện khung");
                   $.ajax({
                    url:'<?=base_url()?>fire/showdata/',type:"GET",cache:false,
                    success:function(d){
                        //console.log(d);
                        var obj = JSON.parse(d);
                        if(obj.error == 0){
                            showmap(obj);
                        }else{
                            alert(obj.reason);
                        }
                    }
                   });
                });
                $('#buttonsubmit').click(function(e){
                    $("#loadingpopup").attr('style','display:block;');
                            $.ajax({
                            url:'<?=base_url()?>fire/thucong_get/',type:"GET",cache:false,
                            success:function(d){
                                var obj = JSON.parse(d);
                                if(obj.error == 0){
                                    $("#loadlai").fadeIn('slow');
                                    $(".access").fadeOut('slow');
                                    $("#loadingpopup").attr('style','');
                                    $("#tutorial").fadeOut(300);
                                    $("#tutorial").html('Đã tính xong toạ độ , bấm load lại để thấy khung');
                                    $("#tutorial").fadeIn(300);
                                }else{
                                    //alert(obj.reason);
                                    $("#tutorial").html(obj.reason);
                                    location.reload();
                                }
                            }
                           });
                });
            });
        </script>
    </head>
    <body class="" >
    <div id="page" class="page">
    <div class="row">
      <div class="col-sm-9">
          <div id="map_canvas" class="field-maps"> </div>
      </div>
      <div class="col-sm-3">
        <div class="block block-block block-infos">
          <div class="block-content">
          <p class="label-name">Chào bạn <span><?=$this->session->userdata('user');?></span> </p>
            <div class="logo">
              <a href="<?=base_url()?>"><img src="<?=base_url('asset/map')?>/assets/logo/logo.svg" width="300" height="72" alt="Back to Home"></a>
              <p id="tutorial" style="display: none;"><span>Lưu ý </span>Hãy chọn 8 toạ độ bất kì trở lên</p>
              <div class="button" style="display: none;">
                  <button id="hienmapra" onclick="hienmapra()">Hiện Khung Ban Đầu</button>
                  <button id="hienkruskal" onclick="hienkruskal()">Hiện Khung Kruskal</button>
                  <button id="hienmapkruskal" onclick="hienmapkruskal()">Hiện Đường Đi thực tế</button>
                  <!--<button onclick="clearmap()">Xoá Khung Đầu</button>
                  <button onclick="xoakruskal()">Xoá Kruskal</button>-->
                  <button id="xoatoado">Xoá hết dữ liệu</button>
              </div>
              <button id="loadlai" style="display: none;">Load lại</button>
                  <input class="access" step="display:none" id="buttonsubmit" value="submit" type="button" /> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>  


<div class="loadingpopup" id="loadingpopup" style="display: block;">
 <div class="inner">
    <center><img src="<?=base_url('asset/map')?>/assets/logo/loading.svg" border="0" id="loadcomplete"></center>
 </div>
</div>


    </body>
</html>