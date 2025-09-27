
let quotation = {
    config : {
        columns : [
        ],
        keys : {
            KEY_ENTER : 13,
            KEY_LEFT : 37,
            KEY_UP : 38,
            KEY_RIGHT: 39,
            KEY_DOWN: 40,
            KEY_ESC : 27
        },
        rows : []
    },

    init : () => {
        quotation.bind();
        quotation.firstFocus();
        quotation.generateRowsColumnsArray();
        quotation.hideRestaurants();
    },
    table : $('body').find('#quotation_table'),
    bind : () => {
        quotation.cellClick();
        quotation.move.moveArrow();
//        $('.saved').on('click', function(){
//            $('.saved').prop('disabled', true);
//            quotation.saveQuotation();
//        });
        $('.update-quotation').on('click', function(){
            quotation.updateQuotation();
        });
		$('.calculate-quotation').on('click', function(){
            quotation.closeCell();
        });
        $('.namesToggle').click(function(){
            quotation.toggleRestaurants();
        });
        $('#mark_up').change(function(){
            calculation.calculate();
        });
        $('#rate').change(function(){
            calculation.calculate();
        });
		$('.confirm-button').on('click', function(){
            quotation.confirm();
        });
        $('.confirm_cancel-button').on('click', function(){
            quotation.confirm_cancel();
        });
    },
	confirm: function(){
        quotation_id = $("#quotation_id").val();
        $.ajax({
            url: '/quotation/'+quotation_id+'/confirm',
            
            type: "POST",
            
            success: function() {
                location.reload();

            }
        });
    },
    confirm_cancel: function(){
        quotation_id = $("#quotation_id").val();
        
        $.ajax({
            url: '/quotation/'+quotation_id+'/confirm_cancel',
            
            type: "POST",
            
            success: function() {
                location.reload();

            }
        });
    },
    form : function(cell) {
        html = '<input type="text" id="form_value" value="'+cell.value+'" autofocus>';
        cell.dom.html(html);
        $('#form_value').focus();
        $('#form_value').select();
    },

    cell : function (cellDom) {
        quotation.closeCell();
        this.row = cellDom.parent().data('row');
        this.column = cellDom.data('column');
        this.value = cellDom.text().trim();
        this.dom = cellDom;
        return this;

    },
    getCellValue : function (row, column) {
        cellDom = quotation.table.find('[data-row='+row+']').find('[data-column='+column+']');
        value = cellDom.text().trim().replace(',', '.');
        if (value === '') {
            return 0;
        }
        return value;
    },
    getColumnSum : function (column) {
        var sum = 0;
        $.each(quotation.config.rows, function (i, row) {
            sum += parseFloat(quotation.getCellValue(row, column));
        });
        return sum * calculation.calc.rate();
    },
    formSubmit : function(oldCell) {
        value = $('#form_value').val();
		if (typeof value === 'undefined') {
 		oldCell.dom.html(value);
        calculation.calculate();
} else {
 
		oldCell.dom.html(`<input type ='text' >${value}</input>`);
        calculation.calculate();
}

    },
    generateRowsColumnsArray : function(){
        $.each(quotation.table.find('tr.quotation-row'), function(i, item){
            row = $(item).data('row');
            quotation.config.rows.push(row);
            if (i === 0) {
                $.each($(this).find('td:not(.additional-cell)'), function(i, item){
                    column = $(item).data('column');
                    quotation.config.columns.push(column);
                });
            }
        });
    },
    editCell : function (cell) {

        form = new quotation.form(cell);
    },
    hideRestaurants : function() {
        quotation.table.find('[data-column=dinnerName]').css('display', 'none');
        quotation.table.find('[data-column=lunchName]').css('display', 'none');
    },
    showRestaurants : function() {
        quotation.table.find('[data-column=dinnerName]').css('display', 'table-cell');
        quotation.table.find('[data-column=lunchName]').css('display', 'table-cell');
    },
    toggleRestaurants : function() {
        if ($('.namesToggle').hasClass('hideTitle')) {
            quotation.showRestaurants();
            $('.namesToggle').html('Hide titles');
        } else {
            quotation.hideRestaurants();
            $('.namesToggle').html('Show titles');
        }
        $('.namesToggle').toggleClass('hideTitle')
    },
    cellClick: function () {
        quotation.table.find('td').on('click', function(){

            if (cell.row != $(this).parent().data('row') || cell.column != $(this).data('column')) {
                cell = new quotation.cell($(this));
                cell.dom.addClass('activeCell');
                quotation.editCell(cell);
            }
            $('#form_value').focus();
            $('#form_value').select();

        });
    },
    constructCell: function (row, column) {
        cellDom = quotation.table.find('[data-row='+row+']').find('[data-column='+column+']');
        return new quotation.cell(cellDom);
    },
    closeCell : function() {
        if (!(typeof cell === 'undefined')) {
            oldCell = cell;
            cell.dom.removeClass('activeCell');
            quotation.formSubmit(oldCell);
        }

    },
    firstFocus : function () {
        cell = quotation.constructCell(0, 'cityName');
    },
    move : {
        moveArrow : function () {
            $('body').on('keydown', function(e) {
                if ($('#form_value').length === 0) {
                    if(e.which === quotation.config.keys.KEY_UP) {
                        quotation.move.moveUp();
                    }
                    if(e.which === quotation.config.keys.KEY_DOWN) {
                        quotation.move.moveDown();
                    }
                    if(e.which === quotation.config.keys.KEY_RIGHT) {
                        quotation.move.moveRight();
                    }
                    if(e.which === quotation.config.keys.KEY_LEFT) {
                        quotation.move.moveLeft();
                    }
                    if(e.which === quotation.config.keys.KEY_ENTER) {
                        quotation.form(cell);
                    }
                } else {
                    if(e.which === quotation.config.keys.KEY_ENTER) {
                        quotation.closeCell();
                        cell = quotation.constructCell(cell.row, cell.column);
                        cell.dom.toggleClass('activeCell');
                    }
                }
            });
        },
        moveUp : function () {
            row = cell.row;
            if (row === quotation.config.rows[0]) {
                new_row = quotation.config.rows.length - 1;
            } else {
                new_row = row-1;
            }
            cell = quotation.constructCell(new_row, cell.column);
            cell.dom.toggleClass('activeCell');

        },
        moveDown : function () {
            row = cell.row;
            if (row === quotation.config.rows[quotation.config.rows.length - 1]) {
                new_row = 0;
            } else {
                new_row = row+1;
            }
            cell = quotation.constructCell(new_row, cell.column);
            cell.dom.toggleClass('activeCell');
        },
        moveRight : function () {
            column = cell.column;
            column_key = quotation.config.columns.indexOf(column);

            if (column_key === quotation.config.columns.length - 1) {
                new_column = 0;
            } else {
                new_column = column_key+1;
            }
            cell = quotation.constructCell(cell.row, quotation.config.columns[new_column]);
            cell.dom.toggleClass('activeCell');
        },
        moveLeft : function () {
            column = cell.column;
            column_key = quotation.config.columns.indexOf(column);
            if (column_key === 0) {
                new_column = quotation.config.columns.length - 1;
            } else {
                new_column = column_key-1;
            }
            cell = quotation.constructCell(cell.row, quotation.config.columns[new_column]);
            cell.dom.toggleClass('activeCell');
        },
    },
    validateName : function() {
        if ($('#quotation_name').val().length == 0) {
            $('#quotation_name').css('border', 'red 1px solid');
            $('.validate-name').removeClass('hide');
            return false;
        }
        $('#quotation_name').css('border', 'black 1px solid');
        $('.validate-name').addClass('hide');
        return true;
    },

    saveQuotation : function () {
		quotation.closeCell();
        if (quotation.validateName()) {
            let data = [];
            let calculation = [];

            $.each(quotation.config.rows, function (i, row){
                let cell_row = {};
                $.each(quotation.config.columns, function (k, column) {
                    cell_data = quotation.constructCell(i, column);
                    cell_row[cell_data.column] = cell_data.value;
                });

                data.push(cell_row);
            });

            $.ajax({
                url: '/quotation/'+tourId+'/save',
                data: {
                    data : data,
                    tourId: tourId,
                    name: $('#quotation_name').val(),
                    note: $('#note').val(),
                    rate: $('#rate').val(),
                    _token : $('meta[name="csrf-token"]').attr('content'),

                },
                type: "POST",
                success: function(href) {
                    window.location.href = href;

                }
            });
        }else{
            $('.saved').prop('disabled', false);
            $('#overlay_delete').remove();
        }

    },
    updateQuotation : function () {
		quotation.closeCell();
        if (quotation.validateName()) {
            jQuery('.loadingoverlay').fadeIn();
            var calculation = $('#summary tr[data-row=person] td');
            var calculationAll = $('#summary_all tr[data-row=person] td');
            var calculationData = [];
            calculation.each(function(){
                person = $(this).attr('data-person');
                item = {};
                if($(this).attr('data-person')) {
                    item['person'] = $(this).parent().parent().find('tr[data-row=person] td[data-person="'+person+'"]').text();
                    item['netto'] = $(this).parent().parent().find('tr[data-row=netto] td[data-person="'+person+'"]').text();
                    item['netto_euro'] = $(this).parent().parent().find('tr[data-row=netto_euro] td[data-person="'+person+'"]').text();
                    item['brutto'] = $(this).parent().parent().find('tr[data-row=brutto] td[data-person="'+person+'"]').text();
                    item['activity'] = $(this).parent().parent().find('tr[data-row=active] td[data-person="'+person+'"] input').is(':checked') ? 1 : 0;
                }
                calculationData.push(item);
            }) ;
            calculationAll.each(function(){
                person = $(this).attr('data-person');
                item = {};
                if($(this).attr('data-person')) {
                    item['person'] = $(this).parent().parent().find('tr[data-row=person] td[data-person="'+person+'"]').text();
                    item['netto'] = $(this).parent().parent().find('tr[data-row=netto] td[data-person="'+person+'"]').text();
                    item['netto_euro'] = $(this).parent().parent().find('tr[data-row=netto_euro] td[data-person="'+person+'"]').text();
                    item['brutto'] = $(this).parent().parent().find('tr[data-row=brutto] td[data-person="'+person+'"]').text();
                    item['activity'] = $(this).parent().parent().find('tr[data-row=active] td[data-person="'+person+'"] input').is(':checked') ? 1 : 0;
                }
                calculationData.push(item);
            }) ;
            let data = [];

            $.each(quotation.config.rows, function (i, row){
                let cell_row = {};

                cell_row['row_id'] = row;
                $.each(quotation.config.columns, function (k, column) {
                    cell_row[column] = $('tr[data-row='+row+'] td[data-column='+column+']:not(.additional-cell)').text().trim();
                });

                data.push(cell_row);
            });

            // var additional = $('input[name*=package_menu]').map(function(){var value = {};value[$(this).attr('name')] = $(this).val();  return value}).get();
            var additional = $('div[data-repeater-list=package_menu] .row').map(function(){
                var value = {};
                value['person'] =$(this).find('#person').val();
                value['price'] =$(this).find('#price').val();
                value['active'] =$(this).find('.active-person').prop('checked') ? 1 : 0;
                return value;
            }).get();

            // SETTING ADDITIONAL COLUMNS
            let columnsNames = [];
            var additional_columns =$('.additional-column').filter(function(){
                if (columnsNames.includes($(this).data('column'))) {
                    return false;
                }
                columnsNames.push($(this).data('column'));
                return true;
            }).map(function(){
                var value = {};
                value['type'] = $(this).data('type');
                value['name'] = $(this).data('column');
                return value;
            }).get();
            //


            let additional_column_values = [];
            for(i in quotation.config.rows) {
                let row = quotation.config.rows[i];
                var value = {};
                let rowDom = $('tr[data-row="' + row+ '"]');
                for (j in columnsNames) {
                    let cell = columnsNames[j];
                    let value = {};
                    value['row'] = row;
                    value['cell'] = cell;
                    value['value'] = rowDom.find('td[data-column="'+cell+'"]').text().trim();
					console.log( value['value']);
                    if (!Number.isInteger(parseInt(value['value']))) {
                        value['value'] = "0";
                    }
					
                    additional_column_values.push(value);
                }
            }

            $.ajax({
                url: '/quotation/'+quotationId+'/updateQuotation',
                data: {
                    data : data,
                    calculation : calculationData,
                    quotationId: quotationId,
                    name: $('#quotation_name').val(),
                    note: $('#note').val(),
                    rate: $('#rate').val(),
                    mark_up : $('#mark_up').val(),
                    note_show : $('#note_show').prop('checked') ? true : false,
                    additional_persons : additional,
                    additional_columns : additional_columns,
                    additional_column_values : additional_column_values,
                    _token : $('meta[name="csrf-token"]').attr('content'),

                },
                type: "POST",
                success: function(href) {
                    window.location.href = href;
                }
            });
        }

    }
};

