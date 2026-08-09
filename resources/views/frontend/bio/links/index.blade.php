@extends('frontend.layout.master')

@section('title', 'Manage Bio Links')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Manage Bio Links</h2>
                <div>
                    <a href="{{ route('freelancer.bio.analytics') }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-chart-bar"></i> Analytics
                    </a>
                    <a href="{{ route('freelancer.bio.settings.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <a href="{{ auth()->user()->bio_url }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-eye"></i> View Bio Page
                    </a>
                </div>
            </div>

            <!-- Analytics Overview -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Clicks</h5>
                            <h3>{{ $analytics['total_clicks'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Unique Clicks</h5>
                            <h3>{{ $analytics['unique_clicks'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Links</h5>
                            <h3>{{ $links->where('is_active', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Bio Views</h5>
                            <h3>{{ auth()->user()->bio_views ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Links</h5>
                    <a href="{{ route('bio.links.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Link
                    </a>
                </div>
                <div class="card-body">
                    @if($links->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="linksTable">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Title</th>
                                        <th>URL</th>
                                        <th>Type</th>
                                        <th>Clicks</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($links as $link)
                                        <tr data-id="{{ $link->id }}" data-order="{{ $link->sort_order }}">
                                            <td>
                                                <i class="fas fa-grip-vertical grip-handle text-muted"></i>
                                                <span class="order-number">{{ $link->sort_order }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($link->icon)
                                                        <i class="{{ $link->icon }} me-2"></i>
                                                    @endif
                                                    @if($link->is_featured)
                                                        <i class="fas fa-star text-warning me-1" title="Featured"></i>
                                                    @endif
                                                    {{ $link->title }}
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ $link->url }}" target="_blank" class="text-decoration-none">
                                                    {{ Str::limit($link->url, 40) }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $link->type === 'affiliate' ? 'success' : ($link->type === 'social' ? 'info' : 'secondary') }}">
                                                    {{ ucfirst($link->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $link->clicks_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm {{ $link->is_active ? 'btn-success' : 'btn-secondary' }} toggle-status"
                                                            data-id="{{ $link->id }}"
                                                            title="{{ $link->is_active ? 'Active' : 'Inactive' }}">
                                                        <i class="fas {{ $link->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                                    </button>
                                                    @if($link->type === 'affiliate')
                                                        <button class="btn btn-sm {{ $link->is_featured ? 'btn-warning' : 'btn-outline-warning' }} toggle-featured"
                                                                data-id="{{ $link->id }}"
                                                                title="{{ $link->is_featured ? 'Featured' : 'Not Featured' }}">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('freelancer.bio.links.edit', $link) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger delete-link" data-id="{{ $link->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-link fa-3x text-muted mb-3"></i>
                            <h4>No Links Yet</h4>
                            <p class="text-muted">Start adding links to your bio page to share with your audience.</p>
                            <a href="{{ route('freelancer.bio.links.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Your First Link
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Performing Links -->
            @if(isset($analytics['top_links']) && $analytics['top_links']->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Top Performing Links (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($analytics['top_links'] as $link)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $link->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($link->url, 50) }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary">{{ $link->clicks_count }}</span>
                                            <br>
                                            <small class="text-muted">clicks</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this link? This action cannot be undone.</p>
                <p class="text-muted"><strong>Note:</strong> Click history for this link will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle link status
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const linkId = this.dataset.id;
            const isActive = this.classList.contains('btn-success');
            
            fetch(`{{ route('freelancer.bio.links.toggle-status', '') }}`.replace('/0', '/' + linkId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle button classes
                    this.classList.toggle('btn-success');
                    this.classList.toggle('btn-secondary');
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                    this.title = data.is_active ? 'Active' : 'Inactive';
                }
            });
        });
    });

    // Toggle featured status
    document.querySelectorAll('.toggle-featured').forEach(button => {
        button.addEventListener('click', function() {
            const linkId = this.dataset.id;
            
            fetch(`{{ route('freelancer.bio.links.toggle-featured', '') }}`.replace('/0', '/' + linkId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle button classes
                    this.classList.toggle('btn-warning');
                    this.classList.toggle('btn-outline-warning');
                    this.title = data.is_featured ? 'Featured' : 'Not Featured';
                }
            });
        });
    });

    // Delete link
    let deleteLinkId = null;
    
    document.querySelectorAll('.delete-link').forEach(button => {
        button.addEventListener('click', function() {
            deleteLinkId = this.dataset.id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteLinkId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('freelancer.bio.links.destroy', '') }}`.replace('/0', '/' + deleteLinkId);
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Sortable functionality
    const tbody = document.querySelector('#linksTable tbody');
    let draggedRow = null;

    tbody.addEventListener('dragstart', function(e) {
        if (e.target.classList.contains('grip-handle') || e.target.closest('tr')) {
            draggedRow = e.target.closest('tr');
            draggedRow.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        }
    });

    tbody.addEventListener('dragend', function(e) {
        if (draggedRow) {
            draggedRow.classList.remove('dragging');
            draggedRow = null;
        }
    });

    tbody.addEventListener('dragover', function(e) {
        e.preventDefault();
        const afterElement = getDragAfterElement(tbody, e.clientY);
        if (afterElement == null) {
            tbody.appendChild(draggedRow);
        } else {
            tbody.insertBefore(draggedRow, afterElement);
        }
    });

    tbody.addEventListener('drop', function(e) {
        e.preventDefault();
        updateOrder();
    });

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('tr:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function updateOrder() {
        const links = [];
        document.querySelectorAll('#linksTable tbody tr').forEach((row, index) => {
            links.push({
                id: row.dataset.id,
                sort_order: index + 1
            });
            row.querySelector('.order-number').textContent = index + 1;
        });

        fetch('{{ route('freelancer.bio.links.reorder') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ links: links })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to update order');
            }
        });
    }
});
</script>

<style>
.grip-handle {
    cursor: grab;
    margin-right: 10px;
}

.grip-handle:active {
    cursor: grabbing;
}

.dragging {
    opacity: 0.5;
}

.order-number {
    font-weight: bold;
    color: #666;
}

#linksTable tr {
    transition: all 0.3s ease;
}

#linksTable tr:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
