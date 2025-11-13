@props(['title', 'breadcrumbs' => [], 'actions' => null])

<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                @if(count($breadcrumbs) > 0)
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            @foreach($breadcrumbs as $breadcrumb)
                                @if($loop->last)
                                    <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
                                @else
                                    <li class="breadcrumb-item">
                                        <a href="{{ $breadcrumb['route'] }}">
                                            @if(isset($breadcrumb['icon']))
                                                <i class="ti ti-{{ $breadcrumb['icon'] }}"></i>
                                            @endif
                                            {{ $breadcrumb['title'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                </div>
                @endif
                <h2 class="page-title">{{ $title }}</h2>
            </div>
            @if($actions)
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    {!! $actions !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
