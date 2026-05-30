<?php

namespace App;
/**
 * App\Client
 *
 * @property int $id
 * @property string $office_id
 * @property string $month
 * @property string $total_earning_amount
 * @property string $total_amount_after_removing_all_expense
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */




use Illuminate\Database\Eloquent\Model;

class Office_Earnings extends Model
{
    public $timestamps = false;
    protected $table ="office_earnings" ;
    /**
     * AUDIT.md CC5 — block mass-assignment of identity / FK columns.
     * Amount / status / business-data fields stay editable so existing
     * update flows (Model::update($request->except(['attach']))) still work,
     * but the FK and identity columns can't be reassigned via a tampered
     * payload.
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'office_id'];
}
