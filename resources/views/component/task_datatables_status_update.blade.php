<select name="status" class="task-status form-control" data-update-link='{{route('task.update', ['task' => $task->id])}}'>
	@foreach($taskStatuses as $status)
	<option value="{{$status->id}}" {{$status->id == $task->status ? 'selected="selected"' : ''}}>{{$status->name}}</option>
    @endforeach
</select>
<script type="text/javascript">
	$('select.task-status').css({	
		'border':'none', 
		'outline':'0px', 
		'background-color':'inherit',
		'-moz-appearance': 'none',
		'-webkit-appearance': 'none',
		'width': '100%'
	});
</script>