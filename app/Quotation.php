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

	/**
	 * Store calculation as JSON, not PHP-serialized.
	 *
	 * AUDIT.md CC15: the old setter called serialize() and the getter called
	 * unserialize() on the raw value. That's a classic PHP-object-injection
	 * vector — anyone with write access to the calculation column (which is
	 * not restricted by $guarded, see CC5) could inject a serialized object
	 * graph whose __wakeup / __destruct fires on every read. JSON has no
	 * such gadget surface.
	 */
	public function setCalculationAttribute($value)
	{
		$this->attributes['calculation'] = json_encode((array) $value);
	}

	/**
	 * Read calculation back. Try JSON first (the new format) and fall back
	 * to a HARDENED unserialize() — with `allowed_classes: false` so any
	 * non-array/scalar payload becomes an inert __PHP_Incomplete_Class
	 * stub instead of triggering a magic-method gadget. This keeps legacy
	 * rows readable while neutralising the POI attack on them too.
	 */
	public function getCalculationAttribute($value)
	{
		if ($value === null || $value === '') {
			return [];
		}

		$json = json_decode($value, true);
		if (json_last_error() === JSON_ERROR_NONE) {
			return (array) $json;
		}

		// Legacy PHP-serialized row. Block all class instantiation.
		$decoded = @unserialize($value, ['allowed_classes' => false]);
		return is_array($decoded) ? $decoded : [];
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
