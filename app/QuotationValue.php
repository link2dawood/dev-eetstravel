<?php

namespace App;

use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\QuotationValue
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
class QuotationValue extends Model
{
	protected $guarded = [];

	public function row()
	{
		return $this->belongsTo(QuotationRow::class, 'quotation_row_id');
	}
}
