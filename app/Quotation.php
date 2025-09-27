<?php

namespace App;

use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Quotation
 *
 * @property int $id
 * @property string $note
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Quotation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Quotation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Quotation whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Quotation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Quotation extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
	protected $guarded = [];

	public function rows()
	{
		return $this->hasMany(QuotationRow::class);
	}

	public function tour()
	{
		return $this->belongsTo(Tour::class);
	}

	public function userName(){
	    $user = User::query()->where('id', $this->user_id)->first();
	    if(!$user) return ' ';

	    return $user->name;
    }

	public function getRowByDate($date)
	{
		return $this->rows->first(function($item) use ($date) {
			$value = $item->getValueByKey('date');
			if(!empty($value->value)){
			return $value->value == $date;
			}
			else{
				return $value->value??"";
			}
		});
	}

	public function getValueByDate($date, $key)
	{

		$row = $this->getRowByDate($date);
		if ($row) {
			$valueObject = $row->getValueByKey($key);
			if ($valueObject) {
				return $valueObject->value;
			}

		}
		return "";
	}
	
	public function getValueObjectByDate($date, $key)
	{
		$row = $this->getRowByDate($date);
		if ($row) {
			return $row->getValueByKey($key);
		}
		return null;
	}

	public function setAdditionalPersonsAttribute($value)
	{
		foreach ($value as $key => $item) {
			if ($item['person'] == 0) {
				unset($value[$key]);
			}
		}

		$this->attributes['additional_persons'] = json_encode($value);
	}

	public function getAdditionalPersonsAttribute($value)
	{
		return (array)json_decode($value);
	}

	public function setCalculationAttribute($value)
	{
		$this->attributes['calculation'] = serialize((array)$value);
	}

	public function getCalculationAttribute($value)
	{
		return unserialize($value);
	}

	public function getCalculationJson()
	{
		return json_encode((array)$this->calculation);
	}

	public function setAdditionalColumnsAttribute($value)
	{
		$this->attributes['additional_columns'] = json_encode($value);
	}

	public function getAdditionalColumnsAttribute($value)
	{
		return (array)json_decode($value);
	}

	public function setAdditionalColumnValuesAttribute($value)
	{
		$this->attributes['additional_column_values'] = json_encode($value);
	}

	public function getAdditionalColumnValuesAttribute($value)
	{
		return (array)json_decode($value);
	}

	public function getAdditionalColumnValueCell($row, $column)
	{
		$cell = (@array_filter($this->additional_column_values,
			function($value) use ($row, $column){
				return ($value->row==$row) && ($value->cell == $column);
			}));

		if ($cell) {
			return reset($cell);
		}
		return null;
	}
}
