<section class="content-header" style="margin-bottom: 20px;">
    @if(!empty($sub_title))
        <h1>
            {{ $title }}
            <small>{{ $sub_title }}</small>
        </h1>
    @endif
        @if(!empty($breadcrumbs))
            <ol class="breadcrumb">
                @foreach($breadcrumbs as $breadcrumb)
                @if(empty($breadcrumb['route']))
                        <li class="active">
                            @if(!empty($breadcrumb['icon']))
                                <i class="fa fa-{{ $breadcrumb['icon']}}"></i>
                            @endif
                                {{ $breadcrumb['title'] }}
                        </li>
                    @else
                        <li><a href="{{ $breadcrumb['route']}}">
                                @if(!empty($breadcrumb['icon']))
                                    <i class="fa fa-{{ $breadcrumb['icon']}}"></i>
                                @endif
                                {{ $breadcrumb['title'] }}
                            </a>
                        </li>
                @endif
                @endforeach
            </ol>
        @endif
</section>
<script type="text/javascript" src="{{asset('js/utils.js')}}"></script>