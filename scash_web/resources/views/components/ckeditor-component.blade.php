<div>
    <textarea id="ck_editor" name="{{ $name }}" class="h-auto ckeditor-container form-control">{{ $value }}</textarea>
</div>
@push('js')
<script src="{{ asset('assets') }}/js/common/ck_editor.js"></script>

@endpush
