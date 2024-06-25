<form id="walkthrough-screen-form" method="POST" action="{{route('admin.configuration.walkthrough.screen.save')}}" enctype="multipart/form-data">

	<div class="row mt-2 gy-4">
		<div class="col-md-6">
			<div class="form-group input-group-outline mt-3" id="imageInput">

				{!! Form::label('image', 'Image', ['class' => 'form-label']) !!}&nbsp;<strong style="color: red">*</strong>

				{!! Form::file((isset($detail->image_url)?'':'image'), ['class' => 'form-control dropzone-image image-ajax-upload dropify','data-default-file'=>$detail->image_url??'','data-name'=>'image']) !!}
				@if(isset($detail->image_url))
				<input type="hidden" name="image" class="image-ajax-response" value="{{$detail->image}}" />
				@endif


				<span class="invalid-feedback" role="alert">
					<strong></strong>
				</span>


			</div>

			<div class="form-group input-group-outline mt-3" id="titleInput">
				{!! Form::label('title','Title', ['class' => 'control-label']) !!}&nbsp;<strong style="color: red">*</strong>
				{!! Form::text('title',$detail->title??"", ['class' => 'form-control']) !!}
				<span class="invalid-feedback" role="alert">
					<strong></strong>
				</span>


			</div>


			<div class="form-group input-group-outline mt-3" id="sub_titleInput">
				{!! Form::label('sub_title','Sub Title', ['class' => 'control-label']) !!}&nbsp;<strong style="color: red">*</strong>
				{!! Form::text('sub_title',$detail->sub_title??"", ['class' => 'form-control']) !!}
				<span class="invalid-feedback" role="alert">
					<strong></strong>
				</span>


			</div>

		</div>
	</div>


	<div class="mt-4">
		<button type="submit" class="btn btn-primary me-2">Save changes</button>

	</div>
</form>