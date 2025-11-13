{{-- 
    Guest List Form Component
    Reusable component for guest list creation/editing
--}}
<form id="roomlist_form" action="{{ route('guestlist.store') }}" method="POST">
    @csrf
    <input type="hidden" name="tourId" id="tourId" value="{{ $tour->id }}">
    <input type="hidden" id="show_url" value="{{ route('roomlist.show', ['id' => $tour->id]) }}">
    <input type="hidden" id="send_url" value="{{ route('guestlist.send', ['id' => $tour->id]) }}">
    
    {{-- Form Fields --}}
    <div class="row g-3">
        {{-- Guest List Name --}}
        <div class="col-md-12">
            <label class="form-label required">Guest List Name</label>
            <input name="name" 
                   class="form-control" 
                   type="text" 
                   placeholder="Enter guest list name" 
                   id="guest_list_name" 
                   required>
            <div class="hide validate-name text-danger mt-1">
                <i class="ti ti-alert-circle me-1"></i>
                <span>{!! trans('main.Nameisrequiredfield') !!}</span>
            </div>
        </div>

        {{-- Template & Hotels Row --}}
        <div class="col-md-6">
            <label class="form-label">{!! trans('main.Template') !!}</label>
            <select id="template_selector_guest" 
                    name="template_selector_guest" 
                    class="form-select">
                <option value="" disabled selected>Choose template...</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">{!! trans('main.Hotels') !!}</label>
            <select multiple 
                    id="hotelselect" 
                    name="hotelIds[]" 
                    class="form-select"
                    size="5">
                @foreach($tour->getHotels() as $hotel)
                    @if ($hotel->name)
                        <option value="{{ $hotel->id }}">{!! $hotel->name !!}</option>
                    @endif
                @endforeach
            </select>
            <div class="form-check mt-2">
                <input type="checkbox" id="checkboxallhotels" class="form-check-input">
                <label class="form-check-label" for="checkboxallhotels">
                    {!! trans('main.SelectAll') !!}
                </label>
            </div>
        </div>

        {{-- Editor Area --}}
        <div class="col-md-12">
            <label class="form-label">Guest List Content</label>
            <textarea name="roomlist_textarea" 
                      id="roomlist_textarea"
                      class="form-control" 
                      style="height: 600px; visibility: hidden; display: none;">
            </textarea>
        </div>
    </div>
</form>

