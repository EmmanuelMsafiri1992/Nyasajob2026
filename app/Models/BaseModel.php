<?php
namespace App\Models;

use App\Models\Traits\ActiveTrait;
use App\Models\Traits\ColumnTrait;
use App\Models\Traits\VerifiedTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	use ColumnTrait, ActiveTrait, VerifiedTrait;
}
