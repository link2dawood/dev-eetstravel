<div class="card shadow-sm h-100">
    @if(Auth::user()->can('dashboard.inbox'))
        <div id="inbox" class="d-flex flex-column h-100">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0 flex-grow-1">
                    <i class="ti ti-inbox me-2 text-primary"></i>Inbox
                </h3>
                <a href="{{ route('email.index') }}" class="btn btn-sm btn-outline-primary me-2">
                    <i class="ti ti-mail-opened me-1"></i>View all
                </a>
                <button type="button"
                        class="btn btn-sm btn-icon btn-ghost-secondary"
                        data-widget="collapse"
                        aria-label="Collapse">
                    <i class="ti ti-minus"></i>
                </button>
                <button type="button"
                        class="btn btn-sm btn-icon btn-ghost-secondary ms-1"
                        data-widget="remove"
                        aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            <div class="card-body border-bottom py-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text"
                           id="search"
                           name="search"
                           class="form-control"
                           v-model="search"
                           placeholder="Search emails…"
                           @keyup.enter="fetchData">
                    <button class="btn btn-primary" @click="fetchData">
                        {{ trans('main.Search') }}
                    </button>
                    <button type="button"
                            class="btn btn-outline-danger"
                            @click="ClearSearchForm()"
                            v-if="search != null && search !== ''">
                        <i class="ti ti-x me-1"></i>Clear
                    </button>
                </div>
            </div>
            
            <div class="card-body p-0 flex-grow-1 d-flex flex-column overflow-hidden">
                <div v-if="loading" class="d-flex align-items-center justify-content-center" style="min-height: 240px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading…</span>
                    </div>
                </div>

                <div v-else class="flex-grow-1 d-flex flex-column overflow-hidden">
                    <div class="table-responsive mailbox-messages flex-grow-1" style="overflow-y:auto;">
                            <table class="table table-hover table-striped finder-disable emails-inbox">
                                <tbody>
                                    <tr v-if="!mails" class="row">
                                        <td class="col-md-12">
                                            <div class="empty-state text-center py-5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                                    <polyline points="3 7 12 13 21 7" />
                                                </svg>
                                                <h2 class="text-muted">{{ trans('main.Thefolderisempty') }}</h2>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr v-for="mail in mails" v-cloak style="cursor: pointer" class="email-row">
                                        <td class="mailbox-star" style="width: 40px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                            </svg>
                                        </td>
                                        <td class="mailbox-name" @click="OpenEmail(mail)" style="width: 200px;">
                                            <a :href="mail.click_redirect" class="d-flex align-items-center text-decoration-none">
                                                <span class="avatar avatar-sm me-2" :style="'background-color: ' + getColorFromEmail(mail.header.from)">
                                                    @{{getInitials(mail.header.from)}}
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <strong class="text-dark">@{{extractName(mail.header.from)}}</strong>
                                                    <small class="text-muted">@{{extractEmail(mail.header.from)}}</small>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="mailbox-subject" @click="OpenEmail(mail)">
                                            <b v-if="mail.header.seen == 0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-blue me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                                    <polyline points="3 7 12 13 21 7" />
                                                </svg>
                                                @{{mail.header.subject}}
                                            </b>
                                            <span v-else class="text-muted">@{{mail.header.subject}}</span>
                                        </td>
                                        <td class="mailbox-attachment" @click="OpenEmail(mail)" style="width: 40px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                            </svg>
                                        </td>
                                        <td class="mailbox-date" @click="OpenEmail(mail)" style="width: 180px;">
                                            <div v-if="mail.header.date" class="text-muted small">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <circle cx="12" cy="12" r="9" />
                                                    <polyline points="12 7 12 12 15 15" />
                                                </svg>
                                                @{{moment(mail.header.date).format('YYYY-MM-DD H:m:s')}}
                                            </div>
                                        </td>
                                        <td style="width: 150px;">
                                            <div class="btn-group pull-right">
                                                <a href="#" class="btn btn-sm btn-icon btn-ghost-warning" @click="openModal(mail)" title="Move to folder">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                        <path d="M16 5l3 3" />
                                                    </svg>
                                                </a>
                                                <a class="btn btn-sm btn-icon btn-ghost-danger" @click="deleteMail(mail)" title="Delete">
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
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="ti ti-mail me-2 text-primary"></i>{{ trans('main.Inbox') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="alert alert-warning d-flex align-items-center mb-0">
                <i class="ti ti-alert-circle me-2"></i>
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
    
    .email-row {
        transition: background-color 0.2s ease;
    }
    
    .email-row:hover {
        background-color: #f8f9fa;
    }
    
    .mailbox-star svg {
        cursor: pointer;
        transition: fill 0.2s ease;
    }
    
    .mailbox-star svg:hover {
        fill: currentColor;
    }
    
    .btn-ghost-primary {
        color: #206bc4;
    }
    
    .btn-ghost-primary:hover {
        background-color: #e6f2ff;
    }
    
    .btn-ghost-warning {
        color: #f59f00;
    }
    
    .btn-ghost-warning:hover {
        background-color: #fff4e6;
    }
    
    .btn-ghost-danger {
        color: #d63939;
    }
    
    .btn-ghost-danger:hover {
        background-color: #ffe6e6;
    }
    
    .empty-state {
        padding: 3rem 0;
    }
    
    .btn-icon {
        padding: 0.375rem 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
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
                    $("#NewEmail").modal();
                },

                openModal(email){
                    this.currentMail = email;
                    $("#modalCreate").modal();
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
                    if(confirm('Are you sure delete Email?')){
                        if(list) this.backToList()
                        let userId = $('meta[name="user-id"]').attr('content');
                        return axios.post(`/api/v1/users/${userId}/email/${email.header.uid}/delete`,{
                            folder:this.currentFolder
                        }).then((result)=>{
                            $.toast({
                                heading: 'Success',
                                text: "Email is deleted",
                                icon: 'success',
                                loader: true,
                                hideAfter : 1500,
                                position: 'top-right',
                            });
                            this.fetchData(true);
                        }).catch((err)=>{
                            $.toast({
                                heading: 'Error',
                                text: "Email not deleted",
                                icon: 'error',
                                loader: true,
                                hideAfter : 1500,
                                position: 'top-right',
                            });
                        });
                    }
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