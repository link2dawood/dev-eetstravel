@foreach($additional_emails as $additional_email)
<div class="item-contact row ">
                <div class="col-sm-5 row mb-3 mt-3">
					<div class="col-sm-8">
                    <input type="email" class="form-control col-sm-3" name="additionalEmail[]" placeholder="Enter additional email" required="" value = "{{$additional_email->additional_email}}">
					</div>
                    <div class="col-sm-3 mx-2">
                        <button class="btn btn-danger remove-email" id="delete_contact_item"  type="button">Remove</button>
                    </div>
                </div>
            </div>
@endforeach