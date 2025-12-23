<?php
namespace App\Providers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxServiceProvider extends ServiceProvider
{
	/**
	 * Register bindings in the container.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
	
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Storage::extend('dropbox', function ($app, $config) {
			$client = new DropboxClient($config['authorization_token']);
			$adapter = new DropboxAdapter($client);
			
			return new FilesystemAdapter(
				new Filesystem($adapter, $config),
				$adapter,
				$config
			);
		});
	}
}
