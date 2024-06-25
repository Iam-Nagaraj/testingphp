@extends('layout/main')

@section('title', 'Term Condition - Cms')

@section('content')

<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0 text-gray-600">Term Condition / <span class="text-dark">Cms</span></h1>
	</div>

	<!-- Content Row -->
	<div class="card video_tab shadow mb-4">
		<div class="card-header user_list py-3">
			<h3 class="mt-3 mb-3">Term Condition</h3>
			<form id="privacy-policy-form" method="POST" action="{{route('admin.cms.term-condition.save')}}">
				@csrf
				<textarea id="ckplot" name="content">{!!$detail->content!!}</textarea>
				<div class="mt-4">
					<button type="submit" class="btn btn-primary me-2">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection
@push('js')
<script src="{{ asset('assets') }}/js/admin/cms.js"></script>

<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script>
      CKEDITOR.replace("ckplot");
    </script>
@endpush