{{--
    =====================================================
    VIEW: dashboard/navbar/index.blade.php
    =====================================================
    Halaman kelola navbar — khusus author.
    Bisa: tambah menu, edit, hapus, toggle aktif,
    dan drag-and-drop untuk reorder.
    =====================================================
--}}
@extends('layouts.dashboard')
@section('title', 'Kelola Navbar - Ceritaku')

@push('styles')
<style>
    /* --- Navbar Manager Styles --- */
    .navbar-manager {
        max-width: 900px;
    }

    /* drag & drop list */
    .navbar-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .navbar-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.2rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        transition: var(--transition);
        cursor: grab;
        position: relative;
    }

    .navbar-item:active {
        cursor: grabbing;
    }

    .navbar-item:hover {
        border-color: var(--primary-light);
        box-shadow: var(--shadow-sm);
    }

    .navbar-item.dragging {
        opacity: 0.5;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px var(--primary-glow);
    }

    .navbar-item.drag-over {
        border-top: 2px solid var(--primary-color);
    }

    .navbar-item.inactive {
        opacity: 0.5;
        background: var(--bg-accent);
    }

    /* grip handle */
    .drag-handle {
        color: var(--text-muted);
        font-size: 1.1rem;
        cursor: grab;
        padding: 0.2rem;
        flex-shrink: 0;
    }

    .drag-handle:active {
        cursor: grabbing;
    }

    /* icon preview di item */
    .navbar-item-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        background: var(--primary-glow);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    /* info label + url */
    .navbar-item-info {
        flex: 1;
        min-width: 0;
    }

    .navbar-item-info .label {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .navbar-item-info .url {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-family: monospace;
    }

    /* status badge */
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .status-dot.active { background: var(--success); }
    .status-dot.inactive { background: var(--text-muted); }

    /* action buttons per item */
    .navbar-item-actions {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        flex-shrink: 0;
    }

    .navbar-item-actions button,
    .navbar-item-actions a {
        width: 32px;
        height: 32px;
        border-radius: var(--radius-sm);
        border: none;
        background: none;
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        transition: var(--transition);
    }

    .navbar-item-actions .btn-toggle:hover {
        background: rgba(56, 161, 105, 0.1);
        color: var(--success);
    }

    .navbar-item-actions .btn-edit-item:hover {
        background: var(--primary-glow);
        color: var(--primary-color);
    }

    .navbar-item-actions .btn-delete-item:hover {
        background: rgba(229, 62, 62, 0.1);
        color: var(--danger);
    }

    /* add form */
    .navbar-add-form {
        background: var(--bg-card);
        border: 2px dashed var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        margin-top: 1.5rem;
        transition: var(--transition);
    }

    .navbar-add-form:hover,
    .navbar-add-form:focus-within {
        border-color: var(--primary-light);
    }

    .navbar-add-form h4 {
        font-size: 1rem;
        margin-bottom: 1rem;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1.5fr 1fr;
        gap: 0.8rem;
        align-items: end;
    }

    .form-grid-3 .form-group label {
        display: block;
        font-weight: 500;
        font-size: 0.85rem;
        margin-bottom: 0.3rem;
        color: var(--text-muted);
    }

    .form-grid-3 .form-group input {
        width: 100%;
        padding: 0.65rem 0.9rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        background: var(--bg-main);
        color: var(--text-main);
        font-family: 'Outfit', sans-serif;
        font-size: 0.9rem;
        outline: none;
        transition: var(--transition);
    }

    .form-grid-3 .form-group input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px var(--primary-glow);
    }

    .form-grid-3 .form-group input::placeholder {
        color: var(--text-muted);
        opacity: 0.6;
    }

    /* edit inline modal overlay */
    .edit-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 500;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .edit-overlay.active {
        display: flex;
    }

    .edit-modal {
        background: var(--bg-card);
        border-radius: var(--radius-md);
        padding: 2rem;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .edit-modal h3 {
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .edit-modal .form-group {
        margin-bottom: 1.2rem;
    }

    .edit-modal label {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
    }

    .edit-modal input {
        width: 100%;
        padding: 0.7rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        background: var(--bg-main);
        color: var(--text-main);
        font-family: 'Outfit', sans-serif;
        font-size: 0.95rem;
        outline: none;
        transition: var(--transition);
    }

    .edit-modal input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .edit-modal .modal-actions {
        display: flex;
        gap: 0.8rem;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }

    /* icon hint */
    .icon-hint {
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .icon-hint a {
        color: var(--primary-color);
        text-decoration: underline;
    }

    /* responsive */
    @media (max-width: 768px) {
        .form-grid-3 {
            grid-template-columns: 1fr;
        }
        
        .navbar-item {
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .navbar-item-actions {
            width: 100%;
            justify-content: flex-end;
            margin-top: 0.3rem;
            padding-top: 0.5rem;
            border-top: 1px solid var(--border-color);
        }
    }
</style>
@endpush

@section('content')
<div class="navbar-manager">
    {{-- header --}}
    <div class="dash-header">
        <div>
            <h1><i class="fa-solid fa-compass"></i> Kelola Navbar</h1>
            <p>Atur menu navigasi website — drag untuk ubah urutan</p>
        </div>
    </div>

    {{-- info total --}}
    <div class="dash-card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
            <span style="font-size: 0.9rem; color: var(--text-muted);">
                <i class="fa-solid fa-bars-staggered"></i> 
                Total: <strong style="color: var(--text-main);">{{ $navbarItems->count() }}</strong> menu
            </span>
            <span style="font-size: 0.9rem; color: var(--success);">
                <i class="fa-solid fa-circle-check"></i> 
                Aktif: <strong>{{ $navbarItems->where('is_active', true)->count() }}</strong>
            </span>
            <span style="font-size: 0.9rem; color: var(--text-muted);">
                <i class="fa-solid fa-circle-xmark"></i> 
                Nonaktif: <strong>{{ $navbarItems->where('is_active', false)->count() }}</strong>
            </span>
        </div>
    </div>

    {{-- daftar menu navbar --}}
    <div class="navbar-list" id="navbar-list">
        @forelse($navbarItems as $item)
            <div class="navbar-item {{ $item->is_active ? '' : 'inactive' }}" 
                 draggable="true" 
                 data-id="{{ $item->id }}">
                {{-- drag handle --}}
                <div class="drag-handle" title="Seret untuk ubah urutan">
                    <i class="fa-solid fa-grip-vertical"></i>
                </div>

                {{-- icon preview --}}
                <div class="navbar-item-icon">
                    <i class="{{ $item->icon ?? 'fa-solid fa-link' }}"></i>
                </div>

                {{-- info --}}
                <div class="navbar-item-info">
                    <div class="label">{{ $item->label }}</div>
                    <div class="url">{{ $item->url }}</div>
                </div>

                {{-- status dot --}}
                <div class="status-dot {{ $item->is_active ? 'active' : 'inactive' }}" 
                     title="{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}"></div>

                {{-- actions --}}
                <div class="navbar-item-actions">
                    {{-- toggle aktif --}}
                    <form action="{{ route('dashboard.navbar.toggle', $item->id) }}" method="POST" style="display:inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-toggle" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="fa-solid {{ $item->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                        </button>
                    </form>

                    {{-- edit --}}
                    <button class="btn-edit-item" 
                            title="Edit" 
                            onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->label) }}', '{{ addslashes($item->url) }}', '{{ addslashes($item->icon) }}')">
                        <i class="fa-solid fa-pen"></i>
                    </button>

                    {{-- hapus --}}
                    <form action="{{ route('dashboard.navbar.destroy', $item->id) }}" method="POST" style="display:inline"
                          onsubmit="return confirm('Yakin mau hapus menu {{ $item->label }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete-item" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state" style="padding: 2rem;">
                <i class="fa-solid fa-compass" style="font-size: 2.5rem; opacity: 0.3;"></i>
                <p>Belum ada menu navigasi. Tambahin di bawah ya!</p>
            </div>
        @endforelse
    </div>

    {{-- form tambah menu baru --}}
    <div class="navbar-add-form">
        <h4><i class="fa-solid fa-plus-circle"></i> Tambah Menu Baru</h4>
        <form action="{{ route('dashboard.navbar.store') }}" method="POST">
            @csrf
            <div class="form-grid-3">
                <div class="form-group">
                    <label>Label</label>
                    <input type="text" name="label" placeholder="Contoh: Beranda" required>
                </div>
                <div class="form-group">
                    <label>URL</label>
                    <input type="text" name="url" placeholder="Contoh: / atau /explore" required>
                </div>
                <div class="form-group">
                    <label>Icon (opsional)</label>
                    <input type="text" name="icon" placeholder="fa-solid fa-home">
                </div>
            </div>
            <div class="icon-hint">
                <i class="fa-solid fa-circle-info"></i>
                Icon dari <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">Font Awesome</a> — contoh: fa-solid fa-home
            </div>
            <div style="margin-top: 1rem; text-align: right;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Menu
                </button>
            </div>
        </form>
    </div>
</div>

{{-- edit modal --}}
<div class="edit-overlay" id="edit-overlay">
    <div class="edit-modal">
        <h3><i class="fa-solid fa-pen-to-square"></i> Edit Menu</h3>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Label</label>
                <input type="text" name="label" id="edit-label" required>
            </div>
            <div class="form-group">
                <label>URL</label>
                <input type="text" name="url" id="edit-url" required>
            </div>
            <div class="form-group">
                <label>Icon</label>
                <input type="text" name="icon" id="edit-icon" placeholder="fa-solid fa-home">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- EDIT MODAL ---
    function openEditModal(id, label, url, icon) {
        document.getElementById('edit-form').action = '/dashboard/navbar/' + id;
        document.getElementById('edit-label').value = label;
        document.getElementById('edit-url').value = url;
        document.getElementById('edit-icon').value = icon;
        document.getElementById('edit-overlay').classList.add('active');
    }

    function closeEditModal() {
        document.getElementById('edit-overlay').classList.remove('active');
    }

    // klik di luar modal: tutup
    document.getElementById('edit-overlay').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    // ESC: tutup modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEditModal();
    });

    // --- DRAG AND DROP REORDER ---
    (function() {
        const list = document.getElementById('navbar-list');
        if (!list) return;

        let draggedItem = null;

        list.querySelectorAll('.navbar-item').forEach(item => {
            // drag start
            item.addEventListener('dragstart', function(e) {
                draggedItem = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });

            // drag end
            item.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                list.querySelectorAll('.navbar-item').forEach(el => {
                    el.classList.remove('drag-over');
                });
                draggedItem = null;

                // kirim urutan baru ke server
                saveOrder();
            });

            // drag over
            item.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                
                list.querySelectorAll('.navbar-item').forEach(el => {
                    el.classList.remove('drag-over');
                });
                
                if (this !== draggedItem) {
                    this.classList.add('drag-over');
                }
            });

            // drop
            item.addEventListener('drop', function(e) {
                e.preventDefault();
                if (draggedItem && draggedItem !== this) {
                    // pindahin posisi DOM
                    const rect = this.getBoundingClientRect();
                    const midY = rect.top + rect.height / 2;
                    if (e.clientY < midY) {
                        list.insertBefore(draggedItem, this);
                    } else {
                        list.insertBefore(draggedItem, this.nextSibling);
                    }
                }
            });
        });

        // simpan urutan baru via fetch
        function saveOrder() {
            const items = list.querySelectorAll('.navbar-item');
            const order = Array.from(items).map(item => parseInt(item.dataset.id));

            fetch('{{ route("dashboard.navbar.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ order: order }),
            })
            .then(res => res.json())
            .then(data => {
                // feedback visual sukses
                if (data.success) {
                    const items = list.querySelectorAll('.navbar-item');
                    items.forEach((item, i) => {
                        item.style.transition = 'background 0.3s ease';
                        item.style.background = 'var(--primary-glow)';
                        setTimeout(() => {
                            item.style.background = '';
                        }, 500);
                    });
                }
            })
            .catch(err => console.error('Gagal simpan urutan:', err));
        }
    })();
</script>
@endpush
