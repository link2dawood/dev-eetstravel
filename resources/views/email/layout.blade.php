@extends('scaffold-interface.layouts.tabler-app')

@section('content')
    @include('layouts.title',
    ['title' => 'Email', 'sub_title' => 'Inbox',
    'breadcrumbs' => [
    ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
    ['title' => 'Emails', 'icon' => 'map-signs', 'route' => null]]])
    <section class="content py-3">
        @if(!$imapConnected)
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="ti ti-alert-triangle me-2"></i>
                <div>
                    Your mailbox is not connected.
                    <a href="{{ route('snappymail.configure') }}" class="alert-link">Configure your mailbox credentials</a>
                    to receive mail here, or
                    <a href="{{ route('snappymail.sso') }}" class="alert-link" target="_blank">open SnappyMail webmail</a>.
                </div>
            </div>
        @else
        <div class="row g-3" id="emailsfolders">
            <div class="col-lg-3 col-md-4">
                <button type="button"
                        class="btn btn-primary w-100 mb-3"
                        v-show="false"
                        @click="openNewEmailModal()">
                    <i class="ti ti-pencil-plus me-2"></i>New email
                </button>

                @include('email.parts.foldersList')
				@include('email.parts.tourfolders')
            </div>
            <div class="col-lg-9 col-md-8" id="tour_box" style="display:none">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="card-title mb-0 flex-grow-1">
                            <i class="ti ti-route me-2 text-primary"></i>Tours
                        </h3>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-arrow-left me-1"></i>Back
                        </a>
                    </div>
                    <div class="card-body">
                        @include('email.tour_index')
                    </div>
                    





                </div>
            </div>
            <!-- /.col -->
            <div class="col-lg-9 col-md-8" id="email_box">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <div class="d-flex align-items-center w-100">
                            <h3 class="card-title mb-0 flex-grow-1 text-uppercase">
                                <i class="ti ti-mail me-2 text-primary"></i>@{{ currentFolder }}
                            </h3>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-arrow-left me-1"></i>Back
                            </a>
                        </div>

                        <div class="input-group mt-3">
                            <span class="input-group-text">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" id="search" name="search" class="form-control"
                                   v-model="search" placeholder="Search emails…"
                                   @keyup.enter="getListEmailsByUser">
                            <button class="btn btn-primary" @click="getListEmailsByUser">
                                <i class="ti ti-search me-1"></i>Search
                            </button>
                            <button type="button" class="btn btn-outline-danger"
                                    @click="ClearSearchForm()"
                                    v-if="search != null && search !== ''">
                                <i class="ti ti-x me-1"></i>Clear
                            </button>
                        </div>
                    </div>

                    <div v-if="tour"
                         class="d-flex align-items-center justify-content-center text-muted"
                         style="min-height: 240px; font-size: 1.1rem;">
                        <div class="text-center">
                            <i class="ti ti-mail-off d-block mb-2" style="font-size: 2.5rem;"></i>
                            You don't have any emails for this tour yet.
                        </div>
                    </div>

                    @yield('main-content')

                    <div v-if="loading" class="d-flex align-items-center justify-content-center" style="min-height: 240px;">
                        <div class="text-center text-muted">
                            <div class="spinner-border text-primary mb-2" role="status">
                                <span class="visually-hidden">Loading…</span>
                            </div>
                            <div>Connecting to mail server…</div>
                        </div>
                    </div>

                   




                </div>
            </div>
            @include('email.modals.createFolder')
            @include('email.modals.changeFolder')
            @include('email.modals.message')
        </div>
    </section>
    @endif

    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.min.js"></script>--}}

    {{--<script src="https://unpkg.com/vuex@3.1.1/dist/vuex.js"></script>--}}

    {{--<script src="https://cdn.jsdelivr.net/npm/vuex-persist@2.0.0/dist/umd/index.js"></script>--}}


    <script>
        // $(function () {
        //     Vue.component('ckeditor', {
        //         template: `<div class="ckeditor"><textarea :id="id" ></textarea></div>`,
        //         props: {
        //             value: {
        //                 type: String
        //             },
        //             id: {
        //                 type: String,
        //                 default: 'editor'
        //             },
        //             height: {
        //                 type: String,
        //                 default: '325px',
        //             },
        //             toolbar: {
        //                 type: Array,
        //                 default: () => [
        //                     ['Undo', 'Redo'],
        //                     ['Bold', 'Italic', 'Strike'],
        //                     ['NumberedList', 'BulletedList'],
        //                     ['Cut', 'Copy', 'Paste'],
        //                 ]
        //             },
        //             language: {
        //                 type: String,
        //                 default: 'en'
        //             },
        //             extraplugins: {
        //                 type: String,
        //                 default: ''
        //             }
        //         },
        //         beforeUpdate() {
        //             const ckeditorId = this.id
        //             if (this.value !== CKEDITOR.instances[ckeditorId].getData()) {
        //                 CKEDITOR.instances[ckeditorId].setData(this.value)
        //             }
        //         },
        //         mounted() {
        //             const ckeditorId = this.id
        //             const ckeditorConfig = {
        //                 toolbar: this.toolbar,
        //                 language: this.language,
        //                 height: this.height,
        //                 extraPlugins: this.extraplugins
        //             }
        //             CKEDITOR.replace(ckeditorId, ckeditorConfig)
        //             CKEDITOR.instances[ckeditorId].setData(this.value)
        //             CKEDITOR.instances[ckeditorId].on('change', () => {
        //                 let ckeditorData = CKEDITOR.instances[ckeditorId].getData()
        //                 if (ckeditorData !== this.value) {
        //                     this.$emit('input', ckeditorData)
        //                 }
        //             })
        //         },
        //         destroyed() {
        //             const ckeditorId = this.id
        //             if (CKEDITOR.instances[ckeditorId]) {
        //                 CKEDITOR.instances[ckeditorId].destroy()
        //             }
        //         }
        //
        //     });
        // });
        $(function () {
            new Vue({
                el: '#emailsfolders',
                data(){
                    return {
                        folders:null,
                        currentFolder: 'INBOX',
                        emailsArray:[],
						toursArray:[],
						tour:false,
                        page:0,
                        folderSend:'INBOX',
                        count: 0,
                        perpage:10,
                        currentMail:null,
                        search:null,
                        newFolder:null,
                        content: '',
                        newEmail:{
                            content:'',
                            to:'',
                            subject:'',
                            files:[]
                        },
                        emailClassErrors:{
                          to:'',
                          subject:'',

                        },
                        email:{},
                        test:'',
                        view:false,
                        reply:false,
                        loading: false,
                        loadingFolders: false,
                        userId:$('meta[name="user-id"]').attr('content'),
                        reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/

                    };
                },
                mounted(){
                    this.getListFolderByUser();
                    this.getListEmailsByUser();
                    let email = localStorage.getItem('openEmail');
                    if(email){
                        this.infoEmail(JSON.parse(email));
                        localStorage.removeItem('openEmail');
                    }
                    let app = this;
                    window.addEventListener("message", function(){
                        app.getListEmailsByUser();
                    }, false);

                },

                computed: {
                    totalPages: function () {
                        return Math.ceil(this.count / this.perpage)
                    },
                },
                methods:{
                    isEmailValid: function() {
                        return (this.newEmail.to == "")? "" : (this.reg.test(this.newEmail.to)) ? true : false;
                    },
                    sendEmail(){
                        this.newEmail.content = CKEDITOR.instances['editor1'].getData();
                        // return false;
                        if(this.newEmail.to.length ==0 || !this.isEmailValid()){
                            this.emailClassErrors.to= 'has-error';
                            $.toast({
                                heading: 'Warning',
                                text: "Sender is empty",
                                icon: 'danger',
                                loader: true,        // Change it to false to disable loader
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
                                loader: true,        // Change it to false to disable loader
                                hideAfter : 1500,
                                position: 'top-right',
                            });
                            return false;
                        }else{
                            this.emailClassErrors.subject= 'has-success'
                        }

                        let formData = new FormData();

                        this.newEmail.files.forEach((file,key)=>{
                            formData.append(`files[${key}]`,file)
                        });

                        formData.append('to',this.newEmail.to);
                        formData.append('subject',this.newEmail.subject);
                        formData.append('content',this.newEmail.content);

// console.log(formData, this.newEmail);
// return false;
                        this.CloseNewEmailModal();
                        axios.post( `/api/v1/users/${this.userId}/emails/send`,
                            formData,
                            {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            }
                        ).then((result)=>{
                            // this.getListEmailsByUser(true,"Sent");
                            localStorage.setItem(`emails[Sent]`,JSON.stringify(result.data));






                            $.toast({
                                heading: 'Success',
                                text: "Send message",
                                icon: 'success',
                                loader: true,        // Change it to false to disable loader
                                hideAfter : 1500,
                                position: 'top-right',
                            });
                            return false;
                        }).catch((e)=>{
                            $.toast({
                                heading: 'Error',
                                text: "Send message"+e,
                                icon: 'error',
                                loader: true,        // Change it to false to disable loader
                                hideAfter : 1500,
                                position: 'top-right',
                            });


                        });
                        return false;
                    },

                    onFileChange(e) {
                        Object.values(this.$refs.file.files).forEach(file => {
                            this.newEmail.files.push(file);
                        });
                    },

                    removeFile(index) {
                        this.newEmail.files = this.newEmail.files.filter((val, key)=>{
                            return key!=index;
                        });
                    },

                    openNewEmailModal(email){
                        if(email){
                            let sentEmails = [];
                            let fromEmails = [];

                            email.header.details.from.forEach(email => {
                                sentEmails.push(email.mailbox+'@'+email.host);
                            });
                            email.header.details.to.forEach(email => {
                                fromEmails.push(email.mailbox+'@'+email.host);
                            });

                            this.newEmail.from = fromEmails.join();
                            this.newEmail.to = sentEmails.join();
                            this.newEmail.subject = 'RE: '+email.header.subject;
                            this.newEmail.content = '<blockquote>'+email.message.html.body+'</blockquote>';
                        } else{
                            this.newEmail.to = '';
                            this.newEmail.subject = '';
                            this.newEmail.content = '';
                            // if(CKEDITOR.instances['editor'])
                            // CKEDITOR.instances['editor'].setData('')
                        }
                        $("#NewEmail").modal();
                    },

                    openCreateFolderModal(){
                        $("#modalAddFolder").modal();
                    },

                    createFolder(){
                        let userId = $('meta[name="user-id"]').attr('content');

                        axios.post(`/api/v1/users/${userId}/emails/createFolder`,{
                            folder: this.newFolder,

                        }).
                        then((result)=>{
                           jSuites.notification({
								name: 'Success',
								message: 'Folder created Successfully',
							});

                            this.newFolder = null;
                            $("#modalAddFolder").modal('hide');
                            this.getListFolderByUser(true);
                        }).catch((err)=>{
							jSuites.notification({
								error: 1,
								name: 'Error message',
								message: 'Folder is not created'+ err,
							});

                        });

                    },

                    ClearSearchForm(){
                        this.search = null;
                        this.getListEmailsByUser(true, 'INBOX');
                    },
					getTours(name){
                        let userId = $('meta[name="user-id"]').attr('content');
                        this.toursArray = [];
                        this.loading = true;

                        // let emails = this.$store.state.emails[this.currentFolder];
                       if(name == 'active'){
						   url = `/api/v1/getTours`;
					   }else{
						    url = `/api/v1/getArchiveTours`;
					   }
                            axios.post(url,{
                               
                                page: this.page,
                                search: this.search
                            })
                                .then((result)=>{
									this.toursArray = result.data.tour;
								   console.log(this.toursArray);
									

                                    // this.$store.commit('setEmails',{'folder':this.currentFolder, data:result.data})


                                }).catch((err)=>{
                                $.toast({
                                    heading: 'Error',
                                    text: err,
                                    icon: 'danger',
                                    loader: true,        // Change it to false to disable loader
                                    hideAfter : 1500,
                                    position: 'top-right',
                                });
                                this.loading = false;
                            });
                        


                    },
					 getListEmailsUser( get = false, folder=false){
						console.log(this.search);
                        let userId = $('meta[name="user-id"]').attr('content');
                        this.emailsArray = [];
                        this.loading = true;


                        let emails = JSON.parse(localStorage.getItem(`emails[${this.currentFolder}]`));
                        // let emails = this.$store.state.emails[this.currentFolder];
                      
                            axios.post(`/api/v1/users/${userId}/touremails`,{
                                folder: (folder)?folder:this.currentFolder,
                                page: this.page,
                                search: this.search,
                            })
                                .then((result)=>{
                                    this.loading = false;
                                    this.emailsArray = result.data.emails;
                                    this.page = result.data.page;
                                    this.count = result.data.count;
                                    this.perpage = result.data.perpage;
									this.search = "";
								
									if(this.emailsArray.length === 0){
									$('#email_box').css("display","none");
									$('#tour_box').css("display","none");
									this.tour = true;
									}
                                    localStorage.setItem(`emails[${(folder)?folder:this.currentFolder}]`,JSON.stringify(result.data));

									
                                    // this.$store.commit('setEmails',{'folder':this.currentFolder, data:result.data})


                                }).catch((err)=>{
                                $.toast({
                                    heading: 'Error',
                                    text: err,
                                    icon: 'danger',
                                    loader: true,        // Change it to false to disable loader
                                    hideAfter : 1500,
                                    position: 'top-right',
                                });
                                this.loading = false;
                            });
                        


                    },

                    getListEmailsByUser( get = false, folder=false){
					
                        let userId = $('meta[name="user-id"]').attr('content');
                        this.emailsArray = [];
                        this.loading = true;


                        let emails = JSON.parse(localStorage.getItem(`emails[${this.currentFolder}]`));
                        // let emails = this.$store.state.emails[this.currentFolder];
                        if(emails && !get && (this.page == 0)){
                            this.emailsArray = emails.emails;
                            this.loading = false;
                            this.page = emails.page;
                            this.count = emails.count;
                            this.perpage = emails.perpage;

                        }else{
                            axios.post(`/api/v1/users/${userId}/emails`,{
                                folder: (folder)?folder:this.currentFolder,
                                page: this.page,
                                search: this.search,
                            })
                                .then((result)=>{
                                    this.loading = false;
                                    this.emailsArray = result.data.emails;
                                    this.page = result.data.page;
                                    this.count = result.data.count;
                                    this.perpage = result.data.perpage;
                                    localStorage.setItem(`emails[${(folder)?folder:this.currentFolder}]`,JSON.stringify(result.data));


                                    // this.$store.commit('setEmails',{'folder':this.currentFolder, data:result.data})


                                }).catch((err)=>{
                                jSuites.notification({
									error: 1,
									name: 'Error message',
									message: 'An  Error Occured',
								});
                                this.loading = false;
                            });
                        }


                    },

                    openModal(email){
                        this.currentMail = email;
                         $("#modalCreate").modal();
                    },

                    infoEmail(email){
                        let userId = $('meta[name="user-id"]').attr('content');
                        email.header.seen = 1;
                        this.loading = true;

                        let emails = JSON.parse(localStorage.getItem(`emails[${this.currentFolder}]`));

                        // let emls = _.replace(emails.emails, { 'header.uid': email.header.uid }, email);
                        let emls = emails.emails.findIndex(obj=>{
                            return obj.header.uid == email.header.uid
                        });
                        emails.emails[emls] = email
								if (typeof email.header.uid === 'undefined') {
									location.reload();
								}

                        localStorage.setItem(`emails[${this.currentFolder}]`,JSON.stringify(emails));
						
                        axios.post(`/api/v1/users/${userId}/email/${email.header.uid}/get`,{
                            folder:this.currentFolder
                        })
                            .then((res)=>{
							console.log(res);
                                this.view = true;
                                this.email = res.data.message;
                                this.loading = false;
                            })
						.catch(function (error) {
							console.log(error);
						  });
					

                    },

                    CloseMoveToModal(){
                        $("#modalCreate").modal('hide') ;
                    },

                    CloseNewEmailModal(){
                        this.newEmail.content = '';
                        CKEDITOR.instances['editor1'].setData('')
                        $("#NewEmail").modal('hide') ;
                    },

                    changeFolder(folder){
						$('#email_box').css("display","block");
						$('#tour_box').css("display","none");
                            this.currentFolder = folder;
                            this.view = false;
							this.tour = false;
                            this.getListEmailsByUser();
                    },
					viewTourEmails(folder,tour){
						$('#email_box').css("display","block");
						$('#tour_box').css("display","none");
						
						this.currentFolder = folder;
                        this.view = false;
						this.search = tour;
						this.tour = false;
                        this.getListEmailsUser();
                         
                    },
					changeTourFolder(name){
						$('#email_box').css("display","none");
						$('#tour_box').css("display","block");
						this.view = false;
						this.tour = false;
                         this.getTours(name);
                    },

                    changePage(page){
                            this.page = page;
                            this.getListEmailsByUser();
                    },

                    moveToFolder(email){
                        let userId = $('meta[name="user-id"]').attr('content');
                        axios.post(`/api/v1/users/${userId}/email/${this.currentMail.header.uid}/move`,{
                            folder:this.currentFolder,
                            moveFolder:this.folderSend,
							message_id:this.currentMail.header.message_id,
                        }).then((result)=>{
							jSuites.notification({
								name: 'Success',
								message: 'Folder is change Successfully',
							});

                            this.getListEmailsByUser(true);
                            this.getListEmailsByUser(true,this.folderSend);
                            $("#modalCreate").modal('hide') ;
                        }).catch((err)=>{
							jSuites.notification({
								error: 1,
								name: 'Error message',
								message: 'Email not moved',
							});
                            

                        });
                    },

                    deleteMail(email, list=false){
                        if(confirm('Are you sure delete Email?')){
                            if(list) this.backToList()
                            let userId = $('meta[name="user-id"]').attr('content');
                            return axios.post(`/api/v1/users/${userId}/email/${email.header.uid}/delete`,{
                                folder:this.currentFolder
                            }).then((result)=>{
								jSuites.notification({
									name: 'Success',
									message: 'Email is deleted',
								});

                                this.getListEmailsByUser(true);
                            }).catch((err)=>{

                                $.toast({
                                    heading: 'Error',
                                    text: "Email not deleted",
                                    icon: 'error',
                                    loader: true,        // Change it to false to disable loader
                                    hideAfter : 1500,
                                    position: 'top-right',
                                });


                            });
                        }
                    },

                    backToList(){
                        this.view = false;
                        // this.getListEmailsByUser();

                    },

                    replyEmail(email){
                        this.reply = true;
                        this.openNewEmailModal(email)
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

                    downloadAttachment(attach){
                        let userId = $('meta[name="user-id"]').attr('content');
                        axios.post(`/api/v1/users/${userId}/attachment`,{attach})
                            .then((res)=>{
                                const link = document.createElement('a');
                                link.href = res.data;
                                link.setAttribute('download', attach.name);
                                document.body.appendChild(link);
                                link.click();
                            });
                    },

                }
            });
        });
    </script>

@endsection
