
function eventHandlerForTour(){

	let elem = this;

    if (this.className.match(/service-description/)) return ;

    if (this.className.match(/click_in_task/)){
        $(elem).find('select').unbind('change');
        $(elem).find('select').on('change', function(){
            let url = $(this).attr('data-update-link');
            taskChanger.status = $(this).val();
            taskChanger.statusName =  $(this).find('option:selected').text();
            taskChanger.uploadUrl = url;           
            taskChanger.update();
        });

    	return false;
	}

    if(this.className === 'service-desc-process'){
    	return false
	}

	if(!finder.checkIfTour(this)){
		finder.class = this.className.trim();
		return finder.finder(this);
	}

	if(this.className.split('-')[1].trim() === 'process'){
		return false;
	}

	if(this.className.split('-')[1].trim() === 'status'){
        return tourChanger.handleChange(elem);
    }else{
        finder.class = elem.className.trim();
        finder.finder(elem);
	}
}

const ENTER_CODE =13;
const ESCAPE_CODE = 27;

let finder = {
	init: () => {
		tourChanger.getCountry();
		tourChanger.getStatuses();
        tourServiceChanger.getStatuses();
		finder.bind();
	},
	bind: () => {
		$(document).on('click', 'table:not(.finder-disable) tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);

        $('.onclick_redirect').on('click', function (e) {
            window.location  = $(this).closest('tr').attr('data-link');
        });
	},
	checkIfTour: (elem) => {
		if (elem.className.split('-')[0].trim() == 'touredit') return true;
		return false;
	},
	finder: (that) => {
		if(finder.class == 'click_tour_in_task'){
            let urlTour = $(that).find('span').attr('data-tour-link');

            if (!urlTour) return false;
            window.location.href = urlTour;
            return false;
		}
		if($(that).find('a').hasClass('click_event')){
            let urlTour = $(that).find('a').attr('href');

            if (!urlTour) return false;
            window.location.href = urlTour;
            return false;
		}

		if (finder.class == 'not-click') return false;
		if (finder.class == 'status') return taskChanger.handleChange(that);
//		if (finder.class == 'service-description') return tourServiceChanger.handleChange(that);
		if(finder.class == 'tour_package_status') return tourServiceChanger.changeStatus(that);
		let url = $(that).closest('tr').find('td:last-child a.show-button').data('link');

		if (!url) return false;
		window.location.href = url;
	},
	settings: () => {
		finder.class = '';
		finder.redirectUrl = '';
		finder.clicks = 0;
		finder.timer = null;
	}
};

let taskChanger = {
	settings: () => {
		taskChanger.status = '';
		taskChanger.uploadUrl = '';
		taskChanger.taskId = null;
		taskChanger.statusName = '';
	},
	update: () => {
		$.ajax({
			url: taskChanger.uploadUrl,
			method: 'PUT',
			data: {
				_token: $('meta[name="csrf-token"]').attr('content'),
				newStatus: taskChanger.status
			}
		}).done( (res) => {
            taskChanger.getTasksBlock();
		})
	},
	getStatusesTask: (status, oldSp) => {
     	$.ajax({
			url: '/task/statuses/list',
			data: {
				taskId: taskChanger.taskId
			}
		}).done( (res) => {
			$(status).text('').append(res);
			$(status).closest('tr').find('.task-status').on('change', function(){
				//@ToDo: check if alls ok with this change, cause of TaskChanger dont work properly when work with few selects

				taskChanger.status = $(this).val();
				taskChanger.uploadUrl = $(this).data('update-link');
				taskChanger.statusName = $(this).find('option:selected').text();
				taskChanger.update();
				$(this).remove();
				$(status).append($(oldSp).text(taskChanger.statusName));
			});
		})
	},
    getTasksBlock: function(){
        $.ajax({
            url: '/task/getTasksBlock',
            type: "GET",
            success: function(data) {
                $('#tasksBlock').html(data);
            }
        });
    },
	handleChange: (status) => {
		let oldSp = $(status).closest('tr').find('.task-data');
		taskChanger.taskId = $(status).closest('tr').find('.task-data').data('task-id');
/*
        if (!taskChanger.taskId) {
			$(status).closest('tr').find('.task-status').on('change', function(){
                //@ToDo: check if alls ok with this change, cause of TaskChanger dont work properly when work with few selects
				//if (taskChanger.status == $(this).val()) return false;
				taskChanger.status = $(this).val();
				taskChanger.uploadUrl = $(this).data('update-link');
				taskChanger.update();
			});
		}
*/
		if (taskChanger.taskId) taskChanger.getStatusesTask(status, oldSp);

        $(document).keyup(function(e) {
            if (e.keyCode === ESCAPE_CODE) {
                $(status).closest('tr').find('.task-status').change();
            }
        });
/*        
        $(status).closest('tr').find('.task-status').on('blur', function(){
            $(status).closest('tr').find('.task-status').change();
        });
*/        
		// $(status).closest('tr').find('.task-status').on('change', function(){
		// 	if (taskChanger.status == $(this).val()) return false;
		// 	taskChanger.status = $(this).val();
		// 	taskChanger.uploadUrl = $(this).data('update-link');
		// 	taskChanger.update();
		// });

	}
};

let tourChanger = {
	settings: () => {
		tourChanger.fieldName = '';
		tourChanger.fieldValue = '';
		tourChanger.updateLink = '';
		tourChanger.cityName = '';
		tourChanger.oldValue = '';
		tourChanger.element;
		tourChanger.countryList;
		tourChanger.statuses;
	},
	handleChange: (that) => {
		$('table').off('click', 'tr td:not(:last-child):not(.fc-event-container)');
		tourChanger.fieldName = that.className.split('-')[1];
        tourChanger.updateLink = $(that).closest('tr').find('a.show-button').data('link');
		var value = $(that).text();
		var valueStatus = $(that).attr('data-name-status');

		if (tourChanger.fieldName == 'name'){
			$(that).text('').append('<input class="edit form-control"></input>');
			$('.edit').val(value).css({'background-color':'inherit', 'border':'none'}).focus().focusout(function(){
				$(this).unbind('keypress');
				tourChanger.fieldValue = $(this).val() ? $(this).val() : value;
				$(this).remove();
				$(that).text(tourChanger.fieldValue);
				tourChanger.updateTour();
				$('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);
			}).keypress(function(e){
				if (e.keyCode == 13) {
					$(this).unbind('focusout');
					tourChanger.fieldValue = $(this).val() ? $(this).val() : value;
					$(this).remove();
					$(that).text(tourChanger.fieldValue);
					tourChanger.updateTour();
					$(this).blur();
					$('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);
				}
			});
		}
		if (tourChanger.fieldName == 'departure_date' || tourChanger.fieldName == 'retirement_date'){
			tourChanger.oldValue = value;
			tourChanger.element = $(that);
			$(that).text('').append('<input class="edit form-control timepicker"></input>');
			$('.edit').datepicker({
				format: 'yyyy-mm-dd'
			}).css({'background-color':'inherit', 'border':'none'});
			$('.edit').datepicker('show').focus().on('hide', function(){
				tourChanger.fieldValue = $('.edit').val() ? $('.edit').val() : value;
				$(that).datepicker('destroy').text(tourChanger.fieldValue);
				tourChanger.updateTour();
				$('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);
			});
			$('.edit').on('keydown', function(e){
				if (e.keyCode == 13){
					$('.edit').datepicker('hide');
				}
			});
		}
		if (tourChanger.fieldName == 'country_begin') {
			// $('table').off('click', 'tr td:not(:last-child)');
			// if (!tourChanger.countryList) tourChanger.getCountry(value, that); else tourChanger.addCountryToSelect(value, that);
			tourChanger.addCountryToSelect(value, that);
		}
		if	(tourChanger.fieldName == 'city_begin'){
			// $('table').off('click', 'tr td:not(:last-child)');
			let countryName = $(that).closest('tr').find('.touredit-country_begin').text();
			let countryAlias = $.grep(tourChanger.countryList, function(e){
				return e.name == countryName;
			});
			tourChanger.countryAlias = countryAlias[0].alias;
			$(that).text('').append('<input type="text" id="city_from_tour" class="form-control"></input>');
			$('#city_from_tour').focus();
			let googleEl = document.getElementById('city_from_tour');
			let options = {
				 types: ['(cities)']
			};
			let autocomplete = new google.maps.places.Autocomplete(googleEl, options);
			autocomplete.setComponentRestrictions( {'country': [countryAlias[0].alias] } );
			autocomplete.addListener('place_changed', function() {
		        let place = autocomplete.getPlace();
				$('#city_from_tour').remove();
				$(that).text(place.name);
				tourChanger.fieldValue = place.place_id;
				tourChanger.cityName = place.name;
				tourChanger.updateTour();
				$('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);
		    });
		}
		if (tourChanger.fieldName == 'status'){
			$(that).attr('class', '');
			$(that).attr('class', 'touredit-process');


			$(that).text('');
			$(that).append('<form><select name="status_tour" class="tour-status form-control"></select></form>');
			$(tourChanger.statuses).each(function(key, status){
				let selected = (status.name == valueStatus) ? 'selected="selected"' : '';
                $(that).find('.tour-status').append('<option value="' + status.id + '"' + ' ' + selected + '>' + status.name + '</option>');
			});

            $(that).find('.tour-status').on('change', function(){
                $(that).attr('data-name-status', '');
				tourChanger.fieldValue = $(this).val();

				$(this).remove();
				let statusName = $.grep(tourChanger.statuses, function(e){
					return e.id == tourChanger.fieldValue;
				});

                $(that).text(statusName[0].name);
                $(that).attr('data-name-status', statusName[0].name);
                tourChanger.updateLink = $(that).attr('data-status-link');
                tourChanger.updateTour();
                $('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);
                $(that).attr('class', '');
                $(that).attr('class', 'touredit-status');
			});
            $(document).keyup(function(e) {
                if (e.keyCode === ESCAPE_CODE) {
                    $(that).find('.tour-status').change();
                }
            });
            $(that).find('.tour-status').on('blur', function(){
                $(that).find('.tour-status').change();
			});
		}
	},
	updateTour: () => {
        console.log(tourChanger.updateLink);
        $.ajax({
			url: tourChanger.updateLink,
			method: 'PUT',
			data: {
				_token: $('meta[name="csrf-token"]').attr('content'),
				fieldName: tourChanger.fieldName,
				fieldValue: tourChanger.fieldValue,
				cityName: tourChanger.cityName,
				countryAlias: tourChanger.countryAlias
			}
		}).done( (res) => {
			if (res == 'wrong date') {
				$(tourChanger.element).text(tourChanger.oldValue);
			}
			
			if(res.status_error) {
                $('#error_tour').find('.error_tour_message').html(res.status_error);
                $('#error_tour').modal();
                $('#error_tour').on('hidden.bs.modal', function () {
                    location.reload();
                });
            }

			tourChanger.fieldName = '';
			tourChanger.fieldValue = '';
			tourChanger.updateLink = '';
			tourChanger.cityName = '';
			tourChanger.oldValue = '';
		})
	},
	addCountryToSelect: (oldValue, el) => {
		$(el).text('').append('<select id="tour-select" class="form-control"></select>');
		$(tourChanger.countryList).each(function(key, country){
			let selected = (country.name == oldValue) ? 'selected="selected"' : '';
			$('#tour-select').append('<option value="' + country.alias +  '"' + ' ' + selected + '>' + country.name + '</option>')
		});
		$('#tour-select').css({'background-color':'inherit', 'border':'none'}).on('change', function(){

			tourChanger.fieldValue = $(this).val();
			tourChanger.updateTour();
			$(this).remove();
			let selectedCountry = $.grep(tourChanger.countryList, function(e){
				return e.alias == tourChanger.fieldValue;
			});
			$(el).text(selectedCountry[0].name);
			$('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);
		});
    	$(document).keyup(function(e) {
        	if (e.keyCode === ESCAPE_CODE) {
                $('#tour-select').change();
            }
        });
        $('#tour-select').on('blur', function(){
            $('#tour-select').change();
        });
	},
	getCountry: () => {
		$.ajax({
			url: '/tour/api/country_list',
		}).done( (res) => {
			tourChanger.countryList = JSON.parse(res);
		})
	},
	getStatuses: () => {

		$.ajax({
			url: '/tour/api/status_list',
		}).done( (res) => {
			tourChanger.statuses = JSON.parse(res);
		})
	}
};

let tourServiceChanger = {
	settings: () => {
		tourServiceChanger.fieldName = '';
		tourServiceChanger.fieldValue = '';
		tourServiceChanger.packageId = null;
		tourServiceChanger.check = true;
		tourServiceChanger.statuses;
		tourServiceChanger.statusesHotel;
		tourServiceChanger.statusesTransfer;
	},
	handleChange: (that) => {
/*        
		$('table').off('click', 'tr td:not(:last-child):not(.fc-event-container)');
		tourServiceChanger.fieldName = that.className.split('-')[1].trim();
		tourServiceChanger.packageId = $(that).closest('tr').find('.package-data').data('package_id');
		let oldValue = $(that).text();
		$(that).text('').append('<textarea class="serv-desc form-control">'+oldValue+'</textarea>');
		$('.serv-desc').select();
        $(that).attr('class', '');
        $(that).attr('class', 'service-desc-process');
		$('.serv-desc').val(oldValue).css({'background-color':'inherit', 'border':'none'}).focus().focusout(function(){
			$(this).unbind('keypress');
			tourServiceChanger.fieldValue = $(this).val() ? $(this).val() : oldValue;
			$(this).remove();
			$(that).text(tourServiceChanger.fieldValue);
			tourServiceChanger.changeDescription();
			$('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);
            $(that).attr('class', '');
            $(that).attr('class', 'service-description');
		}).keypress(function(e){
			if (e.keyCode == 13) {
				$(this).unbind('focusout');
				if($(this).val() !== ''){
                    tourServiceChanger.fieldValue = $(this).val() ? $(this).val() : oldValue;
                }else{
                    tourServiceChanger.fieldValue = '';
				}
				$(this).remove();
				$(that).text(tourServiceChanger.fieldValue);
				tourServiceChanger.changeDescription();
				$(this).blur();
				$('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);
                $(that).attr('class', '');
                $(that).attr('class', 'service-description');
			}
		});
*/
	},
	changeDescription: () => {
		$.ajax({
			url: '/package/api/ajax_update',
			data: {
				package_id: tourServiceChanger.packageId,
				fieldName: tourServiceChanger.fieldName,
				fieldValue: tourServiceChanger.fieldValue
			}
		}).done( (res) => {
			tourServiceChanger.fieldName = '';
			tourServiceChanger.fieldValue = '';
			tourServiceChanger.packageId = null;
		})
	},
    changeStatus: (that) => {
        $(that).attr('class', '');
        $(that).attr('class', 'touredit-process');
        var check_action = true;
        const valueStatus = $(that).attr('data-info-status');
        const packageId = $(that).attr('data-info-package-id');
    	const packageType = $(that).attr('data-info-package-type');

        $(that).text('');
        $(that).append('<form><select name="status_tour_package" class="tour-status form-control"></select></form>');

        if(packageType == 'transfer'){
		  $(tourServiceChanger.statusesTransfer).each(function (key, status) {
                let selected = (status.name === valueStatus) ? 'selected="selected"' : '';
                $(that).find('.tour-status').append('<option value="' + status.id + '"' + ' ' + selected + '>' + status.name + '</option>');
            });		
		}else if(packageType == 'hotel'){
			$(tourServiceChanger.statusesHotel).each(function (key, status) {
                let selected = (status.name === valueStatus) ? 'selected="selected"' : '';
                $(that).find('.tour-status').append('<option value="' + status.id + '"' + ' ' + selected + '>' + status.name + '</option>');
            });
        }else{
            $(tourServiceChanger.statuses).each(function (key, status) {
                let selected = (status.name === valueStatus) ? 'selected="selected"' : '';
                $(that).find('.tour-status').append('<option value="' + status.id + '"' + ' ' + selected + '>' + status.name + '</option>');
            });
        }

        $(that).find('.tour-status').on('change', function(){
            $(that).closest('tr').find('td:last-child > a.open-modal-change-service').css({'display' : 'none'});
			var package_id = $(that).attr('data-info-package-id');
			var _this = $(this);
            tourServiceChanger.fieldValue = $(this).val();
            if(!check_action){
                tourServiceChanger.fieldValue = $(that).attr('data-info-status_id');
			}

            let stat = [];			

            if(packageType === 'hotel') {
                stat = tourServiceChanger.statusesHotel;
            }else if(packageType === 'transfer'){
				stat = tourServiceChanger.statusesTransfer;
			}else{
            	stat = tourServiceChanger.statuses;
			}

            let statusName = $.grep(stat, function(e){
                return e.id == tourServiceChanger.fieldValue;
            });

            console.log(check_action);

			if(check_action){

                if(statusName[0].name === 'On Option' && packageType === 'hotel'){

                    $('#date_modal').find('#package_id').val(package_id);
                    $('#date_modal').find('#status_id').val(statusName[0].id);
                    $('#date_modal').find('#submit_on_option_task').removeAttr("disabled");
                    $('#date_modal').modal();


                    $('.btn-task-tour-hotel_cancel').click(function (e) {
                        $('#date_modal').modal('hide');
                        check_action = false;
                        setTimeout(function (e) {
                            $(that).find('.tour-status').change();
                        }, 200)
                    });

                    $('#date_modal').find('#submit_on_option_task').click(function (e) {
                        e.preventDefault();
                    	$(this).attr('disabled','disabled');
                        $.ajax({
                            url: '/tour_package/api/update_status',
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                status_id: tourServiceChanger.fieldValue,
                                package_id: packageId,
								tour_id : $('#date_modal').find('#tour_id').val(),
								date :  $('#date_modal').find('#start_date').val(),
                                time :  $('#date_modal').find('#end_time').val()
                            }
                        }).done( (res) => {
                        	console.log('ok');
                            check_action = false;
                            $('#date_modal').modal('hide');
                            window.location.href= '/tour/'+$('#date_modal').find('#tour_id').val();
                            return false;
                        })

                    });

                    return false;
                }

                if(statusName[0].name === 'Confirmed' && packageType === 'hotel'){
                    $('#confirmed_hotel').modal();

                    $.ajax({
                        url: `/get_packages_for_delete/${package_id}`,
                    }).done( (res) => {
                        $('.confirmed_hotel_block').html(res);

                        $('.btn-send-confirmed_hotel_add').click(function (e) {
                        	tourServiceChanger.fellowconfirmHotel(package_id);
                            tourServiceChanger.updateStatusTourPackage($(that), _this, statusName, packageType, package_id, packageId);
                            $('#confirmed_hotel').modal('hide');
                            setTimeout(function (e) {
                                addService.addPackages();
                            }, 200);
                        });

                        $('.btn-send-hotel_cancel').click(function (e) {
                            $('#confirmed_hotel').modal('hide');
                            check_action = false;
                            setTimeout(function (e) {
                                $(that).find('.tour-status').change();
                            }, 200)
                        });
                        return false;
                    });

                    return false;
                }
            }

            tourServiceChanger.updateStatusTourPackage($(that), $(this), statusName, packageType, package_id, packageId);

        });
        $(document).keyup(function(e) {
            if (e.keyCode === ESCAPE_CODE) {
                check_action = false;
                setTimeout(function (e) {
                    $(that).find('.tour-status').change();
                }, 200)
            }
        });

	},
    deleteTourPackagesHotel(package_id){
        $.ajax({
            url: `/delete_packages_hotel_cancelled/${package_id}`,
        }).done( (res) => {});
	},
	fellowconfirmHotel(package_id){
        $.ajax({
            url: `/fellowconfirmHotel/${package_id}`,
        }).done( (res) => {});
	},
	updateStatusTourPackage(that, _this, statusName, packageType, package_id, packageId){
        $(that).attr('data-info-status', '');
        $(that).attr('data-info-status_id', '');
        $(_this).remove();

        console.log(statusName);
        if(statusName[0].name !== 'Confirmed'){
            $(that).closest('tr').find('td:last-child > a.open-modal-change-service').css({'display' : 'inline-block'})
        }

        $(that).text(statusName[0].name);

        if(packageType === 'hotel') {
            var list_hotels_packages = $('.tour_package_status[data-info-package-id='+package_id+']');

            for (var i = 0; i < list_hotels_packages.length; i++){
                $(list_hotels_packages[i]).text('');
                $(list_hotels_packages[i]).text(statusName[0].name);
                $(list_hotels_packages[i]).attr('data-info-status', statusName[0].name);
                $(list_hotels_packages[i]).attr('data-info-status_id', statusName[0].id);
            }
        }

        $(that).attr('data-info-status', statusName[0].name);
        $(that).attr('data-info-status_id', statusName[0].id);

        tourServiceChanger.updatePackageStatus(packageId);
        $('table').on('click', 'tr td:not(:last-child):not(.fc-event-container)', eventHandlerForTour);
        $(that).attr('class', '');
        $(that).attr('class', 'tour_package_status');
	},
    getStatuses(){

        $.ajax({
            url: '/tour_package/api/status_list',
        }).done( (res) => {
            tourServiceChanger.statuses = JSON.parse(res);
        });

        $.ajax({
            url: '/tour_package/api/status_list_hotel',
        }).done( (res) => {
            tourServiceChanger.statusesHotel = JSON.parse(res);
    	});
		
		$.ajax({
            url: '/tour_package/api/status_list_transfer',
        }).done( (res) => {
            tourServiceChanger.statusesTransfer = JSON.parse(res);
    	})
	},
    updatePackageStatus(packageId){
        $.ajax({
            url: '/tour_package/api/update_status',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                status_id: tourServiceChanger.fieldValue,
                package_id: packageId,
            }
        }).done( (res) => {
            tourServiceChanger.fieldValue = '';
        })
	}
};

$(document).on("change", ".task-status", function(even){
    taskChanger.status = $(this).val();
    taskChanger.uploadUrl = $(this).data('update-link');
    taskChanger.update();
})

$(document).ready(function(){
	finder.init();
    var that_;
    $(document).on('click', '.service-description', function(){

        that_ = this;
        $('#service-description-edit').modal();
            tourServiceChanger.fieldName = that_.className.split('-')[1].trim();
            tourServiceChanger.packageId = $(that_).closest('tr').find('.package-data').data('package_id');
            let oldValue = $(that_).html();
            CKEDITOR.instances['description-edit'].setData(oldValue);

    });

    $('.save-description').click(function(){
        tourServiceChanger.fieldValue = CKEDITOR.instances['description-edit'].getData();
        $(that_).html(tourServiceChanger.fieldValue);
        tourServiceChanger.changeDescription();
        $('#service-description-edit .close').click();
    }); 
});