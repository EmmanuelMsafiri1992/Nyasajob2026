@extends('admin.layouts.master')

@section('header')
    <section class="content-header">
        <h1>Premium Subscriptions Dashboard</h1>
        <ol class="breadcrumb">
            <li><a href="{{ admin_url() }}">Dashboard</a></li>
            <li class="active">Premium Subscriptions</li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="row">
        {{-- Statistics Cards --}}
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total Subscriptions</p>
                </div>
                <div class="icon"><i class="fa fa-users"></i></div>
                <a href="{{ admin_url('premium-subscriptions') }}" class="small-box-footer">
                    View All <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $stats['active'] }}</h3>
                    <p>Active Subscriptions</p>
                </div>
                <div class="icon"><i class="fa fa-check-circle"></i></div>
                <a href="{{ admin_url('premium-subscriptions?status=active') }}" class="small-box-footer">
                    View Active <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $stats['expiring_soon'] }}</h3>
                    <p>Expiring Soon (7 days)</p>
                </div>
                <div class="icon"><i class="fa fa-clock-o"></i></div>
                <a href="#expiring" class="small-box-footer">
                    View Details <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>${{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon"><i class="fa fa-dollar"></i></div>
                <span class="small-box-footer">
                    This Month: ${{ number_format($stats['monthly_revenue'], 2) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Status Breakdown --}}
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Subscription Status</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <td>Active</td>
                            <td><span class="badge bg-green">{{ $stats['active'] }}</span></td>
                        </tr>
                        <tr>
                            <td>Pending</td>
                            <td><span class="badge bg-yellow">{{ $stats['pending'] }}</span></td>
                        </tr>
                        <tr>
                            <td>Cancelled</td>
                            <td><span class="badge bg-secondary">{{ $stats['cancelled'] }}</span></td>
                        </tr>
                        <tr>
                            <td>Expired</td>
                            <td><span class="badge bg-red">{{ $stats['expired'] }}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="box-footer">
                    <form action="{{ route('admin.premium-subscriptions.expire-overdue') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fa fa-clock-o"></i> Expire Overdue Subscriptions
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Recent Subscriptions --}}
        <div class="col-md-8">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Recent Subscriptions</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ admin_url('premium-subscriptions') }}" class="btn btn-info btn-sm">View All</a>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Expires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentSubscriptions as $sub)
                                <tr>
                                    <td>{{ $sub->user->name ?? 'N/A' }}</td>
                                    <td>{{ $sub->user->email ?? 'N/A' }}</td>
                                    <td><span class="badge {{ $sub->statusBadgeClass }}">{{ $sub->statusLabel }}</span></td>
                                    <td>${{ number_format($sub->amount, 2) }}</td>
                                    <td>{{ $sub->expires_at?->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ admin_url('premium-subscriptions/' . $sub->id . '/edit') }}" class="btn btn-xs btn-default">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @if ($sub->isActive())
                                            <form action="{{ route('admin.premium-subscriptions.cancel', $sub->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Cancel this subscription?');">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></button>
                                            </form>
                                            <form action="{{ route('admin.premium-subscriptions.extend', $sub->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Extend by 1 month?');">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-success"><i class="fa fa-plus"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No subscriptions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($expiringSoon->isNotEmpty())
        <div class="row" id="expiring">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Expiring Soon (Within 7 Days)</h3>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Expires</th>
                                    <th>Auto Renew</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expiringSoon as $sub)
                                    <tr>
                                        <td>{{ $sub->user->name ?? 'N/A' }}</td>
                                        <td>{{ $sub->user->email ?? 'N/A' }}</td>
                                        <td>{{ $sub->expires_at->format('M d, Y') }} ({{ $sub->daysRemaining() }} days)</td>
                                        <td>{{ $sub->auto_renew ? 'Yes' : 'No' }}</td>
                                        <td>
                                            <form action="{{ route('admin.premium-subscriptions.extend', $sub->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-success">
                                                    <i class="fa fa-plus"></i> Extend 1 Month
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
