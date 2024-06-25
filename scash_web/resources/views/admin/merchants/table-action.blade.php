<ul>
	<li data-id="{{$id}}"><a href="{{$view_url}}"><i class="fa fa-eye" aria-hidden="true"></i></a></li>
	<li data-id="{{$id}}"><a onclick="return confirm('Are you sure you want to delete this account?');" href="{{$delete_url}}"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
	@if($status == 1)
	<li data-id="{{$id}}">	<button type="button" onClick='payUser("{{$uuid}}", "{{$userName}}")' >Pay</button></li>
	@endif
</ul>