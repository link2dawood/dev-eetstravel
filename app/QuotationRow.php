<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\QuotationRow
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
class QuotationRow extends Model
{
	protected $guarded = [];

	public function quotation()
	{
		return $this->belongsTo(Quotation::class);
	}

	public function values()
	{
		return $this->hasMany(QuotationValue::class);
	}

	public function getValueByKey($key)
	{
		return $this->values->first(function ($item) use ($key) {
			return $key == $item->key;
		});
	}

}
