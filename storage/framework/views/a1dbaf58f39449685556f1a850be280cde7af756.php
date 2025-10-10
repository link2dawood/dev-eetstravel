<?php
use App\Tour;
$tours = Tour::all();
?>
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
						<?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<option value="<?php echo e($tour->id); ?>"><?php echo e($tour->name); ?></option>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<option v-if="folders.INBOX" v-for="(folder, index) in folders.INBOX"  :value="index">{{ index }}</option>
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

<?php /**PATH /var/www/html/resources/views/email/modals/changeFolder.blade.php ENDPATH**/ ?>