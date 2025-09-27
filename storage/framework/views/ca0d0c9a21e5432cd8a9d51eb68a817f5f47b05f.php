<?php $__env->startSection('title','Index'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.title',
        ['title' => 'Activities', 'sub_title' => 'Activities List',
        'breadcrumbs' => [
        ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
        ['title' => 'Activities', 'icon' => null, 'route' => null]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<section class="content">
  <div class="box box-primary">
    <div class="box-body">
      <table id="activity-table" class="table table-striped table-bordered table-hover" style='background:#fff'>
        <thead>
        <tr>
          <th><?php echo trans('main.CreatedTime'); ?></th>
          <th><?php echo trans('main.UserCreated'); ?></th>
          
          <th><?php echo trans('main.Description'); ?></th>
          <th class="actions-button"></th>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $activitiesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($activity->created_at); ?></td>
            <td><?php echo e($activity->causer); ?></td>
            <td><?php echo e($activity->formatted_description); ?></td>
            <td><?php echo $activity->link_button; ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
        <tr>
          <th><?php echo trans('main.CreatedTime'); ?></th>
          <th><?php echo trans('main.UserCreated'); ?></th>
          
          <th><?php echo trans('main.Description'); ?></th>
          <th class="not"></th>
        </tr>
        </tfoot>
      </table>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
  <script>
      $(document).ready(function() {
          let table = $('#activity-table').DataTable({
              dom: "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
              "<'row'<'col-sm-12'tr>>" +
              "<'row'<'col-sm-5'i><'col-sm-7'p>>",
              buttons: [
                  {
                      extend: 'csv',
                      title: 'Activities List',
                      exportOptions: {
                          columns: ':not(.actions-button)'
                      }
                  },
                  {
                      extend: 'excel',
                      title: 'Activities List',
                      exportOptions: {
                          columns: ':not(.actions-button)'
                      }
                  },
                  {
                      extend: 'pdfHtml5',
                      title: 'Activities List',
                      exportOptions: {
                          columns: ':not(.actions-button)'
                      }
                  }
              ],
              language : {
                  search: "Global Search :"
              },
              pageLength: 50,
              columnDefs: [
                  { targets: [3], orderable: false } // Actions/Link column not sortable
              ]
          });
          $('#activity-table tfoot th').each(function() {
              let column = this;
              if(column.className !== 'not') {
                  let title = $(this).text();
                  $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
              }
          });
          table.columns().every(function() {
              let that = this;

              $('input', this.footer()).on('keyup change', function() {
                  if(that.search() !== this.value) {
                      that.search(this.value).draw();
                  }
              });
          });
          $('#activity-table tfoot th').appendTo('#activity-table thead');
      });
  </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('scaffold-interface.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/activities/index.blade.php ENDPATH**/ ?>