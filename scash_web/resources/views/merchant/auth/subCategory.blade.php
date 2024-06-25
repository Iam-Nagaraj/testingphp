<label class="control-label my-2">Business Sub Category</label>
<select class="form-control" name="business_sub_category" id="business_sub_category_id" required="required" aria-label="Default select example">
<option value="">Select Business Sub Category</option>
@foreach($BusinessSubCategory as $singleSubCategory)
<option value="{{$singleSubCategory->id}}">{{$singleSubCategory->name}}</option>
@endforeach
</select>