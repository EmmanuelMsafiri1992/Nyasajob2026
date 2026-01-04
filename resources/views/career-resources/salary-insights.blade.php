@extends('layouts.master')

@section('content')
    @include('common.spacer')
    <div class="main-container">
        <div class="container">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('career.index') }}">Career Resources</a></li>
                    <li class="breadcrumb-item active">Salary Insights</li>
                </ol>
            </nav>

            {{-- Header --}}
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h2 mb-2"><i class="fa-solid fa-chart-line text-primary me-2"></i> Salary Insights</h1>
                    <p class="text-muted">Explore salary ranges by job category and location to understand your earning potential</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('career.salary-insights') }}" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label for="category" class="form-label">Job Category</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categoriesWithSalary as $cat)
                                    <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="city" class="form-label">Location</label>
                            <select name="city" id="city" class="form-select">
                                <option value="">All Locations</option>
                                @foreach($citiesWithSalary as $city)
                                    <option value="{{ $city->id }}" {{ $selectedCity == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-search me-1"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Salary Statistics --}}
            <div class="row g-4 mb-5">
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="fa-solid fa-briefcase fa-2x"></i>
                            </div>
                            <div class="h3 mb-1">{{ number_format($salaryStats['count']) }}</div>
                            <small class="text-muted">Jobs with Salary Data</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="fa-solid fa-dollar-sign fa-2x"></i>
                            </div>
                            <div class="h3 mb-1">{{ config('country.currency', 'MWK') }} {{ number_format($salaryStats['overall_avg']) }}</div>
                            <small class="text-muted">Average Salary</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="fa-solid fa-arrow-down fa-2x"></i>
                            </div>
                            <div class="h3 mb-1">{{ config('country.currency', 'MWK') }} {{ number_format($salaryStats['min_salary']) }}</div>
                            <small class="text-muted">Minimum Salary</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="fa-solid fa-arrow-up fa-2x"></i>
                            </div>
                            <div class="h3 mb-1">{{ config('country.currency', 'MWK') }} {{ number_format($salaryStats['max_salary']) }}</div>
                            <small class="text-muted">Maximum Salary</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Salary by Category --}}
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fa-solid fa-chart-bar text-primary me-2"></i> Top Paying Categories</h5>
                        </div>
                        <div class="card-body">
                            @if(count($salaryByCategory) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th class="text-end">Avg. Salary</th>
                                                <th class="text-end">Jobs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($salaryByCategory as $index => $cat)
                                            <tr>
                                                <td>
                                                    @if($index < 3)
                                                        <i class="fa-solid fa-trophy text-warning me-1"></i>
                                                    @endif
                                                    {{ $cat['name'] }}
                                                </td>
                                                <td class="text-end fw-bold">{{ config('country.currency', 'MWK') }} {{ number_format($cat['salary']) }}</td>
                                                <td class="text-end text-muted">{{ $cat['jobs'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-4">No salary data available</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Salary by Location --}}
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fa-solid fa-map-marker-alt text-danger me-2"></i> Salary by Location</h5>
                        </div>
                        <div class="card-body">
                            @if(count($salaryByLocation) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Location</th>
                                                <th class="text-end">Avg. Salary</th>
                                                <th class="text-end">Jobs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($salaryByLocation as $index => $loc)
                                            <tr>
                                                <td>
                                                    <i class="fa-solid fa-location-dot text-muted me-1"></i>
                                                    {{ $loc['name'] }}
                                                </td>
                                                <td class="text-end fw-bold">{{ config('country.currency', 'MWK') }} {{ number_format($loc['salary']) }}</td>
                                                <td class="text-end text-muted">{{ $loc['jobs'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center py-4">No salary data available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Salary Tips --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body p-4">
                            <h4 class="mb-4"><i class="fa-solid fa-lightbulb text-warning me-2"></i> Salary Negotiation Tips</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-3">
                                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                                            <strong>Research market rates</strong> - Know the typical salary range for your role and experience level
                                        </li>
                                        <li class="mb-3">
                                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                                            <strong>Consider total compensation</strong> - Include benefits, bonuses, and perks in your evaluation
                                        </li>
                                        <li class="mb-3">
                                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                                            <strong>Document your achievements</strong> - Prepare specific examples of your value
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-3">
                                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                                            <strong>Practice your pitch</strong> - Be confident when discussing salary expectations
                                        </li>
                                        <li class="mb-3">
                                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                                            <strong>Be willing to negotiate</strong> - Have a range in mind, not just one number
                                        </li>
                                        <li class="mb-3">
                                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                                            <strong>Get it in writing</strong> - Always confirm the final offer in writing
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <h4>Ready to find your next opportunity?</h4>
                    <p class="text-muted">Browse thousands of jobs matching your skills and salary expectations</p>
                    <a href="{{ \App\Helpers\UrlGen::search() }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-search me-2"></i> Search Jobs Now
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
