<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        body {
            font-size: 14px;
            margin: 0;
            padding: 0;

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

    </style>
</head>
<body>
<img src="{{ public_path() . '/img/eets_logo.jpg'}}" width="180px">
<h4 style="float: right; margin: 0; padding: 0; top: 0;">
    EETS EUROPE EXPRESS & EAST EUROPE TRAVEL SERVICE INT'L CO., LTD<br>
    ( Associates ) / Budapest operation office:<br>
    RADAY utca 15. 1/14, Budapest 1092 , Hungary <br>
    TEL: +36 1 2019416 / 2019422 , FAX: +36 1 2019418 <br>
    Office Email : eets@eets.hu</h4>
<span style="clear: both;"></span>
<table style="margin-top: 20px" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <td><b>ATTN:</b></td>
        <td>Cola / 淑楨</td>
        <td><b>TEL:</b></td>
        <td>02-25267-2898</td>
        <td><b>FAX:</b></td>
        <td>02-2521-6686</td>
    </tr>
    <tr>
        <td><b>FROM:</b></td>
        <td>EETS/TPE/Jerry</td>
        <td><b>DATE</b></td>
        <td>{{@date('Y/m/d',strtotime($quotation->created_at))}}</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><b>RE:</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td><b>TOTAL:</b></td>
        <td>2 PAGE(S)</td>
    </tr>
    </tbody>
</table>

<table class="border" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <td><b>DATE</b></td>
        <td><b>CITY</b></td>
        <td rowspan="2"><b>TRNSF & ASSIST. <br>TOUR,GUIDE,ENTRANCE</b></td>
        <td rowspan="2"><b>MEALS</b></td>
    </tr>
    <tr>
        <td colspan="2"><b>HOTEL</b></td>
    </tr>
    </thead>
    <tbody>
    @if(!empty($tourdays))
        @foreach($tourdays as $tourday)
            <tr>
                <td><b>{{@date('d/m', strtotime($tourday->date))}}</b></td>
                <td> @if($tourday->firstHotel() && $tourday->firstHotel()->service()->cityObject)
                        {{$tourday->firstHotel()->service()->cityObject->name}}
                    @endif&nbsp;
                </td>
                <td rowspan="2">
                    @foreach($tourday-> packages as $pack)
                        @if($pack-> type != 0)
                            +{{$pack-> name}}
                        @endif
                    @endforeach&nbsp;
                </td>
                <td rowspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    @if($tourday->firstHotel())
                        {{$tourday->firstHotel()->name}}
                    @endif&nbsp;
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
<div>
    <ul>
        <li>** The QT rate only for normal period **</li>
        <li>** If group not include all meals,pls must arrange meals for the driver **</li>
        <li>** The QT exclude proterage service **</li>
        <li>** 3 course menu for local meal & 6 dishes + 1 soup for Chinese meal **</li>
        <li>**All Local meals excluded any drinks and water **</li>
        <li>** Any other entrance fee not include in this quotation will be provide as supplement later **</li>
        <li>** If facing conference period will be advise hotel supplement after group go-ahead **</li>
        <li>** FD = Full day / HD = Half day / EF = Entrance included / CH = Chinese GD = Local Guide / APT = Airport
            **
        </li>
    </ul>
</div>
@if($quotation->note_show)
    <div>
        <b>Notes : </b>
        <span>
        {{$quotation->note}}
    </span>
    </div>
@endif

<div style="page-break-after: always;"></div>

<img src="{{ public_path() . '/img/eets_logo.jpg'}}" width="180px">
<h4 style="float: right; margin: 0; padding: 0; top: 0;">EETS EUROPE EXPRESS & EAST EUROPE TRAVEL SERVICE INT'L CO., LTD<br>
    ( Associates ) / Budapest operation office:<br>
    RADAY utca 15. 1/14, Budapest 1092 , Hungary <br>
    TEL: +36 1 2019416 / 2019422 , FAX: +36 1 2019418 <br>
    Office Email : eets@eets.hu</h4>
<span style="clear: both;"></span>
<table style="margin-top: 20px; margin-bottom: 0px" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <td><b>ATTN:</b></td>
        <td>Cola / 淑楨</td>
        <td><b>TEL:</b></td>
        <td>02-25267-2898</td>
        <td><b>FAX:</b></td>
        <td>02-2521-6686</td>
    </tr>
    <tr>
        <td><b>FROM:</b></td>
        <td>EETS/TPE/Jerry</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><b>DATE:</b></td>
        <td colspan="5">{{@date('Y/m/d',strtotime($quotation->created_at))}}</td>
    </tr>
    <tr>
        <td colspan="6">&nbsp;</td>
    </tr>
    <tr>
        <td><b>RE:</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td><b>TOTAL:</b></td>
        <td>2 PAGE(S)</td>
    </tr>
    <tr>
        <td><b>QUOTATION</b></td>
        <td><b>(IN EURO €歐元 P.P.)</b></td>
    </tr>
    </tbody>
</table>
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
<table class="border" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="4">SINGLE SUPPLEMENT: P.P.</td>
        <td>220</td>
        <td><b>( 歐元 ) €</b></td>
        <td>(One Free Single Room For T/L)</td>
    </tr>
</table>

<table>
    <tr>
        <td>( 歐元 ) €</td>
        <td colspan="4" style="border-bottom: 1px solid #c0c0c0"></td>
    </tr>
    <tr>
        <td></td>
        <td width="100px">Rate are valid from</td>
        <td>{{@date('Y/m/d',strtotime($quotation->created_at))}}</td>
        <td width="100px">till</td>
        <td>{{@date('Y/m/d',strtotime($quotation->created_at))}}</td>
    </tr>
</table>
<div>
    QUOTATION INCLUDED:
</div>
<ul class="quotations">
    <li>
        <b>A)--Accommodation in following hotels or similar : {{@count($tourdays)-1}}Night(s)</b>
        <div style="background: #c9cccf; margin: 5px">
            @if(!empty($tourdays))
                @foreach($tourdays as $tourday)
                    <div>
                        @if($tourday->firstHotel() && $tourday->firstHotel()->service()->cityObject)
                            {{$tourday->firstHotel()->service()->cityObject->name}} -
                        @endif
                        @if($tourday->firstHotel())
                            {{$tourday->firstHotel()->name}}
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </li>
	@php
                                                    $sortedQuotationRows = $quotation->rows->sortBy(function($quotationRow){
                                                      return $quotationRow->getValueByKey('date')->value??"";
                                                    });
													$lunch_count = 0;
													$dinner_count = 0;
													$breakfast_count = 0;
													foreach ($sortedQuotationRows as $key => $row){
														foreach ($row->values as $value){
															if($value->key == "lunch"){
																	if($value->value != ""){
																$lunch_count = $lunch_count + 1;
																					}
																}
															if($value->key == "dinner"){
																	if($value->value != ""){
																$dinner_count = $dinner_count + 1;
																					}
																}
															if($value->key == "hotelName"){
																	if($value->value != ""){
																$breakfast_count = $breakfast_count + 1;
																					}
																}
	
														}
	
													}
													
                                                @endphp
    <li>
        <b>B)--Meals included :</b><br>
        <span class="border_bottom_gray">
           x Continental Breakfast
        </span>
        <span class="border_bottom_gray">
            {{$breakfast_count}} x Buffet Breakfast
        </span>
        <span class="border_bottom_gray">
          {{ $lunch_count }}x Lunch
        </span>
        <span class="border_bottom_gray">
            {{$dinner_count}} x Dinner
        </span>
    </li>
    <li>
        <b>C)--Tour,Guide,Entrance,&Transportation included, according to our english itinerary. Luxury airconditioned
            LDC</b><br>
        from Arr. : VIE to dept. : VIE at disposal
    </li>
    <li>
        <b>D)--Porterage one piece p.p. at</b><br>
        <span class="border_bottom_gray">
        --Hotel : No
        </span>
        <span class="border_bottom_gray">Airport: No</span>
        <span class="border_bottom_gray">Station: No</span>
    </li>
    <li>
        <b>E)--Tips for Driver,Guides, Assistant are not included.</b>
    </li>
    <li>
        <b>F)-- Additional Information: Rates will be adjusted once fair periods in any city above.</b>
    </li>
    <li>
        <b class="red">G)- During the Conference period and Bank holiday in Prague - Supplement will be advised</b>
    </li>
</ul>

<ul style="padding-left: 0px">
    <li>* Child under 12 y.old : additional bed with parent 80 % tour fee ; Without bed 60 % tour fee</li>
    <li>** Any other entrance fee not include in this quotation will be provide as supplement later **</li>
    <li>** If group not include all meals,pls must arrange meals for the driver **</li>
</ul>
<div class="red">
    IF YOU AGREE THE ABOVE SERVICE ,PLEASE SIGN BACK FOR REFERENCE
</div>
<div style="clear: both"></div>
<div style="text-align: right" style="padding-top: 30px"><b>THANK YOU & BEST REGARDS !!</b></div>
<div style="text-align: right"><span class="border_bottom_gray" style="width: 200px !important; height: 40px">
        ________________________
    </span></div>
<div style="text-align: right">SIGNATURE BY AGENT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
</body>
</html>