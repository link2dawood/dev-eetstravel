		

    <div class="box-body no-padding" id="emailList">

        <div class="mailbox-controls">
            <div class="pull-right">
            </div>
        </div>
		
        <div class="table-responsive mailbox-messages"   id="emaillists">
            <div  v-if="view">
                <?php echo $__env->make('email.parts.viewEmail', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <table class="table table-hover table-striped finder-disable" v-if="toursArray">
                <tbody>
                <tr v-if="toursArray" style="cursor: pointer" v-for="(tour) in toursArray" >
                    <td class="mailbox-star onclick_redirect">
                    </td>

                    <td class="mailbox-name" @click="viewTourEmails('',tour.name)">
                        <a >
                            <div>{{tour.id}}</div>
                        </a></td>
                    <td class="mailbox-subject onclick_redirect" @click="viewTourEmails('',tour.name)">
                        <b >{{tour.name}}</b>
                        <span v-else>{{tour.name}}</span>
                    </td>
                    <td class="mailbox-date onclick_redirect">
                        {{tour.departure_date}}
                    </td>
                  

                </tr>
                </tbody>
            </table>
        </div>
        <div class="box-footer no-padding">
            <div class="mailbox-controls">
                <div class="pull-right">
                    <ul class="pagination" v-if="!loading">

                        <li :class="{active: (page+1) === pageNumber, last: (pageNumber == totalPages && Math.abs(pageNumber - page) > 3), first:(pageNumber == 1 && Math.abs(pageNumber - page) > 3)}" v-for="pageNumber in totalPages" v-if="Math.abs(pageNumber - page) < 5 || pageNumber == totalPages || pageNumber == 1">
                            <a :key="pageNumber" href="#" @click="changePage(pageNumber)" >{{ pageNumber }}</a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php /**PATH /var/www/html/resources/views/email/tour_index.blade.php ENDPATH**/ ?>