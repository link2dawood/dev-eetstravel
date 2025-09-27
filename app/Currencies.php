<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Currencies
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $symbol
 * @property string|null $cent
 * @property string|null $symbol_cent
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Currencies whereCent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Currencies whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Currencies whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Currencies whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Currencies whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Currencies whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Currencies whereSymbolCent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Currencies whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Currencies extends Model
{
    protected $table = 'currencies';
}
