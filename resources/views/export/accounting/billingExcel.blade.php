  <table width="100%">

      <tr>
      </tr>
      <tr>
          <td>
              <img
                  style="padding-top: 50px;padding-bottom: 20px; margin-left:500px; text-align: center; "src="{{ public_path() . '/img/eets_logo_small.jpg' }}" />

          </td>

          @for ($i = 0; $i < 4; $i++)
              <td>
              </td>
          @endfor
          <td>
              <h2 class="float-right text_color" style="">{{ $office->office_name }}
              </h2>
          </td>
      </tr>

      <tr>
          @for ($i = 0; $i < 5; $i++)
              <td>
              </td>
          @endfor
          <td>
              <h2 class="float-right text_color" style="">{{ $office->office_address }}
              </h2>

          </td>
      </tr>

      <tr>
          @for ($i = 0; $i < 5; $i++)
              <td>
              </td>
          @endfor
          <td>
              <h2 class="float-right text_color" style="">Tel: {{ $office->tel }}
              </h2>

          </td>
      </tr>

      <tr>
          @for ($i = 0; $i < 5; $i++)
              <td>
              </td>
          @endfor
          <td>
              <h2 class="float-right text_color" style="">Fax : {{ $office->fax }}
              </h2>

          </td>
      </tr>
      <tr>
		  <td></td>
          <hr>
      </tr>
  </table>

  <table>
      <tr>
          <td>
          </td>
          <td>
              <h4 class="text_color  float-left" style="  margin-left:50px; width=:100%">{{ $client->name }}
              </h4>
          </td>


      </tr>
      <tr>
          <td>
          </td>
          <td>
              <h4 class="text_color  float-left" style="  margin-left:50px;">{{ $client->address }}
              </h4>
          </td>
      </tr>

      <tr>
          @for ($i = 0; $i < 9; $i++)
              <td>
              </td>
          @endfor
          <td>

              <h4 class="float-right" style="text-align:right">Date
              </h4>
          </td>
          <td>

              <h4 class="float-right" style="text-align:right">09/05/2023
              </h4>
          </td>
      </tr>
      <tr>
          @for ($i = 0; $i < 5; $i++)
              <td>
              </td>
          @endfor
          <td>
              <h1 style="text-align: center">Invoice</h1>
          </td>
      </tr>
      <tr>
          <td>
          </td>
          <td>
              <h4>Tour Name : {{ $tour->name }}
              </h4>
          </td>
      </tr>
		<tr>
		  <td></td>
          <hr>
      </tr>
  </table>


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
  @php
      $count = 1;
      $total = 0;
  @endphp
  <table width="100%">

      <thead>
		  <tr>
		  <th></th>
          <hr>
      	  </tr>
          <tr>
              <th></th>
              <th></th>
              <th style="text-align: left">Item</th>
              <th></th>
              <th style="text-align: left">Pax</th>
              <th></th>
              <th style="text-align: left">Euro</th>
              <th></th>
              <th style="text-align: right ; padding-right:85px; ">Amount</th>
          </tr>

      </thead>
      <tbody>
          @foreach ($calculations as $calc)
              @if (isset($calc['activity']) && $calc['activity'])
                  @php
                      $quotation_total = $calc['brutto'] * $tour->pax;
                      $total = $total + $quotation_total;
                  @endphp
                  <tr>
                      <td></td>
                      <td>1:</td>
                      <td>Landpackage</td>
                      <td></td>
                      <td>{{ $tour->pax }}</td>
                      <td></td>
                      <td>{{ number_format(isset($calc['brutto']) ? $calc['brutto'] : '', 0, '.', ',') }}</td>
                      <td></td>
                      <td style="text-align: right ; padding-right:85px; ">
                          {{ number_format($quotation_total, 0, '.', ',') }}</td>
                      <td></td>
                  </tr>
                  <tr>
                  </tr>
              @endif
          @endforeach
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
		  <tr>
		  <td></td>
          <hr>
     	 </tr>
      </tbody>
  </table>

  <table>
      <tbody>
          <tr>
              @for ($i = 0; $i < 4; $i++)
                  <td>
                  </td>
              @endfor
	  <td>TOTAL  AMOUNT  :</td>
	  @for ($i = 0; $i < 2; $i++)
                  <td>
                  </td>
              @endfor
	  <td>EUR</td>
              <td>
               {{ number_format($total, 0, '.', ',') }}
              </td>
          </tr>
          <tr></tr>
          <tr>
              <td></td>
              <td>
                  <h2> Confirmed Quotation Prices</h2>
              </td>
          </tr>
          <tr>
              <td></td>
              <hr>


          </tr>
      </tbody>
  </table>

  <table class="border" cellpadding="0" cellspacing="0" style="border: 1px solid #c0c0c0;">

      @if (!empty($calculations))
          <tr>
              <td>
              </td>
              @php$tdNumber = 0;
              @endphp
              @foreach ($calculations as $calc)
                  @if (isset($calc['activity']) && $calc['activity'])
                      @php
                          $tdNumber++;
                      @endphp
                      <td>{{ isset($calc['person']) ? $calc['person'] : '' }}</td>
                  @endif
              @endforeach
              @if ($tdNumber == 0)
                  <td>There is no active configurations</td>
              @endif
          </tr>
      @endif
      @if (!empty($calculations))
          <tr>
              <td>
              </td>
              @php$tdNumber = 0;
              @endphp
              @foreach ($calculations as $calc)
                  @if (isset($calc['activity']) && $calc['activity'])
                      @php
                          $tdNumber++;
                      @endphp
                      <td>{{ isset($calc['brutto']) ? $calc['brutto'] : '' }}</td>
                  @endif
              @endforeach
              @if ($tdNumber == 0)
                  <td></td>
              @endif
          </tr>
      @endif
	  <tr>
		  <td></td>
          <hr>
      </tr>

  </table>
  <hr class="page-break">
  @if (!empty($quotation->additional_persons))
      <b>Additional:</b>
      <table class="border" cellpadding="0" cellspacing="0" style="border: 1px solid #c0c0c0;">

          @if (!empty($quotation->additional_persons))
              <tr>
                  @php$tdNumber = 0;
                  @endphp
                  @foreach ($quotation->additional_persons as $person)
                      @if ($person->active)
                          @php
                              $tdNumber++;
                          @endphp
                          <td>{{ $person->person }}</td>
                      @endif
                  @endforeach
                  @if ($tdNumber == 0)
                      <td>There is no active configurations</td>
                  @endif
              </tr>
          @else
              <tr>
                  <td>There is no active configurations</td>
              </tr>
          @endif
          @if (!empty($quotation->additional_persons))
              <tr>
                  @php$tdNumber = 0;
                  @endphp
                  @foreach ($quotation->additional_persons as $person)
                      @if ($person->active)
                          @php
                              $tdNumber++;
                          @endphp
                          <td>{{ $person->price }}</td>
                      @endif
                  @endforeach
                  @if ($tdNumber == 0)
                      <td></td>
                  @endif
              </tr>
          @endif
          <tr>
		  <td></td>
          <hr>
      	</tr>
      </table>
  @endif



  <table>
      <footer style="margin-left:80px; margin-top:30px">
          <tr>
              <td>
              </td>

              <td colspan="8">

                  <h4 class="footer_headings">Beneficiary Name : EUROPE EXPRESS TRAVEL SERVICE INT'L CO., LTD.</h4>

              </td>
          </tr>
          <tr>
          </tr>
          <tr>
              <td>
              </td>

              <td>
                  <h4 class="footer_headings">Bank Name :{{ $office->bank_name }}.</h4>

              </td>
          </tr>
          <tr></tr>
          <tr>
              <td>
              </td>

              <td>
                  <h4 class="footer_headings">Bank Address : {{ $client->address }}, </h4>
              </td>
          </tr>
          <tr></tr>
          <tr>
              <td>
              </td>

              <td>
                  <h4 class="footer_headings">SWIFT CODE : {{ $office->swift_code }}</h4>
              </td>
          </tr>
          <tr></tr>

          <tr>
              <td style="width:200px">
              </td>

              <td>
                  <h4 class="footer_headings">Account : {{ $office->account_no }}</h4>

              </td>
          </tr>
      </footer>
  </table>
