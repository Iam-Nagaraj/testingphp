@extends('layout/app')

@section('title', 'Admin Login')

@section('content')
<div class="container">
  <h2>Terms And Condition</h2>
  <div>
    {!! $detail->cms_value !!}
  </div>
</div>
@endsection

@push('style')
<style>
  #wrapper #content-wrapper #content {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
@endpush

@push('js')
@endpush
