<?php

namespace App\Http\Controllers;


use Amranidev\Ajaxis\Ajaxis;
use App\Driver;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Repository\Contracts\DriverRepository;
use App\Transfer;
use Illuminate\Http\Request;
use URL;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;

class DriverController extends Controller
{

    use FileTrait;

    /**
     * @var DriverRepository
     */
    private $driverRepository;

    public function getButton($id, $driver)
	{
		$url = array('show'       => route('driver.show', ['id' => $id]),
		             'edit'       => route('driver.edit', ['id' => $id]),
		             'delete_msg' => "/driver/{$id}/delete_msg");

		return DatatablesHelperController::getActionButton($url, false, $driver);
	}

}
