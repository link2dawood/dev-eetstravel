<select name="package_status" class="package-status form-control">
    @foreach($statuses as $status)
        <option value="{{$status->id}}" {{$status->id == $selected_status ? 'selected="selected"' : ''}}>{{$status->name}}</option>
    @endforeach
</select>

<script type="text/javascript">
    $('select.package-status').css({
        'border':'none',
        'outline':'0px',
        'background-color':'inherit',
        '-moz-appearance': 'none',
        '-webkit-appearance': 'none',
        'width': '100%'
    });
</script>