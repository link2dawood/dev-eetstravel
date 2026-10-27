<div class="box box-primary dashboard-inbox">
    @if(Auth::user()->can('dashboard.inbox'))
        <div id="inbox">
            <div class="box-header">
                <h4 class="mb-0">Inbox</h4>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{route('email.index')}}" class="btn btn-default btn-sm inbox-view-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                            <polyline points="3 7 12 13 21 7" />
                        </svg>
                        View All Emails
                    </a>
                    <div class="box-tools">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="search_emails mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="10" cy="10" r="7" />
                                <line x1="21" y1="21" x2="15" y2="15" />
                            </svg>
                        </span>
                        <input aria-label="Search emails..." type="text" id="search" name="search" class="form-control" v-model="search" @keyup.enter="fetchData(true)" placeholder="Search emails...">
                        <button type="button" class="btn btn-primary" @click="fetchData(true)">
                            {{ trans('main.Search') }}
                        </button>
                        <button type="button" class="btn btn-danger" @click="ClearSearchForm()" v-if="search!=null">
                            Clear
                        </button>
                    </div>
                </div>

                <div>
                    <div v-if="loading">
                        <div style="height: 300px;">
                            <div class="loader"></div>
                        </div>
                    </div>

                    <div v-else>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover finder-disable emails-inbox mb-0">
                                <tbody>
                                    <tr v-if="!mails || !mails.length">
                                        <td colspan="6">
                                            <div class="empty-state text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                                    <polyline points="3 7 12 13 21 7" />
                                                </svg>
                                                <h2 class="text-muted">{{ trans('main.Thefolderisempty') }}</h2>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr v-for="mail in mails" v-cloak class="email-row" :class="{ 'email-unread': mail.header.seen == 0 }">
                                        <td class="mailbox-star">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                            </svg>
                                        </td>
                                        <td class="mailbox-name" @click="OpenEmail(mail)">
                                            <a :href="mail.click_redirect" class="mailbox-sender text-decoration-none">
                                                <span class="mailbox-avatar" :style="'background-color: ' + getColorFromEmail(mail.header.from)">@{{getInitials(mail.header.from)}}</span>
                                                <span class="mailbox-sender-text">
                                                    <strong class="mailbox-sender-name" :title="extractName(mail.header.from)">@{{extractName(mail.header.from)}}</strong>
                                                    <small class="mailbox-sender-email" :title="extractEmail(mail.header.from)">@{{extractEmail(mail.header.from)}}</small>
                                                </span>
                                            </a>
                                        </td>
                                        <td class="mailbox-subject" @click="OpenEmail(mail)">
                                            <span class="mailbox-subject-text" :title="mail.header.subject">
                                                <svg v-if="mail.header.seen == 0" xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-blue" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                                    <polyline points="3 7 12 13 21 7" />
                                                </svg>
                                                <span>@{{mail.header.subject}}</span>
                                            </span>
                                        </td>
                                        <td class="mailbox-attachment" @click="OpenEmail(mail)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                            </svg>
                                        </td>
                                        <td class="mailbox-date" @click="OpenEmail(mail)">
                                            <span v-if="mail.header.date" class="mailbox-date-text">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="12" cy="12" r="9" />
                                                    <polyline points="12 7 12 12 15 15" />
                                                </svg>
                                                @{{moment(mail.header.date).format('YYYY-MM-DD HH:mm')}}
                                            </span>
                                        </td>
                                        <td class="mailbox-actions">
                                            <div class="mailbox-actions-group">
                                                <a href="#" class="btn btn-sm dash-act dash-act-edit" @click.prevent="openModal(mail)" title="Move to folder">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                        <path d="M16 5l3 3" />
                                                    </svg>
                                                </a>
                                                <a href="#" class="btn btn-sm dash-act dash-act-delete" @click.prevent="deleteMail(mail)" title="Delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            @include('email.modals.message')
                            @include('email.modals.changeFolder')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="box-header">
            <h4>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <rect x="3" y="5" width="18" height="14" rx="2" />
                    <polyline points="3 7 12 13 21 7" />
                </svg>
                {{ trans('main.Inbox') }}
            </h4>
        </div>
        <div class="box-body">
            <div class="alert alert-warning d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="9" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <div>{{ trans('main.Youdonthavepermissions') }}</div>
            </div>
        </div>
    @endif
