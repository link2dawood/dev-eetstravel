@if(isset($isDoc) and $isDoc)
<?php
header("Content-Type: application/vnd.ms-word");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=". $download_name ."");
?>
@endif

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{!!trans('main.Itinerary')!!} - {{ $tour->name }}</title>
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
            /*@if(!$isHtml) background-image: url("{{ asset('img/background2.jpg') }}") @else  background-image: url("{{ asset('img/background2.jpg') }}") @endif;*/
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

        /* Itinerary layout: fluid widths only so the page never scrolls sideways */
        .itinerary-page { max-width: 210mm; margin: 0 auto; padding: 0 15px; overflow-x: hidden; }
        .itinerary-page .row { margin-left: 0; margin-right: 0; }
        .itinerary-page table { max-width: 100%; }
        .itinerary-page img { max-width: 100%; height: auto; }
        .itinerary-header td { padding-top: 30px; padding-bottom: 15px; }
        .itinerary-logo { max-height: 70px; }
        .itinerary-title {
            display: inline-block;
            margin: 30px 0 10px;
            padding-bottom: 4px;
            font-size: 26px;
            border-bottom: 2px solid #666767;
        }
        .itinerary-summary .summary-col { padding: 0 10px 0 0; }
        .summary-box { background-color: #f3f3f3; padding: 12px 16px; }
        .field-label {
            margin-top: 10px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #666767;
        }
        .field-label:first-child { margin-top: 0; }
        .field-value { font-size: 14px; color: #333; word-wrap: break-word; }
        .itinerary-page h3.text_color { font-size: 18px; }
        .itinerary-page h4.text_color { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin: 8px 0 2px; }
    </style>

</head>
<body>

<div class="container-fluid itinerary-page">
    @php
        $peopleCount = 0;
        $roomsCodes = '';
        $roomsList = [];
        foreach ($listRoomsHotel as $item) {
            if (!$item->room_types) {
                continue;
            }
            $peopleCount += isset(App\TourPackage::$roomsPeopleCount[$item->room_types->code])
                ? App\TourPackage::$roomsPeopleCount[$item->room_types->code] * $item->count : 0;
            $roomsCodes .= ($roomsCodes === '' ? '' : '+') . $item->count . $item->room_types->code;
            $roomsList[] = $item->count . ' ' . $item->room_types->name;
        }
        $paxText = trim($tour->pax . ($tour->getRawOriginal('pax_free') ? ' ' . $tour->pax_free : ''));
        $tourDisplayName = trim($tour->name . ' ' . $tour->external_name);
    @endphp

    <table class="itinerary-header" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td valign="bottom">
                <h2 class="itinerary-title text_color">{!!trans('main.Itinerary')!!}</h2>
            </td>
            <td align="right" valign="bottom">
                <img class="itinerary-logo" src="{{asset('/img/eets_logo_small.jpg' )}}" alt="" />
            </td>
        </tr>
    </table>

    <table class="itinerary-summary" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td class="summary-col" valign="top" width="50%">
                @if($paxText !== '')
                    <div class="field-label">Pax</div>
                    <div class="field-value">{{ $paxText }}</div>
                @endif

                @if(count($roomsList))
                    <div class="field-label">{!!trans('main.Room')!!}</div>
                    <div class="field-value">{{ implode(', ', $roomsList) }}</div>
                @endif

                @if(trim((string) $tour->itinerary_tl) !== '')
                    <div class="field-label">T/L</div>
                    <div class="field-value">{{ $tour->itinerary_tl }}</div>
                @endif

                @if(trim(strip_tags((string) $tour->phone)) !== '')
                    <div class="field-label">{!!trans('main.Mobile')!!}</div>
                    <div class="field-value">{!! $tour->phone !!}</div>
                @endif

                @foreach (collect($tourTransfers)->where('type', 3) as $package)
                    @foreach($package->getTransferDrivers() as $driver)
                        <div class="field-label">{!!trans('main.Driver')!!}</div>
                        <div class="field-value">{{ $driver->name }}</div>
                        @if($driver->phone)
                            <div class="field-label">{!!trans('main.Mobile')!!}</div>
                            <div class="field-value">{{ $driver->phone }}</div>
                        @endif
                    @endforeach
                    <div class="field-label">Coach Details</div>
                    <div class="field-value">
                        {{ $package->name }}
                        @php
                            $coachFrom = $package->time_from ? \Carbon\Carbon::parse($package->time_from) : null;
                            $coachTo = $package->time_to ? \Carbon\Carbon::parse($package->time_to) : null;
                        @endphp
                        @if($coachFrom)
                            <br>{{ display_date($coachFrom) }} {{ $coachFrom->format('H:i') }}
                            @if($coachTo)
                                - @if(!$coachTo->isSameDay($coachFrom)){{ display_date($coachTo) }} @endif{{ $coachTo->format('H:i') }}
                            @endif
                        @endif
                    </div>
                @endforeach
            </td>
            <td class="summary-col" valign="top" width="50%">
                <div class="summary-box">
                    <div class="field-label">{!!trans('main.Name')!!}</div>
                    <div class="field-value">{{ $tourDisplayName }}</div>

                    <div class="field-label">{!!trans('main.DepDate')!!}</div>
                    <div class="field-value">{{ display_date($tour->departure_date) }}</div>

                    <div class="field-label">{!!trans('main.RetDate')!!}</div>
                    <div class="field-value">{{ display_date($tour->retirement_date) }}</div>

                    @if($paxText !== '')
                        <div class="field-label">Pax</div>
                        <div class="field-value">{{ $paxText }}</div>
                    @endif

                    @if($roomsCodes !== '')
                        <div class="field-label">{!!trans('main.ROOMS')!!}</div>
                        <div class="field-value">{{ $roomsCodes }}</div>
                    @endif

                    @if(is_object($usersResponsible))
                        <div class="field-label">{!!trans('main.Personincharge')!!}</div>
                        <div class="field-value">{{ $usersResponsible->name }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
    <?php $countDay = 0; ?>
    @if(isset($isDoc) and $isDoc)
        <br><br>
    @else
        <p style="page-break-after: always"></p>
    @endif
    <div class="row">
        <div class="col-sm-12 nopadding">
            <h2 class="itinerary-title text_color">{!!trans('main.Itinerary')!!}</h2>
        </div>
    </div>
	<div class="row">
        <div class="col-sm-12 nopadding">
        <!-- foreach start -->
		<table class='hei' width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
                @foreach($tourDays as $tourDay)
                    <?php $countDay++ ; ?>
                            <tr>
                                <td width="13%" align="right">
                                </td>
                                <td width="1%" align="center" valign="top" >
                                </td>
                                <td width="86%" align="left" valign="top">
                                    <h3 class="text_color" style="margin-top: -5px;">&nbsp;&nbsp;Day {{ $countDay }} - {{ (new \Carbon\Carbon($tourDay->date))->formatLocalized('%B %d, %Y (%A)') }}</h3>
                                </td>
                            </tr>
                            @foreach($tourDay->packages as $package)
                                @if (!in_array($package->id, $exclude))
                                <tr>
                                    {{-- Hotels show check-in only; their end time is a 23:59 placeholder --}}
                                    <td align="right" valign="top" class="text_color">{{ Carbon\Carbon::parse($package->time_from)->format('H:i')}} @if(!$package->description_package)@if((int) $package->type !== 0)- {{ Carbon\Carbon::parse($package->time_to)->format('H:i') }}@endif
                                            <br>
                                            @if($package->type !== null)
                                                {{ ucfirst($serviceTypes[$package->type]) }}
                                            @endif
                                            <br>
                                        @endif
                                    </td>

                                    <td width="1%" align="center" valign="top" >
                                    </td>
                                    <td>
                                        @if($package->description_package)
                                            <?php
                                                $package->description = strip_tags($package->description,'<p>');
                                                $package->description = strip_tags($package->description,'</p>');
                                                $lines = explode('<br />', nl2br($package->description));


                                                $lineCounter = 0;
                                            ?>
                                            @foreach ($lines as $line)
                                                @php
                                                    $lineCounter++
                                                @endphp
                                                @if(strlen( $line ) < 75)
                                                    <span class="text_color" >
                                                        {{ $line }}
                                                    </span>
                                                    <br>
                                                @else

                                                    <?php
                                                        $words = explode(" ",$line);
                                                        $subline = '';

                                                        foreach($words as $word){
                                                            $subline .= ' '.$word;
                                                            if(strlen($subline)>74){
                                                    ?>
                                                            <span class="text_color" >
                                                                {!! $subline !!}
                                                            </span>
                                                               <br>
                                                    <?php
                                                                $subline = '';
                                                                $lineCounter++;
                                                                if($lineCounter >= 40){
                                                                    $lineCounter =0;
                                                                    echo '
                                                                        </td>
                                                                       </tr>
                                                                      </tbody>
                                                                     </table>

                                                                    <div class="page-break"></div>

                                                                    <table class="hei" width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                     <tbody>
                                                                       <tr>
                                                                         <td width="13%" align="right"></td>
                                                                          <td width="1%" align="center" valign="top" ></td>
                                                                           <td width="86%" height="42" align="left" valign="top">
                                                                    ';
                                                                }

                                                            }

                                                        }
                                                    ?>
                                                        <span class="text_color" >
                                                            {!! $subline !!}
                                                        </span>
                                                @endif

                                                @if($lineCounter >= 40)
                                                    @php
                                                        $lineCounter =0;
                                                    @endphp
                                                        </td>
                                                       </tr>
                                                      </tbody>
                                                     </table>



                <table class='hei2' width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td width="13%" align="right"></td>
                            <td width="1%" align="center" valign="top"></td>
                            <td width="86%" height="42" align="left" valign="top">
                                @endif
                                @endforeach
                                @endif

                                &nbsp;&nbsp;{!! $package->name !!}
                                <br>
                                <?php
                                $srv = $package->service();
                                $menus = '';
                                ?>
                                @if ($srv)
                                    @php
                                        // Strip stray leading separators (e.g. "-40998772") from contact numbers
                                        $srvPhone = ltrim(trim((string) $srv->work_phone), " -/.,");
                                        $srvFax = ltrim(trim((string) $srv->work_fax), " -/.,");
                                        // Street is often split across address_first/address_second
                                        $srvCity = method_exists($srv, 'cityObject') && $srv->cityObject ? $srv->cityObject->name : '';
                                        $srvAddress = collect([$srv->address_first ?? '', $srv->address_second ?? '', $srvCity])
                                            ->map(function ($part) { return trim(strip_tags((string) $part), " ,"); })
                                            ->filter()
                                            ->implode(', ');
                                    @endphp
                                    @if ($srvPhone !== '' || $srvFax !== '')
                                        <div>
                                            @if ($srvPhone !== '')
                                                <span class="text_color">{!! trans('main.Tel') !!}:</span> {{ $srvPhone }}
                                            @endif
                                            @if ($srvFax !== '')
                                                @if ($srvPhone !== '')&nbsp;&nbsp;@endif<span class="text_color">Fax:</span> {{ $srvFax }}
                                            @endif
                                        </div>
                                    @endif
                                    @if ($srvAddress !== '')
                                        <div><span class="text_color">{!! trans('main.Address') !!}:</span> {{ $srvAddress }}</div>
                                    @endif
                                    <br>
                                @endif
                                    @if ($package->type == 0)
                                        <?php
                                        
                                        $room_typesDvo = \App\RoomTypes::query()
                                            ->orderBy('sort_order', 'ASC')
                                            ->get();
                                        $room_types = [];
                                        foreach ($room_typesDvo as &$room_type) {
                                            $room_type['count_room'] = 0;
                                            $room_type['price_room'] = 0;
                                        }
                                        $room_types = $room_typesDvo;
                                        $selected_room_types = [];
                                        foreach ($package->room_types_hotel as $item) {
                                            $item->room_types['count_room'] = $item->count;
                                            $item->room_types['price_room'] = $item->price;
                                            $selected_room_types[] = $item->room_types;
                                        }
                                        
                                        ?>
										@if(!$package->description_package)
                                        @if (count($selected_room_types) > 0)
                                            <span class="text_color"> Rooms: </span><br>
                                            @foreach ($selected_room_types as $room)
                                                {{ $room->count_room }}{{ $room->code }}
                                            @endforeach
                                        @endif
										@endif
                                    @endif
                                    <?php
                                    if ($package->menus->count()) {
    // Initialize $menus as an empty string
												$menus = '';

												foreach ($package->menus as $pmenu) {
													// Check if the menu is not null before accessing its properties
													if ($pmenu->menu_id) {
														$menu = App\Menu::find($pmenu->menu_id);

														if ($menu) {
															$menus .= $pmenu->count . ' - ' . $menu->name . '<br>';
														}
													}
												}
											}

                                    ?>

                                    @if ($menus != '')
                                        <br>
                                        <span class="text_color">Meals:</span>
                                        <br>
                                        {!! $menus !!}
                                    @endif
                                    <br> <br> <br>
									

                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    @endforeach
                </table>
	  </div>
	</div>
</div>



</body>
</html>