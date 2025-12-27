@extends('admin.layouts.master')

@section('header')
    <div class="row page-titles">
        <div class="col-md-12">
            <h4 class="mb-0">{{ $title ?? trans('admin.php_info') }}</h4>
        </div>
        <div class="col-md-12">
            <ol class="breadcrumb mb-0 p-0 bg-transparent">
                <li class="breadcrumb-item"><a href="{{ admin_url('dashboard') }}">{{ trans('admin.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ admin_url('system') }}">{{ trans('admin.system_info') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('admin.php_info') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fa-brands fa-php me-2"></i>
                        {{ trans('admin.php_info') }}
                    </h5>
                    <a href="{{ admin_url('system') }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> {{ trans('admin.back') }}
                    </a>
                </div>
                <div class="card-body">
                    <style>
                        .phpinfo-container table {
                            width: 100%;
                            margin-bottom: 1rem;
                            border-collapse: collapse;
                        }
                        .phpinfo-container table td,
                        .phpinfo-container table th {
                            padding: 0.5rem;
                            border: 1px solid #dee2e6;
                            vertical-align: top;
                        }
                        .phpinfo-container table tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }
                        .phpinfo-container h1 {
                            font-size: 1.5rem;
                            margin-bottom: 1rem;
                            color: #5c4ac7;
                        }
                        .phpinfo-container h2 {
                            font-size: 1.25rem;
                            margin: 1.5rem 0 0.75rem;
                            padding-bottom: 0.5rem;
                            border-bottom: 2px solid #5c4ac7;
                            color: #333;
                        }
                        .phpinfo-container .e {
                            background-color: #f1f1f1;
                            font-weight: 600;
                            width: 30%;
                        }
                        .phpinfo-container .v {
                            word-break: break-all;
                        }
                        .phpinfo-container img {
                            display: none;
                        }
                        .phpinfo-container a:link {
                            color: #5c4ac7;
                        }
                        .phpinfo-container hr {
                            margin: 1rem 0;
                        }
                    </style>
                    <div class="phpinfo-container">
                        {!! $phpinfo !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
