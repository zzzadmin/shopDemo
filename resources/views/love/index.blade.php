<title>粉丝列表</title>
<center>
	<table border="1">
		<tr>
			<td>编号</td>
			<td>openid</td>
			<td>操作</td>
		</tr>
		@foreach($info as $k=>$v)
		<tr>
			<td>{{$k}}</td>
			<td>{{$v}}</td>
			<td>
				<a href="{{'/love/send'}}?openid={{$v}}">表白</a>
			</td>
		</tr>
		@endforeach
	</table>
</center>
	