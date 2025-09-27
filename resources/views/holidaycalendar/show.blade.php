@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
           ['title' => 'Holiday', 'sub_title' => 'Holiday Show',
           'breadcrumbs' => [
           ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
           ['title' => 'Holidays', 'icon' => null, 'route' => route('holiday.index')],
           ['title' => 'Show', 'route' => null]]])
    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="margin_button">
                            <a href="javascript:history.back()">
                                <button class='btn btn-primary'>{!!trans('main.Back')!!}</button>
                            </a>
                            <a href="{!! route('holiday.edit', $holidaycalendarday->id) !!}">
                                <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                            </a>
                        </div>
                    </div>
                </div>
                <table class = 'table table-bordered'>
                    <tbody>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{!!trans('main.Name')!!} : </i></b>
                        </td>
                        <td>{!!$holidaycalendarday->name!!}</td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{!!trans('main.Color')!!} : </i></b>
                        </td>
                        <td style="color:{!! $holidaycalendarday->backgroundcolor !!}">{!! $holidaycalendarday->backgroundcolor !!}</td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{!!trans('main.Date')!!} : </i></b>
                        </td>
                        <td>{!! Carbon\Carbon::parse($holidaycalendarday->start_time )->format('Y-m-d') !!}</td>
                    </tr>
                   
                    </tbody>
                </table>
            </div>
        </div>

    </section>
@endsection

@section('post_scripts')
    <script src="{{ asset('js/comment.js') }}"></script>
@endsection