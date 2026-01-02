<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Models\JobFeedLog;
use App\Models\JobFeedSource;

class JobFeedLogController extends PanelController
{
    public function setup()
    {
        $this->xPanel->setModel(JobFeedLog::class);
        $this->xPanel->setRoute(admin_uri('job-feeds/logs'));
        $this->xPanel->setEntityNameStrings('fetch log', 'fetch logs');
        $this->xPanel->with(['feedSource']);

        // Read-only - no create/edit/delete
        $this->xPanel->denyAccess(['create', 'update', 'delete']);
        $this->xPanel->orderBy('created_at', 'DESC');

        $this->setupFilters();
        $this->setupColumns();
    }

    protected function setupFilters()
    {
        $this->xPanel->addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => trans('admin.Status'),
        ], [
            'success' => 'Success',
            'partial' => 'Partial',
            'failed' => 'Failed',
        ], fn($value) => $this->xPanel->addClause('where', 'status', $value));

        $this->xPanel->addFilter([
            'name' => 'feed_source_id',
            'type' => 'dropdown',
            'label' => 'Feed Source',
        ], JobFeedSource::pluck('name', 'id')->toArray(),
            fn($value) => $this->xPanel->addClause('where', 'feed_source_id', $value)
        );

        $this->xPanel->addFilter([
            'name' => 'created_at',
            'type' => 'date_range',
            'label' => 'Date',
        ], false, function ($value) {
            $dates = json_decode($value);
            $this->xPanel->addClause('whereDate', 'created_at', '>=', $dates->from);
            $this->xPanel->addClause('whereDate', 'created_at', '<=', $dates->to);
        });
    }

    protected function setupColumns()
    {
        $this->xPanel->addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'feed_source_id',
            'label' => 'Source',
            'type' => 'model_function',
            'function_name' => 'getSourceName',
        ]);

        $this->xPanel->addColumn([
            'name' => 'status',
            'label' => trans('admin.Status'),
            'type' => 'model_function',
            'function_name' => 'getStatusBadgeHtml',
        ]);

        $this->xPanel->addColumn([
            'name' => 'items_found',
            'label' => 'Found',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'items_new',
            'label' => 'New',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'items_duplicate',
            'label' => 'Duplicates',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'items_failed',
            'label' => 'Failed',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'duration_ms',
            'label' => 'Duration',
            'type' => 'model_function',
            'function_name' => 'getDurationFormatted',
        ]);

        $this->xPanel->addColumn([
            'name' => 'error_message',
            'label' => 'Error',
            'type' => 'text',
            'limit' => 50,
        ]);

        $this->xPanel->addColumn([
            'name' => 'created_at',
            'label' => 'Date',
            'type' => 'datetime',
        ]);
    }
}
