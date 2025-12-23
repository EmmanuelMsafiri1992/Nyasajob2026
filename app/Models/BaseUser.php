<?php
namespace App\Models;

use App\Models\Traits\ColumnTrait;
use App\Models\Traits\VerifiedTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BaseUser extends Authenticatable
{
	use ColumnTrait, VerifiedTrait;
}
