<?php
namespace App\Observers;

use App\Helpers\Files\Storage\StorageDisk;
use App\Models\ThreadMessage;

class ThreadMessageObserver
{
	/**
	 * Listen to the Entry deleting event.
	 *
	 * @param ThreadMessage $message
	 * @return void
	 */
	public function deleting(ThreadMessage $message)
	{
		if (!empty($message->filename)) {
			// Storage Disk Init.
			$pDisk = StorageDisk::getDisk();
			if (str_starts_with($message->filename, 'resumes/')) {
				$pDisk = StorageDisk::getDisk('private');
			}
			
			// Delete the message's file
			if ($pDisk->exists($message->filename)) {
				$pDisk->delete($message->filename);
			}
		}
	}
}
