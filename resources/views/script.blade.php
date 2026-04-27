<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. GLOBAL UTILITIES ---
        const inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach(input => {
            input.style.textTransform = "uppercase";
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });

        // Popup closer
        ['success-popup', 'error-popup'].forEach(id => {
            const popup = document.getElementById(id);
            if (popup) {
                setTimeout(() => popup.style.display = 'none', 3000);
            }
        });

        // --- 2. MODAL CONTROLS (Common) ---
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        window.closeCustomModal = closeModal;

        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const modalId = this.getAttribute('onclick')?.match(/'([^']+)'/)?.[1] || this.closest('.custom-modal')?.id;
                if (modalId) closeModal(modalId);
            });
        });

        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('custom-modal')) {
                event.target.style.display = "none";
                document.body.style.overflow = 'auto';
            }
        });

        // --- 3. MASTER DASHBOARD LOGIC ---
        if (typeof Chart !== 'undefined' && document.getElementById('logsChart')) {
            window.openMasterModal = function(id) {
                document.getElementById(id).style.display = 'block';
                document.body.style.overflow = 'hidden'; 
            };
            
            window.closeMasterModal = closeModal;

            // Chart logic (Simplified for partial)
            if (typeof chartDataSets !== 'undefined') {
                window.changeChart = function(period) {
                    // (Logic should be provided by the template or defined globally)
                };
            }
        }

        // --- 4. ADMIN DASHBOARD LIVE MONITORING ---
        const detectionDetails = document.getElementById("detection-details");
        if (detectionDetails && typeof liveUrl !== 'undefined') {
            async function fetchLatestDetection() {
                try {
                    const response = await fetch(liveUrl);
                    if (!response.ok) return;
                    const data = await response.json();
                    if (data.plate) {
                        updateDetectionPanel(data);
                    }
                } catch (error) { console.error(error); }
            }

            function updateDetectionPanel(data) {
                const container = document.getElementById("detection-details");
                const isAuth = data.status.toLowerCase().includes('authorized') && !data.status.toLowerCase().includes('un');
                const statusClass = isAuth ? 'status-authorized' : 'status-unauthorized';
                const icon = isAuth ? 'fa-check-circle' : 'fa-times-circle';

                container.innerHTML = `
                    <div class="detail-item">
                        <label>License Plate</label>
                        <span class="plate-large">${data.plate}</span>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="status-badge ${statusClass}"><i class="fas ${icon}"></i> ${data.status}</span>
                    </div>
                    <div class="detail-item">
                        <label>Owner / Driver</label>
                        <span>${data.owner ? data.owner.f_name + ' ' + data.owner.l_name : 'No Owner Record'}</span>
                    </div>
                    <div class="detail-item">
                        <label>Time Detected</label>
                        <span class="text-slate-500 font-600">${data.timestamp || new Date().toLocaleTimeString()}</span>
                    </div>
                `;
                const lastUpdated = document.getElementById("last-updated");
                if (lastUpdated) lastUpdated.innerText = "Updated: " + new Date().toLocaleTimeString();
            }

            setInterval(fetchLatestDetection, 3000);
            fetchLatestDetection();
        }

        // --- 5. ADMIN LOGS LOGIC ---
        if (document.getElementById('detailsModal')) {
            window.openLogDetails = function(uniqueId, plateNumber) {
                const content = document.getElementById('content-' + uniqueId).innerHTML;
                document.getElementById('detailsModalBody').innerHTML = content;
                document.getElementById('detailsModalTitle').innerText = 'Logs for ' + plateNumber;
                
                const sortSelect = document.getElementById('logSortOrder');
                if (sortSelect) sortSelect.value = 'desc';
                
                new bootstrap.Modal(document.getElementById('detailsModal')).show();
            };

            window.sortModalLogs = function() {
                const order = document.getElementById('logSortOrder').value;
                const tableBody = document.querySelector('#detailsModalBody tbody');
                if (!tableBody) return;

                const rows = Array.from(tableBody.querySelectorAll('tr'));
                rows.sort((a, b) => {
                    const timeA = parseInt(a.getAttribute('data-timestamp')) || 0;
                    const timeB = parseInt(b.getAttribute('data-timestamp')) || 0;
                    return order === 'asc' ? timeA - timeB : timeB - timeA;
                });

                tableBody.innerHTML = '';
                rows.forEach((row, index) => {
                    const indexCell = row.querySelector('.row-index');
                    if (indexCell) indexCell.textContent = index + 1;
                    tableBody.appendChild(row);
                });
            };

            window.openRegisterModal = function(plateNumber) {
                const plateInput = document.getElementById('plate_number');
                if (plateInput) plateInput.value = plateNumber;
                new bootstrap.Modal(document.getElementById('vehicleModal')).show();
            };

            window.openDeleteModal = function(formId) {
                window.currentDeleteFormId = formId; 
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            };

            window.confirmDeletionSubmit = function() {
                if (window.currentDeleteFormId) {
                    document.getElementById(window.currentDeleteFormId).submit();
                }
            };

            window.setDeleteAction = function(actionUrl) {
                const deleteForm = document.getElementById('deleteGuardForm');
                if (deleteForm) deleteForm.action = actionUrl;
            };
        }

        // --- 6. GUARD ACCOUNTS LOGIC ---
        const guardModalEl = document.getElementById('guardModal');
        if (guardModalEl) {
            const guardModal = new bootstrap.Modal(guardModalEl);
            if (typeof hasErrors !== 'undefined' && hasErrors) {
                guardModal.show();
            }
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'create') {
                guardModal.show();
            }
        }

        // --- 7. VEHICLE OWNER LOGIC ---
        window.openEditOwnerModal = function(button) {
            const id = button.getAttribute('data-id');
            const fname = button.getAttribute('data-fname');
            const lname = button.getAttribute('data-lname');
            const address = button.getAttribute('data-address');
            const contact = button.getAttribute('data-contact');
            const type = button.getAttribute('data-type');
            const valid = button.getAttribute('data-valid');
            const rfidCode = button.getAttribute('data-rfid-code');

            const form = document.getElementById('editOwnerForm');
            if (form) {
                form.action = `/admin/vehicle_owners/${id}`;
                document.getElementById('edit_f_name').value = fname;
                document.getElementById('edit_l_name').value = lname;
                document.getElementById('edit_address').value = address;
                document.getElementById('edit_contact_number').value = contact;
                document.getElementById('edit_valid_id').value = valid;
                
                const typeSelect = document.getElementById('edit_type_of_owner');
                if(typeSelect) typeSelect.value = type;

                document.getElementById('edit_rfid_code').value = rfidCode || "";

                new bootstrap.Modal(document.getElementById('editOwnerModal')).show();
            }
        };

        window.openShowOwnerModal = function(button) {
            document.getElementById('show_name').innerText = button.getAttribute('data-fname') + ' ' + button.getAttribute('data-lname');
            document.getElementById('show_address').innerText = button.getAttribute('data-address');
            document.getElementById('show_contact').innerText = button.getAttribute('data-contact');
            document.getElementById('show_valid').innerText = button.getAttribute('data-valid');
            document.getElementById('show_rfid').innerText = button.getAttribute('data-rfid-code') || "None Assigned";
            
            const typeBadge = document.getElementById('show_type_badge');
            const type = button.getAttribute('data-type');
            typeBadge.innerText = type;
            typeBadge.className = 'status-badge ' + (type === 'student' ? 'status-authorized' : 'status-pending');

            new bootstrap.Modal(document.getElementById('showOwnerModal')).show();
        };

        const createOwnerModalEl = document.getElementById('createOwnerModal');
        if (createOwnerModalEl) {
            if (typeof hasErrors !== 'undefined' && hasErrors) {
                new bootstrap.Modal(createOwnerModalEl).show();
            }
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'create') {
                new bootstrap.Modal(createOwnerModalEl).show();
            }
        }

        // --- 8. VEHICLES LOGIC ---
        window.openEditVehicleModal = function(button) {
            const id = button.getAttribute('data-id');
            const owner = button.getAttribute('data-owner');
            const type = button.getAttribute('data-type');
            const plate = button.getAttribute('data-plate');

            const form = document.getElementById('editVehicleForm');
            if (form) {
                form.action = `/admin/vehicles/${id}`;
                if(document.getElementById('edit_plate_number')) document.getElementById('edit_plate_number').value = plate;
                if(document.getElementById('edit_owner_id')) document.getElementById('edit_owner_id').value = owner;
                if(document.getElementById('edit_vehicle_type')) document.getElementById('edit_vehicle_type').value = type;

                new bootstrap.Modal(document.getElementById('editVehicleModal')).show();
            }
        };

        const vehicleModalEl = document.getElementById('vehicleModal');
        if (vehicleModalEl) {
            if (typeof hasErrors !== 'undefined' && hasErrors) {
                new bootstrap.Modal(vehicleModalEl).show();
            }
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'create') {
                new bootstrap.Modal(vehicleModalEl).show();
            }
        }

        // --- 9. GUARD DASHBOARD LOGIC ---
        if (document.getElementById('rfid_input')) {
            const rfidInput = document.getElementById('rfid_input');
            const rfidStatusText = document.getElementById('rfid_status_text');
            
            rfidInput.addEventListener('input', function() {
                if (this.value.length >= 10 && typeof processRfid === 'function') {
                    processRfid(this.value);
                }
            });

            rfidInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && typeof processRfid === 'function') {
                    processRfid(this.value);
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.custom-modal') && !['BUTTON', 'A', 'INPUT'].includes(e.target.tagName)) {
                    rfidInput.focus();
                }
            });
        }
    });
</script>