</div>

<style>
    .select2-container--default .select2-results > .select2-results__options {
        max-height: 200px !important;
    }

    .select2-results {
        overflow-y: hidden;
    }
    
    .dashboard-inbox .box-header {
        flex-wrap: wrap;
    }

    .dashboard-inbox .inbox-view-all {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    .dashboard-inbox .mailbox-messages {
        max-height: 600px;
        overflow-y: auto;
        border: 1px solid var(--tblr-border-color, #e5e7eb);
        border-radius: 8px;
    }

    .dashboard-inbox .emails-inbox {
        table-layout: fixed;
        width: 100%;
        min-width: 760px;
    }

    .dashboard-inbox .emails-inbox td {
        vertical-align: middle;
        padding: 0.75rem;
        border-color: #eef0f3;
    }

    .email-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .email-row:hover {
        background-color: #f8f9fa;
    }

    .dashboard-inbox .mailbox-star { width: 44px; text-align: center; }
    .dashboard-inbox .mailbox-name { width: 34%; }
    .dashboard-inbox .mailbox-attachment { width: 44px; text-align: center; }
    .dashboard-inbox .mailbox-date { width: 150px; }
    .dashboard-inbox .mailbox-actions { width: 112px; }

    .dashboard-inbox .mailbox-sender {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        min-width: 0;
    }

    .dashboard-inbox .mailbox-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        color: #ffffff;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
    }

    .dashboard-inbox .mailbox-sender-text {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .dashboard-inbox .mailbox-sender-name,
    .dashboard-inbox .mailbox-sender-email,
    .dashboard-inbox .mailbox-subject-text > span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dashboard-inbox .mailbox-sender-name {
        color: #1f2937;
        font-weight: 600;
    }

    .dashboard-inbox .mailbox-sender-email {
        color: #6b7280;
        font-size: 0.8125rem;
    }

    .dashboard-inbox .mailbox-subject-text {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        min-width: 0;
        color: #6b7280;
    }

    .dashboard-inbox .email-unread .mailbox-subject-text {
        color: #1f2937;
        font-weight: 600;
    }

    .dashboard-inbox .mailbox-subject-text .icon {
        flex: 0 0 auto;
    }

    .dashboard-inbox .mailbox-date-text {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        color: #6b7280;
        font-size: 0.8125rem;
        white-space: nowrap;
    }

    .dashboard-inbox .mailbox-actions-group {
        display: flex;
        justify-content: flex-end;
        gap: 0.375rem;
    }

    .mailbox-star svg {
        cursor: pointer;
        transition: fill 0.2s ease;
    }

    .mailbox-star svg:hover {
        fill: currentColor;
    }

    .empty-state {
        padding: 3rem 0;
    }
    
</style>

<script>
    $(function () {
        new Vue({
            el: '#inbox',
            mixins:['newMessage'],
            data: {
                mails: null,
                imapConnected: false,
                currentFolder: 'INBOX',
                folderSend:'INBOX',
                currentMail:null,
                loading: true,
                loadingFolders:false,
                folders:null,
                search:null,
                newEmail: {
                    to:'',
                    subject:'',
                    content:'',
                    files:[]
                },
                emailClassErrors:{
                    to:'',
                    subject:'',
                },
                reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/
            },

            created: function () {
                this.fetchData();
                this.getListFolderByUser();
            },
            
            mounted() {
                let app = this;
                window.addEventListener("message", function(){
                    app.fetchData();
                }, false);
            },
            
            methods: {
                // Extract name from email format "Name <email@domain.com>" or just "email@domain.com"
                extractName(email) {
                    if (!email) return '';
                    const match = email.match(/^(.+?)\s*<.+>$/);
                    if (match) {
                        return match[1].trim();
                    }
                    // If no name, return part before @
                    return email.split('@')[0];
                },
                
                // Extract email address from format "Name <email@domain.com>" or just "email@domain.com"
                extractEmail(email) {
                    if (!email) return '';
                    const match = email.match(/<(.+?)>/);
                    if (match) {
                        return match[1];
                    }
                    return email;
                },
                
                // Get initials from email
                getInitials(email) {
                    const name = this.extractName(email);
                    const words = name.split(/\s+/);
                    if (words.length >= 2) {
                        return (words[0][0] + words[1][0]).toUpperCase();
                    }
                    return name.substring(0, 2).toUpperCase();
                },
                
                // Generate color from email string
                getColorFromEmail(email) {
                    const colors = [
                        '#206bc4', '#4299e1', '#0ca678', '#f59f00', 
                        '#d63939', '#ae3ec9', '#fd7e14', '#20c997'
                    ];
                    let hash = 0;
                    for (let i = 0; i < email.length; i++) {
                        hash = email.charCodeAt(i) + ((hash << 5) - hash);
                    }
                    return colors[Math.abs(hash) % colors.length];
                },
                isEmailValid: function() {
                    return (this.newEmail.to == "")? "" : (this.reg.test(this.newEmail.to)) ? true : false;
                },

                ClearSearchForm(){
                    this.search = null;
                    this.fetchData(true, 'INBOX');
                },

                clearForm(){
                    this.newEmail.to = '';
                    this.newEmail.subject = '';
                    this.newEmail.content = '';
                    this.newEmail.files = [];
                    if(CKEDITOR.instances['editor1']){
                        CKEDITOR.instances['editor1'].setData('');
                    }
                },

                openNewEmailModal(){
                    this.clearForm();
                    $("#NewEmail").modal("show");
                },

                openModal(email){
                    this.currentMail = email;
                    $("#modalCreate").modal("show");
                },

                fetchData(get = false, folder=false) {
                    var self = this;
                    var userId = $('meta[name="user-id"]').attr('content');
                    this.mails=[];
                    this.loading = true;

                    let emails = JSON.parse(localStorage.getItem(`emails[${this.currentFolder}]`));
                    if(emails && !get ){
                        this.mails = emails.emails;
                        this.loading = false;
                        this.page = emails.page;
                        this.count = emails.count;
                        this.perpage = emails.perpage;
                    }else{
                        axios.post(`/api/v1/users/${userId}/emails`,{
                            search: this.search,
                            folder: (folder)?folder:this.currentFolder,
                        })
                        .then((result)=>{
                            this.loading = false;
                            this.mails = result.data.emails;
                            localStorage.setItem(`emails[${(folder)?folder:this.currentFolder}]`,JSON.stringify(result.data));
                        }).catch((err)=>{
                            $.toast({
                                heading: 'Warning',
                                text: "Subject is empty",
                                icon: 'danger',
                                loader: true,
                                hideAfter : 1500,
                                position: 'top-right',
                            });
                        });
                    }
                },

                openInbox: function (link) {
                    window.location.href = link;
                },

                onFileChange(e) {
                    Object.values(this.$refs.file.files).forEach(file => {
                        this.newEmail.files.push(file);
                    });
                },

                sendEmail(){
                    this.newEmail.content = CKEDITOR.instances['editor1'].getData();
                    if(this.newEmail.to.length ==0 || !this.isEmailValid()){
                        this.emailClassErrors.to= 'has-error';
                        $.toast({
                            heading: 'Warning',
                            text: "Sender is empty",
                            icon: 'danger',
                            loader: true,
                            hideAfter : 1500,
                            position: 'top-right',
                        });
                        return false;
                    }else{
                        this.emailClassErrors.to= 'has-succes'
                    }
                    if(this.newEmail.subject.length ==0){
                        this.emailClassErrors.subject= 'has-error'
                        $.toast({
                            heading: 'Warning',
                            text: "Subject is empty",
                            icon: 'danger',
                            loader: true,
                            hideAfter : 1500,
                            position: 'top-right',
                        });
                        return false;
                    }else{
                        this.emailClassErrors.subject= 'has-success'
                    }

                    let userId = $('meta[name="user-id"]').attr('content');
                    let formData = new FormData();

                    this.newEmail.files.forEach((file,key)=>{
                        formData.append(`files[${key}]`,file)
                    });

                    formData.append('to',this.newEmail.to);
                    formData.append('subject',this.newEmail.subject);
                    formData.append('content',this.newEmail.content);
                    this.CloseNewEmailModal();

                    axios.post( `/api/v1/users/${userId}/emails/send`,
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        }
                    ).then((result)=>{
                        this.loading = false;
                        localStorage.setItem(`emails[Sent]`,JSON.stringify(result.data));
                        $.toast({
                            heading: 'Success',
                            text: "Send message",
                            icon: 'success',
                            loader: true,
                            hideAfter : 1500,
                            position: 'top-right',
                        });
                    }).catch((e)=>{
                        $.toast({
                            heading: 'Error',
                            text: "Send message"+e,
                            icon: 'error',
                            loader: true,
                            hideAfter : 1500,
                            position: 'top-right',
                        });
                    });
                },

                CloseNewEmailModal(){
                    this.newEmail.content = '<p></p>';
                    $("#NewEmail").modal('hide') ;
                },

                removeFile(index) {
                    this.newEmail.files = this.newEmail.files.filter((val, key)=>{
                        return key!=index;
                    });
                },

                deleteMail(email, list=false){
                    const confirmDelete = typeof window.appConfirm === 'function'
                        ? window.appConfirm('Are you sure you want to delete this email?', {
                            title: 'Confirm delete',
                            confirmText: 'Delete',
                            cancelText: 'Cancel'
                        })
                        : Promise.resolve(true);

                    confirmDelete.then((confirmed) => {
                        if (!confirmed) {
                            return;
                        }

                        if(list) this.backToList()
                        let userId = $('meta[name="user-id"]').attr('content');
                        return axios.post(`/api/v1/users/${userId}/email/${email.header.uid}/delete`,{
                            folder:this.currentFolder
                        }).then((result)=>{
                            if (typeof window.appToast === 'function') {
                                window.appToast('Email is deleted', 'success', 'Success');
                            }
                            this.fetchData(true);
                        }).catch((err)=>{
                            if (typeof window.appToast === 'function') {
                                window.appToast('Email not deleted', 'error', 'Error');
                            }
                        });
                    });
                },

                getListFolderByUser(get= false){
                    let userId = $('meta[name="user-id"]').attr('content');
                    this.loadingFolders = true;
                    this.folders=null;

                    let folders = JSON.parse(localStorage.getItem('folders'));

                    if(folders && !get){
                        this.folders = folders;
                        this.loadingFolders = false;
                    } else {
                        axios.post(`/api/v1/users/${userId}/emailfolders`)
                            .then((res)=>{
                                this.loadingFolders = false;
                                this.folders = res.data.folders;
                                localStorage.setItem('folders',JSON.stringify(res.data.folders));
                            });
                    }
                },

                moveToFolder(email){
                    let userId = $('meta[name="user-id"]').attr('content');
                    axios.post(`/api/v1/users/${userId}/email/${this.currentMail.header.uid}/move`,{
                        folder:this.currentFolder,
                        moveFolder:this.folderSend
                    }).then((result)=>{
                        $.toast({
                            heading: 'Success',
                            text: "Folder is changed",
                            icon: 'success',
                            loader: true,
                            hideAfter : 1500,
                            position: 'top-right',
                        });
                        this.fetchData(true);
                        this.fetchData(true,this.folderSend);
                        $("#modalCreate").modal('hide') ;
                    }).catch((err)=>{
                        $.toast({
                            heading: 'Error',
                            text: "Email not moved",
                            icon: 'error',
                            loader: true,
                            hideAfter : 1500,
                            position: 'top-right',
                        });
                    });
                },

                CloseMoveToModal(){
                    $("#modalCreate").modal('hide') ;
                },

                OpenEmail(email){
                    localStorage.setItem('openEmail',JSON.stringify(email));
                    window.location.href = "{{route('email.index')}}";
                },
            }
        });
    });
</script>
