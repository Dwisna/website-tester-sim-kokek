<button type="button" class="theme-toggle btn btn-sm btn-outline-secondary d-flex align-items-center" data-theme-toggle aria-label="Ganti tema tampilan" style="gap:8px;">
    <span class="icon-light" style="display:inline-flex;align-items:center;justify-content:center">@include('components.icon', ['name' => 'sun', 'size' => 16])</span>
    <span class="icon-dark" style="display:none;align-items:center;justify-content:center">@include('components.icon', ['name' => 'moon', 'size' => 16])</span>
</button>

<style>
    /* Toggle visibility based on .dark body class */
    body.dark .theme-toggle .icon-light { display: none !important; }
    body.dark .theme-toggle .icon-dark { display: inline-flex !important; }
</style>
