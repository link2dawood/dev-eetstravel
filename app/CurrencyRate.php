<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CurrencyRate
 *
 * @property int $id
 * @property string $currency
 * @property float $rate
 * @property string $date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrencyRate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrencyRate whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrencyRate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrencyRate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrencyRate whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CurrencyRate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CurrencyRate extends Model
{
    protected $table = 'currency_rates';
}
