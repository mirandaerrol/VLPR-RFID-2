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
        <!-- LEFT COLUMN -->
        <div class="left-column-stack">
            <!-- Video Feed -->
            <div class="card video-card">
                <div class="card-header">
                    <h3><i class="fas fa-video" style="color: #3b82f6;"></i> Live Feed</h3>
                    <p><span style="color:red; font-weight:bold;">●</span> LIVE</p>
                </div>
                <div class="live-stream-container">
                    <!-- HYBRID MODE: Connects to Local Python App on your laptop -->
                    <img src="http://127.0.0.1:5000/video_feed"
                         onerror="this.onerror=null; this.src=''; this.style.opacity='0.5'; this.alt='Stream Offline (Check Local Python App)';"
                         alt="Live Stream">
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

        <!-- RIGHT COLUMN -->
        <div class="right-column-stack">
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

            <!-- Recent History -->
            <div class="card" style="margin-top: 15px; flex-grow: 1; min-height: 300px;">
                <div class="card-header">
                    <h3><i class="fas fa-history" style="color: #64748b;"></i> Recent History (Last 10)</h3>
                </div>
                <div class="history-list-container" style="overflow-y: auto; max-height: 400px; padding: 10px;">
                    <table class="table table-sm" style="width: 100%; font-size: 0.9em;">
                        <thead>
                            <tr style="color: #64748b; border-bottom: 1px solid #eee;">
                                <th style="padding: 8px;">Time</th>
                                <th style="padding: 8px;">Plate</th>
                                <th style="padding: 8px;">Status</th>
                                <th style="padding: 8px;">Method</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body">
                            <tr>
                                <td colspan="4" style="text-align: center; color: #94a3b8; padding: 15px;">No history yet...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Vehicle Selection Modal -->
    <div id="vehicleSelectionModal" class="selection-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; color:var(--text-dark); font-size:1.2rem;">
                    <i class="fas fa-car-alt" style="color:var(--primary-color);"></i> Select Vehicle
                </h3>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            <p style="color:var(--text-gray); margin-bottom:15px;">This owner has multiple vehicles. Which one is entering?</p>
            <div id="vehicleButtons" style="display:flex; flex-direction:column; gap:10px;"></div>
        </div>
    </div>

    <script>
        const csrfToken = "{{ csrf_token() }}";
        const reportUrl = "{{ route('guard.report') }}";
        
        // HYBRID MODE: Connects to Local Python App on your laptop for real-time polling
        const liveUrl = "http://127.0.0.1:5000/latest_detection"; 
        
        // DYNAMIC URL GENERATION TO FIX MIXED CONTENT ISSUES
        // Uses window.location.origin to ensure HTTPS is used if the site is on HTTPS
        const rfidScanUrl = window.location.origin + "/guard/rfid/scan";
        const selectUrl = window.location.origin + "/guard/rfid/select";
        
        const rfidInput = document.getElementById('rfid_input');
        const rfidStatusText = document.getElementById('rfid_status_text');
        const manualBtn = document.getElementById('manual_check_btn');
        const detectionDetails = document.getElementById("detection-details");
        const alertSound = document.getElementById("alert_sound");
        const historyTableBody = document.getElementById("history-table-body");
        
        const modal = document.getElementById('vehicleSelectionModal');
        const btnContainer = document.getElementById('vehicleButtons');
        let pendingRfid = null;
        let lastAlertedPlate = null;
        let lastProcessedPlate = null; 
        let lastProcessedTime = 0;

        document.addEventListener('click', function(e) {
            if (e.target.closest('.custom-modal') || e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.tagName === 'INPUT') { return; }
            rfidInput.focus();
        });

        manualBtn.addEventListener('click', function() { processRfid(rfidInput.value); });
        rfidInput.addEventListener('input', function() { if (rfidInput.value.length >= 10) processRfid(rfidInput.value); });
        rfidInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') processRfid(rfidInput.value); });

        function closeModal() {
            modal.style.display = 'none';
            rfidInput.value = '';
            rfidInput.focus();
        }

        async function processRfid(code) {
            if(!code) return;
            rfidInput.blur();
            rfidStatusText.innerHTML = '<span style="color: #f59e0b; font-weight:bold;"><i class="fas fa-spinner fa-spin"></i> Processing...</span>';

            try {
                const response = await fetch(rfidScanUrl, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ rfid_code: code })
                });

                let data;
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.error("Non-JSON response:", text);
                    throw new Error("Server Error: Response was not JSON.");
                }

                if (data.multiple_vehicles) {
                    pendingRfid = code;
                    showSelectionModal(data.vehicles);
                    rfidStatusText.innerHTML = '<span style="color: #3b82f6;">Please select a vehicle...</span>';
                    return; 
                }

                if (response.ok && data.success) {
                    handleSuccess(data);
                } else {
                    rfidStatusText.innerHTML = `<span style="color: #ef4444; font-weight:bold;"><i class="fas fa-times-circle"></i> ${data.message}</span>`;
                    setTimeout(() => {
                        rfidStatusText.innerHTML = '<span style="color: #94a3b8;"><i class="fas fa-arrow-down"></i> Tap card or type below</span>';
                        rfidInput.value = '';
                        rfidInput.focus();
                    }, 4000);
                }
            } catch (error) {
                console.error("RFID Error:", error);
                // More user friendly error message if it's a fetch failure
                let msg = error.message;
                if (msg === 'Failed to fetch') msg = 'Network Error: Cannot reach server.';
                rfidStatusText.innerHTML = `<span style="color: #ef4444; font-size: 0.9em;">${msg || 'System Error'}</span>`;
                setTimeout(() => {
                    rfidStatusText.innerHTML = '<span style="color: #94a3b8;"><i class="fas fa-arrow-down"></i> Tap card or type below</span>';
                    rfidInput.value = '';
                    rfidInput.focus();
                }, 4000);
            }
        }

        function showSelectionModal(vehicles) {
            btnContainer.innerHTML = ''; 
            vehicles.forEach(v => {
                const btn = document.createElement('button');
                btn.innerHTML = `<b>${v.plate_number}</b> <span style="font-size:0.9em; opacity:0.9;">(${v.vehicle_type})</span>`;
                btn.style.cssText = "padding:12px; background:var(--primary-color); color:white; border:none; border-radius:6px; cursor:pointer; font-size:1rem; transition:0.2s;";
                btn.onclick = () => confirmSelection(v.vehicle_id);
                btnContainer.appendChild(btn);
            });
            modal.style.display = 'block'; 
        }

        async function confirmSelection(vehicleId) {
            modal.style.display = 'none';
            rfidStatusText.innerHTML = '<span style="color: #f59e0b;">Finalizing...</span>';
            try {
                const response = await fetch(selectUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ rfid_code: pendingRfid, vehicle_id: vehicleId })
                });
                const data = await response.json();
                if (data.success) { handleSuccess(data); } 
                else { alert("Error: " + data.message); resetInput(); }
            } catch(e) { console.error(e); resetInput(); }
        }

        function handleSuccess(data) {
            rfidStatusText.innerHTML = `<span style="color: #27ae60; font-weight:bold;"><i class="fas fa-check-circle"></i> Logged: ${data.plate}</span>`;
            updateDetectionPanel({
                plate: data.plate,
                status: data.status,
                method: data.method,
                owner: data.owner
            }, true);
            rfidInput.value = '';
            setTimeout(() => {
                rfidStatusText.innerHTML = '<span style="color: #94a3b8;"><i class="fas fa-arrow-down"></i> Tap card or type below</span>';
                rfidInput.focus();
            }, 2500);
        }

        function resetInput() {
            setTimeout(() => {
                rfidStatusText.innerHTML = '<span style="color: #94a3b8;"><i class="fas fa-arrow-down"></i> Tap card or type below</span>';
                rfidInput.value = '';
                rfidInput.focus();
            }, 2500);
        }

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
            
            // UPDATED: Removed client-side timestamp check.
            // We now trust the backend to only send us recent data.
            let isFresh = true; 

            if (isFresh || isManual) {
                let reportBtn = '';
                if(!isAuth) {
                    reportBtn = `<div style="margin-top:20px; border-top:1px dashed #eee; padding-top:15px;"><button id="reportBtn" onclick="reportVehicle('${data.plate}')" class="delete" style="width:100%;">Report to Admin</button></div>`;
                }
                
                let methodBadge = '';
                if(data.method === 'RFID') methodBadge = '<span style="background:#e8f5e9; color:#2e7d32; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:0.8rem; margin-left: 10px;">RFID</span>';
                else if(data.method && (data.method.includes('PLATE') || data.method.includes('CAM'))) methodBadge = '<span style="background:#e3f2fd; color:#1565c0; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:0.8rem; margin-left: 10px;">PLATE</span>';

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
                        <label>Owner</label>
                        <span>${data.owner ? data.owner.f_name + ' ' + data.owner.l_name : 'No Owner Record'}</span>
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

                // FIXED: Removed "&& isManual" so camera detections are also added to history
                if (!isDuplicate) {
                    addToHistory(data, isAuth, statusStr);
                    lastProcessedPlate = data.plate;
                    lastProcessedTime = currentTime;
                }
            }
        }

        function addToHistory(data, isAuth, statusStr) {
            const emptyRow = historyTableBody.querySelector('td[colspan="4"]');
            if (emptyRow) emptyRow.parentElement.remove();

            const row = document.createElement('tr');
            row.style.borderBottom = "1px solid #f1f5f9";
            
            const timeString = new Date().toLocaleTimeString('en-US', { hour12: false });
            let displayStatus = "Unauthorized Login";
            let statusColor = '#ef4444'; 

            if (isAuth) {
                statusColor = '#22c55e';
                // FIXED LOGIC: Check for both 'logged out' and 'exited'
                if (statusStr.includes('logged out') || statusStr.includes('exited')) {
                    displayStatus = "Log Out";
                } else {
                    displayStatus = "Authorize Login";
                }
            }

            row.innerHTML = `
                <td style="padding: 8px; color: #64748b;">${timeString}</td>
                <td style="padding: 8px; font-weight: bold;">${data.plate}</td>
                <td style="padding: 8px;"><span style="color: ${statusColor}; font-weight:bold; font-size:0.85em;">${displayStatus}</span></td>
                <td style="padding: 8px; font-size:0.85em; color: #64748b;">${data.method || '-'}</td>
            `;

            historyTableBody.insertBefore(row, historyTableBody.firstChild);
            if (historyTableBody.children.length > 10) historyTableBody.lastElementChild.remove();
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