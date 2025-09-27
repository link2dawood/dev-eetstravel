
<style>
	.heading{
		background-color:#000000; color:#EE0000 
	}
</style>

<div class = "heading" > 
<table class="table table-bordered" class = "heading"  >
                                                <thead  class = "heading" >
                                                <th>Date</th>
                                                <th  class = "heading" > City</th>
                                                <th  class = "heading" >Hotel</th>
                                                @foreach ($listRoomsHotel as $room)
                                                    @if ($room->room_types->code == 'SIN')
                                                        <th class = "heading" > 
                                                            SS
                                                        </th>
                                                        @elseif($room->room_types->code == 'DOU')
                                                        <th class = "heading">
                                                            HPP
                                                        </th>
                                                        @else
                                                        <th class = "heading" >
                                                            {{$room->room_types->code}}
                                                        </th>
                                                        @endif
                                                @endforeach
													
                                                <th class = "heading">L.Name</th>
                                                <th class = "heading" >Lun</th>
                                                <th class = "heading" >D.Name</th>
                                                <th class = "heading" >Din</th>
                                                <th class = "heading" >Entr</th>
                                                <th class = "heading" >Com</th>
                                                <th class = "heading" >LGD</th>
                                                <th class = "heading" >BUS</th>
                                                <th class = "heading" >GC</th>
                                                <th class = "heading"  >Dri</th>
                                                <th class = "heading" >Por</th>
                                                @foreach($quotation->additional_columns as $column)
                                                    <th class = "heading" >{{$column->name}}</th>
                                                @endforeach
                                                </thead>
                                                
                                            </table>
	</div>
<table>
	
<tbody id="quotation_table">
                                                @php
                                                    $sortedQuotationRows = $quotation->rows->sortBy(function($quotationRow){
                                                      return $quotationRow->getValueByKey('date')->value;
                                                    });
                                                @endphp
                                                @foreach ($sortedQuotationRows as $key => $row)
                                                    <tr>
                                                        @foreach ($row->values as $value)
                                                            @if(!empty($value->value))
                                                            <td style="background-color:#FFFF00; ">
                                                                
                                                            {{$value->value}}
                                                            </td>
                                                            @else
                                                            <td  >
                                                          {{$value->value}}
                                                            </td>
                                                            
                                                            @endif
                                                            
                                                        @endforeach
                                                            @foreach($quotation->additional_columns as $column)
                                                                <td  style="background-color:#00FFFF; ">
                                                                    {{--{{dump($row->id, $column->name, @$quotation->getAdditionalColumnValueCell($row->id, $column->name)->value)}}--}}
                                                                    {{@$quotation->getAdditionalColumnValueCell($row->id, $column->name)->value}}
                                                                </td>
                                                            @endforeach
                                                    </tr>
                                                @endforeach

                                                </tbody>
</table>
<table class="border" cellpadding="0" cellspacing="0" style="border: 1px solid #c0c0c0;">

    @if(!empty($calculations))
        <tr style="background-color:blue; color:#FDFEFE ">
			<td>Persons</td>
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
            <tr  style="background-color:#9C640C; color:#FDFEFE ">
				<td>Netto EURO</td>
                @php $tdNumber = 0;
                @endphp
                @foreach($calculations as $calc)
                    @if(isset($calc['activity']) && $calc['activity'])
                        @php
                            $tdNumber++;
                        @endphp
                        <td>{{isset($calc['netto_euro']) ? $calc['netto_euro'] : ''}}</td>
                    @endif
                @endforeach
                @if($tdNumber == 0)
                    <td></td>
                @endif
            </tr>
			<tr>
				<td>Mark Up</td>
                @php $tdNumber = 0;
                @endphp
                @foreach($calculations as $calc)
                    @if(isset($calc['activity']) && $calc['activity'])
                        @php
                            $tdNumber++;
                        @endphp
                        <td>{{$quotation->mark_up != "" ? $quotation->mark_up :  '0'}}</td>
                    @endif
                @endforeach
                @if($tdNumber == 0)
                    <td></td>
                @endif
            </tr>
			<tr>
				<td> Brutto</td>
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