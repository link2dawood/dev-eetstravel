<?php
namespace App\Http\Requests\Tour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateTourRequest extends FormRequest {
    public function authorize() { return auth()->check() && auth()->user()->can('tour.edit'); }
    public function rules() {
        $tour = $this->route('tour');
        $id = $tour instanceof \App\Tour ? $tour->id : $tour;
        return [
            'name'=>['sometimes','required','string','min:3','max:255',Rule::unique('tours')->ignore($id)->whereNull('deleted_at')],
            'overview'=>'sometimes|nullable|string|max:5000', 'remark'=>'sometimes|nullable|string|max:2000',
            'departure_date'=>'sometimes|required|date', 'retirement_date'=>'sometimes|nullable|date',
            'pax'=>'sometimes|required|integer|min:1|max:1000', 'rooms'=>'sometimes|nullable|integer|min:1|max:500',
            'country_begin'=>'sometimes|required|string|max:100', 'city_begin'=>'sometimes|required|integer|exists:cities,id',
            'country_end'=>'sometimes|nullable|string|max:100', 'city_end'=>'sometimes|nullable|integer|exists:cities,id',
            'invoice'=>'sometimes|nullable|date', 'ga'=>'sometimes|nullable|date',
            'status'=>'sometimes|required|integer|exists:status,id',
            'responsible_users'=>'sometimes|required|array|min:1', 'responsible_users.*'=>'integer|exists:users,id',
            'assigned_users'=>'sometimes|array', 'assigned_users.*'=>'integer|exists:users,id',
            'external_name'=>'sometimes|required|string|max:255', 'client_id'=>'sometimes|nullable|exists:clients,id',
        ];
    }
    protected function prepareForValidation() {
        $data=$this->all();
        foreach(['name','overview','remark','country_begin','country_end'] as $key) if(isset($data[$key]) && is_string($data[$key])) $data[$key]=trim($data[$key]);
        $this->merge(\App\Support\TourLocations::normalize($data));
    }
    public function withValidator($validator) {
        $validator->after(function($validator) {
            if($validator->errors()->isNotEmpty()) return;
            $tour=$this->route('tour');
            if(!$tour instanceof \App\Tour) $tour=\App\Tour::find($tour);
            if(!$tour) return;
            $start=$this->input('departure_date',$tour->departure_date);
            $end=$this->input('retirement_date',$tour->retirement_date);
            if($start && $end && \Carbon\Carbon::parse($end)->lt(\Carbon\Carbon::parse($start))) $validator->errors()->add('retirement_date','Return date must be on or after departure date.');
        });
    }
}
