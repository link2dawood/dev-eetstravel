<?php
namespace App\Repository\Contracts;

interface SettingsRepository {
	public function all();
	public function getById($id);
}