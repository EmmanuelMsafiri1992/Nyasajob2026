<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Models\ApiCredential;
use Illuminate\Http\Request;

class ApiCredentialController extends PanelController
{
    public function setup()
    {
        $this->xPanel->setModel(ApiCredential::class);
        $this->xPanel->setRoute(admin_uri('api-credentials'));
        $this->xPanel->setEntityNameStrings(trans('admin.api_credential'), trans('admin.api_credentials'));

        $this->xPanel->addButtonFromModelFunction('line', 'test', 'testButton', 'beginning');

        // Disable create from list - use dedicated setup page
        $this->xPanel->denyAccess('create');

        $this->setupFilters();
        $this->setupColumns();
        $this->setupFields();
    }

    protected function setupFilters()
    {
        $this->xPanel->addFilter([
            'name' => 'provider',
            'type' => 'dropdown',
            'label' => 'Provider',
        ], collect(ApiCredential::PROVIDERS)->mapWithKeys(fn($p, $k) => [$k => $p['name']])->toArray(),
            fn($value) => $this->xPanel->addClause('where', 'provider', $value)
        );

        $this->xPanel->addFilter([
            'name' => 'is_active',
            'type' => 'dropdown',
            'label' => trans('admin.Status'),
        ], [
            '1' => 'Active',
            '0' => 'Inactive',
        ], fn($value) => $this->xPanel->addClause('where', 'is_active', $value));
    }

