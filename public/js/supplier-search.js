let globalSearch = {
	run: (actionColumn = 'action') => {
		globalSearch.prepareOptions(actionColumn);
		//globalSearch.generateTable();
		globalSearch.bindEvents();
	},
	prepareOptions: (actionColumn) => {

	    var val = $('#service-select').val();

        if( $('#service-select').val() === 'Bus Company')  val = 'Transfer';

		globalSearch.service = val;
		globalSearch.actionColumn = actionColumn;
		globalSearch.criterias = [];
		globalSearch.rate = '';
        globalSearch.city_code = '';
        globalSearch.countryAlias = '';
        globalSearch.searchName = $('#searchTextField').val();
	},
	bindEvents: () => {
		$('.option-radio').on('click', function(){
            $(this).attr('disabled', true);
            var service_select = $(this);
            var tmp = this.value;
            if(tmp === 'Bus Company') { tmp = 'Transfer';}
			globalSearch.service = tmp;
			globalSearch.rate = '';
			globalSearch.criterias = [];
            globalSearch.countryAlias = $('#country').val();
            globalSearch.searchName = $('#searchTextField').val();
            globalSearch.city_code = $('#city_code').val();
			$('#search-table').DataTable().destroy();
            globalSearch.generateTable(service_select);
            $('.filters').html('');
            if(globalSearch.service !== 'Service'){
                globalSearch.getCriterias();
            }          
		});
        
        $('#country').on('change', function(){
			globalSearch.countryAlias = this.value;
			globalSearch.rate = '';
			globalSearch.criterias = [];
            globalSearch.searchName = $('#searchTextField').val();
            globalSearch.city_code = $('#city_code').val();
			$('#search-table').DataTable().destroy();
            globalSearch.generateTable();            
            
            $('.filters').html('');
            if(globalSearch.service !== 'Service'){
                globalSearch.getCriterias();
            }          
		});        
        
        $('#supplierSearchButton').on('click', function(){
            $('#search-table').DataTable().destroy();
            globalSearch.city_code = $('#city_code').val();

            globalSearch.searchName = $('#searchTextField').val();
            globalSearch.generateTable();
        });
	},
	getCriterias: () => {
		$.ajax({
			method: 'get',
			url: '/supplier_criteria',
			data: {
				serviceType: globalSearch.service
			}
		}).done((res) => {
			$('.filters').html(res);
			$(':checkbox').change(function(){
				let index = globalSearch.criterias.indexOf(this.name);
				if (index < 0){
					globalSearch.criterias.push(this.name);
				}
				else {
                    globalSearch.criterias.splice(index, 1);
                }

				$('#search-table').DataTable().destroy();
				globalSearch.generateTable();
			});
			$(':radio').click(function(){
				if ($(this).hasClass('option-radio')) {
                    // This is a radio button with the 'option-radio' class,
                    // so we don't want to execute the existing functionality.
                    return;
                }
				globalSearch.rate = this.value;
				$('#search-table').DataTable().destroy();
				globalSearch.generateTable();
			});
			$('#clear-filter').click(function(e){
				e.preventDefault();
				globalSearch.criterias = [];
				globalSearch.rate = '';
				$(':checkbox').attr('checked', false);
				$(':radio').attr('checked', false);
				$('#search-table').DataTable().destroy();
				globalSearch.generateTable();
			})
		})
	},
	setOnlyTransferServiceEnabled : () => {
        //$('#service-select').find('option:not(:contains("Bus"))').prop('disabled', true);
        //$('#service-select').select2();
	},
	setTransferDisable : () => {
        //$('#service-select').find('option:contains("Bus")').prop('disabled', true);
        //$('#service-select').select2();
	},
    setAllServicesEnabled : () => {
        //$('#service-select').find('option:not(:contains("Bus"))').prop('disabled', false);
        //$('#service-select').select2();
    },
	generateTable: (service_select = null) => {
		
		// function focusSearch (){
		// }
		// if ($.fn.DataTable.isDataTable('#search-table')) return;
		if(globalSearch.service == "Hotel"){
			$("#hotel_service_create").css("display","block");
			$("#guide_service_create").css("display","none");
			$("#event_service_create").css("display","none");
			$("#res_service_create").css("display","none");
			$("#bus_service_create").css("display","none");
		}
		else if(globalSearch.service == "Guide"){
			$("#guide_service_create").css("display","block");
			$("#hotel_service_create").css("display","none");
			$("#event_service_create").css("display","none");
			$("#res_service_create").css("display","none");
			$("#bus_service_create").css("display","none");
		}
		else if(globalSearch.service == "Event"){
			$("#event_service_create").css("display","block");
			$("#guide_service_create").css("display","none");
			$("#hotel_service_create").css("display","none");
			$("#res_service_create").css("display","none");
			$("#bus_service_create").css("display","none");
		}
		else if(globalSearch.service == "Transfer"){
			$("#bus_service_create").css("display","block");
			$("#guide_service_create").css("display","none");
			$("#hotel_service_create").css("display","none");
			$("#res_service_create").css("display","none");
			$("#event_service_create").css("display","none");
		}
		else{
			$("#res_service_create").css("display","block");
			$("#guide_service_create").css("display","none");
			$("#hotel_service_create").css("display","none");
			$("#event_service_create").css("display","none");
			$("#bus_service_create").css("display","none");
		}
		
		let table = $('#search-table').DataTable({
			dom: 	"<'row'<'col-sm-7'f><'col-sm-5 toRight'l>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
			processing: true,
            serverSide: true,
            pageLength: 50,
            sort: false,
            ajax: {
                url: "/supplier_show",
                data: {
                	service: globalSearch.service,
                	actionColumn: globalSearch.actionColumn,
                	criterias: globalSearch.criterias,
                	rates: globalSearch.rate,
                    city_code: globalSearch.city_code,
                    countryalias: globalSearch.countryAlias,
                    searchname: globalSearch.searchName
                }
            },
            columns: [
                {data: 'nameService', name: 'nameService'},
                {data: 'address_first', name: 'address_first'},
                {data: 'country', name: 'country'},
                {data: 'city', name: 'city'},
                {data: 'work_phone', name: 'work_phone'},
                {data: 'contact_name', name: 'contact_name'},
                {data: globalSearch.actionColumn, sortable: false}
            ],
			
            "initComplete": function(settings, json) {
				/*this.api().columns().every(function () {
                let column = this;
                let header = $(column.header());
                let input = $('<br><input type="text" placeholder="Search..." style= "color:black; width:15rem">')
                    .appendTo(header)
                    .on('input', function () {
                        column.search($(this).val()).draw();
                    });
            });*/
                if(service_select){
                    $(service_select).attr('disabled', false);
                }
            },
			
			rowCallback: function(row, data) {
             // Find the action column cell
            var actionCell = $(row).find('td:last');

            // Find the anchor element within the action cell
            var anchorElement = actionCell.find('a.show-button');

            // Get the data-link attribute value
            var dataLink = anchorElement.attr('data-link');


           if (dataLink !== undefined) {
				// Add a click event listener to the row
				$(row).on('click', function() {
					// Navigate to the supplier's page
					window.location.href = dataLink;
				});
			}
        }
		});
		$('#search-table_filter').css('display', 'none');

		// Add search by city and hotel name
		$('#search-table_filter').after('<label>City:<input type="text" id="city-search" style="margin-right: 10px;"></label>');
		$('#search-table_filter').before('<label>Name:<input type="text" id="hotel-name-search" style="margin-left: 10px;"></label>');

		$('#city-search').on('keyup', function () {
			table.column(3).search(this.value).draw();
		});

		$('#hotel-name-search').on('keyup', function () {
			table.column(0).search(this.value).draw();
		});
	}
};

