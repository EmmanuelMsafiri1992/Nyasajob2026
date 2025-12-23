<?php
namespace App\Http\Controllers\Web\Account;

use Larapen\LaravelMetaTags\Facades\MetaTag;

class TransactionsController extends AccountBaseController
{
	/**
	 * List Transactions
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function index()
	{
		// Call API endpoint
		$endpoint = '/payments';
		$queryParams = [
			'embed' => 'post,paymentMethod,package,currency',
			'sort'  => 'created_at',
		];
		$queryParams = array_merge(request()->all(), $queryParams);
		$data = makeApiRequest('get', $endpoint, $queryParams);
		
		$apiMessage = $this->handleHttpError($data);
		$apiResult = data_get($data, 'result');
		
		// Meta Tags
		MetaTag::set('title', t('My Transactions'));
		MetaTag::set('description', t('My Transactions on', ['appName' => config('settings.app.name')]));
		
		return appView('account.transactions', compact('apiResult', 'apiMessage'));
	}
}