    protected function setupColumns()
    {
        $this->xPanel->addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'provider',
            'label' => 'Provider',
            'type' => 'model_function',
            'function_name' => 'getProviderNameHtml',
        ]);

        $this->xPanel->addColumn([
            'name' => 'name',
            'label' => trans('admin.Name'),
            'type' => 'text',
        ]);

        $this->xPanel->addColumn([
            'name' => 'status',
            'label' => trans('admin.Status'),
            'type' => 'model_function',
            'function_name' => 'getStatusBadgeHtml',
        ]);

        $this->xPanel->addColumn([
            'name' => 'usage',
            'label' => 'Daily Usage',
            'type' => 'model_function',
            'function_name' => 'getUsageHtml',
        ]);

        $this->xPanel->addColumn([
            'name' => 'last_used_at',
            'label' => 'Last Used',
            'type' => 'model_function',
            'function_name' => 'getLastUsedHtml',
        ]);
    }

    protected function setupFields()
    {
        $this->xPanel->addField([
            'name' => 'name',
            'label' => trans('admin.Name'),
            'type' => 'text',
            'attributes' => ['placeholder' => 'e.g., Production API Key'],
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);

        $this->xPanel->addField([
            'name' => 'provider',
            'label' => 'Provider',
            'type' => 'select2_from_array',
            'options' => collect(ApiCredential::PROVIDERS)->mapWithKeys(fn($p, $k) => [$k => $p['name']])->toArray(),
            'wrapperAttributes' => ['class' => 'col-md-6'],
            'attributes' => ['disabled' => 'disabled'],
        ]);

        $this->xPanel->addField([
            'name' => 'is_active',
            'label' => 'Active',
            'type' => 'checkbox',
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);

        $this->xPanel->addField([
            'name' => 'rate_limit_per_minute',
            'label' => 'Rate Limit (per minute)',
            'type' => 'number',
            'hint' => 'Max API calls per minute (leave empty for no limit)',
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);

        $this->xPanel->addField([
            'name' => 'daily_quota',
            'label' => 'Daily Quota',
            'type' => 'number',
            'hint' => 'Max API calls per day (leave empty for no limit)',
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);

        $this->xPanel->addField([
            'name' => 'api_base_url',
            'label' => 'Custom API URL',
            'type' => 'url',
            'hint' => 'Override default API base URL (optional)',
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);

        $this->xPanel->addField([
            'name' => 'notes',
            'label' => 'Notes',
            'type' => 'textarea',
            'wrapperAttributes' => ['class' => 'col-md-12'],
        ]);
    }

    /**
     * Custom setup page to configure all API credentials
     */
    public function setup_page()
    {
        $credentials = [];

        foreach (ApiCredential::PROVIDERS as $provider => $config) {
            $credential = ApiCredential::firstOrCreate(
                ['provider' => $provider],
                ['name' => $config['name'], 'is_active' => false]
            );
            $credentials[$provider] = [
                'model' => $credential,
                'config' => $config,
            ];
        }

        return view('admin.api-credentials.setup', [
            'credentials' => $credentials,
            'title' => 'API Credentials Setup',
        ]);
    }

    /**
     * Save API credentials from setup page
     */
    public function save_credentials(Request $request)
    {
        $validated = $request->validate([
            'credentials' => 'required|array',
            'credentials.*.provider' => 'required|string',
            'credentials.*.is_active' => 'nullable|boolean',
        ]);

        foreach ($request->input('credentials', []) as $provider => $data) {
            $credential = ApiCredential::where('provider', $provider)->first();

            if (!$credential) {
                $config = ApiCredential::PROVIDERS[$provider] ?? null;
                if (!$config) continue;

                $credential = new ApiCredential([
                    'provider' => $provider,
                    'name' => $config['name'],
                ]);
            }

            // Build credentials array from provider-specific fields
            $providerConfig = ApiCredential::PROVIDERS[$provider] ?? [];
            $credentialsData = [];

            foreach ($providerConfig['fields'] ?? [] as $field => $fieldConfig) {
                $value = $data[$field] ?? null;
                // Only update if a new value is provided (don't clear existing)
                if (!empty($value)) {
                    $credentialsData[$field] = $value;
                } elseif ($credential->exists) {
                    // Keep existing value
                    $existingCreds = $credential->credentials;
                    if (!empty($existingCreds[$field])) {
                        $credentialsData[$field] = $existingCreds[$field];
                    }
                }
            }

            $credential->fill([
                'is_active' => !empty($data['is_active']),
                'credentials' => $credentialsData,
                'daily_quota' => $data['daily_quota'] ?? null,
                'rate_limit_per_minute' => $data['rate_limit_per_minute'] ?? null,
            ]);

            $credential->save();
        }

        notification('API credentials saved successfully!', 'success');
        return redirect()->back();
    }

    /**
     * Test API credentials
     */
    public function test($id)
    {
        $credential = ApiCredential::findOrFail($id);

        if (!$credential->hasCredentials()) {
            notification('API credentials are not configured', 'error');
            return redirect()->back();
        }

        try {
            $result = $this->testApiConnection($credential);

            if ($result['success']) {
                $credential->markAsVerified();
                notification("API test successful! {$result['message']}", 'success');
            } else {
                notification("API test failed: {$result['message']}", 'error');
            }
        } catch (\Exception $e) {
            notification("API test error: {$e->getMessage()}", 'error');
        }

        return redirect()->back();
    }

    /**
     * Test API connection based on provider
     */
    protected function testApiConnection(ApiCredential $credential): array
    {
        switch ($credential->provider) {
            case 'adzuna':
                return $this->testAdzunaApi($credential);
            case 'jooble':
                return $this->testJoobleApi($credential);
            case 'careerjet':
                return $this->testCareerjetApi($credential);
            default:
                return ['success' => false, 'message' => 'Unknown provider'];
        }
    }

    protected function testAdzunaApi(ApiCredential $credential): array
    {
        $appId = $credential->getCredential('app_id');
        $appKey = $credential->getCredential('app_key');

        $url = "https://api.adzuna.com/v1/api/jobs/gb/search/1?" . http_build_query([
            'app_id' => $appId,
            'app_key' => $appKey,
            'results_per_page' => 1,
            'what' => 'developer',
        ]);

        $response = @file_get_contents($url);

        if ($response === false) {
            return ['success' => false, 'message' => 'Could not connect to Adzuna API'];
        }

        $data = json_decode($response, true);

        if (isset($data['results'])) {
            $count = $data['count'] ?? 0;
            return ['success' => true, 'message' => "Connected successfully. {$count} jobs available."];
        }

        return ['success' => false, 'message' => $data['error'] ?? 'Unknown error'];
    }

    protected function testJoobleApi(ApiCredential $credential): array
    {
        $apiKey = $credential->getCredential('api_key');

        $url = "https://jooble.org/api/{$apiKey}";

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode([
                    'keywords' => 'developer',
                    'location' => 'United States',
                    'page' => 1,
                ]),
                'timeout' => 10,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return ['success' => false, 'message' => 'Could not connect to Jooble API'];
        }

        $data = json_decode($response, true);

        if (isset($data['jobs'])) {
            $count = $data['totalCount'] ?? count($data['jobs']);
            return ['success' => true, 'message' => "Connected successfully. {$count} jobs available."];
        }

        return ['success' => false, 'message' => $data['error'] ?? 'Unknown error'];
    }

    protected function testCareerjetApi(ApiCredential $credential): array
    {
        $affiliateId = $credential->getCredential('affiliate_id');

        $url = "http://public.api.careerjet.net/search?" . http_build_query([
            'affid' => $affiliateId,
            'keywords' => 'developer',
            'location' => 'USA',
            'pagesize' => 1,
            'page' => 1,
            'locale_code' => 'en_US',
        ]);

        $response = @file_get_contents($url);

        if ($response === false) {
            return ['success' => false, 'message' => 'Could not connect to Careerjet API'];
        }

        $data = json_decode($response, true);

        if (isset($data['jobs'])) {
            $count = $data['hits'] ?? count($data['jobs']);
            return ['success' => true, 'message' => "Connected successfully. {$count} jobs available."];
        }

        if (isset($data['error'])) {
            return ['success' => false, 'message' => $data['error']];
        }

        return ['success' => false, 'message' => 'Unknown error'];
    }
}
