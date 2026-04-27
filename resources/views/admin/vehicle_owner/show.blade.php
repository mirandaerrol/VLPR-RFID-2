<div class="card card-padding" style="border: none; box-shadow: none;">
    <div class="flex items-center gap-6 mb-8">
        <div class="flex-center" style="width: 5rem; height: 5rem; background-color: var(--slate-100); border-radius: 1rem;">
            <i class="fas fa-user-tie" style="font-size: 2.5rem; color: var(--slate-400);"></i>
        </div>
        <div>
            <h2 id="show_name" class="text-3xl font-800 text-dark m-0"></h2>
            <span id="show_type_badge" class="status-badge status-authorized mt-2" style="text-transform: uppercase; font-size: 0.7rem;"></span>
        </div>
    </div>

    <div class="admin-monitor-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="detail-item">
            <label><i class="fas fa-map-marker-alt"></i> Address</label>
            <span id="show_address" class="text-slate-600"></span>
        </div>
        <div class="detail-item">
            <label><i class="fas fa-phone"></i> Contact Number</label>
            <span id="show_contact" class="text-slate-600"></span>
        </div>
        <div class="detail-item">
            <label><i class="fas fa-id-card"></i> Valid ID Number</label>
            <span id="show_valid" class="text-slate-600"></span>
        </div>
        <div class="detail-item">
            <label><i class="fas fa-wifi"></i> Assigned RFID Tag</label>
            <span id="show_rfid" style="color: var(--green-500); font-family: monospace;" class="font-bold"></span>
        </div>
    </div>

    <div class="flex gap-4 mt-8" style="justify-content: flex-end;">
        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Close Details</button>
    </div>
</div>
