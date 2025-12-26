{{--
 * Bootstrap 5 Toast Notifications Component
 * Displays flash notifications as Bootstrap toasts
--}}
@php
    $toasts = session('toast_notifications', []);
    $flashTypes = ['success', 'error', 'warning', 'info'];
    foreach ($flashTypes as $type) {
        if (session()->has($type)) {
            $toasts[] = [
                'level' => $type === 'error' ? 'danger' : $type,
                'message' => session($type),
                'title' => null,
            ];
        }
    }
@endphp

@if (!empty($toasts))
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    @foreach($toasts as $index => $toast)
        @php
            $level = $toast['level'] ?? 'info';
            $bgClass = match($level) {
                'success' => 'text-bg-success',
                'danger', 'error' => 'text-bg-danger',
                'warning' => 'text-bg-warning',
                'info' => 'text-bg-info',
                'primary' => 'text-bg-primary',
                default => 'text-bg-secondary',
            };
            $icon = match($level) {
                'success' => 'fa-solid fa-check-circle',
                'danger', 'error' => 'fa-solid fa-exclamation-circle',
                'warning' => 'fa-solid fa-exclamation-triangle',
                'info' => 'fa-solid fa-info-circle',
                default => 'fa-solid fa-bell',
            };
        @endphp
        <div class="toast align-items-center {{ $bgClass }} border-0 show" 
             role="alert" aria-live="assertive" aria-atomic="true" 
             data-bs-autohide="true" data-bs-delay="{{ $toast['duration'] ?? 5000 }}"
             id="toast-{{ $index }}">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="{{ $icon }} me-2"></i>
                    {!! $toast['message'] !!}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endforeach
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toast').forEach(function(el) {
        new bootstrap.Toast(el).show();
    });
});
</script>
@endif