let addTour = {
	run: () => {
		addTour.prepareOptions();
		addTour.bindEvents();
	},
	prepareOptions: () => {
		addTour.service = {
			id: 0,
			type: '',
			name: '',
			bus_id : null,
			drivers_id : []

		},
		addTour.tour = {
			tourDayId: 0,
            tourDayIdRetirement: 0,
            departure_date_transfer: 0,
            retirement_date_transfer: 0,
			tour_id: 0,
		},
		addTour.route = ''
	},
    getDriverAndBusTransfer: () => {
        $.ajax({
            method: 'GET',
            url: `/driver_bus_transfer/api/${addTour.service.id}`,
            data: {}
        }).done((res) => {
            $('.list-driver-and-buses').html(res);
        });
    },
	bindEvents: () => {
		$('#search-table tbody').on('click', '.tourAdd', function(){
			addTour.service.id = $(this).data('id');
			addTour.service.type = $(this).data('type');
			addTour.service.name = $(this).data('service_name');
			addTour.tourAddModal();
		});
		$('#tour-table tbody').on('click', '.tour_package_add', function(e){
			if (addTour.service.type != 'Transfer') {
                $('#selectDateForTour').modal();
                const tour_id = $(this).data('tour_id');

                $.ajax({
                    method: "POST",
                    url: "/get_tour_days",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        tour_id: tour_id,
                    }
                }).done((res) => {
                    $('.datepickerDisabled').val('').datepicker("remove");
                    $('.datepickerDisabled').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose : true,
                        startDate: res.startDate,
                        endDate: res.endDate
                    });

                    $('#date_service_retirement').attr('disabled', true);
                    if(addTour.service.type === 'Hotel'){
                        $('#date_service_retirement').attr('disabled', false);
                    }

                    $('.addTourWithDate').attr('data-link', '');
                    $('.addTourWithDate').attr('data-tour_id', '');
                    $('.addTourWithDate').attr('data-link', $(this).data('link'));
                    $('.addTourWithDate').attr('data-tour_id', tour_id);


                    $('#date_service').datepicker().on('changeDate', function(e) {

                    		if( $('#date_service_retirement').is(':disabled') ) return ;

                            var date2 = $('#date_service').datepicker('getDate');
                            var tempStartDate = new Date(date2);
                            var default_end = new Date(tempStartDate.getFullYear(), tempStartDate.getMonth(), tempStartDate.getDate()+1);
                            var nextDayDateformated = moment(default_end).format('YYYY-MM-DD');
                        	$('#date_service_retirement').val('').datepicker("remove");
                        	$('#date_service_retirement').datepicker({
                            format: 'yyyy-mm-dd'});
                            $('#date_service_retirement').datepicker('update', nextDayDateformated);
                    });


                })
			} else {

                addTour.route = $(this).data('link');
                addTour.tour.id = $(this).data('tour_id');

                $('#selectDateForTransferPackage').modal();

                setTimeout(function (e) {
                    $.ajax({
                        method: "POST",
                        url: "/get_tour_days",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            tour_id: addTour.tour.id,
                        }
                    }).done((res) => {
                        $(document).find('.datepickerDisabledTransferPackage').val('').datepicker("remove");
                        $(document).find('#date_service_transfer_package').val(res.startDate);
                        $(document).find('#date_service_transfer_retirement_package').val(res.endDate);
                        $(document).find('.datepickerDisabledTransferPackage').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose : true,
                            startDate: res.startDate,
                            endDate: res.endDate
                        });
                        $('.addTransferPackageWithDate').attr('data-link', '');
                        $('.addTransferPackageWithDate').attr('data-tour_id', '');
                        $('.addTransferPackageWithDate').attr('data-link', $(this).attr('data-link'));
                        $('.addTransferPackageWithDate').attr('data-tour_id', addTour.tour.id);

                    })
                }, 200);

			}
		});

        $(document).on('click', '.addTransferPackageWithDate', function () {

            $('#selectDateForTransferPackage').find('.error_date').html(' ');
            $('#selectDateForTransferPackage').find('.error_date').css({'display':'none'});

            if($('#date_service_transfer_package').val() === '' || $('#date_service_transfer_retirement_package').val() === ''){
                $('#selectDateForTransferPackage').find('.error_date').css({'display':'block'});
                $('#selectDateForTransferPackage').find('.error_date').append('Enter the date');
                return false;
            }

            if($('#date_service_transfer_package').val() > $('#date_service_transfer_retirement_package').val()){
                $('#selectDateForTransferPackage').find('.error_date').css({'display':'block'});
                $('#selectDateForTransferPackage').find('.error_date').append('Date from can not be less than the date to');
                return false;
            }

            addTour.tour.departure_date_transfer = $('#date_service_transfer_package').val();
            addTour.tour.retirement_date_transfer = $('#date_service_transfer_retirement_package').val();

            $('#selectDateForTransferPackage').modal('hide');

            setTimeout(function (e) {
                addTour.getDriverAndBusTransfer();
                $('#select-driver-and-bus').modal();
            }, 200)

        });


        $(document).on('click', '.btn-send-transfer_add', function () {
            let oldForm = document.forms.form_transfer_buses_drivers;
            let form = new FormData(oldForm);
            addTour.service.bus_id = form.get('bus_transfer');
            addTour.service.drivers_id = form.getAll('driver_transfer');

            addTour.addServiceToTour();

        });





		$('#selectDateForTour').on('click', '.addTourWithDate', function (e) {
            // e.preventDefault();
            // let link = $(this).attr('href') + '?service_type=' + addTour.service.type + '&service_id=' + addTour.service.id;
            // console.log(link);
            // $(this).attr('href', link);
            // return true;
            $('#selectDateForTour').find('.error_date').html(' ');
            $('#selectDateForTour').find('.error_date').css({'display':'none'});


			if($('#date_service').val() === ''){
                $('#selectDateForTour').find('.error_date').css({'display':'block'});
                $('#selectDateForTour').find('.error_date').append('Enter the date');
				return false;
			}


			if($('#date_service_retirement').val() !== ''){
                if($('#date_service').val() > $('#date_service_retirement').val()){
                    $('#selectDateForTour').find('.error_date').css({'display':'block'});
                    $('#selectDateForTour').find('.error_date').append('Date from can not be less than the date to');
                	return false;
                }
			}

			$.ajax({
				method: "POST",
				url: "/get_tour_days_id",
				data: {
					_token: $('meta[name="csrf-token"]').attr('content'),
					tour_id: $(this).attr('data-tour_id'),
					tour_day_date: $('#date_service').val(),
				}
			}).done((tour_day_id) => {
				addTour.route = $(this).data('link');
				addTour.tour.tourDayId = tour_day_id;
				addTour.tour.tourDayIdRetirement = $('#date_service_retirement').val() === '' ? null : $('#date_service_retirement').val();
				addTour.addServiceToTour();
			});
        });
	},
	tourAddModal: () => {
		console.log('tourAddModal click');
		$('#addTourModal').modal();
		addTour.generateTourTable();
	},

	generateTourTable: () => {
		if ($.fn.DataTable.isDataTable('#tour-table')) return;
		let table = $('#tour-table').DataTable({
			processing: true,
            serverSide: true,
            ajax: {
	        	url: "/tour/api/data",
	        },
	        columns: [
		        {data: 'id', name: 'id'},
		        {data: 'name', name: 'name'},
		        {data: 'departure_date', name: 'departure_date'},
		        {data: 'retirement_date', name: 'retirement_date'},
		        {data: 'pax', name: 'pax'},
		        {data: 'link'}
	      	],
		});
	},

	addServiceToTour: () => {
		$.ajax({
            method: 'POST',
            url: addTour.route,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                tourDayId: addTour.tour.tourDayId,
                tourDayIdRetirement: addTour.tour.tourDayIdRetirement,
                tourId: addTour.tour.id,
                serviceType: addTour.service.type,
                serviceId: addTour.service.id,
                serviceName: addTour.service.name,
				bus_id : addTour.service.bus_id,
				drivers_id: addTour.service.drivers_id,
                dep_date_transfer: addTour.tour.departure_date_transfer,
                ret_date_transfer: addTour.tour.retirement_date_transfer
            }
        }).done((res) => {
			if(res.error_driver){
                $('.block-error-driver').text('');
                $('.block-error-driver').append('<span>'+res.message+'</span>');
                $('.block-error-driver').css({'display': 'block'});
			}
            else if(res.transfer_add_date){
                $('.block-error-driver-transfer').text('');
                $('.block-error-driver-transfer').append('<span>'+res.transfer_message+'</span>');
                $('.block-error-driver-transfer').css({'display': 'block'});
                $('#selectDateForTransferPackage').modal('hide');
                $('#select-driver-and-bus').modal('hide');
                $('#addTourModal').modal('hide');
            }
            else if(res.bus_busy){
                $('.block-error-driver-transfer').text('');
                $('.block-error-driver-transfer').append('<span>'+res.bus_busy_message+'</span>');
                $('.block-error-driver-transfer').css({'display': 'block'});
                $('#selectDateForTransferPackage').modal('hide');
                $('#select-driver-and-bus').modal('hide');
                $('#addTourModal').modal('hide');
            }
			else{
                window.location.href = res;
                $('#service-modal').modal('hide');
                $('#select-driver-and-bus').modal('hide');
                $('#overlay_delete').remove();
            }
        })
	}
};

let globalSearchApp = {
	run: () => {
		globalSearch.run();
		addTour.run();
	}
};

var xtime;
    $('#searchTextField').keyup(function(e){
        e.preventDefault();
        var keycode = (e.keyCode ? e.keyCode : e.which);
        clearTimeout(xtime);
        if (keycode != '13') {
            var inp = $(this).val();
            if(inp == '' || keycode == '8'){
                $('#supplierSearchButton').click();
            }
           xtime = setTimeout(function(){
               if (inp == $('#searchTextField').val() ){
                    $('#supplierSearchButton').click();
               }
           }, 2000);
        }
    });
                
    $(document).keypress(function(e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == '13') {
            e.preventDefault();
            $('#supplierSearchButton').click();
        }
    });