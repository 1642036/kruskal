<!DOCTYPE html>
<html>
    <head>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=AIzaSyDCWdLUnroyaD93J96bVYGc2RzpHpBkh80"></script>
        <script src="<?=base_url()?>assets/grocery_crud/js/jquery-1.11.1.min.js"></script>
        <script src="<?=base_url()?>asset/js/underscore.js"></script>
        <script type="text/javascript">
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
function codeAddress(check) {
    geocoder = new google.maps.Geocoder();
    var address = document.getElementById("my-address").value;
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
google.maps.event.addDomListener(window, 'load', initialize);
        </script>
        <script>
        var isPaused = false;
        var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var labelIndex = 0;
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
            var bd1 = new google.maps.Map(document.getElementById("map_canvas"), opt);
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
                if(dem == 3){
                    alert('Đã thêm thành công 4 toạ độ , tiếp tục thêm toạ độ của từng cạnh');
                }
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
            var bd1 = new google.maps.Map(document.getElementById("map_canvas"), opt);
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
            obj.data = removeA(obj.data , 0); // cần kiểm tra lại có nên xoá obj 0 hay ko
            
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
                    //themmarker(res[0],res[1],res[2],bd1,i*200);
                }
                //thempath(tmp_path,bd1,i*200,1);// path khung
            }
            console.log(obj.data);
            //console.log(dsdinh); return;
            var kruskal_arr = kruskal(tmp,obj.data);
            console.log(kruskal_arr);
            var tmp = []; // ds các đỉnh với key  và value = tên đỉnh
            var chieudai = kruskal_arr.length;
            for (i = 0; i < chieudai; i++) {
                var poly = kruskal_arr[i];
                var path = [];
                for (j = 0;j < 2 ; j++){
                    var str = poly[j];
                    var res = str.split(","); // lat:0 , lng:1 , ten:2
                        tmp.push(res[2]);
                        path.push({ lat:parseFloat(res[0]) , lng:parseFloat(res[1]) });
                        themmarker(res[0],res[1],res[2],bd1 , i*200 );
                                            
                }
                thempath(path,bd1,i*200,1,"#4248f4",1);                
                
            }
            console.log(path);
            /*var x = toObject(tmp);
            //obj
            obj.data.sort(function(a, b) {// sort mảng
              return a[2] - b[2];
            });
            var kruskal_arr=[]; var dx = 1;
            for(i= 0;i<i<obj.data.length ; i++){
                kruskal_arr[i] = [];
            }
            var size_x = Object.size(x);
            for( i = 0 ; i<obj.data.length ; i++){
                var poly = obj.data[i];
                var str1 = poly[0]; var str2 = poly[1]; 
                var str3 = poly[2];
                var res1 = str1.split(",");// lat:0 , lng:1 , ten:2
                var res2 = str2.split(",");// lat:0 , lng:1 , ten:2
                if(x[res1[2]] != x[res2[2]] ){
                    kruskal_arr[dx - 1][0] = str1; 
                    kruskal_arr[dx - 1][1] = str2;
                    kruskal_arr[dx - 1][2] = str3; // khoang cach
                    kruskal_arr[dx][0] = -1;
                    checklienthong(obj,kruskal_arr,x,size_x,i);
                    dx++;
                }
            }
            console.log(kruskal_arr);*/
            /*var line = new google.maps.Polyline({
                        path: path,
                        strokeColor: "#FF0000",
                        strokeOpacity: 1.0,
                        strokeWeight: 1,
                        geodesic: true,
                    });
                line.setMap(bd1);*/
        }
        function kruskal(nodes, edges) {
            var mst = [];
            var forest = _.map(nodes, function(node) { return [node]; });
            var sortedEdges = _.sortBy(edges, function(edge) { return -edge[2]; });
            while(forest.length > 1) {
                var edge = sortedEdges.pop();
                var str1 = edge[0].split(","),
                    str2 = edge[1].split(",");
                var n1 = str1[2] , n2 = str2[2]; 
                var t1 = _.filter(forest, function(tree) { // lặp qua forest , tra về arr thoả include (tree la value)
                    return _.include(tree, n1); // trả về true nếu n1 có trong array tree
                });
                var t2 = _.filter(forest, function(tree) {
                    return _.include(tree, n2);
                });
                //console.log( "t1 = "+ t1 + " t2= " + t2);
                if (!_.isEqual(t1, t2)) {
                    forest = _.without(forest, t1[0], t2[0]); // trả về mảng ko có t1[0] , t2[0]
                    forest.push(_.union(t1[0], t2[0])); // push vào t1[0] , t2[0] nếu trùng sẽ ghi đè lên chứ ko tạo mới
                    mst.push(edge);
                }
            }
            return mst;
        }
        function toObject(arr) {
              var rv = {};
              for (var i = 0; i < arr.length; ++i)
                if (arr[i] !== undefined) rv[i] = arr[i];
              return rv;
        }
        /*function GanNhan(kruskal_arr, tmp, TongDinh, ChiSo1, ChiSo0){
            // t = kruskal_arr , x = tmp , mang = obj , mang[chisocanh][0] = res1[2] , 
            //mang[chisocanh][1] = res2[2] var ll = kruskal_arr[i][0].split(","); t[j].v = kruskal_arr[j][0]
            var tam = 0;
        	for (j = 0; j < TongDinh; j++){
        		if (kruskal_arr[j][0] == -1) break;
                var ll_1 = kruskal_arr[j][0].split(",");
                var ll_2 = kruskal_arr[j][1].split(",");
        		if (ChiSo1 == tmp[ll_1[2]]) {
        			tmp[ll_1[2]] = tmp[ll_2[2]] = ChiSo1 = ChiSo0;
        			tam++;
        		}
        	}
        	if (tam == 0){
        		ChiSo1 = ChiSo0;
        	}
        	tam = 0;
        }
        function checklienthong(obj , kruskal_arr , tmp, tongdinh, chisocanh ){
            var tam =0; // t = kruskal_arr , x = tmp , mang = obj , mang[chisocanh][0] = res1[2] , 
            //mang[chisocanh][1] = res2[2]
            for (i = 0; i < tongdinh; i++){
                var poly = obj.data[chisocanh];
                var str1 = poly[0]; var str2 = poly[1]; var str3 = poly[2];
                var res1 = str1.split(",");// lat:0 , lng:1 , ten:2
                var res2 = str2.split(",");// lat:0 , lng:1 , ten:2
                if (kruskal_arr[i][0] == -1) return; // điều kiện dừng
                var ll = kruskal_arr[i][0].split(",");
                if (tmp[res1[2]] == tmp[ll[2]] ){// neu dinh 1 co canh lien thong
        			GanNhan(kruskal_arr, tmp, tongdinh, tmp[res2[2]], tmp[res1[2]]);
        		}//if
        		if (tmp[res2[2]] == tmp[ll[2]]){
        			GanNhan(kruskal_arr, tmp, tongdinh, tmp[res1[2]], tmp[res2[2]] );
        		}
            }
        }*/
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
            }, time);
        }
        function themmarker(lat,lng,ten,map,time){
            var objlatlng = new google.maps.LatLng(lat,lng);
            /*var marker = new google.maps.Marker({
              position: objlatlng,
              label: ten, 
              map: map
            });*/
            window.setTimeout(function() {
            var marker =  new google.maps.Marker({
                position: objlatlng,
                map: map,
                animation: google.maps.Animation.DROP,
                label: ten, 
              });
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
                $("#getCords").click(function(e){
                    if(!checktontai()){
                        alert("bắt đầu thêm mới vành đai quận");
                        $(".access").fadeIn('slow');
                        codeAddress(0);// hàm này sẽ gọi hàm lưu toạ độ vào db
                    }else{
                        //codeAddress(1);
                        $(".button").fadeIn('slow');
                    }
                });
                $("#xoatoado").click(function(e){
                    alert('minh dang test ban ko the xoa haha');
                    return;
                   $.ajax({
                    url:'<?=base_url()?>api/xoatotaltoado/',type:"POST",cache:false,
                    data:"key=toilalam"
                   });
                });
                $("#loadlai").click(function(e){
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
                $('.pause').on('click', function(e) {
                  e.preventDefault();
                  isPaused = true;
                });
                
                $('.play').on('click', function(e) {
                  e.preventDefault();
                  isPaused = false;
                });
            });
        </script>
    </head>
    <body>
        <input type="text" id="my-address">
        <button id="getCords">Tìm Toạ Độ</button>
        <div class="access" style="display:none;">
            <form method="post" action="<?=base_url('fire/kruskal')?>">
                <input type="text" id="kc-tram" name="kctram" placeholder="khoảng cách trạm" value="" />
                <input type="text" id="kc-nha" name="kcnha" placeholder="khoảng cách từ nhà đến cột" value="" />
                <input type="submit" value="Gửi dữ liệu vừa nhập" />
            </form>
        </div>
        <div class="button" style="display:none;">
            <button id="xoatoado">xoá</button>
            <button id="loadlai">load lại</button>
        </div>
        <div id="map_canvas" style="height:450px; width:850px;"> </div>
    </body>
</html>