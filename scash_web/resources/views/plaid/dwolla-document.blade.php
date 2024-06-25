@extends('layout/blank')

@section('title', 'Merchant Login')

@section('content')
<br>
<form action="{{route('auth.upload-document')}}" method="post" enctype="multipart/form-data">
  @csrf
  <input type="file" name="document" id=""><br><br><br>
  <input type="submit">
</form>

@endsection

@push('style')
<style>

</style>
@endpush

@push('js')
@endpush
