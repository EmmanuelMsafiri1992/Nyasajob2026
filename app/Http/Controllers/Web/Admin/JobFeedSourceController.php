<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Models\JobFeedSource;
use App\Models\Country;
use App\Models\Category;
use App\Models\PostType;
use App\Services\RssFeed\RssFeedFetcherService;

class JobFeedSourceController extends PanelController
{
    public function setup()
    {
        $this->xPanel->setModel(JobFeedSource::class);
        $this->xPanel->setRoute(admin_uri('job-feeds/sources'));
        $this->xPanel->setEntityNameStrings(trans('admin.feed_source'), trans('admin.feed_sources'));
        $this->xPanel->with(['country', 'category']);

        $this->xPanel->addButtonFromModelFunction('line', 'test_fetch', 'testFetchButton', 'beginning');

        $this->setupFilters();
        $this->setupColumns();
        $this->setupFields();
    }

    protected function setupFilters()
    {
        $this->xPanel->addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => trans('admin.Status'),
        ], [
            'active' => 'Active',
            'paused' => 'Paused',
            'failed' => 'Failed',
            'testing' => 'Testing',
        ], fn($value) => $this->xPanel->addClause('where', 'status', $value));

        $this->xPanel->addFilter([
            'name' => 'country_code',
            'type' => 'dropdown',
            'label' => trans('admin.Country'),
        ], Country::active()->pluck('name', 'code')->toArray(),
            fn($value) => $this->xPanel->addClause('where', 'country_code', $value)
        );
    }

    protected function setupColumns()
    {
        $this->xPanel->addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'name',
            'label' => trans('admin.Name'),
            'type' => 'text',
        ]);

        $this->xPanel->addColumn([
            'name' => 'country_code',
            'label' => trans('admin.Country'),
            'type' => 'model_function',
            'function_name' => 'getCountryNameHtml',
        ]);

        $this->xPanel->addColumn([
            'name' => 'status',
            'label' => trans('admin.Status'),
            'type' => 'model_function',
            'function_name' => 'getStatusBadgeHtml',
        ]);

        $this->xPanel->addColumn([
            'name' => 'total_jobs_fetched',
            'label' => 'Fetched',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'total_jobs_imported',
            'label' => 'Imported',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'success_rate',
            'label' => 'Success %',
            'type' => 'model_function',
            'function_name' => 'getSuccessRateHtml',
        ]);

        $this->xPanel->addColumn([
            'name' => 'last_fetched_at',
            'label' => 'Last Fetch',
            'type' => 'datetime',
        ]);
    }

    protected function setupFields()
    {
        $this->xPanel->addField([
            'name' => 'name',
            'label' => trans('admin.Name'),
            'type' => 'text',
            'attributes' => ['placeholder' => 'e.g., RemoteOK Jobs'],
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);

        $this->xPanel->addField([
            'name' => 'feed_url',
            'label' => 'Feed URL',
            'type' => 'url',
            'attributes' => ['placeholder' => 'https://example.com/jobs.rss'],
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);

        $this->xPanel->addField([
            'name' => 'country_code',
            'label' => trans('admin.Country'),
            'type' => 'select2_from_array',
            'options' => Country::active()->pluck('name', 'code')->toArray(),
            'allows_null' => false,
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);

        $this->xPanel->addField([
            'name' => 'category_id',
            'label' => trans('admin.Category'),
            'type' => 'select2_from_array',
            'options' => ['' => '-- Default --'] + Category::where('parent_id', 0)->orWhereNull('parent_id')->pluck('name', 'id')->toArray(),
            'allows_null' => true,
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);

        $this->xPanel->addField([
            'name' => 'post_type_id',
            'label' => 'Post Type',
            'type' => 'select2_from_array',
            'options' => PostType::pluck('name', 'id')->toArray(),
            'default' => 1,
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);

        $this->xPanel->addField([
            'name' => 'feed_format',
            'label' => 'Feed Format',
            'type' => 'select2_from_array',
            'options' => [
                'rss' => 'RSS 2.0',
                'atom' => 'Atom',
                'json' => 'JSON Feed',
            ],
            'default' => 'rss',
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);

        $this->xPanel->addField([
            'name' => 'status',
            'label' => trans('admin.Status'),
            'type' => 'select2_from_array',
            'options' => [
                'testing' => 'Testing',
                'active' => 'Active',
                'paused' => 'Paused',
            ],
            'default' => 'testing',
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);

        $this->xPanel->addField([
            'name' => 'priority',
            'label' => 'Priority (1-10)',
            'type' => 'number',
            'default' => 5,
            'attributes' => ['min' => 1, 'max' => 10],
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);

        $this->xPanel->addField([
            'name' => 'auto_approve',
            'label' => 'Auto Import',
            'type' => 'checkbox',
            'hint' => 'Jobs will be imported automatically without manual review',
            'wrapperAttributes' => ['class' => 'col-md-3'],
        ]);

        $this->xPanel->addField([
            'name' => 'fetch_interval_minutes',
            'label' => 'Fetch Interval (minutes)',
            'type' => 'number',
            'default' => 360,
            'hint' => 'How often to check for new jobs (360 = 6 hours)',
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);

        $this->xPanel->addField([
            'name' => 'max_items_per_fetch',
            'label' => 'Max Items Per Fetch',
            'type' => 'number',
            'default' => 50,
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);

        $this->xPanel->addField([
            'name' => 'rate_limit_delay_ms',
            'label' => 'Rate Limit Delay (ms)',
            'type' => 'number',
            'default' => 1000,
            'hint' => 'Delay between requests to respect server limits',
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);

        $this->xPanel->addField([
            'name' => 'notes',
            'label' => 'Notes',
            'type' => 'textarea',
            'wrapperAttributes' => ['class' => 'col-md-12'],
        ]);
    }

    /**
     * Test fetch a source
     */
    public function testFetch($id)
    {
        $source = JobFeedSource::findOrFail($id);
        $fetcher = app(RssFeedFetcherService::class);

        $log = $fetcher->fetchSource($source);

        if ($log->status === 'success') {
            notification("Fetch successful: {$log->items_new} new jobs found, {$log->items_duplicate} duplicates", 'success');
        } elseif ($log->status === 'partial') {
            notification("Partial success: {$log->items_new} new, {$log->items_failed} failed", 'warning');
        } else {
            notification("Fetch failed: {$log->error_message}", 'error');
        }

        return redirect()->back();
    }
}
