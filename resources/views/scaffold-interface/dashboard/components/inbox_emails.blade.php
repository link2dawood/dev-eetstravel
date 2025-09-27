
<div class="box box-primary" style="height: 55rem;">
    @if(Auth::user()->can('dashboard.inbox'))
        <div  id="inbox">
        <div class="box-header" >
            <h4>Inbox</h4>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <a @click="openNewEmailModal()">
                        <button   class="btn btn-default btn-flat" style="display:none">
                            New Email
                        </button>
                    </a>
                    <a href="{{route('email.index')}}">
                        <button href="javascript:void(0)" class="btn btn-default btn-flat pull-right">View All Emails
                        </button>
                    </a>
                </div>
            </div>
        </div>
            <div class="col-md-12" style="margin-bottom: 20px">
                <div class="row">
                    <div class="col-md-12">
                        <div class="search_emails" style="margin-top: 20px">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="search" name="search" class="form-control" v-model="search">
                                    <span class="input-group-btn">
                               <button class="btn btn-primary" @click="fetchData">{{ trans('main.Search') }}</button>
                            <button type="button" class="btn btn-danger btn-sm" @click="ClearSearchForm()" v-if="search!=null">Clear</button>

                        </span>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">

                    </div>
                </div>
                <div>

                    <div v-if="loading">
                        <div style="height: 300px;">
                            <div class="loader"></div>

                        </div>
                    </div>

                    <div v-else>
                        <div class="table-responsive mailbox-messages" style="height: 600px;">
                            <table class="table table-hover table-striped finder-disable emails-inbox">
                                <tbody>
                                <tr v-if="!mails" class="row">
                                    <td class="col-md-12">
                                        <h2>{{ trans('main.Thefolderisempty') }}</h2>
                                    </td>
                                </tr>


                                <tr v-for="mail in mails" v-cloak style="cursor: pointer">
                                    <td class="mailbox-star ">
                                    </td>
                                    <td class="mailbox-name" @click="OpenEmail(mail)"><a
                                                :href="mail.click_redirect">
                                            @{{mail.header.from}}</a>
                                    </td>
                                    <td
                                        class="mailbox-subject"  @click="OpenEmail(mail)">
                                        <b v-if="mail.header.seen == 0">@{{mail.header.subject}}</b>
                                        <span v-else>@{{mail.header.subject}}</span>
                                    </td>
                                    <td class="mailbox-attachment "   @click="OpenEmail(mail)"></td>
                                    <td
                                        class="mailbox-date "   @click="OpenEmail(mail)">
                                        <div v-if="mail.header.date">
                                            @{{moment(mail.header.date).format('YYYY-MM-DD H:m:s')}}
                                        </div>
                                    </td>
                                    <td style="width: 150px;">
                                        <div class="btn-group pull-right">
                                            <a href="#" class="btn btn-sm btn-default" @click="openModal(mail)"><i
                                                        class="fa fa-folder-open" ></i></a>
                                                {{--<a class="btn  btn-default btn-sm reply"--}}
                                                   {{--:data-reply-message="mail.message_id"--}}
                                                   {{--:data-to="mail.from"--}}
                                                   {{--:data-reply-folder="currentFolder"--}}
                                                   {{--:data-link="mail.compose_form"><i--}}
                                                            {{--class="fa fa-reply" aria-hidden="true"></i></a>--}}
                                            <a class="btn btn-danger btn-sm" @click="deleteMail(mail)"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </div>
                                    </td>

                                </tr>

                                </tbody>
                            </table>
                            <!-- /.table -->

                            @include('email.modals.message')

                            @include('email.modals.changeFolder')

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="box-header">
            <h4>{{ trans('main.Inbox') }}</h4>
        </div>
        <div class="box-body">
            {{ trans('main.Youdonthavepermissions') }}
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
</style>
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
                                loader: true,        // Change it to false to disable loader
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
                    // console.log(this.newEmail.content);
                    // return false
                    this.newEmail.content = CKEDITOR.instances['editor1'].getData();
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
                        // this.fetchData(true,'Sent');
                        this.loading = false;
                        localStorage.setItem(`emails[Sent]`,JSON.stringify(result.data));

                        $.toast({
                            heading: 'Success',
                            text: "Send message",
                            icon: 'success',
                            loader: true,        // Change it to false to disable loader
                            hideAfter : 1500,
                            position: 'top-right',
                        });

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
                                loader: true,        // Change it to false to disable loader
                                hideAfter : 1500,
                                position: 'top-right',
                            });

                            this.fetchData(true);
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
                            loader: true,        // Change it to false to disable loader
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
                            loader: true,        // Change it to false to disable loader
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