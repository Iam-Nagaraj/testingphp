@extends('layout/main')

@section('title', 'Walkthrough - Configuration')



@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0">Walkthrough / <span>Configuration</span></h1>
	</div>

	<!-- Content Row -->
	<div class="card video_tab shadow mb-4">
		<div class="card-header user_list py-3">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
						Video
					</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
						Screen
					</button>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
					<div class="card-body p-0">
						<form id="walkthrough-video-form" method="POST" action="{{route('admin.configuration.walkthrough.video.save')}}" enctype="multipart/form-data">
							@csrf
							<div class="row">
								
								<div class="col-md-6">
									<label>Video</label>
									<input id="file-input" class="video-ajax-upload" type="file" name="video" accept="video/*" />
									Max size 5 MB
									<span class="invalid-feedback" role="alert">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									@if(!empty($detail) && !empty($detail->config_value))
									<video width="200" height="200" controls="" class="vimg_4932">
										<source src="{{Storage::disk('s3')->url($detail->config_value)}}" />
									</video>
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<button type="submit">Save Changes</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
					<div class="card-body p-0">
						<form id="walkthrough-screen-form" method="POST" action="{{route('admin.configuration.walkthrough.screen.save')}}" enctype="multipart/form-data">
							@csrf
							<div class="row">
								<div class="col-md-6">
									{!! Form::label('image', 'Image', ['class' => 'form-label']) !!}&nbsp;<strong style="color: red">*</strong>

									{!! Form::file((isset($detail->image_url)?'':'image'), ['id' => 'file-input','class' => 'form-control image-ajax-upload','data-default-file'=>$detail->image_url??'','data-name'=>'image']) !!}
									@if(isset($detail->image_url))
									<input type="hidden" name="image" class="image-ajax-response" value="{{$detail->image}}" />
									@endif


									<span class="invalid-feedback" role="alert">
										<strong></strong>
									</span>
									<h1>image</h1>
									<img src="{{$detail->url}}" alt="">
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<label>Title *</label>
									<input type="text" name="title" value="{{$videoDetail->title}}" class="form-control" placeholder="Minimum 3% instant cash back or more!" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<label>Sub Title *</label>
									<input type="text" value="{{$videoDetail->sub_title}}" name="sub_title" class="form-control" placeholder="Every time, Everywhere" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<label>Screen</label>
									@if(!empty($videoDetail->image))
									<img src="{{$videoDetail->image}}" style="height:100px; width:100px;" data-name="image">
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col-md-6 mt-4">
									<button type="submit">Save Changes</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /.container-fluid -->
@endsection
@push('js')
<script src="{{ asset('assets') }}/js/admin/configuration.js"></script>

<script>
    var upload_image_url = "{{ route('file.image.upload') }}";
    var upload_video_url = "{{ route('file.video.upload') }}";

  </script>
@endpush