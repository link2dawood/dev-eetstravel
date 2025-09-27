@extends('scaffold-interface.layouts.app')
@section('title','Show')
@section('content')
    @include('layouts.title',
       ['title' => 'Tax', 'sub_title' => 'Tax Show',
       'breadcrumbs' => [
       ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
       ['title' => 'Taxes', 'icon' => null, 'route' => route('taxes.index')],
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
                            <a href="{!! route('taxes.edit', $tax->id) !!}">
                                <button class='btn btn-warning'>{!!trans('main.Edit')!!}</button>
                            </a>
                        </div>
                    </div>
                </div>
                <table class = 'table table-bordered'>
                    <tbody>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{!!trans('Tax Name')!!} : </i></b>
                        </td>
                        <td>{!!$tax->name!!}</td>
                    </tr>
                    <tr>
                        <td class="show_width_td">
                            <b><i>{!!trans('Value')!!} : </i></b>
                        </td>
                        <td>{!!$tax->value!!}</td>
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