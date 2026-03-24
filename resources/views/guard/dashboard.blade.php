<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guard Dashboard - VLPR</title> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('style')

</head>
<body class="guard-body">

    <audio id="alert_sound" src="{{ asset('sounds/mixkit-sci-fi-error-alert-898.wav') }}" preload="auto"></audio>

    <nav class="navbar-guard">
        <h1 class="logo"><i class="fas fa-shield-alt"></i> Guard Panel</h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn" style="width:auto; background:rgba(255,255,255,0.1); color:white;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>

    <div class="dashboard-container-grid">
        <div class="left-column-stack">
            <!-- Dual Camera Feed -->
            <div class="card video-card">
                <div class="card-header">
                    <h3><i class="fas fa-video" style="color: #3b82f6;"></i> Live Camera Feeds</h3>
                    <p><span style="color:red; font-weight:bold;">●</span> LIVE</p>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; padding: 8px;">
                    <div>
                        <div style="text-align:center; padding:4px 0; font-weight:600; font-size:0.8rem; color:#22c55e;">
                            <i class="fas fa-sign-in-alt"></i> ENTRY GATE
                        </div>
                        <div class="live-stream-container" style="margin:0;">
                            <img src="{{ $detectionBackendUrl }}/video_feed/entry?api_key={{ $detectionApiKey }}"
                                 onerror="this.onerror=null; this.src=''; this.style.opacity='0.5'; this.alt='Entry Camera Offline';"
                                 alt="Entry Gate" style="width:100%; border-radius:6px;">
                        </div>
                    </div>
                    <div>
                        <div style="text-align:center; padding:4px 0; font-weight:600; font-size:0.8rem; color:#ef4444;">
                            <i class="fas fa-sign-out-alt"></i> EXIT GATE
                        </div>
                        <div class="live-stream-container" style="margin:0;">
                            <img src="{{ $detectionBackendUrl }}/video_feed/exit?api_key={{ $detectionApiKey }}"
                                 onerror="this.onerror=null; this.src=''; this.style.opacity='0.5'; this.alt='Exit Camera Offline';"
                                 alt="Exit Gate" style="width:100%; border-radius:6px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- RFID Scanner Input -->
            <div class="card rfid-card">
                <div class="card-header">
                    <h3><i class="fas fa-wifi" style="color: #8b5cf6;"></i> RFID Scanner</h3>
                </div>
                <div class="rfid-content">
                    <div id="rfid_status_text" style="text-align:center; margin-bottom: 5px;">
                        <span style="color: #94a3b8; font-weight: 500;">
                            <i class="fas fa-arrow-down"></i> Tap card or type below
                        </span>
                    </div>
                    <div class="input-group">
                        <input type="text" id="rfid_input" class="rfid-input" placeholder="Tap Card..." autofocus autocomplete="off">
                        <button id="manual_check_btn" class="rfid-btn">Check</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-column-stack">
            <!-- Latest Detection Panel -->
            <div class="card info-card">
                <div class="card-header">
                    <h3><i class="fas fa-id-card" style="color: #f59e0b;"></i> Latest Detection</h3>
                    <p id="last-updated">Waiting...</p>
                </div>
                <div id="detection-details" class="detection-details-container">
                    <div class="placeholder-text">
                        <i class="fas fa-car-side"></i>
                        <p>No vehicle detected yet.</p>
                    </div>
                </div>
            </div>

            <!-- Recent History with Pagination -->
            <div class="card" style="margin-top: 15px; flex-grow: 1; min-height: 300px;">
                <div class="card-header">
                    <h3><i class="fas fa-history" style="color: #64748b;"></i> Recent History</h3>
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;"><i class="fas fa-hand-pointer"></i> Click row for details</p>
                </div>
                <div class="history-list-container" style="padding: 10px;">
                    <table class="table table-sm" style="width: 100%; font-size: 0.9em; margin-bottom: 0;">
                        <thead>
                            <tr style="color: #64748b; border-bottom: 1px solid #eee;">
                                <th style="padding: 8px;">Time</th>
                                <th style="padding: 8px;">Plate</th>
                                <th style="padding: 8px;">Status</th>
                                <th style="padding: 8px;">Method</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                    
                    <!-- Pagination Controls -->
                    <div id="pagination-controls" style="display:none; justify-content:space-between; align-items:center; margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee;">
                        <button id="prevBtn" class="page-btn" onclick="changePage(-1)">&laquo; Prev</button>
                        <span id="pageIndicator" style="font-weight: 600; color: #64748b; font-size: 0.85rem;">Page 1</span>
                        <button id="nextBtn" class="page-btn" onclick="changePage(1)">Next &raquo;</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal: Select Vehicle (For RFID Multi-Vehicle Owners) -->
    <div id="vehicleSelectionModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; color:var(--text-dark); font-size:1.2rem;">
                    <i class="fas fa-car-alt" style="color:var(--primary-color);"></i> Select Vehicle
                </h3>
                <span class="close-btn" onclick="closeModal('vehicleSelectionModal')">&times;</span>
            </div>
            
            <!-- Owner Info Box (Injected via JS) -->
            <div id="selectionOwnerDetails" style="background-color: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--primary-color); display: none;">
            </div>

            <p style="color:var(--text-gray); margin-bottom:15px; font-weight: 500;">Multiple vehicles detected for this owner. Which one is entering?</p>
            <div id="vehicleButtons" style="display:flex; flex-direction:column; gap:10px;"></div>
        </div>
    </div>

    <!-- Modal: History Details (Clickable Rows) -->
    <div id="historyDetailsModal" class="custom-modal">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h3 style="margin:0; color:var(--text-dark); font-size:1.2rem;">
                    <i class="fas fa-list-alt" style="color:var(--primary-color);"></i> Log Details
                </h3>
                <span class="close-btn" onclick="closeModal('historyDetailsModal')">&times;</span>
            </div>
            <div id="historyModalBody">
                <!-- Content injected via JS -->
            </div>
        </div>
    </div>

    <script>
        const csrfToken = "{{ csrf_token() }}";
        const reportUrl = "{{ route('guard.report') }}";
        const liveUrl = "{{ $detectionBackendUrl }}/latest_detection?api_key={{ $detectionApiKey }}"; 
        
        const rfidScanUrl = window.location.origin + "/guard/rfid/scan";
        const selectUrl = window.location.origin + "/guard/rfid/select";
        
        const rfidInput = document.getElementById('rfid_input');
        const rfidStatusText = document.getElementById('rfid_status_text');
        const manualBtn = document.getElementById('manual_check_btn');
        const detectionDetails = document.getElementById("detection-details");
        const alertSound = document.getElementById("alert_sound");
        
        const selectionModal = document.getElementById('vehicleSelectionModal');
        const historyModal = document.getElementById('historyDetailsModal');
        const btnContainer = document.getElementById('vehicleButtons');
        
        let pendingRfid = null;
        let lastAlertedPlate = null;
        let lastProcessedPlate = null; 
        let lastProcessedTime = 0;

        // --- History Pagination State ---
        let allHistoryLogs = {!! json_encode($initialLogs ?? []) !!};
        let currentPage = 1;
        const itemsPerPage = 6;

        document.addEventListener('DOMContentLoaded', () => {
            // Process initial logs from controller to ensure correct formatting
            allHistoryLogs = allHistoryLogs.map(log => {
                let timeDateObj = log.updated_at ? new Date(log.updated_at) : new Date();
                return {
                    ...log,
                    timeString: timeDateObj.toLocaleTimeString('en-US', { hour12: false })
                };
            });
            renderHistoryTable();
        });

        // Focus RFID input unless clicking a modal or button
        document.addEventListener('click', function(e) {
            if (e.target.closest('.custom-modal') || e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.tagName === 'INPUT') { return; }
            rfidInput.focus();
        });

        manualBtn.addEventListener('click', function() { processRfid(rfidInput.value); });
        rfidInput.addEventListener('input', function() { if (rfidInput.value.length >= 10) processRfid(rfidInput.value); });
        rfidInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') processRfid(rfidInput.value); });

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            rfidInput.value = '';
            rfidInput.focus();
        }

        // --- RFID SCANNING LOGIC ---
        async function processRfid(code) {
            if(!code) return;
            rfidInput.blur();
            rfidStatusText.innerHTML = '<span style="color: #f59e0b; font-weight:bold;"><i class="fas fa-spinner fa-spin"></i> Processing...</span>';

            try {
                const response = await fetch(rfidScanUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ rfid_code: code })
                });

                let data;
                if (response.headers.get("content-type")?.includes("application/json")) {
                    data = await response.json();
                } else {
                    throw new Error("Server Error: Response was not JSON.");
                }

                if (data.multiple_vehicles) {
                    pendingRfid = code;
                    showSelectionModal(data.vehicles, data.owner); // Pass owner data to modal
                    rfidStatusText.innerHTML = '<span style="color: #3b82f6;">Please select a vehicle...</span>';
                    return; 
                }

                if (response.ok && data.success) {
                    handleSuccess(data);
                } else {
                    rfidStatusText.innerHTML = `<span style="color: #ef4444; font-weight:bold;"><i class="fas fa-times-circle"></i> ${data.message}</span>`;
                    resetInput();
                }
            } catch (error) {
                console.error("RFID Error:", error);
                rfidStatusText.innerHTML = `<span style="color: #ef4444; font-size: 0.9em;">Network/System Error</span>`;
                resetInput();
            }
        }

        // Display Modal with Owner Info & Vehicle Options
        function showSelectionModal(vehicles, owner) {
            const ownerDetailsDiv = document.getElementById('selectionOwnerDetails');
            
            // Populate Owner Information in Modal
            if (owner) {
                const ownerType = owner.type_of_owner ? owner.type_of_owner.toUpperCase() : 'N/A';
                ownerDetailsDiv.innerHTML = `
                    <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 4px;">Registered Owner</div>
                    <div style="font-size: 1.2rem; font-weight: 700; color: #1e293b;"><i class="fas fa-user-circle" style="margin-right: 5px; color: var(--primary-color);"></i> ${owner.f_name} ${owner.l_name}</div>
                    <div style="font-size: 0.85rem; color: #64748b; margin-top: 5px;">
                        <i class="fas fa-id-badge" style="margin-right: 4px;"></i> ${ownerType} 
                        <span style="margin: 0 8px;">|</span> 
                        <i class="fas fa-phone" style="margin-right: 4px;"></i> ${owner.contact_number || 'No Contact'}
                    </div>
                `;
                ownerDetailsDiv.style.display = 'block';
            } else {
                ownerDetailsDiv.style.display = 'none';
            }

            // Populate Vehicle Buttons
            btnContainer.innerHTML = ''; 
            vehicles.forEach(v => {
                const btn = document.createElement('button');
                btn.innerHTML = `<b>${v.plate_number}</b> <span style="font-size:0.9em; opacity:0.9;">(${v.vehicle_type || 'Unknown Type'})</span>`;
                btn.style.cssText = "padding:12px; background:var(--primary-color); color:white; border:none; border-radius:6px; cursor:pointer; font-size:1rem; transition:0.2s;";
                btn.onclick = () => confirmSelection(v.vehicle_id);
                btnContainer.appendChild(btn);
            });
            selectionModal.style.display = 'block'; 
        }

        async function confirmSelection(vehicleId) {
            selectionModal.style.display = 'none';
            rfidStatusText.innerHTML = '<span style="color: #f59e0b;">Finalizing...</span>';
            try {
                const response = await fetch(selectUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ rfid_code: pendingRfid, vehicle_id: vehicleId })
                });

                if (!response.ok) {
                    alert("System Error: The server returned an error. Check console.");
                    resetInput();
                    return;
                }

                const data = await response.json();
                if (data.success) { 
                    handleSuccess(data); 
                } else { 
                    alert("Error: " + data.message); 
                    resetInput(); 
                }
            } catch(e) { 
                console.error(e); 
                alert("Connection Error: " + e.message);
                resetInput(); 
            }
        }

        function handleSuccess(data) {
            rfidStatusText.innerHTML = `<span style="color: #27ae60; font-weight:bold;"><i class="fas fa-check-circle"></i> Logged: ${data.plate}</span>`;
            updateDetectionPanel(data, true);
            resetInput();
        }

        function resetInput() {
            setTimeout(() => {
                rfidStatusText.innerHTML = '<span style="color: #94a3b8;"><i class="fas fa-arrow-down"></i> Tap card or type below</span>';
                rfidInput.value = '';
                rfidInput.focus();
            }, 3000);
        }

        // --- POLLING & PANEL UPDATE ---
        async function fetchLatestDetection() {
            try {
                const response = await fetch(liveUrl);
                if (!response.ok) return;
                const data = await response.json();
                if (data.plate) updateDetectionPanel(data, false);
            } catch (error) { console.error(error); }
        }

        function updateDetectionPanel(data, isManual = false) {
            const statusStr = (data.status || "").toLowerCase();
            const isAuth = statusStr.includes('authorized') || statusStr.includes('logged out') || statusStr.includes('exited');
            const statusClass = isAuth ? 'status-authorized' : 'status-unauthorized';
            const icon = isAuth ? 'fa-check-circle' : 'fa-times-circle';
            const currentTime = new Date().getTime();

            const isDuplicate = !isManual && data.plate === lastProcessedPlate && (currentTime - lastProcessedTime < 5000);
            
            if (isManual || !isDuplicate) {
                let reportBtn = '';
                if(!isAuth) {
                    reportBtn = `<div style="margin-top:15px; border-top:1px dashed #eee; padding-top:15px;"><button id="reportBtn" onclick="reportVehicle('${data.plate}')" class="delete" style="width:100%;">Report to Admin</button></div>`;
                }
                
                let methodBadge = '';
                if(data.method === 'RFID') methodBadge = '<span style="background:#e8f5e9; color:#2e7d32; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:0.8rem; margin-left: 10px;">RFID</span>';
                else if(data.method && data.method.includes('PLATE')) methodBadge = '<span style="background:#e3f2fd; color:#1565c0; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:0.8rem; margin-left: 10px;">PLATE</span>';

                let ownerHtml = `<span style="color:#ef4444;"><i class="fas fa-user-times"></i> Unregistered</span>`;
                if(data.owner) {
                    ownerHtml = `
                        <div style="display:flex; flex-direction:column; gap:2px;">
                            <span style="font-size:1.1rem;"><i class="fas fa-user" style="color:#94a3b8; margin-right:5px; font-size:0.9rem;"></i> ${data.owner.f_name} ${data.owner.l_name}</span>
                            <span style="font-size:0.9rem; color:#64748b; font-weight:500;"><i class="fas fa-phone" style="color:#94a3b8; margin-right:5px;"></i> ${data.owner.contact_number || 'No Contact Number'}</span>
                        </div>
                    `;
                }

                detectionDetails.innerHTML = `
                    <div class="detail-item">
                        <label>License Plate</label>
                        <div style="display:flex; align-items:center;">
                            <span style="font-size: 2rem;">${data.plate}</span>
                            ${methodBadge}
                        </div>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="status-badge ${statusClass}"><i class="fas ${icon}" style="margin-right:5px;"></i> ${data.status}</span>
                    </div>
                    <div class="detail-item">
                        <label>Owner Information</label>
                        ${ownerHtml}
                    </div>
                    ${reportBtn}
                `;
                document.getElementById("last-updated").innerText = new Date().toLocaleTimeString();

                if (!isAuth) {
                    if (isManual || lastAlertedPlate !== data.plate) {
                        try {
                            alertSound.currentTime = 0;
                            alertSound.play().catch(e => console.log("Audio needed interaction"));
                            lastAlertedPlate = data.plate; 
                        } catch (err) { console.error(err); }
                    }
                } else { lastAlertedPlate = null; }

                // Add to history Array
                addToHistory(data, isAuth, statusStr);
                lastProcessedPlate = data.plate;
                lastProcessedTime = currentTime;
            }
        }

        // --- HISTORY & PAGINATION LOGIC ---
        function addToHistory(data, isAuth, statusStr) {
            let timeDateObj = new Date();
            if (data.updated_at) { timeDateObj = new Date(data.updated_at); } 
            else if (data.time_in) { timeDateObj = new Date(data.time_in); }

            const newLog = {
                ...data,
                timeString: timeDateObj.toLocaleTimeString('en-US', { hour12: false }),
                isAuth: isAuth,
                statusStr: statusStr
            };

            // Prevent UI duplicates by checking the last added plate and time
            if (allHistoryLogs.length > 0) {
                const lastLog = allHistoryLogs[0];
                if (lastLog.plate === newLog.plate && lastLog.status === newLog.status) {
                    // It's a duplicate of the immediate last event, skip adding
                    return;
                }
            }

            allHistoryLogs.unshift(newLog); 
            if(allHistoryLogs.length > 200) allHistoryLogs.pop(); 

            currentPage = 1; 
            renderHistoryTable();
        }

        function renderHistoryTable() {
            const tbody = document.getElementById("history-table-body");
            tbody.innerHTML = '';

            if (allHistoryLogs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 15px;">No history yet...</td></tr>`;
                document.getElementById('pagination-controls').style.display = 'none';
                return;
            }

            document.getElementById('pagination-controls').style.display = 'flex';
            
            const totalPages = Math.ceil(allHistoryLogs.length / itemsPerPage);
            if (currentPage > totalPages) currentPage = totalPages;

            const startIdx = (currentPage - 1) * itemsPerPage;
            const pageLogs = allHistoryLogs.slice(startIdx, startIdx + itemsPerPage);

            pageLogs.forEach((log, index) => {
                const actualIndex = startIdx + index;
                const row = document.createElement('tr');
                row.className = 'history-row';
                row.style.borderBottom = "1px solid #f1f5f9";
                
                let displayStatus = "Unauthorized";
                let statusColor = '#ef4444'; 

                let isAuthorized = log.isAuth;
                if(isAuthorized === undefined) {
                     const str = (log.status || "").toLowerCase();
                     isAuthorized = str.includes('authorized') || str.includes('logged out') || str.includes('exited');
                }

                if (isAuthorized) {
                    statusColor = '#22c55e';
                    const str = (log.statusStr || log.status || "").toLowerCase();
                    if (str.includes('logged out') || str.includes('exited')) { displayStatus = "Log Out"; } 
                    else { displayStatus = "Log In"; }
                }

                let timeStr = log.timeString || (log.updated_at ? new Date(log.updated_at).toLocaleTimeString('en-US', {hour12:false}) : '--');

                row.innerHTML = `
                    <td style="padding: 10px 8px; color: #64748b;">${timeStr}</td>
                    <td style="padding: 10px 8px; font-weight: bold;">${log.plate}</td>
                    <td style="padding: 10px 8px;"><span style="color: ${statusColor}; font-weight:bold; font-size:0.85em;">${displayStatus}</span></td>
                    <td style="padding: 10px 8px; font-size:0.85em; color: #64748b;">${log.method || '-'}</td>
                `;
                
                // Add click listener to open Modal with the exact log index
                row.onclick = () => openHistoryModal(actualIndex);
                tbody.appendChild(row);
            });

            // Update Pagination UI
            document.getElementById('pageIndicator').innerText = `Page ${currentPage} of ${totalPages}`;
            document.getElementById('prevBtn').disabled = currentPage === 1;
            document.getElementById('nextBtn').disabled = currentPage === totalPages;
        }

        window.changePage = function(direction) {
            const totalPages = Math.ceil(allHistoryLogs.length / itemsPerPage);
            currentPage += direction;
            if(currentPage < 1) currentPage = 1;
            if(currentPage > totalPages) currentPage = totalPages;
            renderHistoryTable();
        }

        // --- HISTORY DETAILS MODAL (CLICKABLE ROWS) ---
        function openHistoryModal(index) {
            const log = allHistoryLogs[index];
            const modalBody = document.getElementById('historyModalBody');
            
            // Check if owner exists, otherwise display unregistered message
            let ownerDetails = `<div style="grid-column: span 2; text-align: center; padding: 15px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-weight: 600;"><i class="fas fa-exclamation-triangle"></i> Unregistered Vehicle</div>`;
            
            if (log.owner && log.owner.f_name) {
                ownerDetails = `
                    <div>
                        <label>Owner Name</label>
                        <span>${log.owner.f_name} ${log.owner.l_name}</span>
                    </div>
                    <div>
                        <label>Contact Number</label>
                        <span>${log.owner.contact_number || 'N/A'}</span>
                    </div>
                `;
            }

            // Format dates cleanly
            let timeInStr = log.time_in ? new Date(log.time_in).toLocaleString() : 'N/A';
            let timeOutStr = log.time_out ? new Date(log.time_out).toLocaleString() : '--';

            // Determine method badge styling
            let methodStyle = log.method === 'RFID' ? "background:#e8f5e9; color:#2e7d32;" : "background:#e3f2fd; color:#1565c0;";

            modalBody.innerHTML = `
                <div style="text-align: center; margin-bottom: 20px;">
                    <span style="font-size: 2.5rem; font-weight: 800; color: var(--text-dark);">${log.plate}</span>
                    <div style="margin-top: 5px;">
                        <span style="${methodStyle} padding:4px 12px; border-radius:12px; font-size:0.85rem; font-weight:bold;">Detected via ${log.method || 'PLATE'}</span>
                    </div>
                </div>
                
                <h4 style="font-size:0.9rem; color:#64748b; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Owner Information</h4>
                <div class="details-grid">
                    ${ownerDetails}
                </div>

                <h4 style="font-size:0.9rem; color:#64748b; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Detection Details</h4>
                <div class="details-grid">
                    <div><label>Status</label><span>${log.status || 'N/A'}</span></div>
                    <div><label>Vehicle Type</label><span>${log.vehicle_type || 'Unknown'}</span></div>
                    <div style="grid-column: span 2;"><label>Time In</label><span>${timeInStr}</span></div>
                    <div style="grid-column: span 2;"><label>Time Out</label><span>${timeOutStr}</span></div>
                </div>
            `;
            
            historyModal.style.display = 'block';
        }

        async function reportVehicle(plate) {
            if(!confirm(`Report vehicle ${plate} as unregistered?`)) return;
             try {
                const response = await fetch(reportUrl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                    body: JSON.stringify({ plate_number: plate })
                });
                const d = await response.json();
                if(response.ok) alert(d.message); else alert("Error: " + d.message);
            } catch (e) { console.error(e); }
        }

        setInterval(fetchLatestDetection, 3000);
    </script>
</body>
</html>