var calculation = {
    config : {

    },
    table : $('#calculation'),
    init : function() {
        calculation.calculate();
    },
    bind : function() {

    }
    ,
    calculate : function() {
        calculation.calc.quotationSum.hotelSum();
        calculation.calc.quotationSum.lunchSum();
        calculation.calc.setQuotationRowSum();
        calculation.calc.quotationSum.dinnerSum();
        calculation.calc.quotationSum.entranceSum();
        calculation.calc.quotationSum.localgdSum();
        calculation.calc.quotationSum.driverSum();
        calculation.calc.quotationSum.singleSupplSum();
		calculation.calc.quotationSum.dfsSupplSum();
        calculation.calc.quotationSum.busSum();
        calculation.calc.quotationSum.groupCostSum();
        calculation.calc.quotationSum.porterageSum();
        calculation.calc.quotationSum.mealsSum();

        calculation.calc.combinedSum.setEntrancePorterageSum();
        calculation.calc.combinedSum.hotelSum();
        calculation.calc.combinedSum.lunchSum();
        calculation.calc.combinedSum.dinnerSum();
        calculation.calc.combinedSum.entranceSum();
        calculation.calc.combinedSum.localgdSum();
        calculation.calc.combinedSum.driverSum();

        calculation.calc.netto.first.build();
        calculation.calc.netto.second.build();
        calculation.calc.netto.third.build();
        calculation.calc.netto.fourth.build();
    },
    calc : {
        setValue : function(row, column, value){
            value = parseFloat(value).toFixed(2);
            calculation.table.find('[data-row='+ row +']').find('[data-column='+column+']').html(value);
        },
        getValue : function(row, column){
            return parseFloat(calculation.table.find('[data-row='+ row +']').find('[data-column='+column+']').html(), 2);
        },
        getAdditionalSum : function (peopleCount) {
            sum = 0;
            $('.additional-column').each(function(){
                let columnName = $(this).data('column');
                let columnType = $(this).data('type');
				
                let colSum = 0;
                    $('td.additional-cell[data-column="'+columnName+'"]').each(function(){

                        colSum += parseFloat($(this).text(), 2);
                    });
                if(columnType == 'all') {
				
                    colSum = parseFloat(colSum/peopleCount, 2);
                }
                sum += colSum;
            });
            return parseFloat(sum.toFixed(2), 2);
        },
        quotationSum : {
            hotelSum : function() {
                calculation.calc.setQuotationRowSum('htlpp', 'htlpp');
            },
            lunchSum : function(){
                calculation.calc.setQuotationRowSum('lunch', 'lunch');
            },
            dinnerSum : function(){
                calculation.calc.setQuotationRowSum('dinner', 'dinner');
            },
            entranceSum : function (){
                calculation.calc.setQuotationRowSum('entrance', 'entrance');
            },
            localgdSum : function (){
                calculation.calc.setQuotationRowSum('local_g_d', 'local_g_d');
            },
            driverSum : function (){
                calculation.calc.setQuotationRowSum('driver', 'driver_room');
            },
            singleSupplSum : function () {
               calculation.calc.setQuotationRowSum('SIN', 'sgl_suppl');
            },
			dfsSupplSum : function () {
               calculation.calc.setQuotationRowSum('DFS', 'dfs_suppl');
            },
            busSum : function () {
                calculation.calc.setQuotationRowSum('bus', 'bus');
            },
            groupCostSum : function () {
                calculation.calc.setQuotationRowSum('group_cost', 'group_cost');
            },
            porterageSum : function () {
                calculation.calc.setQuotationRowSum('porterage', 'porterage');
            },
            mealsSum : function () {
                lunch = calculation.calc.getValue('quotation_sum', 'lunch');
                dinner = calculation.calc.getValue('quotation_sum', 'dinner');
                calculation.calc.setValue('quotation_sum', 'total_meals', parseFloat(lunch) + parseFloat(dinner));
            },
        },
        combinedSum : {
            setEntrancePorterageSum : function () {
                entrance = calculation.calc.getValue('quotation_sum', 'entrance');
                porterage = calculation.calc.getValue('quotation_sum', 'porterage');
                calculation.calc.setValue('combined_sum', 'entrance_porterage', entrance + porterage);
            },
            hotelSum : function() {
                entrance = calculation.calc.getValue('quotation_sum', 'entrance');
                porterage = calculation.calc.getValue('quotation_sum', 'porterage');
                hotel = calculation.calc.getValue('quotation_sum', 'htlpp');
                calculation.calc.setValue('combined_sum', 'htlpp', entrance + porterage + hotel);
            },
            lunchSum : function() {
                lunch = calculation.calc.getValue('quotation_sum', 'lunch');
                dinner = calculation.calc.getValue('quotation_sum', 'dinner');
                calculation.calc.setValue('combined_sum', 'lunch', lunch + dinner);
            },
            dinnerSum : function() {
                hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                lunch = calculation.calc.getValue('combined_sum', 'lunch');
                calculation.calc.setValue('combined_sum', 'dinner', hotel + lunch);
            },
            entranceSum : function() {
                lunch = calculation.calc.getValue('quotation_sum', 'lunch');
                dinner = calculation.calc.getValue('quotation_sum', 'dinner');
                entrance = calculation.calc.getValue('quotation_sum', 'entrance');
                calculation.calc.setValue('combined_sum', 'entrance', lunch + dinner + entrance);
            },
            localgdSum : function() {
                localgd = calculation.calc.getValue('quotation_sum', 'local_g_d');
                driver = calculation.calc.getValue('quotation_sum', 'driver_room');
                bus = calculation.calc.getValue('quotation_sum', 'bus');
                groupCost = calculation.calc.getValue('quotation_sum', 'group_cost');
                calculation.calc.setValue('combined_sum', 'local_g_d', localgd + driver + bus + groupCost);
            },
            driverSum : function() {
                driverRoom = calculation.calc.getValue('quotation_sum', 'driver_room');
                calculation.calc.setValue('combined_sum', 'driver_room', driverRoom);
            }

        },
        netto : {
            first : {
                data : [],
                dom : $('#netto_first'),
                configuration : [
                    {
                        pax : 45,
                        pax_free : 2
                    },
                    {
                        pax : 40,
                        pax_free : 2
                    },
                    {
                        pax : 35,
                        pax_free : 2
                    },
                    {
                        pax : 30,
                        pax_free : 2
                    },
                    {
                        pax : 25,
                        pax_free : 1
                    },

                    {
                        pax : 20,
                        pax_free : 1
                    },
                    {
                        pax : 15,
                        pax_free : 1
                    },
                    {
                        pax : 10,
                        pax_free : 1
                    },
                ],
                build : function () {
                    calculation.calc.netto.first.clearData();
                    calculation.calc.netto.summary.clearData();
                    let data = [];
                    calculation.calc.netto.first.appendData('free', '<td>FREE</td>');
                    calculation.calc.netto.first.appendData('person', '<td>Person</td>');
                    calculation.calc.netto.first.appendData('meals', '<td>Meals</td>');
                    calculation.calc.netto.first.appendData('package', '<td>Package</td>');
                    calculation.calc.netto.first.appendData('foc', ' <td>F.O.C</td>');
                    calculation.calc.netto.first.appendData('bus_g_d', '<td>BUS.G/D - p.p</td>');
                    calculation.calc.netto.first.appendData('netto', '<td>Netto</td>');
                    calculation.calc.netto.first.appendData('netto_euro', '<td>Netto EURO</td>');

                    calculation.calc.netto.summary.appendData('person', '<td>Person</td>');
                    calculation.calc.netto.summary.appendAllData('person', '<td>Person</td>');
                    calculation.calc.netto.summary.appendData('netto_euro', '<td>Netto EURO</td>');
                    calculation.calc.netto.summary.appendAllData('netto_euro', '<td>Netto EURO</td>');
                    calculation.calc.netto.summary.appendData('mark_up', '<td> Mark up : EURO</td>');
                    calculation.calc.netto.summary.appendAllData('mark_up', '<td> Mark up : EURO</td>');

                    calculation.calc.netto.summary.appendData('brutto', '<td> Brutto</td>');
                    calculation.calc.netto.summary.appendAllData('brutto', '<td> Brutto</td>');
                    calculation.calc.netto.summary.appendData('active', '<td> Active</td>');
                    calculation.calc.netto.summary.appendAllData('active', '<td> Active</td>');

                    // FREE
                    $.each(calculation.calc.netto.first.configuration , function (i, item) {
                        td = '<td ' +
                            'data-person="'+item.pax + '+' + item.pax_free+'">'
                            + item.pax_free + '</td>';
                        calculation.calc.netto.first.appendData('free', td);
                    });
                    // Person
                    $.each(calculation.calc.netto.first.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + item.pax + ' + ' + item.pax_free + '</td>';
                        calculation.calc.netto.first.appendData('person', td);

                        calculation.calc.netto.summary.appendData('person', td);
                    });
                    //Meals
                    $.each(calculation.calc.netto.first.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.first.getMeals(item) + '</td>';
                        calculation.calc.netto.first.appendData('meals', td);
                    });
                    //Package
                    $.each(calculation.calc.netto.first.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.first.getPackage(item) + '</td>';
                        calculation.calc.netto.first.appendData('package', td);
                    });
                    //FOC
                    $.each(calculation.calc.netto.first.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.first.getFOC(item) + '</td>';
                        calculation.calc.netto.first.appendData('foc', td);
                    });
                    //BUS
                    $.each(calculation.calc.netto.first.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.first.getBus(item) + '</td>';
                        calculation.calc.netto.first.appendData('bus_g_d', td);
                    });
                    //NETTO
                    $.each(calculation.calc.netto.first.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.first.getNetto(item) + '</td>';
                        calculation.calc.netto.first.appendData('netto', td);
                    });
                    //NETTO- EURO
                    $.each(calculation.calc.netto.first.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.first.getNettoEuro(item) + '</td>';
                        markupValue = parseFloat($('#mark_up').val());
                        td_brutto = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.first.getNettoEuro(item) + markupValue) + '</td>';
                        calculation.calc.netto.first.appendData('netto_euro', td);
                        tdWithAdditional = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.first.getNettoEuro(item)+calculation.calc.getAdditionalSum(item.pax + item.pax_free)).toFixed(2) + '</td>';
                        tdWithAdditionalBrutto = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.first.getNettoEuro(item)+calculation.calc.getAdditionalSum(item.pax + item.pax_free)+ markupValue).toFixed(2) + '</td>';
                        console.log(calculation.calc.netto.first.getNettoEuro(item)+calculation.calc.getAdditionalSum(item.pax + item.pax_free)+ markupValue);
                        console.log(calculation.calc.netto.first.getNettoEuro(item),calculation.calc.getAdditionalSum(item.pax + item.pax_free), markupValue);
                        calculation.calc.netto.summary.appendData('netto_euro', tdWithAdditional);
                        calculation.calc.netto.summary.appendData('mark_up', '<td>' + markupValue + '</td>');
                        calculation.calc.netto.summary.appendData('brutto', tdWithAdditionalBrutto);
                    });
                    //active
                    $.each(calculation.calc.netto.first.configuration , function (i, item) {
                        calcObject = calculation.calc.netto.summary.getCalculationObjectByPerson(item.pax + '+' + item.pax_free);
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '"><input type="checkbox" value="'+ item.pax+ '+' + item.pax_free +'" '+calculation.calc.netto.summary.getChecked(item)+' ></td>';

                        calculation.calc.netto.summary.appendData('active', td);
                    });
                    calculation.calc.netto.first.data = data;
                },
                appendData: function (row, data) {
                    row = calculation.calc.netto.first.dom.find('tbody tr[data-row='+ row +']');
                    row.append(data);
                },
                clearData : function () {
                    calculation.calc.netto.first.dom.find('tbody tr').html('');
                },
                getMeals : function (item) {
                    meals = calculation.calc.getValue('combined_sum', 'lunch');
                    result =  (meals + meals/item.pax) ;
                    if (item.pax_free == 1 && item.pax != 10) {
                        result = meals;
                    }

                    return result.toFixed(2);
                },
                getPackage : function (item) {
                    hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                    return (hotel).toFixed(2) ;
                },
                getFOC : function (item) {
                    result = 0;
                    if (item.pax_free === 1) {
						dfsSuppl = calculation.calc.getValue('quotation_sum', 'dfs_suppl');
						
                        sglSuppl = calculation.calc.getValue('quotation_sum', 'sgl_suppl');
                        dinner = calculation.calc.getValue('combined_sum', 'dinner');
                        result = (sglSuppl+dfsSuppl + dinner) / item.pax;
                        if (item.pax == 25) {
                            result = sglSuppl / 25;
                        }

                        if (item.pax == 20) {
                            result = sglSuppl/ 20;
                        }
                        if (item.pax == 15) {
                            hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                            result = (sglSuppl+dfsSuppl + hotel) / 15;
                        }
                        if (item.pax == 10) {
                            hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                            result = (sglSuppl+dfsSuppl + hotel) / 10;
                        }
                    }
                    if (item.pax_free === 2) {
                        entrance = calculation.calc.getValue('quotation_sum', 'entrance');
                        sglSuppl = calculation.calc.getValue('quotation_sum', 'sgl_suppl');
						dfsSuppl = calculation.calc.getValue('quotation_sum', 'dfs_suppl');
                        porterage = calculation.calc.getValue('quotation_sum', 'porterage');
                        result = (entrance + sglSuppl + porterage+dfsSuppl) / item.pax;
                        if (item.pax == 35) {
                            hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                            result = (sglSuppl + hotel+dfsSuppl) / 35;
                        }
                        if (item.pax == 30) {
                            hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                            result = (sglSuppl + hotel+dfsSuppl) / 30;
                        }

                    }

                    return result.toFixed(2);
                },
                getBus : function (item) {
                    localgd = calculation.calc.getValue('combined_sum', 'local_g_d');
                    return (localgd / item.pax).toFixed(2) ;
                },
                getNetto : function (item) {
                    $meals = parseFloat(this.getMeals(item));
                    $package = parseFloat(this.getPackage(item));
                    $foc = parseFloat(this.getFOC(item));
                    $bus = parseFloat(this.getBus(item));
                    return ($meals + $package + $foc + $bus).toFixed(2) ;
                },
                getNettoEuro : function (item) {
                    netto = parseFloat(this.getNetto(item));
                    return Math.round(netto);
                },

            },
            second : {
                dom : $('#netto_second'),
                configuration : [
                    {
                        pax : 5,
                        pax_free : 1
                    },
                    {
                        pax : 6,
                        pax_free : 1
                    },
                    {
                        pax : 7,
                        pax_free : 1
                    },
                    {
                        pax : 8,
                        pax_free : 1
                    },
                    {
                        pax : 9,
                        pax_free : 1
                    },

                    {
                        pax : 10,
                        pax_free : 1
                    },
                    {
                        pax : 11,
                        pax_free : 1
                    },
                    {
                        pax : 12,
                        pax_free : 1
                    },
                    {
                        pax : 13,
                        pax_free : 1
                    },
                    {
                        pax : 14,
                        pax_free : 1
                    },
                    {
                        pax : 15,
                        pax_free : 1
                    },
                    {
                        pax : 16,
                        pax_free : 1
                    },
                    {
                        pax : 17,
                        pax_free : 1
                    },
                    {
                        pax : 18,
                        pax_free : 1
                    },
                    {
                        pax : 19,
                        pax_free : 1
                    },
                    {
                        pax : 20,
                        pax_free : 1
                    },
                ],
                build : function () {
                    calculation.calc.netto.second.clearData();
                    calculation.calc.netto.second.appendData('free', '<td>FREE</td>');
                    calculation.calc.netto.second.appendData('person', '<td>Person</td>');
                    calculation.calc.netto.second.appendData('meals', '<td>Meals</td>');
                    calculation.calc.netto.second.appendData('package', '<td>Package</td>');
                    calculation.calc.netto.second.appendData('foc', ' <td>F.O.C</td>');
                    calculation.calc.netto.second.appendData('bus_g_d', '<td>BUS.G/D - p.p</td>');
                    calculation.calc.netto.second.appendData('netto', '<td>Netto</td>');
                    calculation.calc.netto.second.appendData('netto_euro', '<td>Netto EURO</td>');
                    // FREE
                    $.each(calculation.calc.netto.second.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + item.pax_free + '</td>';
                        calculation.calc.netto.second.appendData('free', td);
                    });
                    // Person
                    $.each(calculation.calc.netto.second.configuration , function (i, item) {
                        exist = calculation.calc.netto.first.configuration.filter(function(secondItem){
                            return item.pax == secondItem.pax;
                        });

                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + item.pax + ' + ' + item.pax_free + '</td>';
                        if(exist.length == 0) {
                            calculation.calc.netto.summary.appendAllData('person', td);
                        }
                        calculation.calc.netto.second.appendData('person', td);
                    });
                    //Meals
                    $.each(calculation.calc.netto.second.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.second.getMeals(item) + '</td>';
                        calculation.calc.netto.second.appendData('meals', td);
                    });
                    //Package
                    $.each(calculation.calc.netto.second.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.second.getPackage(item) + '</td>';
                        calculation.calc.netto.second.appendData('package', td);
                    });
                    //FOC
                    $.each(calculation.calc.netto.second.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.second.getFOC(item) + '</td>';
                        calculation.calc.netto.second.appendData('foc', td);
                    });
                    //BUS
                    $.each(calculation.calc.netto.second.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.second.getBus(item) + '</td>';
                        calculation.calc.netto.second.appendData('bus_g_d', td);
                    });
                    //NETTO
                    $.each(calculation.calc.netto.second.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.second.getNetto(item) + '</td>';
                        calculation.calc.netto.second.appendData('netto', td);
                    });
                    //NETTO- EURO
                    $.each(calculation.calc.netto.second.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.second.getNettoEuro(item) + '</td>';
                        markupValue = parseFloat($('#mark_up').val());
                        td_brutto = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.second.getNettoEuro(item) + markupValue) + '</td>';
                        calculation.calc.netto.second.appendData('netto_euro', td);
                        exist = calculation.calc.netto.first.configuration.filter(function(secondItem){
                            return item.pax == secondItem.pax;
                        });
                        if(exist.length == 0) {
                            tdWithAdditional = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.second.getNettoEuro(item)+calculation.calc.getAdditionalSum(item.pax + item.pax_free)).toFixed(2) + '</td>';
                            tdWithAdditionalBrutto = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.second.getNettoEuro(item)+calculation.calc.getAdditionalSum(item.pax + item.pax_free)+ markupValue).toFixed(2) + '</td>';
                            calculation.calc.netto.summary.appendAllData('mark_up', '<td>' + markupValue + '</td>');
                            calculation.calc.netto.summary.appendAllData('netto_euro', tdWithAdditional);
                            calculation.calc.netto.summary.appendAllData('brutto', tdWithAdditionalBrutto);
                        }

                    });
                    //active
                    $.each(calculation.calc.netto.second.configuration , function (i, item) {
                        exist = calculation.calc.netto.first.configuration.filter(function(secondItem){
                            return item.pax == secondItem.pax;
                        });
                        if(exist.length == 0) {
                            calcObject = calculation.calc.netto.summary.getCalculationObjectByPerson(item.pax + '+' + item.pax_free);
                            td = '<td data-person="' + item.pax + '+' + item.pax_free + '"><input type="checkbox" value="' + item.pax + '+' + item.pax_free + '" ' + calculation.calc.netto.summary.getChecked(item) + ' ></td>';
                            // data[i]['active'] = calculation.calc.netto.first.getNetto(item);
                            calculation.calc.netto.summary.appendAllData('active', td);
                        }
                    });
                },
                appendData: function (row, data) {
                    row = calculation.calc.netto.second.dom.find('tbody tr[data-row='+ row +']');
                    row.append(data);
                },
                clearData : function () {
                    calculation.calc.netto.second.dom.find('tbody tr').html('');
                },
                getMeals : function (item) {
                    meals = calculation.calc.getValue('combined_sum', 'lunch');
                    result =  (meals + meals/item.pax) ;
                    if (item.pax_free == 1) {
                        if (item.pax == 15 || item.pax == 16 || item.pax == 17 || item.pax == 18 || item.pax == 19 || item.pax == 20) {
                            result = meals;
                        }

                    }

                    return result.toFixed(2);
                },
                getPackage : function (item) {
                    hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                    return (hotel).toFixed(2) ;
                },
                getFOC : function (item) {
                    result = 0;
					
						dfsSuppl = calculation.calc.getValue('quotation_sum', 'dfs_suppl');		
                        sglSuppl = calculation.calc.getValue('quotation_sum', 'sgl_suppl');
                         dinner = calculation.calc.getValue('combined_sum', 'dinner');
					
                        result = (sglSuppl + dfsSuppl + dinner) / item.pax;
                        if ([10, 11, 12, 13, 14, 15, 16, 17, 18, 19].includes(item.pax)) {

                            hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                            result = (sglSuppl+ dfsSuppl + hotel) / item.pax;
                        }
                        if (item.pax == 20) {
                            result = (sglSuppl+ dfsSuppl) / item.pax;
                        }

                    return result.toFixed(2);
                },
                getBus : function (item) {
                    localgd = calculation.calc.getValue('combined_sum', 'local_g_d');
                    return (localgd / item.pax).toFixed(2) ;
                },
                getNetto : function (item) {
                    $meals = parseFloat(this.getMeals(item));
                    $package = parseFloat(this.getPackage(item));
                    $foc = parseFloat(this.getFOC(item));
                    $bus = parseFloat(this.getBus(item));
                    return ($meals + $package + $foc + $bus).toFixed(2) ;
                },
                getNettoEuro : function (item) {
                    netto = parseFloat(this.getNetto(item));
                    return Math.round(netto);
                },
            },
            third : {
                dom : $('#netto_third'),
                configuration : [
                    {
                        pax : 21,
                        pax_free : 1
                    },
                    {
                        pax : 22,
                        pax_free : 1
                    },
                    {
                        pax : 23,
                        pax_free : 1
                    },
                    {
                        pax : 24,
                        pax_free : 1
                    },
                    {
                        pax : 25,
                        pax_free : 1
                    },

                    {
                        pax : 26,
                        pax_free : 1
                    },
                    {
                        pax : 27,
                        pax_free : 1
                    },
                    {
                        pax : 28,
                        pax_free : 1
                    },
                    {
                        pax : 29,
                        pax_free : 1
                    },
                    {
                        pax : 30,
                        pax_free : 2
                    },
                    {
                        pax : 31,
                        pax_free : 2
                    },
                    {
                        pax : 32,
                        pax_free : 2
                    },
                    {
                        pax : 33,
                        pax_free : 2
                    },
                    {
                        pax : 34,
                        pax_free : 2
                    },
                    {
                        pax : 35,
                        pax_free : 2
                    },
                    {
                        pax : 36,
                        pax_free : 2
                    },
                ],
                build : function () {
                    calculation.calc.netto.third.clearData();

                    calculation.calc.netto.third.appendData('free', '<td>FREE</td>');
                    calculation.calc.netto.third.appendData('person', '<td>Person</td>');
                    calculation.calc.netto.third.appendData('meals', '<td>Meals</td>');
                    calculation.calc.netto.third.appendData('package', '<td>Package</td>');
                    calculation.calc.netto.third.appendData('foc', ' <td>F.O.C</td>');
                    calculation.calc.netto.third.appendData('bus_g_d', '<td>BUS.G/D - p.p</td>');
                    calculation.calc.netto.third.appendData('netto', '<td>Netto</td>');
                    calculation.calc.netto.third.appendData('netto_euro', '<td>Netto EURO</td>');
                    // FREE
                    $.each(calculation.calc.netto.third.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + item.pax_free + '</td>';
                        calculation.calc.netto.third.appendData('free', td);
                    });
                    // Person
                    $.each(calculation.calc.netto.third.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + item.pax + ' + ' + item.pax_free + '</td>';
                        calculation.calc.netto.third.appendData('person', td);
                        exist = calculation.calc.netto.first.configuration.filter(function(secondItem){
                            return item.pax == secondItem.pax;
                        });
                        if(exist.length == 0) {
                            calculation.calc.netto.summary.appendAllData('person', td);
                        }
                    });
                    //Meals
                    $.each(calculation.calc.netto.third.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.third.getMeals(item) + '</td>';
                        calculation.calc.netto.third.appendData('meals', td);
                    });
                    //Package
                    $.each(calculation.calc.netto.third.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.third.getPackage(item) + '</td>';
                        calculation.calc.netto.third.appendData('package', td);
                    });
                    //FOC
                    $.each(calculation.calc.netto.third.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.third.getFOC(item) + '</td>';
                        calculation.calc.netto.third.appendData('foc', td);
                    });
                    //BUS
                    $.each(calculation.calc.netto.third.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.third.getBus(item) + '</td>';
                        calculation.calc.netto.third.appendData('bus_g_d', td);
                    });
                    //NETTO
                    $.each(calculation.calc.netto.third.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.third.getNetto(item) + '</td>';
                        calculation.calc.netto.third.appendData('netto', td);
                    });
                    //NETTO- EURO
                    $.each(calculation.calc.netto.third.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.third.getNettoEuro(item) + '</td>';
                        markupValue = parseFloat($('#mark_up').val());
                        td_brutto = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.third.getNettoEuro(item) + markupValue) + '</td>';
                        calculation.calc.netto.third.appendData('netto_euro', td);
                        exist = calculation.calc.netto.first.configuration.filter(function(secondItem){
                            return item.pax == secondItem.pax;
                        });
                        if(exist.length == 0) {
                            tdWithAdditional = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.third.getNettoEuro(item)+calculation.calc.getAdditionalSum(item.pax + item.pax_free)).toFixed(2) + '</td>';
                            tdWithAdditionalBrutto = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.third.getNettoEuro(item)+calculation.calc.getAdditionalSum(item.pax + item.pax_free)+ markupValue).toFixed(2) + '</td>';
                            calculation.calc.netto.summary.appendAllData('mark_up', '<td>' + markupValue + '</td>');
                            calculation.calc.netto.summary.appendAllData('netto_euro', tdWithAdditional);
                            calculation.calc.netto.summary.appendAllData('brutto', tdWithAdditionalBrutto);
                        }
                    });
                    $.each(calculation.calc.netto.third.configuration , function (i, item) {
                        calcObject = calculation.calc.netto.summary.getCalculationObjectByPerson(item.pax + '+' + item.pax_free);
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '"><input type="checkbox" value="'+ item.pax+ '+' + item.pax_free +'" '+calculation.calc.netto.summary.getChecked(item)+' ></td>';
                        // data[i]['active'] = calculation.calc.netto.first.getNetto(item);
                        exist = calculation.calc.netto.first.configuration.filter(function(secondItem){
                            return item.pax == secondItem.pax;
                        });
                        if(exist.length == 0) {
                            calculation.calc.netto.summary.appendAllData('active', td);
                        }
                    });
                },
                appendData: function (row, data) {
                    row = calculation.calc.netto.third.dom.find('tbody tr[data-row='+ row +']');
                    row.append(data);
                },
                clearData : function () {
                    calculation.calc.netto.third.dom.find('tbody tr').html('');
                },
                getMeals : function (item) {
                    meals = calculation.calc.getValue('combined_sum', 'lunch');
                    result =  (meals) ;
                    if (item.pax_free == 2) {
                       result = meals + meals / item.pax;
                    }

                    return result.toFixed(2);
                },
                getPackage : function (item) {
                    hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                    return (hotel).toFixed(2) ;
                },
                getFOC : function (item) {
                    result = 0;
					dfsSuppl = calculation.calc.getValue('quotation_sum', 'dfs_suppl');
                    sglSuppl = calculation.calc.getValue('quotation_sum', 'sgl_suppl');
                    hotel = calculation.calc.getValue('combined_sum', 'htlpp');

                    result = (sglSuppl+ dfsSuppl) / item.pax;
                    if (item.pax_free == 2) {
                        result = (sglSuppl+ dfsSuppl + hotel) / item.pax;
                    }

                    return result.toFixed(2);
                },
                getBus : function (item) {
                    localgd = calculation.calc.getValue('combined_sum', 'local_g_d');
                    return (localgd / item.pax).toFixed(2) ;
                },
                getNetto : function (item) {
                    $meals = parseFloat(this.getMeals(item));
                    $package = parseFloat(this.getPackage(item));
                    $foc = parseFloat(this.getFOC(item));
                    $bus = parseFloat(this.getBus(item));
                    return ($meals + $package + $foc + $bus).toFixed(2) ;
                },
                getNettoEuro : function (item) {
                    netto = parseFloat(this.getNetto(item));
                    return Math.round(netto);
                },
            },
            fourth : {
                dom : $('#netto_fourth'),
                configuration : [
                    {
                        pax : 37,
                        pax_free : 2
                    },
                    {
                        pax : 38,
                        pax_free : 2
                    },
                    {
                        pax : 39,
                        pax_free : 2
                    },
                    {
                        pax : 40,
                        pax_free : 2
                    },
                    {
                        pax : 41,
                        pax_free : 2
                    },

                    {
                        pax : 42,
                        pax_free : 2
                    },
                    {
                        pax : 43,
                        pax_free : 2
                    },
                    {
                        pax : 44,
                        pax_free : 2
                    },
                    {
                        pax : 45,
                        pax_free : 2
                    },
                    {
                        pax : 46,
                        pax_free : 2
                    },
                    {
                        pax : 47,
                        pax_free : 2
                    },
                    {
                        pax : 48,
                        pax_free : 2
                    },
                    {
                        pax : 49,
                        pax_free : 2
                    },
                    {
                        pax : 50,
                        pax_free : 2
                    },
                    {
                        pax : 45,
                        pax_free : 3
                    },
                    {
                        pax : 46,
                        pax_free : 3
                    },
                    {
                        pax : 47,
                        pax_free : 3
                    },
                    {
                        pax : 48,
                        pax_free : 3
                    },
                    {
                        pax : 49,
                        pax_free : 3
                    },
                ],
                build : function () {
                    calculation.calc.netto.fourth.clearData();
                    calculation.calc.netto.fourth.appendData('free', '<td>FREE</td>');
                    calculation.calc.netto.fourth.appendData('person', '<td>Person</td>');
                    calculation.calc.netto.fourth.appendData('meals', '<td>Meals</td>');
                    calculation.calc.netto.fourth.appendData('package', '<td>Package</td>');
                    calculation.calc.netto.fourth.appendData('foc', ' <td>F.O.C</td>');
                    calculation.calc.netto.fourth.appendData('bus_g_d', '<td>BUS.G/D - p.p</td>');
                    calculation.calc.netto.fourth.appendData('netto', '<td>Netto</td>');
                    calculation.calc.netto.fourth.appendData('netto_euro', '<td>Netto EURO</td>');
                    // FREE
                    $.each(calculation.calc.netto.fourth.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + item.pax_free + '</td>';
                        calculation.calc.netto.fourth.appendData('free', td);
                    });
                    // Person
                    $.each(calculation.calc.netto.fourth.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + item.pax + ' + ' + item.pax_free + '</td>';
                        exist = calculation.calc.netto.first.configuration.filter(function(secondItem){
                            return item.pax == secondItem.pax;
                        });
                        if(exist.length == 0) {
                            calculation.calc.netto.summary.appendAllData('person', td);
                        }
                        calculation.calc.netto.fourth.appendData('person', td);
                    });
                    //Meals
                    $.each(calculation.calc.netto.fourth.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.fourth.getMeals(item) + '</td>';
                        calculation.calc.netto.fourth.appendData('meals', td);
                    });
                    //Package
                    $.each(calculation.calc.netto.fourth.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.fourth.getPackage(item) + '</td>';
                        calculation.calc.netto.fourth.appendData('package', td);
                    });
                    //FOC
                    $.each(calculation.calc.netto.fourth.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.fourth.getFOC(item) + '</td>';
                        calculation.calc.netto.fourth.appendData('foc', td);
                    });
                    //BUS
                    $.each(calculation.calc.netto.fourth.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.fourth.getBus(item) + '</td>';
                        calculation.calc.netto.fourth.appendData('bus_g_d', td);
                    });
                    //NETTO
                    $.each(calculation.calc.netto.fourth.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.fourth.getNetto(item) + '</td>';
                        calculation.calc.netto.fourth.appendData('netto', td);
                    });
                    //NETTO- EURO
                    $.each(calculation.calc.netto.fourth.configuration , function (i, item) {
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + calculation.calc.netto.fourth.getNettoEuro(item) + '</td>';
                        calculation.calc.netto.fourth.appendData('netto_euro', td);
                        markupValue = parseFloat($('#mark_up').val());
                        td_brutto = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.fourth.getNettoEuro(item) + markupValue) + '</td>';
                        exist = calculation.calc.netto.first.configuration.filter(function(secondItem){
                            return item.pax == secondItem.pax;
                        });
                        if(exist.length == 0) {
                            tdWithAdditional = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.third.getNettoEuro(item)+calculation.calc.getAdditionalSum(item.pax + item.pax_free)).toFixed(2) + '</td>';
                            tdWithAdditionalBrutto = '<td data-person="'+item.pax + '+' + item.pax_free + '">' + (calculation.calc.netto.third.getNettoEuro(item)+calculation.calc.getAdditionalSum(item.pax + item.pax_free)+ markupValue).toFixed(2) + '</td>';
                            calculation.calc.netto.summary.appendAllData('mark_up', '<td>' + markupValue + '</td>');
                            calculation.calc.netto.summary.appendAllData('netto_euro', tdWithAdditional);
                            calculation.calc.netto.summary.appendAllData('brutto', tdWithAdditionalBrutto);
                        }
                    });
                    $.each(calculation.calc.netto.fourth.configuration , function (i, item) {
                        calcObject = calculation.calc.netto.summary.getCalculationObjectByPerson(item.pax + '+' + item.pax_free);
                        td = '<td data-person="'+item.pax + '+' + item.pax_free + '"><input type="checkbox" value="'+ item.pax+ '+' + item.pax_free +'" '+calculation.calc.netto.summary.getChecked(item)+' ></td>';
                        // data[i]['active'] = calculation.calc.netto.first.getNetto(item);
                        exist = calculation.calc.netto.first.configuration.filter(function(secondItem){
                            return item.pax == secondItem.pax;
                        });
                        if(exist.length == 0) {
                            calculation.calc.netto.summary.appendAllData('active', td);
                        }
                    });
                },
                appendData: function (row, data) {
                    row = calculation.calc.netto.fourth.dom.find('tbody tr[data-row='+ row +']');
                    row.append(data);
                },
                clearData : function () {
                    calculation.calc.netto.fourth.dom.find('tbody tr').html('');
                },
                getMeals : function (item) {
                    meals = calculation.calc.getValue('combined_sum', 'lunch');
                    result =  meals + meals / item.pax * 2 ;
                    if (item.pax_free == 2) {
                        result = meals + meals / item.pax;
                    }

                    return result.toFixed(2);
                },
                getPackage : function (item) {
                    hotel = calculation.calc.getValue('combined_sum', 'htlpp');
                    return (hotel).toFixed(2) ;
                },
                getFOC : function (item) {
                    result = 0;
					dfsSuppl = calculation.calc.getValue('quotation_sum', 'dfs_suppl');
                    sglSuppl = calculation.calc.getValue('quotation_sum', 'sgl_suppl');
                    entrance_porterage = calculation.calc.getValue('combined_sum', 'entrance_porterage');
                    result = (sglSuppl+ dfsSuppl + entrance_porterage) / item.pax;
                    if ([37, 38, 39].includes( item.pax) || item.pax_free == 3) {
                        result = (sglSuppl+ dfsSuppl + hotel) / item.pax;
                    }

                    return result.toFixed(2);
                },
                getBus : function (item) {
                    localgd = calculation.calc.getValue('combined_sum', 'local_g_d');
                    return (localgd / item.pax).toFixed(2) ;
                },
                getNetto : function (item) {
                    $meals = parseFloat(this.getMeals(item));
                    $package = parseFloat(this.getPackage(item));
                    $foc = parseFloat(this.getFOC(item));
                    $bus = parseFloat(this.getBus(item));
                    return ($meals + $package + $foc + $bus).toFixed(2) ;
                },
                getNettoEuro : function (item) {
                    netto = parseFloat(this.getNetto(item));
                    return Math.round(netto);
                },
            },
            summary : {
                dom : $('#summary'),
                domAll : $('#summary_all'),
                appendData: function (row, data) {
                    row = calculation.calc.netto.summary.dom.find('tbody tr[data-row='+ row +']');
                    row.append(data);
                },
                getCalculationObjectByPerson : function (person) {
                    var array = $.map(calculationArray, function(value, index) {
                        return [value];
                    });
                    returnValue = null;
                    $.each(array, function(i, item){
                        if(item.person == person) {
                            returnValue= item;
                        }
                    });
                    return returnValue;
                },
                appendAllData: function (row, data) {
                    row = calculation.calc.netto.summary.domAll.find('tbody tr[data-row='+ row +']');
                    row.append(data);
                },
                clearData : function () {
                    calculation.calc.netto.summary.dom.find('tbody tr').html('');
                    calculation.calc.netto.summary.domAll.find('tbody tr').html('');
                },
                getChecked : function(item) {
                    calcObject = calculation.calc.netto.summary.getCalculationObjectByPerson(item.pax + ' + ' + item.pax_free);
                    if (calcObject && calcObject.activity == "1") {
                        checked = 'checked';
                    } else {
                        checked = '';
                    }
                    return checked;
                }
            }

        },

        setQuotationRowSum : function (rowName, resultCell) {
            sum = quotation.getColumnSum(rowName);
            calculation.calc.setValue('quotation_sum', resultCell, sum);
        },
        rate : function() {
            return parseFloat($('#rate').val());
        }
    }

};


