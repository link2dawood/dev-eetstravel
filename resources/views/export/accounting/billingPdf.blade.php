@if(isset($isDoc) and $isDoc)
<?php
header("Content-Type: application/vnd.ms-word");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=". $download_name .".doc");
?>
@endif

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <!-- Bootstrap -->
	
    <style>
        /*!
         * Bootstrap v3.3.7 (http://getbootstrap.com)
         * Copyright 2011-2016 Twitter, Inc.
         * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
         *//*! normalize.css v3.0.3 | MIT License | github.com/necolas/normalize.css */
        html {
            font-family: sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%
        }

        body {
            margin: 0;
        }
        table {
            width: 100%;
            margin-bottom: 30px;
        }

        tr {
            border: 1px solid #c0c0c0;
        }

        h4 {
            padding-bottom: 25px;
        }

        table.border td {
            border: 1px solid #c0c0c0;
            padding: 2px;
        }

        li {
            list-style-type: none;
        }

        thead {
            text-align: center;
        }

        ul.quotations {
            margin-top: 0px;
        }

        ul.quotations li {
            margin-top: 5px;
        }

        .border_bottom_gray {
            border-bottom: #c0c0c0 1px solid;
        }

        .red {
            color: red;
        }
        .page-break{
            page-break-after: always;
            margin-top: 30px;
        }

        .column{
            width: 50%;

        }
        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box
        }

        :after, :before {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box
        }

        html {
            font-size: 10px;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0)
        }

        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.42857143;
            color: #333;
            background-color: #fff
        }

        button, input, select, textarea {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit
        }

        a {
            color: #337ab7;
            text-decoration: none
        }

        a:focus, a:hover {
            color: #23527c;
            text-decoration: underline
        }

        a:focus {
            outline: 5px auto -webkit-focus-ring-color;
            outline-offset: -2px
        }
        figure {
            margin: 0
        }

        img {
            vertical-align: middle
        }

        [role=button] {
            cursor: pointer
        }

        .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
            font-family: inherit;
            font-weight: 500;
            line-height: 1.1;
            color: inherit
        }

        .h1 .small, .h1 small, .h2 .small, .h2 small, .h3 .small, .h3 small, .h4 .small, .h4 small, .h5 .small, .h5 small, .h6 .small, .h6 small, h1 .small, h1 small, h2 .small, h2 small, h3 .small, h3 small, h4 .small, h4 small, h5 .small, h5 small, h6 .small, h6 small {
            font-weight: 400;
            line-height: 1;
            color: #777
        }

        .h1, .h2, .h3, h1, h2, h3 {
            margin-top: 20px;
            margin-bottom: 10px
        }

        .h1 .small, .h1 small, .h2 .small, .h2 small, .h3 .small, .h3 small, h1 .small, h1 small, h2 .small, h2 small, h3 .small, h3 small {
            font-size: 65%
        }

        .h4, .h5, .h6, h4, h5, h6 {
            margin-top: 10px;
            margin-bottom: 10px
        }

        .h4 .small, .h4 small, .h5 .small, .h5 small, .h6 .small, .h6 small, h4 .small, h4 small, h5 .small, h5 small, h6 .small, h6 small {
            font-size: 75%
        }

        .h1, h1 {
            font-size: 36px
        }

        .h2, h2 {
            font-size: 30px
        }

        .h3, h3 {
            font-size: 24px
        }

        .h4, h4 {
            font-size: 18px
        }

        .h5, h5 {
            font-size: 14px
        }

        .h6, h6 {
            font-size: 12px
        }

        p {
            margin: 0 0 10px
        }

        .lead {
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: 300;
            line-height: 1.4
        }
        .container-fluid {
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: 15px;
            max-width: 220mm;
            text-align: left;
        }

        .row {
            margin-right: -15px;
            margin-left: -15px
        }

        .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9 {
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px
        }
        .footer_headings{
            margin-top : 0px;
        }

        @media (min-width: 768px) {
            .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9 {
                float: left
            }

            .col-sm-12 {
                width: 100%
            }

            .col-sm-11 {
                width: 91.66666667%
            }

            .col-sm-10 {
                width: 83.33333333%
            }

            .col-sm-9 {
                width: 75%
            }

            .col-sm-8 {
                width: 66.66666667%
            }

            .col-sm-7 {
                width: 58.33333333%
            }

            .col-sm-6 {
                float: left;
                width: 50%
            }

            .col-sm-5 {
                width: 41.66666667%
            }

            .col-sm-4 {
                width: 33.33333333%
            }

            .col-sm-3 {
                width: 25%
            }

            .col-sm-2 {
                width: 16.66666667%
            }

            .col-sm-1 {
                width: 8.33333333%
            }

            .col-sm-pull-12 {
                right: 100%
            }

            .col-sm-pull-11 {
                right: 91.66666667%
            }

            .col-sm-pull-10 {
                right: 83.33333333%
            }

            .col-sm-pull-9 {
                right: 75%
            }

            .col-sm-pull-8 {
                right: 66.66666667%
            }

            .col-sm-pull-7 {
                right: 58.33333333%
            }

            .col-sm-pull-6 {
                right: 50%
            }

            .col-sm-pull-5 {
                right: 41.66666667%
            }

            .col-sm-pull-4 {
                right: 33.33333333%
            }

            .col-sm-pull-3 {
                right: 25%
            }

            .col-sm-pull-2 {
                right: 16.66666667%
            }

            .col-sm-pull-1 {
                right: 8.33333333%
            }

            .col-sm-pull-0 {
                right: auto
            }

            .col-sm-push-12 {
                left: 100%
            }

            .col-sm-push-11 {
                left: 91.66666667%
            }

            .col-sm-push-10 {
                left: 83.33333333%
            }

            .col-sm-push-9 {
                left: 75%
            }

            .col-sm-push-8 {
                left: 66.66666667%
            }

            .col-sm-push-7 {
                left: 58.33333333%
            }

            .col-sm-push-6 {
                left: 50%
            }

            .col-sm-push-5 {
                left: 41.66666667%
            }

            .col-sm-push-4 {
                left: 33.33333333%
            }

            .col-sm-push-3 {
                left: 25%
            }

            .col-sm-push-2 {
                left: 16.66666667%
            }

            .col-sm-push-1 {
                left: 8.33333333%
            }

            .col-sm-push-0 {
                left: auto
            }

            .col-sm-offset-12 {
                margin-left: 100%
            }

            .col-sm-offset-11 {
                margin-left: 91.66666667%
            }

            .col-sm-offset-10 {
                margin-left: 83.33333333%
            }

            .col-sm-offset-9 {
                margin-left: 75%
            }

            .col-sm-offset-8 {
                margin-left: 66.66666667%
            }

            .col-sm-offset-7 {
                margin-left: 58.33333333%
            }

            .col-sm-offset-6 {
                margin-left: 50%
            }

            .col-sm-offset-5 {
                margin-left: 41.66666667%
            }

            .col-sm-offset-4 {
                margin-left: 33.33333333%
            }

            .col-sm-offset-3 {
                margin-left: 25%
            }

            .col-sm-offset-2 {
                margin-left: 16.66666667%
            }

            .col-sm-offset-1 {
                margin-left: 8.33333333%
            }

            .col-sm-offset-0 {
                margin-left: 0
            }
        }

        @media screen and (min-width: 768px) {
            .carousel-control .glyphicon-chevron-left, .carousel-control .glyphicon-chevron-right, .carousel-control .icon-next, .carousel-control .icon-prev {
                width: 30px;
                height: 30px;
                margin-top: -10px;
                font-size: 30px
            }

            .carousel-control .glyphicon-chevron-left, .carousel-control .icon-prev {
                margin-left: -10px
            }

            .carousel-control .glyphicon-chevron-right, .carousel-control .icon-next {
                margin-right: -10px
            }

            .carousel-caption {
                right: 20%;
                left: 20%;
                padding-bottom: 30px
            }

            .carousel-indicators {
                bottom: 20px
            }
        }

        .btn-group-vertical > .btn-group:after, .btn-group-vertical > .btn-group:before, .btn-toolbar:after, .btn-toolbar:before, .clearfix:after, .clearfix:before, .container-fluid:after, .container-fluid:before, .container:after, .container:before, .dl-horizontal dd:after, .dl-horizontal dd:before, .form-horizontal .form-group:after, .form-horizontal .form-group:before, .modal-footer:after, .modal-footer:before, .modal-header:after, .modal-header:before, .nav:after, .nav:before, .navbar-collapse:after, .navbar-collapse:before, .navbar-header:after, .navbar-header:before, .navbar:after, .navbar:before, .pager:after, .pager:before, .panel-body:after, .panel-body:before, .row:after, .row:before {
            display: table;
            content: " "
        }

        .btn-group-vertical > .btn-group:after, .btn-toolbar:after, .clearfix:after, .container-fluid:after, .container:after, .dl-horizontal dd:after, .form-horizontal .form-group:after, .modal-footer:after, .modal-header:after, .nav:after, .navbar-collapse:after, .navbar-header:after, .navbar:after, .pager:after, .panel-body:after, .row:after {
            clear: both
        }

        .center-block {
            display: block;
            margin-right: auto;
            margin-left: auto
        }

        .pull-right {
            float: right !important
        }

        .pull-left {
            float: left !important
        }

        .hide {
            display: none !important
        }

        .show {
            display: block !important
        }

        .invisible {
            visibility: hidden
        }

        .text-hide {
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0
        }

        .hidden {
            display: none !important
        }

        .affix {
            position: fixed
        }
		
        @-ms-viewport {
            width: device-width
        }

        .visible-lg, .visible-md, .visible-sm, .visible-xs {
            display: none !important
        }

        .visible-lg-block, .visible-lg-inline, .visible-lg-inline-block, .visible-md-block, .visible-md-inline, .visible-md-inline-block, .visible-sm-block, .visible-sm-inline, .visible-sm-inline-block, .visible-xs-block, .visible-xs-inline, .visible-xs-inline-block {
            display: none !important
        }

        @media (max-width: 767px) {
            .visible-xs {
                display: block !important
            }

            table.visible-xs {
                display: table !important
            }

            tr.visible-xs {
                display: table-row !important
            }

            td.visible-xs, th.visible-xs {
                display: table-cell !important
            }
        }

        @media (max-width: 767px) {
            .visible-xs-block {
                display: block !important
            }
        }

        @media (max-width: 767px) {
            .visible-xs-inline {
                display: inline !important
            }
        }

        @media (max-width: 767px) {
            .visible-xs-inline-block {
                display: inline-block !important
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .visible-sm {
                display: block !important
            }

            table.visible-sm {
                display: table !important
            }

            tr.visible-sm {
                display: table-row !important
            }

            td.visible-sm, th.visible-sm {
                display: table-cell !important
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .visible-sm-block {
                display: block !important
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .visible-sm-inline {
                display: inline !important
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .visible-sm-inline-block {
                display: inline-block !important
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .visible-md {
                display: block !important
            }

            table.visible-md {
                display: table !important
            }

            tr.visible-md {
                display: table-row !important
            }

            td.visible-md, th.visible-md {
                display: table-cell !important
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .visible-md-block {
                display: block !important
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .visible-md-inline {
                display: inline !important
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .visible-md-inline-block {
                display: inline-block !important
            }
        }

        @media (min-width: 1200px) {
            .visible-lg {
                display: block !important
            }

            table.visible-lg {
                display: table !important
            }

            tr.visible-lg {
                display: table-row !important
            }

            td.visible-lg, th.visible-lg {
                display: table-cell !important
            }
        }

        @media (min-width: 1200px) {
            .visible-lg-block {
                display: block !important
            }
        }

        @media (min-width: 1200px) {
            .visible-lg-inline {
                display: inline !important
            }
        }

        @media (min-width: 1200px) {
            .visible-lg-inline-block {
                display: inline-block !important
            }
        }

        @media (max-width: 767px) {
            .hidden-xs {
                display: none !important
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .hidden-sm {
                display: none !important
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .hidden-md {
                display: none !important
            }
        }

        @media (min-width: 1200px) {
            .hidden-lg {
                display: none !important
            }
        }

        .visible-print {
            display: none !important
        }

        @media print {
            .visible-print {
                display: block !important
            }

            table.visible-print {
                display: table !important
            }

            tr.visible-print {
                display: table-row !important
            }

            td.visible-print, th.visible-print {
                display: table-cell !important
            }
        }

        .visible-print-block {
            display: none !important
        }

        @media print {
            .visible-print-block {
                display: block !important
            }
        }

        .visible-print-inline {
            display: none !important
        }

        @media print {
            .visible-print-inline {
                display: inline !important
            }
        }

        .visible-print-inline-block {
            display: none !important
        }

        @media print {
            .visible-print-inline-block {
                display: inline-block !important
            }
        }

        @media print {
            .hidden-print {
                display: none !important
            }
        }

        /*# sourceMappingURL=bootstrap.min.css.map */

        @page {
            margin: 0px;
        }

        html, body {
            margin: 10px 0;
            display: block;
			height: 100%;
            font-family: DejaVu Sans, sans-serif;
        }

        .text_color {
            color: #666767;
            font-weight: 300;
        }

        .nopadding {
            padding: 0 !important;
            margin: 0 !important;
        }
		

        .hei {
            /*height: 97%;*/
           /* overflow-y: hidden;*/
           
            background-position: right top;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .hei2 {
            /*height: 97%;*/
            overflow-y: hidden;
            background-image: url("{{ asset('img/background2.jpg') }}");
        }

        .container-fluid {
            overflow-y: auto;
        }

        .border_up{
            border-top:2px solid rgb(102, 103, 103);
            width:20px;
        }
        
    </style>

</head>
<body>
    <div class="container-fluid" >
        <table class="row" width="100%">
            <tr>
                <td class="column" >
                    <img style="padding-top: 50px;padding-right: 60px;padding-bottom: 20px; margin-left:50px;" src="{{ public_path() . '/img/eets_logo_small.jpg' }}" />
                </td>
                <td class="column">
                 
                    <h3 class="float-right text_color" style="text-decoration: underline;margin-top: 0px;">{{$office->office_name}}<br>{{$office->office_address}}<br>Tel: {{$office->tel}}<br> Fax : {{$office->fax}}
                        </h2>
                </td>
                
            </tr>
        </table>
        <hr>
        <table class="row" width="100%">
            <tr>
                <td class="column" >
                    <h4 class="text_color  float-left" style="margin-top: 0px;  margin-left:50px;">{{$client->name}}<br>{{$client->address}}<br>
                        </h4>
                </td>
                <td class="column">
                 
                    <h4 class="float-right" style="text-align:right">Date : 2023/May
                        </h4>
                </td>
                
            </tr>
        </table>
        <h1 style="text-align: center">Invoice</h1>
        <h4>Tour Name : {{$tour->name}}
        </h4>
        <hr>
		
        <table width="100%">
            
            <thead>
                <tr>
                    <th></th>
                    <th style="text-align: left">Item</th>
                    <th style="text-align: left">Pax</th>
                    <th style="text-align: left">Price</th>
                    <th style="text-align: left">Total Amount</th>
                </tr>
            </thead>

            <tbody >
               @if(!empty($tourDates))
				@php $count = 0 ; $total = 0;@endphp
                @foreach($tourDates as $tourDate)
                @if(!empty($tourDate->packages))
                @foreach($tourDate->packages as $package)
             
				@if($package->name != "")
				 @php $count++;$overall_price = $package->pax*$package->total_amount;$total = $total + $overall_price;@endphp
                <tr>
                    <td>{{$count}}:</td>
                    <td>{{$package->name??""}}</td>
                    <td >{{$package->pax ??""}}</td>
                    <td >{{$package->total_amount??""}}</td>
                    <td style="text-align: center">{{$overall_price}}</td>
                </tr>
				@endif
                @endforeach
                @endif
                @endforeach
				@endif

            </tbody>
            
        </table>
		 @php $count = 1 ; $total = 0;@endphp
		<table width="100%">
            
            <thead>
                <tr>
                    <th></th>
                    <th style="text-align: left">Item</th>
                    <th style="text-align: left">Pax</th>
                    <th style="text-align: left">Euro</th>
                    <th style="text-align: right ; padding-right:85px; ">Amount</th>
                </tr>
				
            </thead>
			<tbody>
				@if(!empty($calculations))
				 @foreach($calculations as $calc)
				@if(isset($calc['activity']) && $calc['activity'])
				@php $quotation_total = $calc['brutto']* $tour->pax; $total = $total + $quotation_total;@endphp
				<tr>
					<td>1:</td>
					<td>Landpackage</td>
					<td>{{$tour->pax}}</td>
					<td>{{number_format(isset($calc['brutto']) ? $calc['brutto'] : '', 0, '.', ',')}}</td>
					<td style="text-align: right ; padding-right:85px; "> {{ number_format($quotation_total, 0, '.', ',')}}</td>
				</tr>
				@endif
				@endforeach
				@endif
				
			    @foreach($invoice_items as $invoice_item)
				@php $count++; $extra_amount =$invoice_item->quantity * $invoice_item->amount ; $total = $total + $invoice_item->total_amount;
				$tax_amount = $invoice_item->total_amount-$extra_amount;@endphp
				<tr>
                     <td>{{$count}}:</td>
                    <td>{{$invoice_item->item_name}}</td>
                    <td>{{$invoice_item->quantity}}</td>
                    <td>{{$invoice_item->amount}}</td>
                    <td style="text-align: right ; padding-right:85px; "> {{number_format($extra_amount, 0, '.', ',')  ." + ". number_format($tax_amount, 0, '.', ',') ." TAX"}}</td>
                </tr>
				@endforeach
			</tbody>
		</table>
		 <hr>
		 <p style="text-align: right; margin-right: 90px;">
                <span >Total Amount : </span>Euro {{ number_format($total, 0, '.', ',')}}</p>
		<br>
		<br>
		<h2> Confirmed Quotation Prices</h2>
		<hr>
		<table class="border" cellpadding="0" cellspacing="0" style="border: 1px solid #c0c0c0;">

    @if(!empty($calculations))
        <tr>
            @php $tdNumber = 0;
            @endphp
            @foreach($calculations as $calc)
                @if(isset($calc['activity']) && $calc['activity'])
                    @php
                        $tdNumber++;
                    @endphp
                    <td>{{isset($calc['person']) ? $calc['person'] : ''}}</td>
                @endif
            @endforeach
            @if($tdNumber == 0)
                <td>There is no active configurations</td>
            @endif
        </tr>
    @endif
    @if(!empty($calculations))
            <tr>
                @php $tdNumber = 0;
                @endphp
                @foreach($calculations as $calc)
                    @if(isset($calc['activity']) && $calc['activity'])
                        @php
                            $tdNumber++;
                        @endphp
                        <td>{{isset($calc['brutto']) ? $calc['brutto'] : ''}}</td>
                    @endif
                @endforeach
                @if($tdNumber == 0)
                    <td></td>
                @endif
            </tr>
    @endif

		</table>
		<hr class = "page-break">
			@if(!empty($quotation->additional_persons))
			<b>Additional:</b>
			<table class="border" cellpadding="0" cellspacing="0" style="border: 1px solid #c0c0c0;">

				@if(!empty($quotation->additional_persons))
					<tr>
						@php $tdNumber = 0;
						@endphp
						@foreach($quotation->additional_persons as $person)
							@if ($person->active)
								@php
									$tdNumber++;
								@endphp
								<td>{{$person->person}}</td>
							@endif
						@endforeach
						@if($tdNumber == 0)
							<td>There is no active configurations</td>
						@endif
					</tr>
				@else
					<tr>
						<td>There is no active configurations</td>
					</tr>
				@endif
				@if(!empty($quotation->additional_persons))
					<tr>
						@php $tdNumber = 0;
						@endphp
						@foreach($quotation->additional_persons as $person)
							@if ($person->active)
								@php
									$tdNumber++;
								@endphp
								<td>{{$person->price}}</td>
							@endif
						@endforeach
							@if($tdNumber == 0)
								<td></td>
							@endif
					</tr>
				@endif

			</table>
			@endif       
            
             <hr>
			<h1>
    INVOICE INCLUDED:
</h1>

<div class="red">
    IF YOU AGREE THE ABOVE SERVICE ,PLEASE SIGN BACK FOR REFERENCE
</div>
<div style="clear: both"></div>
<div style="text-align: right" style="padding-top: 30px"><b>THANK YOU & BEST REGARDS !!</b></div>
<div style="text-align: right ; margin-top:30px"><span class="border_bottom_gray" style="width: 200px !important; height: 40px">
        ________________________
    </span></div>
<div style="text-align: right">SIGNATURE BY AGENT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
             <footer style="margin-left:80px; margin-top:30px">
                <h4 class="footer_headings">Beneficiary Name : EUROPE EXPRESS TRAVEL SERVICE INT'L CO., LTD.</h4>
                <h4 class="footer_headings">Bank Name :{{$office->bank_name}}.</h4>
                <h4 class="footer_headings">Bank Address : {{$client->address}}, </h4>
                <h4 class="footer_headings">SWIFT CODE : {{$office->swift_code}}</h4>
                <h4 class="footer_headings">Account : {{$office->account_no}}</h4>
            </footer>
    </div>
  
	</body>
</html>