<template>
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Folders</h3>

            <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body no-padding">
            <ul class="nav nav-pills nav-stacked">
                <li v-for="folder in folders">
                    <a>
                        <i class="fa fa-inbox" ></i> {{folder.name}}
                    </a>
                </li>
                <li>
                    <a href="#" class="email-add-folder" >
                        Add Folder
                    </a>

                </li>
            </ul>
        </div>
    </div>
</template>

<script type="text/javascript">
    export default {
        name: "folder-list",
        data() {
            return {
                folders:[],
            }
        },
    }
</script>

<style scoped>

</style>