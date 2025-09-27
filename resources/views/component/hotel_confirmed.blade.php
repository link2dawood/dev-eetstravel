<div>
    <h3>{!!trans('main.Areyousureyouwanttochangethe')!!} "{{ $dvo[0]['confirmed_hotel']}}" {!!trans('main.statusonConfirmed')!!}
        <?php $check = false; ?>
        @foreach($dvo as $item)
            @if(count($item['count_delete']) > 0)
                <?php $check = true; ?>
            @endif
        @endforeach
        {{ $check ? trans('main.andtodeletehotels') : '' }}</h3>

    <ul>
        @foreach($dvo as $item)
            @if(count($item['count_delete']) > 0)
            <li>
                <b>{!!trans('main.Date')!!} : {{ $item['date'] }}</b> <br>
                @foreach($item['count_delete']  as $value)
                    <span>{{ $value->name }}</span> <br>
                @endforeach
            </li>
            @endif
        @endforeach
    </ul>
</div>