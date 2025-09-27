<?php
namespace App\Repository\SettingRepository;

use App\Repository\Contracts\SettingsRepository;
use App\Setting;
use Illuminate\Database\Eloquent\Collection;

/**
* Settings repository
*/
class EloquentSettingsRepository implements SettingsRepository
{
	public function all(): Collection 
	{
		return Setting::all();
	}

	public function getById($id){
	    return Setting::query()->where('id', $id)->first();
    }
}