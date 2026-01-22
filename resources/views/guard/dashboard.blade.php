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

            <div class="card video-card">
                <div class="card-header">
                    <h3><i class="fas fa-video" style="color: #3b82f6;"></i> Live Feed</h3>
                    <p><span style="color:red; font-weight:bold;">●</span> LIVE</p>
                </div>
                <div class="live-stream-container">
                    <img src="{{ env('FLASK_API_URL', 'http://127.0.0.1:5000') }}/video_feed"
                         onerror="this.onerror=null; this.src=''; this.style.opacity='0.5'; this.alt='Stream Offline (Check Ngrok)';"
                         alt="Live Stream">
                </div>
            </div>

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
                        <input type="text"
                               id="rfid_input"
                               class="rfid-input"
                               placeholder="Tap Card..."
                               autofocus
                               autocomplete="off">
                        <button id="manual_check_btn" class="rfid-btn">Check</button>
                    </div>
                </div>
            </div>

        </div>

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
        </div>

    </div>
    
    <div id="vehicleSelectionModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; color:var(--text-dark); font-size:1.2rem;">
                    <i class="fas fa-car-alt" style="color:var(--primary-color);"></i> Select Vehicle
                </h3>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            
            <p style="color:var(--text-gray); margin-bottom:15px;">
                This owner has multiple vehicles. Which one is entering?
            </p>
            
            <div id="vehicleButtons" style="display:flex; flex-direction:column; gap:10px;">
                </div>
        </div>
    </div>

    <script>
        const csrfToken = "{{ csrf_token() }}";
        const reportUrl = "{{ route('guard.report') }}";
        const liveUrl = "{{ route('vehicle_detect_live') }}";
        const rfidScanUrl = "{{ route('guard.rfid.scan') }}";
        
        // --- ELEMENTS ---
        const rfidInput = document.getElementById('rfid_input');
        const rfidStatusText = document.getElementById('rfid_status_text');
        const manualBtn = document.getElementById('manual_check_btn');
        const detectionDetails = document.getElementById("detection-details");
        const alertSound = document.getElementById("alert_sound");

        let lastAlertedPlate = null;

        const selectUrl = "{{ route('guard.rfid.select') }}";
        const modal = document.getElementById('vehicleSelectionModal');
        const btnContainer = document.getElementById('vehicleButtons');
        let pendingRfid = null;

        // EVENT LISTENERS 
        document.addEventListener('click', function(e) {
            // Prevent auto-focus if clicking inside modal or buttons
            if (e.target.closest('.custom-modal') || e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.tagName === 'INPUT') {
                return;
            }
            rfidInput.focus();
        });

        manualBtn.addEventListener('click', function() {
            processRfid(rfidInput.value);
        });

        rfidInput.addEventListener('input', function() {
            if (rfidInput.value.length >= 10) {
                processRfid(rfidInput.value);
            }
        });

        rfidInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                processRfid(rfidInput.value);
            }
        });

        function closeModal() {
            modal.style.display = 'none';
            rfidInput.value = '';
            rfidInput.focus();
        }

        // PROCESS RFID (MANUAL)
        async function processRfid(code) {
            if(!code) return;
            rfidInput.blur(); 
            rfidStatusText.innerHTML = '<span style="color: #f59e0b; font-weight:bold;"><i class="fas fa-spinner fa-spin"></i> Processing...</span>';

            try {
                const response = await fetch(rfidScanUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ rfid_code: code })
                });

                const data = await response.json();

                if (data.multiple_vehicles) {
                    pendingRfid = code;
                    showSelectionModal(data.vehicles);
                    rfidStatusText.innerHTML = '<span style="color: #3b82f6;">Please select a vehicle...</span>';
                    return; 
                }S
                if (response.ok && data.success) {
                    handleSuccess(data);
                } else {
                    rfidStatusText.innerHTML = `<span style="color: #ef4444; font-weight:bold;"><i class="fas fa-times-circle"></i> ${data.message}</span>`;
                    setTimeout(() => {
                        rfidStatusText.innerHTML = '<span style="color: #94a3b8;"><i class="fas fa-arrow-down"></i> Tap card or type below</span>';
                        rfidInput.value = '';
                        rfidInput.focus();
                    }, 2500);
                }

            } catch (error) {
                console.error("RFID Error:", error);
                rfidStatusText.innerHTML = '<span style="color: #ef4444;">System Error</span>';
                setTimeout(() => {
                        rfidStatusText.innerHTML = '<span style="color: #94a3b8;"><i class="fas fa-arrow-down"></i> Tap card or type below</span>';
                        rfidInput.value = '';
                        rfidInput.focus();
                    }, 2500);
            }
        }

        // SHOW MODAL
        function showSelectionModal(vehicles) {
            btnContainer.innerHTML = ''; 
            
            vehicles.forEach(v => {
                const btn = document.createElement('button');
                btn.innerHTML = `<b>${v.plate_number}</b> <span style="font-size:0.9em; opacity:0.9;">(${v.vehicle_type})</span>`;
                btn.style.cssText = "padding:12px; background:var(--primary-color); color:white; border:none; border-radius:6px; cursor:pointer; font-size:1rem; transition:0.2s;";
                btn.onmouseover = function() { this.style.background = 'var(--primary-dark)'; };
                btn.onmouseout = function() { this.style.background = 'var(--primary-color)'; };
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
                
                if (data.success) {
                    handleSuccess(data);
                } else {
                    alert("Error logging selection");
                    resetInput();
                }
            } catch(e) {
                console.error(e);
                resetInput();
            }
        }

        // Helper for success state
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

        // UPDATE UI
        function updateDetectionPanel(data, isManual = false) {
            const statusStr = (data.status || "").toLowerCase();
            const isAuth = statusStr.includes('authorized') || statusStr.includes('logged out');
            
            const statusClass = isAuth ? 'status-authorized' : 'status-unauthorized';
            const icon = isAuth ? 'fa-check-circle' : 'fa-times-circle';
            
            if (!isAuth) {
                if (isManual || lastAlertedPlate !== data.plate) {
                    try {
                        alertSound.currentTime = 0;
                        alertSound.play().catch(e => console.log("Audio needed interaction"));
                        lastAlertedPlate = data.plate; 
                    } catch (err) { console.error(err); }
                }
            } else {
                lastAlertedPlate = null; 
            }

            let reportBtn = '';
            if(!isAuth) {
                reportBtn = `<div style="margin-top:20px; border-top:1px dashed #eee; padding-top:15px;">
                    <button id="reportBtn" onclick="reportVehicle('${data.plate}')" class="delete" style="width:100%;">Report to Admin</button>
                </div>`;
            }
            let methodBadge = '';
            if(data.method === 'RFID') {
                methodBadge = '<span style="background:#e8f5e9; color:#2e7d32; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:0.8rem; margin-left: 10px;">RFID</span>';
            } else if(data.method && (data.method.includes('PLATE') || data.method.includes('CAM'))) {
                methodBadge = '<span style="background:#e3f2fd; color:#1565c0; padding:2px 8px; border-radius:4px; font-weight:bold; font-size:0.8rem; margin-left: 10px;">PLATE</span>';
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
                    <label>Owner</label>
                    <span>${data.owner ? data.owner.f_name + ' ' + data.owner.l_name : 'No Owner Record'}</span>
                </div>
                ${reportBtn}
            `;
            document.getElementById("last-updated").innerText = new Date().toLocaleTimeString();
        }

        // Live Fetch Loop
        async function fetchLatestDetection() {
            try {
                const response = await fetch(liveUrl);
                if (!response.ok) return;
                const data = await response.json();
                if (data.plate) {
                    updateDetectionPanel(data, false);
                }
            } catch (error) { console.error(error); }
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
                if(response.ok) alert(d.message);
                else alert("Error: " + d.message);
            } catch (e) { console.error(e); }
        }

        setInterval(fetchLatestDetection, 3000);
    </script>
</body>
</html>