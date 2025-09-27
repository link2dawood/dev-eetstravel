
let addService = {
    run: () => {
        addService.config();
        addService.bindEvents();
        addService.clickMainHotel();

        if($('#change_service_with_edit').attr('data-info') !== 'change_edit_service'){
            addService.addPackages();
        }
    },
    addOrder: () => {
        $('.package-service-table').each(function(index){
            let tb = $(this).find('tbody').children();
            $(tb).each(function(i){
                if (this.className == 'unused') return;
                $(this).attr('data-order', i);
            })
            // console.log(tb);
        })
    },
    config: () => {
        addService.tourDayId = '';
        addService.route = '';
        addService.tour_id = $('#default_reference_id').val();
        addService.service_id = '';
        addService.service_type = '';
        addService.tourTransfer = '';
        addService.service_name = '';
        addService.service_old_time = null;
        addService.departure_date = $('#tour_dates').data('departure_date');
        addService.retirement_date = $('#tour_dates').data('retirement_date');
        addService.departure_date_transfer = null;
        addService.retirement_date_transfer = null;
        addService.package_id = '';
        addService.page_service = null;
        addService.link_redirect_edit = null;
        addService.drivers_id = [];
        addService.bus_id = null;
        addService.tourDayIdRetirement = null;
        addService.click_day = null; 
		 addService.click_day = null; 
		 addService.click_day = null; 
		 addService.pickup = null; 
		addService.drop = null; 
    },
    dragNDrop: () => {
        if($('.package-service-table tbody').length >0) {
            $('.package-service-table tbody').sortable({
                connectWith: '.package-service-table tbody',
                accept: '.package-service-table tbody tr',
                helper: 'clone',
                sort: function (event, ui) {
                    var $target = $(event.target);
                    if (!/html|body/i.test($target.offsetParent()[0].tagName)) {
                        var top = event.pageY - $target.offsetParent().offset().top - (ui.helper.outerHeight(true) / 2);
                        ui.helper.css({'top': top + 'px'});
                    }
                },
                stop: (event, ui) => {
                    addService.checkTables();
                    let target = ui.item;
                    let oldTourDayId = $(target).find('.package-data').data('tour_day_id');
                    let newTourDayId = $(target).closest('tbody').data('default_tour_day_id');
                    let packageId = $(target).find('.package-data').data('package_id');
                    let type = $(target).find('.package-data').data('package-type');
                    let is_main = $(target).find('.package-data').data('is_main');

                    let parent_time_from = $(target).find('.package-data').parents('tr').prev().find("input[name='time_from']").val();
                    let next_time_from = $(target).find('.package-data').parents('tr').next().find("input[name='time_from']").val();
                    let idprev = $(target).find('.package-data').parents('tr').prev().data('package_id');
                    let idnext = $(target).find('.package-data').parents('tr').next().data('package_id');
                    console.log(parent_time_from + "/" + idprev);
                    console.log(next_time_from + "/" + idnext);

                    if (oldTourDayId !== newTourDayId) addService.changeTourDay(packageId, oldTourDayId, newTourDayId, type, idprev, idnext, is_main);
                    if (oldTourDayId == newTourDayId) addService.reorderService(target, oldTourDayId);
                }
            });
        }
    },
    reorderService: (target, tourDay) => {
        let targetChild = $(target).closest('tbody').children();
        let targetServiceId = $(target).data('package_id');
        let targetChangeOrder = null;
        let getTimeFromPackage = null;
        let type = $(target).data('type');
        let is_main = $(target).data('is_main');
        if ($(targetChild).length <= 1) return;
        $(targetChild).each(function(index){
            if ( $(this).data('package_id') == targetServiceId && $(this).data('order') !== index) {
                console.log('id:' + $(this).data('package_id') + ',oldOrder:' + $(this).data('order') + ',newOrder:' + index);
                targetChangeOrder = index;
            }
        })
        if (targetChangeOrder !== null){
            $(targetChild).each(function(index){
                if ($(this).data('order') == targetChangeOrder){
                    console.log('target time get:' + $(this).data('package_id'));
                    getTimeFromPackage = $(this).data('package_id');
                }
            })
        }

        if (getTimeFromPackage == null) return;
        $.ajax({
            url: '/reorder_package',
            method: 'GET',
            data: {
                tourDayId: tourDay,
                targetId: targetServiceId,
                getTime: getTimeFromPackage,
                type : type,
                is_main : is_main
            }
        }).done((res) => {
            addService.addPackages();
        })
    },
    changeServiceTime: () => {
        $('.service-time').datetimepicker({
                format: 'HH:mm', 'sideBySide' : true,
                tooltips: {
                    incrementHour: '',
                    pickHour: '',
                    decrementHour:'',
                    incrementMinute: '',
                    pickMinute: '',
                    decrementMinute:'',
                    incrementSecond: '',
                    pickSecond: '',
                    decrementSecond:'',
                }
            }).on("dp.hide", function (e) {

                 let timeKey = $(this).attr('name');
                 let type = $(this).data('type');
                 let is_main =  $(this).data('is_main');

            // $(this).datetimepicker('hide');
                 $.ajax({
                    url: '/tour_package/' + $(this).data('package_id') + '/change_time',
                    method: 'GET',
                    data: {
                        timeKey: timeKey,
                        timeValue: $(this).val(),
                        type : type,
                        is_main : is_main
                    }
                 }).done( (res) => {
                    addService.addPackages();
                 })
                 // return false;
            });
    },
    changeTourDay: (packageId, oldTourDayId, newTourDayId , type , idprev , idnext , is_main) => {
        let url = '/tour_package/' + packageId + '/change_tour_day';
        $.ajax({
            url: url,
            data: {
                remove_tour_day: oldTourDayId,
                add_tour_day: newTourDayId,
                id: packageId,
                type: type,
                idprev : idprev,
                idnext : idnext,
                is_main: is_main
            }
        }).done((res) => {
            if(res.message) {

                    $('#error_hotel').find('#message').html(res.message);
                    $('#error_hotel').modal();
                    $('#error_hotel').find('#ok').on('click', function (e) {
                        $('#error_hotel').modal('hide');
                        addService.addPackages();
                    });
                    $('#error_hotel').on('hidden.bs.modal', function () {
                        addService.addPackages();
                    })

            }else {
                addService.addPackages();
            }
        })
    },
    checkTables: () => {
        $('.package-service-table tbody').each(function(index){
            if($(this).children().length > 1){
                $(this).find('.unused').remove();
            }
            if($(this).children().length < 1){
                $(this).append('<tr class="unused" style="height: 20px"><td colspan="12"></td></tr>');
            };
        })
    },
    setGetDate : (res) => {
        let date2 = $('#date_service_package').datepicker('getDate');
        let tempStartDate = new Date(date2);
        let default_end = new Date(tempStartDate.getFullYear(), tempStartDate.getMonth(), tempStartDate.getDate()+1);
        let nextDayDateformated = moment(default_end).format('YYYY-MM-DD');

        $('#date_service_retirement_package').val('').datepicker("remove");
        $('#date_service_retirement_package').datepicker({
            format: 'yyyy-mm-dd',
            autoclose : true,
            startDate: res.startDate,
            endDate: res.endDate
        });
        $('#date_service_retirement_package').datepicker('update', nextDayDateformated);

        console.log(res);



    },
    bindEvents: () => {
        $('body').on('click', '.add-service-quick', function(){
            $('#service-modal').modal();
            $('#service-modal').on('shown.bs.modal', function(){
                let target = $(this).find('div.dataTables_filter input');
                $(target).focus();
            });

            if ($(this).data('tour_transfer')) {
                addService.tourTransfer = true;
                $('#service-select').val('Bus Company').trigger('change').change();
                $('.modal-title').text('Add Bus Company');
              //  globalSearch.setOnlyTransferServiceEnabled();
            } else {
                $('#service-select').val('All').trigger('change').change();
                $('.modal-title').text('Add service');
               // globalSearch.setAllServicesEnabled();
               // globalSearch.setTransferDisable();
                addService.tourTransfer = '';
            }

            addService.tourDayId = $(this).data('tourdayid');
            addService.route = $(this).data('link');
        });
        $('body').one('click', '.add-service-quick', function(e){
            globalSearch.run('add-service-column');

        });
        $('body').on('click', '.add-service-button', function(e){
            e.preventDefault();
            addService.service_type = $(this).data('service_type');
            addService.service_id = $(this).data('service_id');
            addService.service_name = $(this).data('service_name');
            var _this = $(this);
            var tour_id = addService.tour_id;
            if(addService.service_type === 'transfer' || addService.tourTransfer === "true"){
                $('#selectDateForTransferPackage').modal();

                setTimeout(function (e) {
                    $.ajax({
                        method: "POST",
                        url: "/get_tour_days",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            tour_id: tour_id,
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
                        $('.addTransferPackageWithDate').attr('data-link', $(_this).attr('data-link'));
                        $('.addTransferPackageWithDate').attr('data-tour_id', tour_id);

                    })
                }, 200);

            }
            else if(addService.service_type === 'hotel'){


                $('#selectDateForHotelPackage').modal();


                $.ajax({
                    method: "GET",
                    url: `/get_date_day/${addService.tourDayId}`,
                    data: {}
                }).done((res) => {
                    addService.click_day = res.date;
                });

                setTimeout(function (e) {
                    $.ajax({
                        method: "POST",
                        url: "/get_tour_days",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            tour_id: tour_id,
                        }
                    }).done((res) => {
                        $(document).find('.datepickerDisabledHotelPackage').val('').datepicker("remove");
                        $(document).find('.datepickerDisabledHotelPackage').val(addService.click_day );
                        $(document).find('.datepickerDisabledHotelPackage').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose : true,
                            startDate: res.startDate,
                            endDate: res.endDate
                        });
                        $('.addHotelPackageWithDate').attr('data-link', '');
                        $('.addHotelPackageWithDate').attr('data-tour_id', '');
                        $('.addHotelPackageWithDate').attr('data-link', $(_this).attr('data-link'));
                        $('.addHotelPackageWithDate').attr('data-tour_id', tour_id);

                        $(document).find('#date_service_package').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose : true,
                            startDate: res.startDate,
                            endDate: res.endDate
                        });


                        addService.setGetDate(res);


                        $('#date_service_package').datepicker().on('changeDate', function(e) {
                            addService.setGetDate(res);



                        });

                    })
                }, 200);
            }
            else{
                addService.storeService();
            }
        });
		 $('#description-service').submit(function (e) {
            // Prevent the default form submission
            e.preventDefault();

            // Get the CKEditor content
            var descriptionContent = CKEDITOR.instances.description.getData();

            // Append the CKEditor content to the form data
            var formData = $(this).serialize() + '&description=' + encodeURIComponent(descriptionContent);

            // Make an AJAX request
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
            }).done((res) => {
				//window.location.replace(res);
				 $('#service-description').modal('hide');
				$('#overlay_delete').remove();
				$('.modal-backdrop').hide();
                    addService.addPackages();
					CKEDITOR.instances['description'].setData("");

			});
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

            addService.departure_date_transfer = $('#date_service_transfer_package').val();
            addService.retirement_date_transfer = $('#date_service_transfer_retirement_package').val();

            $('#selectDateForTransferPackage').modal('hide');


            setTimeout(function (e) {
                addService.getDriverAndBusTransfer();
                $('#select-driver-and-bus').modal();
            }, 200)

        });


        $(document).on('click', '.addHotelPackageWithDate', function (e) {
            $('#selectDateForHotelPackage').find('.error_date').html(' ');
            $('#selectDateForHotelPackage').find('.error_date').css({'display':'none'});

            if($('#date_service_package').val() === ''){
                $('#selectDateForHotelPackage').find('.error_date').css({'display':'block'});
                $('#selectDateForHotelPackage').find('.error_date').append('Enter the date');
                return false;
            }


            if($('#date_service_retirement_package').val() !== ''){
                if($('#date_service_package').val() > $('#date_service_retirement_package').val()){
                    $('#selectDateForHotelPackage').find('.error_date').css({'display':'block'});
                    $('#selectDateForHotelPackage').find('.error_date').append('Date from can not be less than the date to');
                    return false;
                }
            }
            $.ajax({
                method: "POST",
                url: "/get_tour_days_id",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    tour_id: $(this).attr('data-tour_id'),
                    tour_day_date: $('#date_service_package').val(),
                }
            }).done((tour_day_id) => {
                $('#selectDateForHotelPackage').modal('hide');
                $('#selectDateForHotelPackage').hide();
                $('#overlay_delete').remove();
                $('.modal-backdrop').hide();
                $.ajax({
                    method: "GET",
                    url: "/checkTourDayConfirmedHotel",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        tour_day_id: tour_day_id,
                    }
                }).done((res) => {
                    if(res == ''){
                        addService.route = $(this).attr('data-link');
                        addService.tourDayId = tour_day_id;
                        addService.tourDayIdRetirement = $('#date_service_retirement_package').val() === '' ? null : $('#date_service_retirement_package').val();
						addService.service_old_time = $('#date_service_time').val();
					
                        addService.storeService();
                    } else{
                        $('#error_hotel').find('#message').html(res);
                        $('#error_hotel').modal();
                        $('#error_hotel').find('#ok').on('click', function (e) {
                            $('#error_hotel').modal('hide');
                        });
                    } 
                });
            });
        });



        $('body').on('click', '.add-description-package', function(){
            let data = $(this).closest('div').find('.add-service-quick').data();
            $('#service-description').modal().on('shown.bs.modal', function(){
                $('.description-time').datetimepicker({
                    format: 'HH:mm'
                });
                $('#tour_day_id').val(data.tourdayid);
            });
        });
        $(document).on('click', '.open-modal-change-service', function(e){
            e.preventDefault();
            addService.openModal($(this));
        });
        $(document).on('click', '.change-service-button', function (e) {
            e.preventDefault();
            addService.changeService($(this));
        });

        $(document).on('click', '.btn-send-transfer_add_transfer_package', function (e) {

            let oldForm = document.forms.form_transfer_buses_drivers_transfer_package;
            let form = new FormData(oldForm);
            addService.bus_id = form.get('bus_transfer');
            addService.drivers_id = form.getAll('driver_transfer');

            var change_service = 'true';
            addService.storeService(change_service);
        });

        $(document).on('click', '.btn-send-transfer_add', function () {
            let oldForm = document.forms.form_transfer_buses_drivers;
            let form = new FormData(oldForm);
            addService.bus_id = form.get('bus_transfer');
            addService.drivers_id = form.getAll('driver_transfer');
			addService.pickup = form.get('pickup_des');
            addService.drop = form.get('drop_des');
            addService.storeService();
        });
    },
    getDriverAndBusTransfer: () => {
        $.ajax({
            method: 'GET',
            url: `/driver_bus_transfer/api/${addService.service_id}`,
            data: {}
        }).done((res) => {
            $('.list-driver-and-buses').html(res);
        });
    },
    getDriverAndBusTransferPackage: () => {
        $.ajax({
            method: 'GET',
            url: `/driver_bus_transfer/api/${addService.service_id}`,
            data: {}
        }).done((res) => {
            $('.list-driver-and-buses_transfer_package').html(res);
        });
    },
    openModal : (_this) => {
        addService.service_id = _this.attr('data-serviceId');
        addService.service_type_id = _this.attr('data-serviceTypeId');
        addService.package_id = _this.attr('data-packageId');
        addService.tourDayId = _this.attr('data-tour-day-id');
        addService.route = _this.attr('data-link');
        addService.service_old_time = _this.attr('data-time-old-service');
        addService.page_service = _this.attr('data-info');
        addService.tour_id = _this.attr('data-tour_id');

        $.ajax({
            method: 'GET',
            url: `/api/get_tour_package_transfer_dates/${addService.package_id}`,
            data: {}
        }).done((res) => {
            if(res){
                addService.departure_date_transfer = res['dep_date'];
                addService.retirement_date_transfer = res['ret_date'];
            }
        });


        var check = 'change';
        addService.generateDataTableServicesType(check);
    },
    changeService : (_this) => {
        addService.service_type = _this.data('service_type');
        addService.service_name = _this.data('service_name');
        addService.service_id = _this.data('service_id');

        if(addService.service_type === 'hotel'){
            $.ajax({
                method: 'GET',
                url: `/get_last_date_hotel/api/${addService.package_id}`,
                data: {}
            }).done((res) => {
                addService.tourDayIdRetirement = res;
            });
        }

        if(addService.service_type === 'transfer'){
            $('#list-tour-packages').modal('hide');

            setTimeout(function (e) {
                addService.getDriverAndBusTransferPackage();
                $('#select-driver-and-bus_transfer_package').modal();
            }, 200);

        }else{
            $.ajax({
                method: 'GET',
                url: `/tour_package/${addService.package_id}/delete`,
                data: {}
            }).done((res) => {
                addService.storeService();
                $('#list-tour-packages').modal('hide');
            });
        }
    },
    storeService: (change_service = null) => {
        $.ajax({
            method: 'POST',
            url: addService.route,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                tourDayId: addService.tourDayId,
                tourId: addService.tour_id,
                serviceType: addService.service_type,
                serviceId: addService.service_id,
                serviceName: addService.service_name,
                tourTransfer: addService.tourTransfer,
                serviceOldTime: addService.service_old_time,
                pageService: addService.page_service,
                drivers_id: addService.drivers_id,
                bus_id: addService.bus_id,
                tourDayIdRetirement: addService.tourDayIdRetirement,
                dep_date_transfer: addService.departure_date_transfer,
                ret_date_transfer: addService.retirement_date_transfer,
                package_id: addService.package_id,
				pickup_des: addService.pickup,
				drop_des: addService.drop,
            }
        }).done((res) => {
            if(res.bus_busy){
                $('.block-error-driver').text('');
                $('.block-error-driver').append('<span>'+res.bus_busy_message+'</span>');
                $('.block-error-driver').css({'display': 'block'});
            }
            else if(res.transfer_add_date){
                $('.block-error-driver').text('');
                $('.block-error-driver').append('<span>'+res.transfer_message+'</span>');
                $('.block-error-driver').css({'display': 'block'});
                $('#selectDateForTransferPackage').modal('hide');
                $('#select-driver-and-bus').modal('hide');
            }
            else{
                if(change_service === 'true'){
                    $.ajax({
                        method: 'GET',
                        url: `/tour_package/${addService.package_id}/delete`,
                        data: {}
                    }).done((data_delete) => {
                        if(addService.page_service !== 'change_edit_service'){
                            addService.addPackages();
                        }else{
                            window.location.replace(res);
                        }
                    });
                }else{
                    if(addService.page_service !== 'change_edit_service'){
                        addService.addPackages();
                    }else{
                        window.location.replace(res);
                    }
                }
            }
            addService.service_old_time = null;
            $('#service-modal').modal('hide');
            $('#select-driver-and-bus').modal('hide');
            $('#select-driver-and-bus_transfer_package').modal('hide');
            $('#overlay_delete').remove();
            $('.modal-backdrop').hide();
        })
    },
    addPackages: () => {
        if(addService.tour_id){
            $.ajax({
                method: "POST",
                url: "/tour/" + addService.tour_id + "/generatePackages",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    departureDate: addService.departure_date,
                    retirementDate: addService.retirement_date,
                    tourId: addService.tour_id
                }
            }).done((res) => {
                $('.tour-packages').children().remove();
            $('.tour-packages').html(res);
            addService.dragNDrop();
            addService.checkTables();
            addService.changeServiceTime();
            addService.addOrder();
        });
            addService.checkTransferIsExist();

        }

    },
    clickMainHotel : () => {
        $(document).on('click', '.main-hotel', function () {
            tourPackageId = $(this).parent().parent().data('package_id');
            let dayTable = $(this).parent().parent().parent();
            let thisButton = $(this);
            $.ajax({
                method: 'GET',
                url: '/tour_package/' + tourPackageId + '/main_hotel',
                data: {
                }
            }).done(function (){
                dayTable.find('.main-hotel').removeClass('btn-success').addClass('btn-danger');
                thisButton.addClass('btn-success').removeClass('btn-danger')
            });

            return false;
        });
    },
    checkTransferIsExist: () => {
        let transferCount = $(document).find('[data-package-type=Transfer]').length;
        if (transferCount === 0) {
            $('.info-row').html(
                '<div class="alert alert-warning alert-dismissible">\n' +
                '                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\n' +
                '                <i class="icon fa fa-warning"></i> Please add Bus Company to tour.\n' +
                '              </div>'
            );
        } else {
            $('.info-row').html('');
        }
    },
    generateDataTableServicesType : (check = 'false') => {
        // Load service list via AJAX for Bootstrap table
        $.ajax({
            url: "/table_service_list",
            data: {
                service_type_id: addService.service_type_id,
                service_id: addService.service_id
            },
            success: function(response) {
                // Update table content with response data
                let tableBody = $('#search-table-service-list tbody');
                tableBody.empty();

                if(response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        let row = `<tr>
                            <td>${item.name || ''}</td>
                            <td>${item.address_first || ''}</td>
                            <td>${item.country || ''}</td>
                            <td>${item.city || ''}</td>
                            <td>${item.work_phone || ''}</td>
                            <td>${item.contact_name || ''}</td>
                            <td>${item['action-change-service'] || ''}</td>
                        </tr>`;
                        tableBody.append(row);
                    });
                }
            }
        });

        if(check === 'false'){
            // Focus on search input if it exists
            setTimeout(function() {
                let searchInput = $('#service-search-input');
                if(searchInput.length) {
                    searchInput.focus().attr('placeholder','Search services...').css({'width':'400px'});
                }
            }, 100);
        }
    }

};

$(document).ready(addService.run());
