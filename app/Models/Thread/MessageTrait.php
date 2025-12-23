<?php
namespace App\Models\Thread;

use Illuminate\Database\Eloquent\Builder;

trait MessageTrait
{
	/**
	 * Recipients of this message.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function recipients()
	{
		return $this->participants()->where('user_id', '!=', $this->user_id);
	}
}
