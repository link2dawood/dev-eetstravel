let tablesFinder = {
    init: () => {
        if (!$('#settings-table')) return;
        tablesFinder.render();
    },
    render: () => {
        let table = $('#settings-table').DataTable({
            dom:    "<'row'<'col-sm-5'l><'col-sm-2'B><'col-sm-5'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csv',
                    title: 'Settings List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'excel',
                    title: 'Settings List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Settings List',
                    exportOptions: {
                        columns: ':not(.actions-button)'
                   }
                }
            ],
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: '/settings/api/data',
            },
            columns: [
                {data: 'description'},
                {data: 'value'},
                {data: 'action', name: 'action', searchable: false, sorting: false, orderable: false}
            ],
        });
    // $('#settings-table tfoot th').each( function () {
    //     let column = this;
    //     if (column.className !== 'not') {
    //         let title = $(this).text();
    //         $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
    //     }
    // });
    // table.columns().every( function () {
    //     let that = this;
    //     $('input', this.footer()).on('keyup change', function() {
    //         if(that.search() !== this.value) {
    //             that.search(this.value).draw();
    //         }
    //     });
    // });
    // $('#settings-table tfoot th').appendTo('#settings-table thead');
    }
}

$(document).ready(tablesFinder.init())