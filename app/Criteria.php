<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CriteriaType;

/**
 * App\Criteria
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string|null $icon
 * @property string|null $criteria_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Criteria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Criteria whereCriteriaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Criteria whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Criteria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Criteria whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Criteria whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Criteria whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Criteria extends Model
{
    protected $table = 'criterias';

    public function criteriaType()
    {
		return CriteriaType::findOrFail($this->criteria_type);    	
    }
}
