<?php
namespace App\Http\Controllers\Web\Post\CreateOrEdit\MultiSteps\Traits\Create;

trait ClearTmpInputTrait
{
	/**
	 * Clear Temporary Inputs & Files
	 */
	public function clearTemporaryInput()
	{
		if (session()->has('postInput')) {
			$postInput = (array)session()->get('postInput');
			if (isset($postInput['company'], $postInput['company']['logo'])) {
				$filePath = $postInput['company']['logo'];
				try {
					$this->removePictureWithItsThumbs($filePath);
				} catch (\Throwable $e) {
					if (!empty($e->getMessage())) {
						flash($e->getMessage())->error();
					}
				}
			}
			session()->forget('postInput');
		}
		
		if (session()->has('paymentInput')) {
			session()->forget('paymentInput');
		}
		
		if (session()->has('uid')) {
			session()->forget('uid');
		}
	}
}
