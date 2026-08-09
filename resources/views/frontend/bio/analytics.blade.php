@extends('frontend.layout.master')

@section('title', 'Bio Analytics')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Bio Page Analytics</h2>
                <div>
                    <a href="{{ route('freelancer.bio.settings.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <a href="{{ route('freelancer.bio.links.index') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-link"></i> Manage Links
                    </a>
                    <a href="{{ auth()->user()->bio_url }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-eye"></i> View Bio Page
                    </a>
                </div>
            </div>

            <!-- Overview Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Views</h5>
                            <h3>{{ auth()->user()->bio_views ?? 0 }}</h3>
                            <small>Page views</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Clicks</h5>
                            <h3>{{ $analytics['total_clicks'] ?? 0 }}</h3>
                            <small>Link clicks</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Unique Clicks</h5>
                            <h3>{{ $analytics['unique_clicks'] ?? 0 }}</h3>
                            <small>Unique visitors</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Click Rate</h5>
                            <h3>
                                @if(auth()->user()->bio_views > 0)
                                    {{ round(($analytics['total_clicks'] ?? 0) / auth()->user()->bio_views * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </h3>
                            <small>Views to clicks</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Clicks Over Time (Last 30 Days)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="clicksChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Top Performing Links</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($analytics['top_links']) && $analytics['top_links']->count() > 0)
                                @foreach($analytics['top_links'] as $link)
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>{{ $link->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($link->url, 30) }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary">{{ $link->clicks_count }}</span>
                                            <br>
                                            <small class="text-muted">clicks</small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">No link data available yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Link Performance Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Link Performance Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Link</th>
                                    <th>Type</th>
                                    <th>Total Clicks</th>
                                    <th>Today</th>
                                    <th>Last 7 Days</th>
                                    <th>Last 30 Days</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(auth()->user()->bioLinks()->withCount('clicks')->get() as $link)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($link->icon)
                                                    <i class="{{ $link->icon }} me-2"></i>
                                                @endif
                                                <div>
                                                    <strong>{{ $link->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($link->url, 40) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $link->type === 'affiliate' ? 'success' : ($link->type === 'social' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($link->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $link->clicks_count ?? 0 }}</span>
                                        </td>
                                        <td>{{ $link->clicks()->today()->count() }}</td>
                                        <td>{{ $link->clicks()->where('created_at', '>=', now()->subDays(7))->count() }}</td>
                                        <td>{{ $link->clicks()->where('created_at', '>=', now()->subDays(30))->count() }}</td>
                                        <td>
                                            @if($link->clicks_count > 0)
                                                <div class="progress" style="height: 20px;">
                                                    <?php
                                                    $maxClicks = auth()->user()->bioLinks()->withCount('clicks')->max('clicks_count');
                                                    $percentage = $maxClicks > 0 ? ($link->clicks_count / $maxClicks) * 100 : 0;
                                                    ?>
                                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%">
                                                        {{ round($percentage, 0) }}%
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">No data</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Geographic Distribution (if available) -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Geographic Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Top Countries</h6>
                            @php
                            $countryStats = \App\Models\LinkClick::whereHas('bioLink', function($query) {
                                $query->where('user_id', auth()->id());
                            })
                            ->whereNotNull('country')
                            ->selectRaw('country, COUNT(*) as count')
                            ->groupBy('country')
                            ->orderBy('count', 'desc')
                            ->limit(5)
                            ->get();
                            @endphp
                            @if($countryStats->count() > 0)
                                @foreach($countryStats as $country)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $country->country }}</span>
                                        <span class="badge bg-secondary">{{ $country->count }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No geographic data available</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Device Types</h6>
                            @php
                            $deviceStats = [
                                'mobile' => 0,
                                'desktop' => 0,
                                'tablet' => 0
                            ];
                            
                            \App\Models\LinkClick::whereHas('bioLink', function($query) {
                                $query->where('user_id', auth()->id());
                            })
                            ->whereNotNull('user_agent')
                            ->get()
                            ->each(function($click) use (&$deviceStats) {
                                $userAgent = strtolower($click->user_agent);
                                if (strpos($userAgent, 'mobile') !== false || strpos($userAgent, 'android') !== false || strpos($userAgent, 'iphone') !== false) {
                                    $deviceStats['mobile']++;
                                } elseif (strpos($userAgent, 'tablet') !== false || strpos($userAgent, 'ipad') !== false) {
                                    $deviceStats['tablet']++;
                                } else {
                                    $deviceStats['desktop']++;
                                }
                            });
                            @endphp
                            @foreach($deviceStats as $device => $count)
                                @if($count > 0)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ ucfirst($device) }}</span>
                                        <span class="badge bg-secondary">{{ $count }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Clicks over time chart
    const ctx = document.getElementById('clicksChart').getContext('2d');
    
    // Get daily clicks data from analytics or generate sample data
    const dailyClicks = @json($analytics['daily_clicks'] ?? []);
    
    const labels = [];
    const data = [];
    
    // Generate last 30 days labels
    for (let i = 29; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        data.push(dailyClicks[dateStr] || 0);
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Daily Clicks',
                data: data,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endsection
