@php
use App\Tour;
$tours = Tour::all();
@endphp
<div id="modalCreate" class="modal fade in" role="dialog" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Choose folder</h3>
            </div>
            <!-- /.box-header -->
            <div class="box box-body" style="border-top: none">
                <div class="form-group" v-if="folders && folders.INBOX">
                    <select  name="folder" class="form-control select2" v-model="folderSend">
						@foreach($tours as $tour)
						<option value="{{$tour->id}}">{{$tour->name}}</option>
						@endforeach
						<option v-if="folders.INBOX" v-for="(folder, index) in folders.INBOX"  :value="index">@{{ index }}</option>
                    </select>

                </div>

                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary " @click="moveToFolder"><i class="fa fa-envelope-o"></i> Send</button>
                    </div>
                    <button type="reset" class="btn btn-default" @click="CloseMoveToModal" ><i class="fa fa-times"></i> Discard</button>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>

