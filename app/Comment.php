<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Trackable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Comment
 *
 * @property int $id
 * @property string|null $content
 * @property int $author_id
 * @property int $reference_type
 * @property int $reference_id
 * @property int $status
 * @property int|null $parent
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $childs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereReferenceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
    use Trackable;


    protected $table = 'comments';
    protected $guarded = [];

    public static $services = [
        'tour' => '1',
        'task' => '2',
        'event' => '3',
        'restaurant' => '4',
        'hotel' => '5',
        'guide' => '6',
        'transfer' => '7',
        'flight' => '8',
        'cruises' => '9',
        'tour_package' => '10',
        'status' => '11',
        'room_types' => '12',
        'rate' => '13',
        'currency_rate' => '14',
        'currencies' => '15',
        'criteria' => '16',
        'client' => '17',
        'bus' => '18',
        'BusDay' => '19',
        'Templates' => '20',
	    'comparison' => '21',
		'client_invoices' => '22',
		'invoices' => '23',
    ];

    public static $serviceRoute = [
        '1' => 'tour.show',
        '2' => 'task.show',
        '3' => 'event.show',
        '4' => 'restaurant.show',
        '5' => 'hotel.show',
        '6' => 'guide.show',
        '7' => 'transfer.show',
        '8' => 'flights.show',
        '9' => 'cruises.show',
        '10' => 'tour_package.show',
        '11' => 'status.show',
        '12' => 'room_types.show',
        '13' => 'rate.show',
        '14' => 'currency_rate.show',
        '15' => 'currencies.show',
        '16' => 'criteria.show',
        '17' => 'clients.show',
        '18' => 'bus.show',
	    '21' => 'comparison.show',
		'22' => 'accounting.show',
		'23' => 'invoices.show',
    ];
    public $service_type = '';

    public function user()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function childs()
    {
        return $this->hasMany('App\Comment', 'parent', 'id');
    }

    public function author()
    {
        return $this->belongsTo('App\User');
    }

    public function delete()
    {
        $this->childs()->delete();

        return parent::delete();
    }
    public function findServiceType()
    {
        $serviceType = array_search($this->reference_type, self::$services);
        $service_explode = explode('_', $serviceType);
        $serviceType = '';
        foreach ($service_explode as $item){
            $serviceType .= ucfirst($item);
        }
        $namespace = 'App\\' . ucfirst($serviceType);
        $this->service_type = ucfirst($serviceType);
        return $namespace;
    }

    public function service()
    {
        $namespace = $this->findServiceType();
        $model = $namespace::find($this->reference_id);
        return $model ?? '';
    }

    public function serviceName()
    {
        if($this->service()) {
            $name = $this->service()->name ?? $this->service()->content ?? $this->service()->first_name ?? $this->service()->date;
            if (!$name) return '';
            return "{$name} ({$this->service_type})";
        }else{
            return '';
        }

    }
}
