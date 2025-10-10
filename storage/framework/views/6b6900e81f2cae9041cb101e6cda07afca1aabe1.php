<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Tour Folders</h3>

        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body no-padding">
        <div v-if="loadingFolders">
            <div style="min-height: 300px;">
                <div class="loader"></div>

            </div>
        </div>
        <ul class="nav nav-pills nav-stacked" v-if="folders" >

            <li >
                <a href="#" class="" @click="changeTourFolder('active')">
                    Active Tour
                </a>

            </li>
            <li>
                <a href="#" class="" @click="changeTourFolder('archive')">
                    Archiver Tours
                </a>
            </li>

        </ul>
    </div>
</div><?php /**PATH /var/www/html/resources/views/email/parts/tourfolders.blade.php ENDPATH**/ ?>