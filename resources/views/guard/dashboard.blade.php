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

    <!-- AUDIO ELEMENT -->
    <audio id="alert_sound" src="{{ asset('sounds/mixkit-sci-fi-error-alert-898.wav') }}" preload="auto"></audio>

    <nav class="navbar-guard">
        <h1 class="logo"><i class="fas fa-shield-alt"></i> Guard Panel</h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
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
                    <img src="http://127.0.0.1:5000/video_feed"
                         onerror="this.onerror=null; this.src=''; this.style.opacity='0.5';"
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

        // Track last alerted plate to prevent loop beeping from camera
        let lastAlertedPlate = null;

        // --- EVENT LISTENERS ---
        document.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A' && e.target.tagName !== 'INPUT') {
                rfidInput.focus();
            }
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

        // --- PROCESS RFID (MANUAL) ---
        async function processRfid(code) {
            if(!code) return;
            rfidInput.value = '';
            rfidInput.focus();

            rfidStatusText.innerHTML = '<span style="color: #f59e0b; font-weight:bold;"><i class="fas fa-spinner fa-spin"></i> Processing...</span>';

            try {
                const response = await fetch(rfidScanUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ rfid_code: code })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    rfidStatusText.innerHTML = `<span style="color: #27ae60; font-weight:bold;"><i class="fas fa-check-circle"></i> Logged: ${data.plate}</span>`;
                    
                    // TRUE = This is a manual scan, so FORCE the sound even if it's the same card
                    updateDetectionPanel({
                        plate: data.plate,
                        status: data.status,
                        owner: data.owner
                    }, true);

                } else {
                    rfidStatusText.innerHTML = `<span style="color: #ef4444; font-weight:bold;"><i class="fas fa-times-circle"></i> ${data.message}</span>`;
                }

                setTimeout(() => {
                    rfidStatusText.innerHTML = '<span style="color: #94a3b8;"><i class="fas fa-arrow-down"></i> Tap card or type below</span>';
                }, 2500);

            } catch (error) {
                console.error("RFID Error:", error);
                rfidStatusText.innerHTML = '<span style="color: #ef4444;">System Error</span>';
            }
        }

        // --- PROCESS CAMERA (AUTO) ---
        async function fetchLatestDetection() {
            try {
                const response = await fetch(liveUrl);
                if (!response.ok) return;
                const data = await response.json();
                
                if (data.plate) {
                    // FALSE = This is a camera scan, so only beep if it's a NEW plate
                    updateDetectionPanel(data, false);
                }
            } catch (error) { console.error(error); }
        }

        // --- UPDATE UI & PLAY SOUND ---
        function updateDetectionPanel(data, isManual = false) {
            const isAuth = data.status.toLowerCase().includes('authorized');
            const statusClass = isAuth ? 'status-authorized' : 'status-unauthorized';
            const icon = isAuth ? 'fa-check-circle' : 'fa-times-circle';
            
            // --- ALERT SOUND LOGIC ---
            if (!isAuth) {
                // Play Sound IF:
                // 1. It is a Manual Scan (Always beep for taps)
                // OR
                // 2. It is a Camera Scan AND the plate is different from the last one (No loop beeping)
                if (isManual || lastAlertedPlate !== data.plate) {
                    try {
                        alertSound.currentTime = 0;
                        alertSound.play().catch(e => console.log("Audio needed interaction"));
                        lastAlertedPlate = data.plate; // Update memory
                    } catch (err) { console.error(err); }
                }
            } else {
                lastAlertedPlate = null; // Reset if authorized vehicle appears
            }

            // UI Rendering
            let reportBtn = '';
            if(!isAuth) {
                reportBtn = `<div style="margin-top:20px; border-top:1px dashed #eee; padding-top:15px;">
                    <button id="reportBtn" onclick="reportVehicle('${data.plate}')" class="delete">Report to Admin</button>
                </div>`;
            }

            detectionDetails.innerHTML = `
                <div class="detail-item">
                    <label>License Plate</label>
                    <span style="font-size: 2rem;">${data.plate}</span>
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

        async function reportVehicle(plate) {
            if(!confirm(`Report vehicle ${plate} as unregistered?`)) return;
            // ... (Report logic same as before) ...
            // Copied from previous logical block for completeness if needed, 
            // but relying on existing `reportUrl` constant defined above.
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