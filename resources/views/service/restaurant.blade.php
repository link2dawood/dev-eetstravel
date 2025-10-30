<style>
.action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 4px;
}

.action-btn svg {
    width: 20px;
    height: 20px;
}

.action-btn.edit svg {
    stroke: #f59e0b;
}

.action-btn.delete svg {
    stroke: #ef4444;
}

.action-btn:hover {
    transform: scale(1.15);
}

.action-btn.edit:hover {
    background-color: rgba(245, 158, 11, 0.1);
}

.action-btn.delete:hover {
    background-color: rgba(239, 68, 68, 0.1);
}
</style>

<div class="box box-primary">
    <div class="box-body">
        <section class="content">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('type', 'Service Type') !!}
                    {!! Form::select('type', $serviceTypes, $serviceType, ['class' => 'form-control', 'id' => 'tour-package-service-type']) !!}
                </div>
                <div class="col-sm-4">
                </div>
                <div class="col-sm-4">
                    <div id="example1_filter" class="bootstrap-search">
                        <label>{!!trans('main.Search')!!}:
                            {!! Form::text('search', $search, ['class' => 'form-control input-sm', 'id' => 'tour-package-search']) !!}
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
            </div>
            <br>
            <br>
            <table class = "table table-striped table-bordered table-hover" style = 'background:#fff'>
                <thead>
                <th>ID</th>
                <th>{!!trans('main.Name')!!}</th>
                <th>{!!trans('main.Country')!!}</th>
                <th>{!!trans('main.City')!!}</th>
                <th>{!!trans('main.Address')!!}</th>
                <th>{!!trans('main.Select')!!}</th>
                <th class="text-center">{!!trans('main.Actions')!!}</th>
                </thead>
                <tbody>
                @foreach($services as $service)
                    <tr>
                        <td>{!! $service->id !!}</td>
                        <td>{!! $service->name !!}</td>
                        <td>
                            {!! \App\Helper\CitiesHelper::getCountryById($service->country)['name']!!}
                        </td>
                        <td>
                            {!! \App\Helper\CitiesHelper::getCityById($service->city)['name']!!}
                        </td>
                        <td>{!! $service->address_first !!}</td>
                        <td>{!! Form::button('select', [
                                    'type' => 'button',
                                    'class' => 'tour_package_select_button',
                                    'data-type' => $filterType,
                                    'data-id' => $service->id,
                                    'data-name' => $service->name]) !!}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('service.edit', $service->id) }}" class="action-btn edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                </a>
                                <a href="#" onclick="confirmServiceDelete(event, '{{ route('service.destroy', $service->id) }}')" class="action-btn delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div id="tour-package-form-service-pagination">
                @include('layouts.pagination', ['paginator' => $services])
            </div>
        </section>
    </div>
</div>

<script>
function confirmServiceDelete(event, deleteUrl) {
    event.preventDefault();
    event.stopPropagation();
    
    if (confirm("Are you sure you want to delete this service?")) {
        const form = document.createElement('form');
        form.action = deleteUrl;
        form.method = 'POST';
        form.style.display = 'none';

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>