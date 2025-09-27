<table width="100%" border="2" id="testTable" >                  
                    <tdead valign="top">
						<!--- hotel --->
					@foreach($data as $hotel)

                    <tr>
                      <td width="20" align="center" valign="top" style="background-color: #cccccc;" ><strong>id</strong></td>
                      <td width="20" align="center" valign="top" style="background-color: #cccccc;" ><strong>{!!trans('main.Name')!!}</strong></td>
                      <td width="20" align="center" valign="top" style="background-color: #cccccc;"><strong>{!!trans('main.Address')!!}</strong></td>
                      <td width="20" align="center" valign="top" style="background-color: #cccccc;"><strong>{!!trans('main.Country')!!}</strong></td>
                      <td width="20" align="center" valign="top" style="background-color: #cccccc;"><strong>{!!trans('main.WorkPhone')!!}</strong></td>
                      <td width="20" align="center" valign="top"style="background-color: #cccccc;" ><strong>{!!trans('main.ContactEmail')!!}</strong></td>
                    </tr>
                    <tr>
                      <td width="20" align="center" valign="top" >{{  $hotel->id }}</td>
                      <td width="20" align="center" valign="top" >{{  $hotel->name }}</td>
                      <td width="20" align="center" valign="top" >{{  $hotel->address_first }}</td>
                      <td width="20" align="center" valign="top" >{{  $hotel->country }}</td>
                      <td width="20" align="center" valign="top" >{{  $hotel->work_phone }}</td>
                      <td width="20" align="center" valign="top" >{{  $hotel->contact_email }}</td>
                    </tr>
						
					<!-- hotel close-->
                        @if(count($hotel->seasons) == 0)
                                <tr>
                                    <td width="20" align="center" valign="top" style="background-color: #cccccc;"><strong>{!!trans('main.Name')!!}</strong></td>
                                    <td width="20" align="center" valign="top" style="background-color: #cccccc;"><strong>{!!trans('main.Fromdata')!!}</strong></td>
                                    <td width="20" align="center" valign="top" style="background-color: #cccccc;"><strong>{!!trans('main.Todate')!!}</strong></td>
                                    <td colspan="3" align="center" valign="top"style="background-color: #cccccc;" ><strong>{!!trans('main.Type')!!}</strong></td>
                                </tr>
                                <tr>
                                    <td width="20" align="center" valign="top" >{!!trans('main.Name')!!}</td>
                                    <td width="20" align="center" valign="top" >{!!trans('main.Fromdata')!!}</td>
                                    <td width="20" align="center" valign="top" >{!!trans('main.Todate')!!}</td>
                                    <td colspan="3" align="center" valign="top" >{!!trans('main.Low')!!}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center" valign="top" >&nbsp;</td>
                                    <td align="center" valign="top" style="background-color:  #dddddd;" width="20"><strong>{!!trans('main.Rooms')!!}</strong></td>
                                    <td colspan="3" align="center" valign="top" style="background-color:  #dddddd;"><strong>{!!trans('main.Price')!!}</strong></td>
                                </tr>

                                    <tr>
                                        <td colspan="2" align="center" valign="top" >&nbsp;</td>
                                        <td width="20" align="center" valign="top" >Dou</td>
                                        <td colspan="3" align="center" valign="top" >0</td>
                                    </tr>

                         @else

                            @foreach ($hotel->seasons as $season)
                    <tr>
                      <td width="20" align="center" valign="top" style="background-color: #cccccc;"><strong>{!!trans('main.Name')!!}</strong></td>
                      <td width="20" align="center" valign="top" style="background-color: #cccccc;"><strong>{!!trans('main.Fromdata')!!}</strong></td>
                      <td width="20" align="center" valign="top" style="background-color: #cccccc;"><strong>{!!trans('main.Todate')!!}</strong></td>
                      <td colspan="3" align="center" valign="top"style="background-color: #cccccc;" ><strong>{!!trans('main.Type')!!}</strong></td>
                      </tr>
                    <tr>
                      <td width="20" align="center" valign="top" >{{ $season->name }}</td>
                      <td width="20" align="center" valign="top" >{{ $season->start_date }}</td>
                      <td width="20" align="center" valign="top" >{{ $season->end_date }}</td>
                      <td colspan="3" align="center" valign="top" >{{ $season->type }}</td>
                      </tr>
                    <tr>
                      <td colspan="2" align="center" valign="top" >&nbsp;</td>
                      <td align="center" valign="top" style="background-color:  #dddddd;" width="20"><strong>{!!trans('main.Rooms')!!}</strong></td>
                      <td colspan="3" align="center" valign="top" style="background-color:  #dddddd;"><strong>{!!trans('main.Price')!!}</strong></td>
                      </tr>
                    @foreach($season->seasons_room_types as $item)
                    <tr>
                      <td colspan="2" align="center" valign="top" >&nbsp;</td>
                      <td width="20" align="center" valign="top" >{{ $season->getRoom($item->room_type_id)->name }}</td>
                      <td colspan="3" align="center" valign="top" >{{ $item->price }}</td>
                      </tr>
                        @endforeach

                           @endforeach
                            @endif

                            @endforeach
																						
																						<!-- -->
                    </tdead>
                    <tbody>
                    </tbody>
                </table>