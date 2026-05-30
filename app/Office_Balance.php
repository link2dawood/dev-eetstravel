<?php

namespace App;
/**
 * App\Client
 *
 * @property int $id
 * @property string $office_id
 * @property string $subject_of_balance
 * @property string $month
 * @property string $total_amount
 * @property string $due_date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */

use Illuminate\Database\Eloquent\Model;

class Office_Balance extends Model
{
    public $timestamps = false;
    protected $table ="office_balance" ;
    /**
     * AUDIT.md CC5 — block mass-assignment of identity / FK columns.
     * Amount / status / business-data fields stay editable so existing
     * update flows (Model::update($request->except(['attach']))) still work,
     * but the FK and identity columns can't be reassigned via a tampered
     * payload.
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'office_id'];
}
