<div class="item-contact row " style="margin-bottom: 0px">
    <div class="col-md-2">
        <div class="form-group">
            <label for="item_name">{!!trans('Item Name')!!}</label>
            <input id="item_name" name="items[{{$count}}][item_name]" type="text" class="form-control" value="" required>
        </div>
    </div>
    <div class="col-md-2">
    <div class="form-group" style="padding-left: 0">
        <label for="item_desc">{!!trans('Quantity')!!}</label>
        <input id="item_desc" name="items[{{$count}}][quantity]" type="number" class="form-control" onchange = "calculateItemTotal(this)" value="" required>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group " style="padding-right: 0">
        <label for="amount">{!!trans('Price(excl. VAT)')!!}</label>
        <input id="amount" name="items[{{$count}}][amount]" type="number" class="form-control" onchange = "calculateItemTotal(this)"  required>
    </div>
	</div>
	<div class="col-md-2">
	<div class="form-group">
                                <label for="vat">VAT Rate</label>
                                <select name="items[{{$count}}][vat]"  id="vat" class="form-control" onchange = "calculateItemTotal(this)" required>
									<option value="" disabled="disabled" selected="selected"  >Choose option</option>
									@foreach($taxes as $tax)
									<option value="{{$tax->value/100}}">{{$tax->name}}</option>
									@endforeach
                                  
                                </select>
		</div>
	</div>
<div class="col-md-2">
    <div class="form-group " style="padding-right: 0">
        <label for="total_amount">{!!trans('Total  Amount')!!}</label>
        <input id="total_amount" name="items[{{$count}}][total_amount]" type="number" class="form-control item_total" value="" readonly>
    </div>
	</div>								
<div class="col-md-1">
    <div class="form-group" style="margin-top: 20px"><button id="delete_contact_item" class="delete btn btn-danger btn-sm" type ="button"><i class="fa fa-trash-o" ></i></button></div>
</div>
   

