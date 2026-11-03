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
        <ul class="nav nav-pills flex-column email-folders py-2" v-if="folders" >

            <li class="nav-item">
                <a href="#" class="nav-link" @click.prevent="changeTourFolder('active')">
                    <i class="ti ti-map-pin"></i><span>Active Tour</span>
                </a>

            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" @click.prevent="changeTourFolder('archive')">
                    <i class="ti ti-archive"></i><span>Archiver Tours</span>
                </a>
            </li>

        </ul>
    </div>
</div>
