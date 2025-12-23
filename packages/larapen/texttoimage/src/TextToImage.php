<?php
namespace Larapen\TextToImage;

use Larapen\TextToImage\Libraries\Settings;
use Larapen\TextToImage\Libraries\TextToImageEngine;

class TextToImage
{
	/**
	 * @param string $string
	 * @param array $overrides
	 * @param bool $encoded
	 * @return \Larapen\TextToImage\Libraries\TextToImageEngine|string
	 */
	public function make(string $string, array $overrides = [], bool $encoded = true)
	{
		if (trim($string) == '') {
			return $string;
		}
		
		$settings = Settings::createFromIni(__DIR__ . DIRECTORY_SEPARATOR . 'settings.ini');
		$settings->assignProperties($overrides);
		$settings->fontFamily = __DIR__ . '/Libraries/font/' . $settings->fontFamily;
		
		$image = new TextToImageEngine($settings);
		$image->setText($string);
		
		if ($encoded) {
			return $image->getEmbeddedImage();
		}
		
		return $image;
	}
}
