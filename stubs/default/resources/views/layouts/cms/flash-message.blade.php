<div class="cms-flash-message fs-5">
    <div class="container-xl">
        <div class="alert alert-{{ session('flash_level') ?? 'info' }} fade show d-flex justify-content-between align-items-center py-2 mb-2" role="alert">

            @if(session('flash_level') == 'success')
                <i class="bi bi-check-circle-fill"></i>
            @elseif(session('flash_level') == 'warning')
                <i class="bi bi-exclamation-triangle-fill"></i>
            @elseif(session('flash_level') == 'danger')
                <i class="bi bi-radioactive"></i>
            @else
                <i class="bi bi-info-circle-fill"></i>
            @endif

            <strong>{{ session('flash_message') }}</strong>

            <button type="button" class="btn-close fs-6" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