let quotationColumn = {
    config : {
    },
    init : function() {
        console.log('init');
        quotationColumn.bind();
    },
    bind : () => {
        $('body').on('click', '#add_quotation_column', function(){
                quotationColumn.functions.addColumn();
                $('#myModal').modal('hide');
        });
        $('body').on('click', '.remove-quotation-column', function(){
            quotationColumn.functions.removeColumn($(this));
        });
    },
    functions : {
        addColumn : function() {
            let columnName = $('#quotation_column_name').val();
            let columnType = $('#quotation_column_type').val();
            typeClass = '';
            if (columnType =='all') {
                typeClass = 'quotation-cell-general'
            }
            if (columnType =='person') {
                typeClass = 'quotation-cell-per-person'
            }
            if ($('th[data-column="'+columnName+'"]').length > 0) {
                alert('Column with this name already exists');
                return false;
            }
            if (columnName.trim().length != 0) {
                $('#quotation_table thead tr').append('<th data-column="' + columnName + '" data-type="'+columnType+'" class="additional-column">' + columnName + '<i class="fa fa-times pull-right remove-quotation-column"></i></th>');
                $(quotation.config.rows).each(function(i, item){
                    $('#quotation_table tr[data-row='+item+']')
                        .append('<td data-column="' + columnName+ '" class="additional-cell '+typeClass+'" >0</td>');
                    $('#quotation_table tr[data-row='+item+'] td[data-column="'+columnName+'"]').on('click', function(){
                        if (cell.row != $(this).parent().data('row') || cell.column != $(this).data('column')) {
                            cell = new quotation.cell($(this));
                            cell.dom.addClass('activeCell');
                            quotation.editCell(cell);
                        }
                        $('#form_value').focus();
                        $('#form_value').select();
                    });
                });
            }
        },
        removeColumn : function(col) {
            col = col.parent();
            let columnName = col.data('column');
            if (confirm('Are you sure want to remove this column?')) {
               col.remove();
                calculation.calculate();
                $('td.additional-cell[data-column="'+columnName+'"]').remove();
            }


        }
    }
};

$(document).ready(function() {
    quotation.init();
    calculation.init();
    quotationColumn.init();
    $('.saved').on('click', function(){
            $('.saved').prop('disabled', true);            
            quotation.saveQuotation();
        });
});
