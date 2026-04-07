<div class="photo-slot bg-light rounded d-flex flex-column align-items-center justify-content-center" 
     style="border: 2px dashed #ddd; height: 160px; cursor: pointer; position: relative;"
     onclick="document.getElementById('{{ $id }}').click()">
    
    <div class="mb-2" style="color: {{ $color ?? '#666' }};">
        <i class="{{ $icon ?? 'fas fa-camera' }} fa-2x"></i>
    </div>
    <span class="text-bold text-muted small uppercase" style="line-height: 1.1; font-size: 0.7rem;">{{ $label }}</span>
    
    <input type="file" name="{{ $id }}" id="{{ $id }}" class="d-none" accept="image/*" {{ ($required ?? false) ? 'required' : '' }} onchange="previewPhoto(this, '{{ $id }}')">
    <div id="thumb_{{ $id }}" class="thumb-preview d-none"><img src="" id="img_{{ $id }}"></div>
</div>

<style>
    .thumb-preview { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; background: #fff; padding: 3px; border-radius: 8px; }
    .thumb-preview img { height: 100%; width: 100%; object-fit: cover; border-radius: 6px; }
    .photo-slot:hover { border-color: #007bff !important; background: #fff !important; }
</style>
