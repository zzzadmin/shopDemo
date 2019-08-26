<center>
  <button id="get_location">获取地理位置信息</button>

	<table border="1">
	<h3>微信推广</h3>
       <tr>
           <td>用户uid</td>
           <td>用户专属推广码</td>
           <td>用户专属二维码</td>
           <td>操作</td>
       </tr>
       @foreach($user_info as $v)
           <tr>
               <td>{{$v->id}}</td>
               <td>{{$v->agent_code}}</td>
               <td><img src="{{$v->qrcode_url}}" alt="" width="200" height="200"></td>
               <td>
                   <a href="{{url('/agent/create_qrcode')}}?uid={{$v->id}}">生成用户专属二维码</a> |
                   <a href="{{url('/agent/agent_list')}}?uid={{$v->id}}">用户推广用户列表</a>
               </td>
           </tr>
       @endforeach
   </table>
</center>
<!-- 应用微信js和jq -->
<script src="{{asset('mstore/jquery.js')}}"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script>
  $(function(){
    // 获取用户地理位置
    wx.getLocation({
      type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
      success: function (res) {
      var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
      var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
      var speed = res.speed; // 速度，以米/每秒计
      var accuracy = res.accuracy; // 位置精度
      }
    });


  })
</script>