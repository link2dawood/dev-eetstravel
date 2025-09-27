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
                    <i class="fa fa-inbox" ></i> @{{index}}
                </a>

            </li>
            <li>
                <a href="#" class="" @click="openCreateFolderModal()">
                    Add Folder
                </a>
            </li>
{{--
            <table class="table table-hover table-striped finder-disable" v-if="emailsArray && !view && !loading">
                <tbody>
                <tr v-if="emailsArray" style="cursor: pointer" v-for="(email, index) in emailsArray" >
                    <td class="mailbox-star onclick_redirect">
                    </td>

                    <td class="mailbox-name" @click="infoEmail(email)">
                        <a >
                            <div v-if="currentFolder == 'INBOX.Sent'">@{{email.header.to}}</div>
                            <div v-else>@{{email.header.from}}</div>
                        </a>
                    </td>
                  

                </tr>
                </tbody>

                <ul class="email-user-list mt-4">
                                                       
                   
                    <li class="user-item active">
                        <a href="#" class="text-decoration-none">

                            <div class="card">

                                <div class="card-body">

                                    <div class="d-flex align-items-center justify-content-between">

                                       
                                        <div class="d-flex align-items-center gap-2">
                                            
                                            <div>
                                                <h3 class="card-title mb-0">
                                                    <p class="card-text mail-text">
                                                       
                                                    </p>
                                            </div>

                                        </div>

                                        <div class="d-flex flex-column align-items-end">
                                            <p class="card-text"></p>
                                            <div class="mail-status"></div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </a>
                    </li>
           

                </ul> 
            </table>--}}
        </ul>
    </div>
</div>


