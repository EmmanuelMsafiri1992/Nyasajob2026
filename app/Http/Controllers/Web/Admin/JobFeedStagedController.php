<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Models\JobFeedStagedItem;
use App\Models\JobFeedSource;
use App\Models\Country;
use App\Services\RssFeed\JobDataCleanerService;
use App\Services\RssFeed\JobImportService;

class JobFeedStagedController extends PanelController
{
    public function setup()
    {
        $this->xPanel->setModel(JobFeedStagedItem::class);
        $this->xPanel->setRoute(admin_uri('job-feeds/staged'));
        $this->xPanel->setEntityNameStrings('staged job', 'staged jobs');
        $this->xPanel->with(['feedSource', 'city', 'category']);

        // Disable create - items come from RSS feeds
        $this->xPanel->denyAccess('create');

        // Add custom buttons
        $this->xPanel->addButtonFromModelFunction('line', 'import_btn', 'importButton', 'beginning');
        $this->xPanel->addButtonFromModelFunction('line', 'process_btn', 'processButton', 'beginning');

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
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'imported' => 'Imported',
            'expired' => 'Expired',
        ], fn($value) => $this->xPanel->addClause('where', 'status', $value));

        $this->xPanel->addFilter([
            'name' => 'feed_source_id',
            'type' => 'dropdown',
            'label' => 'Feed Source',
        ], JobFeedSource::pluck('name', 'id')->toArray(),
            fn($value) => $this->xPanel->addClause('where', 'feed_source_id', $value)
        );

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
            'name' => 'title',
            'label' => 'Job Title',
            'type' => 'text',
            'limit' => 50,
        ]);

        $this->xPanel->addColumn([
            'name' => 'company_name',
            'label' => 'Company',
            'type' => 'text',
            'limit' => 25,
        ]);

        $this->xPanel->addColumn([
            'name' => 'feed_source_id',
            'label' => 'Source',
            'type' => 'model_function',
            'function_name' => 'getSourceName',
        ]);

        $this->xPanel->addColumn([
            'name' => 'country_code',
            'label' => 'Country',
            'type' => 'text',
        ]);

        $this->xPanel->addColumn([
            'name' => 'status',
            'label' => trans('admin.Status'),
            'type' => 'model_function',
            'function_name' => 'getStatusBadgeHtml',
        ]);

        $this->xPanel->addColumn([
            'name' => 'published_at',
            'label' => 'Published',
            'type' => 'datetime',
        ]);
    }

    protected function setupFields()
    {
        $this->xPanel->addField([
            'name' => 'title',
            'label' => 'Job Title',
            'type' => 'text',
            'attributes' => ['readonly' => 'readonly'],
            'wrapperAttributes' => ['class' => 'col-md-12'],
        ]);

        $this->xPanel->addField([
            'name' => 'company_name',
            'label' => 'Company',
            'type' => 'text',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);

        $this->xPanel->addField([
            'name' => 'location_raw',
            'label' => 'Location',
            'type' => 'text',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);

        $this->xPanel->addField([
            'name' => 'status',
            'label' => trans('admin.Status'),
            'type' => 'select2_from_array',
            'options' => [
                'pending' => 'Pending Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ],
            'wrapperAttributes' => ['class' => 'col-md-4'],
        ]);

        $this->xPanel->addField([
            'name' => 'rejection_reason',
            'label' => 'Rejection Reason',
            'type' => 'textarea',
            'wrapperAttributes' => ['class' => 'col-md-8'],
        ]);

        $this->xPanel->addField([
            'name' => 'cleaned_description',
            'label' => 'Cleaned Description',
            'type' => 'textarea',
            'attributes' => ['rows' => 10],
            'wrapperAttributes' => ['class' => 'col-md-12'],
        ]);

        $this->xPanel->addField([
            'name' => 'raw_description',
            'label' => 'Raw Description',
            'type' => 'textarea',
            'attributes' => ['rows' => 10, 'readonly' => 'readonly'],
            'wrapperAttributes' => ['class' => 'col-md-12'],
        ]);
    }

    /**
     * Process a staged item (clean description, resolve location)
     */
    public function process($id)
    {
        $item = JobFeedStagedItem::findOrFail($id);
        $cleaner = app(JobDataCleanerService::class);

        try {
            $cleaner->cleanStagedItem($item);
            notification('Item processed successfully', 'success');
        } catch (\Throwable $e) {
            notification("Processing failed: {$e->getMessage()}", 'error');
        }

        return redirect()->back();
    }

    /**
     * Import a staged item to posts
     */
    public function import($id)
    {
        $item = JobFeedStagedItem::findOrFail($id);
        $importer = app(JobImportService::class);

        $post = $importer->importStagedItem($item);

        if ($post) {
            notification("Job imported as Post #{$post->id}", 'success');
        } else {
            notification('Import failed - check that item has been processed and has valid city', 'error');
        }

        return redirect()->back();
    }

    /**
     * Bulk approve items
     */
    public function bulkApprove()
    {
        $ids = request('entries', []);

        if (empty($ids)) {
            notification('No items selected', 'warning');
            return redirect()->back();
        }

        $count = JobFeedStagedItem::whereIn('id', $ids)
            ->where('status', 'pending')
            ->update(['status' => 'approved']);

        notification("{$count} items approved", 'success');

        return redirect()->back();
    }

    /**
     * Bulk reject items
     */
    public function bulkReject()
    {
        $ids = request('entries', []);

        if (empty($ids)) {
            notification('No items selected', 'warning');
            return redirect()->back();
        }

        $count = JobFeedStagedItem::whereIn('id', $ids)
            ->whereIn('status', ['pending', 'approved'])
            ->update([
                'status' => 'rejected',
                'rejection_reason' => 'Bulk rejected by admin',
            ]);

        notification("{$count} items rejected", 'success');

        return redirect()->back();
    }

    /**
     * Bulk import items
     */
    public function bulkImport()
    {
        $ids = request('entries', []);

        if (empty($ids)) {
            notification('No items selected', 'warning');
            return redirect()->back();
        }

        $importer = app(JobImportService::class);
        $results = $importer->bulkImport($ids);

        $message = "{$results['success']} imported";
        if ($results['failed'] > 0) {
            $message .= ", {$results['failed']} failed";
        }
        if ($results['skipped'] > 0) {
            $message .= ", {$results['skipped']} skipped";
        }

        notification($message, $results['failed'] > 0 ? 'warning' : 'success');

        return redirect()->back();
    }
}
