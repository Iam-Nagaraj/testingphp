<form id="walkthrough-video-form" method="POST" action="{{route('admin.configuration.walkthrough.video.save')}}">
	<div class="row mt-2 gy-4">
		<div class="col-md-6">
			{!! Form::label('video', 'Video', ['class' => 'form-label']) !!}&nbsp;<strong style="color: red">*</strong>

			<div class="form-group input-group-outline mt-3" id="videoInput">
				<div class="dropzone dropzone-previews" id="my-awesome-dropzone-video" type="3">
					<div class="dz-default dz-message"><span>Click here to upload</span>
					</div>
					{!! Form::file((isset($detail->url)?'':'video'), ['class' => 'form-control dropzone video-ajax-upload','accept'=>".mp4", 'style' => "top: 188px;left: 39px;width: 650px !important;"]) !!}


				</div>
				Max size 5 mb
				<span class="invalid-feedback" role="alert">
					<strong></strong>
				</span>
				@if(isset($detail->url))
				<input type="hidden" name="video" class="video-ajax-response" value=""  />
				<div class="bg-light mt-2 video-output-div" style="height: 100%;width: max-content;">
					<video width="200" height="200" controls="" class="vimg_4932">
						<source src="{{$detail->url}}">
					</video>
					<!-- <i class="fa fa-trash m-2 dropify-delete-video" style="color:red;font-size:24px;float: right;position: relative;z-index: 9;cursor: pointer;"></i> -->
				</div>
				<!-- <button class="btn btn-danger dropify-delete-video">delete</button> -->
				@endif
			</div>
		</div>
	</div>
	<div class="mt-4">
		<button type="submit" class="btn btn-primary me-2">Save changes</button>

	</div>
</form>