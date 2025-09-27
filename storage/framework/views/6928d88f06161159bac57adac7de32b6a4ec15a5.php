<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Folders</h3>

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
			<li  class="{active:(index==currentFolder)}">
                <a href="#" @click="changeFolder('INBOX')">
                    <i class="fa fa-inbox" ></i> Inbox
                </a>

            </li>
            <li  v-for="(folder, index) in folders.INBOX" :class="{active:(index==currentFolder)}"  v-if="index !== 'Drafts'">
                <a href="#" @click="changeFolder(index)">
                    <i class="fa fa-inbox" ></i> {{index}}
                </a>

            </li>
            <li>
                <a href="#" class="" @click="openCreateFolderModal()">
                    Add Folder
                </a>
            </li>

        </ul>
    </div>
</div>


<?php /**PATH /var/www/html/resources/views/email/parts/foldersList.blade.php ENDPATH**/ ?>