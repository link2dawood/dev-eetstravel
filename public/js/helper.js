

function handleImportEvent(clients) {
	$('#import-form').one('submit',function(e){
			e.preventDefault();
			let service = clients ? 'clients' : $('#service-name').data('service-name');
			$('#service_name').val(service);
			$(this).submit();
		})
}
function getModalForImport(clients = false) {
	$.ajax({
		url: '/import/modal',
	}).done((res) => {
		$('body').append(res);
		$('#import-modal').modal('show');
		handleImportEvent(clients);
	});
}

function getModalForImportSeasons() {
    $.ajax({
        url: '/import/modal',
    }).done((res) => {
        $('body').append(res);
        $('#import-modal-seasons').modal('show');
    });
}
function checkSearchInput(){
	let data = {};
	$('.column_search').each(function(index){
		if ($(this).val()) {
			let what = $(this).data('what-search');
			data[what] = $(this).val();
		}
	});
	return data;
}
function exportAll(exportType = null){
	let serviceName = $('#service-name').data('service-name');
	let search = $('#table-search-input').val() || '';
	let data = checkSearchInput();
	data = JSON.stringify(data);
	window.location.href = '/export?service_name=' + serviceName + '&search=' + search + '&column=' + data + '&type=' + exportType;
}
function historyServices(){
	let check = document.getElementById('history-container');
	if(check){
        $.ajax({
            url: $('#services_name').data('history-route'),
            data: {
                service_name: $('#services_name').data('service-name'),
            }
        }).done((res) => {
            $('#history-container').html(res);
        })
    }
}

function exportWithSeasonsPrices(exportType = null){
    window.location.href = '/export_seasons?&type=' + exportType;
}

function addExportButtons() {
    // Add simple export buttons for tables
    $('.table').each(function() {
        const tableId = $(this).attr('id');
        if (tableId && !$(this).siblings('.export-buttons').length) {
            const exportButtons = $(`
                <div class="export-buttons mb-3">
                    <button class="btn btn-sm btn-success" onclick="exportTableToCSV('${tableId}', '${tableId}_export.csv')">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                </div>
            `);
            $(this).closest('.table-responsive').before(exportButtons);
        }
    });
}

function logoutForm(){
    localStorage.clear();
    document.getElementById('logout-form').submit();
}
$(document).ready(function(){


	// Initialize Bootstrap tables and export functionality
	$('.clone-tour-button').show();
    $('.clone-tour-button').on('click', function(){
        let id = $(this).data('id');
        $('.block-error').text('');
        $('.block-error').css({'display': 'none'});
        $('#tour-clone-modal-form').attr('action', '/tour/' + id + '/clone');
    });

    $('#clone_tour_send').on('click', function (e) {
    	e.preventDefault();
        $('.block-error').text('');
        $('.block-error').css({'display': 'none'});

    	if($('#departure_date').val() === ''){
            $('.block-error').text('Enter Date');
            $('.block-error').css({'display': 'block'});

            setTimeout(function () {
                $('#overlay_delete').remove();
            }, 300);
		}else{
            $('#tour-clone-modal-form').submit();
		}
    });

    // Add export buttons for Bootstrap tables
    addExportButtons();
	historyServices();
	$('select.task-status').select2('destroy').css({	
		'border':'none', 
		'outline':'0px', 
		'background-color':'inherit',
		'-moz-appearance': 'none',
		'-webkit-appearance': 'none'
	});
	$('select.task-status').css({	
		'border':'none', 
		'outline':'0px', 
		'background-color':'inherit',
		'-moz-appearance': 'none',
		'-webkit-appearance': 'none'
	});
})