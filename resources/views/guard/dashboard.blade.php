<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guard Dashboard - VLPR</title> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('style')
    <style>
        .guard-dashboard-body {
            background-color: var(--bg-body); 
            color: var(--text-dark);
            min-height: 100vh; 
            display: flex; 
            flex-direction: column;
        }
        .navbar-guard {
            display: flex; justify-content: space-between; align-items: center;
            background-color: #0f172a; padding: 1rem 2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; width: 100%;
        }
        .navbar-guard .logo { color: #fff; font-weight: 700; font-size: 1.4rem; margin: 0; }
        
        .dashboard-content {
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        /* Task 2: Increase Size of Live Camera Feeds by adjusting grid ratio */
        .admin-monitor-grid {
            display: grid;
            grid-template-columns: 2.2fr 0.8fr; /* Focus more on cameras */
            gap: 2rem;
            margin-bottom: 2rem;
            align-items: start;
        }

        .rfid-input-group {
            display: flex;
            gap: 10px;
            margin-top: 1rem;
        }

        .history-list-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .history-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .history-row:hover {
            background-color: var(--slate-50);
        }

        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--slate-100);
        }

        .page-btn {
            padding: 0.4rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--slate-200);
            background: white;
            color: var(--slate-600);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .page-btn:hover:not(:disabled) {
            background: var(--slate-50);
            border-color: var(--slate-300);
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Video Feed Specifics */
        .camera-feed-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            padding: 1.5rem;
        }

        .stream-img {
            width: 100%;
            height: auto;
            min-height: 400px; /* Increased height for Task 2 */
            object-fit: cover;
            border-radius: 0.75rem;
            border: 2px solid var(--slate-200);
            box-shadow: var(--shadow);
        }

        .live-indicator {
            color: #ef4444;
            font-weight: 700;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .live-indicator span {
            height: 10px;
            width: 10px;
            background-color: #ef4444;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        /* Responsive adjustment for Task 2 */
        @media (max-width: 1200px) {
            .admin-monitor-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="guard-dashboard-body">

    <audio id="alert_sound" src="{{ asset('sounds/mixkit-sci-fi-error-alert-898.wav') }}" preload="auto"></audio>

    <nav class="navbar-guard">
        <h1 class="logo"><i class="fas fa-shield-alt" style="color: var(--primary-color);"></i> Guard Panel</h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn" style="width:auto; background:rgba(255,255,255,0.1); color:white; border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>

    <div class="dashboard-content">
        <div class="admin-monitor-grid">
            <!-- LEFT COLUMN: Live Camera & RFID Scanner -->
            <div class="left-column">
                <!-- 1. Live Camera Feeds -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-video" style="color: var(--blue-500);"></i> Live Camera Feeds</h3>
                        <div class="live-indicator"><span></span> LIVE</div>
                    </div>
                    <div class="camera-feed-container">
                        <div>
                            <div class="gate-label entry-label" style="font-size: 1rem; padding: 0.75rem;"><i class="fas fa-sign-in-alt"></i> ENTRY GATE</div>
                            <img src="{{ $detectionBackendUrl }}/video_feed/entry?api_key={{ $detectionApiKey }}"
                                 onerror="this.onerror=null; this.src=''; this.style.opacity='0.5'; this.alt='Entry Camera Offline';"
                                 alt="Entry Gate" class="stream-img">
                        </div>
                        <div>
                            <div class="gate-label exit-label" style="font-size: 1rem; padding: 0.75rem;"><i class="fas fa-sign-out-alt"></i> EXIT GATE</div>
                            <img src="{{ $detectionBackendUrl }}/video_feed/exit?api_key={{ $detectionApiKey }}"
                                 onerror="this.onerror=null; this.src=''; this.style.opacity='0.5'; this.alt='Exit Camera Offline';"
                                 alt="Exit Gate" class="stream-img">
                        </div>
                    </div>
                </div>

                <!-- 2. RFID Scanner -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-wifi" style="color: var(--purple-500);"></i> RFID Scanner</h3>
                    </div>
                    <div class="card-padding">
                        <div id="rfid_status_text" style="text-align:center; margin-bottom: 1rem;">
                            <span class="text-slate-400 font-600">
                                <i class="fas fa-arrow-down"></i> Tap card or type below
                            </span>
                        </div>
                        <div class="rfid-input-group">
                            <input type="text" id="rfid_input" class="form-control" placeholder="TAP CARD OR TYPE RFID CODE..." autofocus autocomplete="off">
                            <button id="manual_check_btn" class="btn btn-primary">Check</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Latest Detection & Recent History -->
            <div class="right-column">
                <!-- 3. Latest Detection -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-id-card" style="color: var(--orange-500);"></i> Latest Detection</h3>
                        <p id="last-updated" class="text-slate-400" style="font-size: 0.8rem;">Waiting...</p>
                    </div>
                    <div id="detection-details" class="detection-details-container">
                        <div class="flex-center" style="flex-direction: column; padding: 2rem; color: var(--slate-400);">
                            <i class="fas fa-car-side" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <p>No vehicle detected yet.</p>
                        </div>
                    </div>
                </div>

                <!-- 4. Recent History -->
                <div class="card" style="flex-grow: 1;">
                    <div class="card-header">
                        <h3><i class="fas fa-history" style="color: var(--slate-500);"></i> Recent History</h3>
                        <p class="text-slate-400" style="font-size: 0.75rem;"><i class="fas fa-hand-pointer"></i> Click row for details</p>
                    </div>
                    <div class="card-padding" style="padding-top: 0;">
                        <div class="table-responsive history-list-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Plate</th>
                                        <th>Status</th>
                                        <th>Method</th>
                                    </tr>
                                </thead>
                                <tbody id="history-table-body">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Controls -->
                        <div id="pagination-controls" class="pagination-wrapper" style="display:none;">
                            <button id="prevBtn" class="page-btn" onclick="changePage(-1)">&laquo; Prev</button>
                            <span id="pageIndicator" class="text-slate-500 font-600" style="font-size: 0.85rem;">Page 1</span>
                            <button id="nextBtn" class="page-btn" onclick="changePage(1)">Next &raquo;</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal: Select Vehicle (For RFID Multi-Vehicle Owners) -->
    <div id="vehicleSelectionModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0;"><i class="fas fa-car-alt" style="color: var(--primary-color);"></i> Select Vehicle</h3>
                <span class="close-btn" onclick="closeCustomModal('vehicleSelectionModal')">&times;</span>
            </div>
            <div class="modal-body">
                <!-- Owner Info Box -->
                <div id="selectionOwnerDetails" style="background-color: var(--slate-100); padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 1.5rem; border-left: 4px solid var(--primary-color); display: none;">
                </div>

                <p class="text-gray mb-4 font-600">Multiple vehicles detected for this owner. Which one is entering?</p>
                <div id="vehicleButtons" style="display:flex; flex-direction:column; gap:0.75rem;"></div>
            </div>
        </div>
    </div>

    <!-- Modal: History Details (Clickable Rows) -->
    <div id="historyDetailsModal" class="custom-modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 style="margin:0;"><i class="fas fa-list-alt" style="color: var(--primary-color);"></i> Log Details</h3>
                <span class="close-btn" onclick="closeCustomModal('historyDetailsModal')">&times;</span>
            </div>
            <div id="historyModalBody" class="modal-body">
                <!-- Content injected via JS -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeCustomModal('historyDetailsModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Task 1: Report Confirmation Modal -->
    <div id="reportConfirmationModal" class="custom-modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 style="margin:0;"><i class="fas fa-exclamation-triangle" style="color: var(--red-500);"></i> Confirm Report</h3>
                <span class="close-btn" onclick="closeCustomModal('reportConfirmationModal')">&times;</span>
            </div>
            <div class="modal-body text-center" style="padding: 2rem;">
                <div style="font-size: 3rem; color: var(--red-500); margin-bottom: 1rem;">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <p class="text-dark font-700" style="font-size: 1.1rem;">Report vehicle <span id="reportPlateDisplay" style="color: var(--red-500);"></span> as unregistered?</p>
                <p class="text-gray" style="margin-top: 0.5rem;">This will alert the administrator for further action.</p>
            </div>
            <div class="modal-footer" style="justify-content: center; gap: 1rem;">
                <button class="btn btn-outline" onclick="closeCustomModal('reportConfirmationModal')">Cancel</button>
                <button class="btn btn-red" onclick="confirmReport()">Confirm Report</button>
            </div>
        </div>
    </div>

    @include('script')

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
        const reportModal = document.getElementById('reportConfirmationModal');
        const btnContainer = document.getElementById('vehicleButtons');
        
        let pendingRfid = null;
        let lastAlertedPlate = null;
        let lastProcessedPlate = null; 
        let lastProcessedTime = 0;
        let plateToReport = null; // Task 1

        // --- History Pagination State ---
        let allHistoryLogs = {!! json_encode($initialLogs ?? []) !!};
        let currentPage = 1;
        const itemsPerPage = 8;

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
        
        // Redefine processRfid as a global function so script.blade.php can find it
        window.processRfid = async function(code) {
            if(!code) return;
            rfidInput.blur();
            rfidStatusText.innerHTML = '<span style="color: var(--orange-500); font-weight:bold;"><i class="fas fa-spinner fa-spin"></i> Processing...</span>';

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
                    showSelectionModal(data.vehicles, data.owner); 
                    rfidStatusText.innerHTML = '<span style="color: var(--blue-500);">Please select a vehicle...</span>';
                    return; 
                }

                if (response.ok && data.success) {
                    handleSuccess(data);
                } else {
                    rfidStatusText.innerHTML = `<span style="color: var(--red-500); font-weight:bold;"><i class="fas fa-times-circle"></i> ${data.message}</span>`;
                    resetInput();
                }
            } catch (error) {
                console.error("RFID Error:", error);
                rfidStatusText.innerHTML = `<span style="color: var(--red-500); font-size: 0.9em;">Network/System Error</span>`;
                resetInput();
            }
        };

        // Display Modal with Owner Info & Vehicle Options
        function showSelectionModal(vehicles, owner) {
            const ownerDetailsDiv = document.getElementById('selectionOwnerDetails');
            
            // Populate Owner Information in Modal
            if (owner) {
                const ownerType = owner.type_of_owner ? owner.type_of_owner.toUpperCase() : 'N/A';
                ownerDetailsDiv.innerHTML = `
                    <div style="font-size: 0.7rem; color: var(--slate-400); text-transform: uppercase; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: 0.05em;">Registered Owner</div>
                    <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark);"><i class="fas fa-user-circle" style="margin-right: 0.5rem; color: var(--primary-color);"></i> ${owner.f_name} ${owner.l_name}</div>
                    <div style="font-size: 0.9rem; color: var(--text-gray); margin-top: 0.5rem; display: flex; gap: 1rem; align-items: center;">
                        <span><i class="fas fa-id-badge" style="margin-right: 0.25rem;"></i> ${ownerType}</span>
                        <span style="color: var(--slate-200);">|</span>
                        <span><i class="fas fa-phone" style="margin-right: 0.25rem;"></i> ${owner.contact_number || 'No Contact'}</span>
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
                btn.className = "btn btn-primary w-full";
                btn.style.justifyContent = "center";
                btn.innerHTML = `<span><i class="fas fa-car"></i> <b>${v.plate_number}</b></span> <span style="font-size:0.85rem; opacity:0.9;">(${v.vehicle_type || 'Unknown Type'})</span>`;
                btn.onclick = () => confirmSelection(v.vehicle_id);
                btnContainer.appendChild(btn);
            });
            selectionModal.style.display = 'block'; 
            document.body.style.overflow = 'hidden';
        }

        async function confirmSelection(vehicleId) {
            closeCustomModal('vehicleSelectionModal');
            rfidStatusText.innerHTML = '<span style="color: var(--orange-500);">Finalizing...</span>';
            try {
                const response = await fetch(selectUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ rfid_code: pendingRfid, vehicle_id: vehicleId })
                });

                if (!response.ok) {
                    alert("System Error: The server returned an error.");
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
            rfidStatusText.innerHTML = `<span style="color: var(--green-500); font-weight:bold;"><i class="fas fa-check-circle"></i> Logged: ${data.plate}</span>`;
            updateDetectionPanel(data, true);
            resetInput();
        }

        function resetInput() {
            setTimeout(() => {
                rfidStatusText.innerHTML = '<span class="text-slate-400 font-600"><i class="fas fa-arrow-down"></i> Tap card or type below</span>';
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
                // Task 1: Show report button if unregistered (no owner)
                if(!data.owner) {
                    reportBtn = `<div style="margin-top:1.5rem; border-top:1px dashed var(--slate-200); padding-top:1rem;"><button id="reportBtn" onclick="reportVehicle('${data.plate}')" class="btn btn-red w-full" style="justify-content:center;"><i class="fas fa-bullhorn"></i> Report Unregistered Vehicle</button></div>`;
                }
                
                let methodBadge = '';
                if(data.method === 'RFID') methodBadge = '<span style="background:var(--primary-light); color:var(--primary-dark); padding:0.25rem 0.75rem; border-radius:1rem; font-weight:700; font-size:0.75rem; margin-left: 1rem;">RFID</span>';
                else if(data.method && data.method.includes('PLATE')) methodBadge = '<span style="background:var(--slate-100); color:var(--blue-500); padding:0.25rem 0.75rem; border-radius:1rem; font-weight:700; font-size:0.75rem; margin-left: 1rem;">PLATE</span>';

                let ownerHtml = `<span style="color:var(--red-500); font-weight:700;"><i class="fas fa-user-times"></i> Unregistered</span>`;
                if(data.owner) {
                    ownerHtml = `
                        <div style="display:flex; flex-direction:column; gap:0.25rem;">
                            <span style="font-size:1.1rem; font-weight:700; color:var(--text-dark);"><i class="fas fa-user" style="color:var(--slate-400); margin-right:0.5rem; font-size:0.9rem;"></i> ${data.owner.f_name} ${data.owner.l_name}</span>
                            <span style="font-size:0.9rem; color:var(--text-gray); font-weight:500;"><i class="fas fa-phone" style="color:var(--slate-400); margin-right:0.5rem;"></i> ${data.owner.contact_number || 'No Contact Number'}</span>
                        </div>
                    `;
                }

                detectionDetails.innerHTML = `
                    <div class="detail-item">
                        <label>License Plate</label>
                        <div style="display:flex; align-items:center;">
                            <span class="plate-large">${data.plate}</span>
                            ${methodBadge}
                        </div>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="status-badge ${statusClass}"><i class="fas ${icon}"></i> ${data.status}</span>
                    </div>
                    <div class="detail-item">
                        <label>Owner Information</label>
                        ${ownerHtml}
                    </div>
                    ${reportBtn}
                `;
                document.getElementById("last-updated").innerText = "Updated: " + new Date().toLocaleTimeString();

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
                tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: var(--slate-400); padding: 2rem;"><i class="fas fa-history" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i> No history yet...</td></tr>`;
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
                
                let displayStatus = "Unauthorized";
                let statusColor = 'var(--red-500)'; 

                let isAuthorized = log.isAuth;
                if(isAuthorized === undefined) {
                     const str = (log.status || "").toLowerCase();
                     isAuthorized = str.includes('authorized') || str.includes('logged out') || str.includes('exited');
                }

                if (isAuthorized) {
                    statusColor = 'var(--green-500)';
                    const str = (log.statusStr || log.status || "").toLowerCase();
                    if (str.includes('logged out') || str.includes('exited')) { displayStatus = "Log Out"; } 
                    else { displayStatus = "Log In"; }
                }

                let timeStr = log.timeString || (log.updated_at ? new Date(log.updated_at).toLocaleTimeString('en-US', {hour12:false}) : '--');

                row.innerHTML = `
                    <td class="text-slate-500 font-600">${timeStr}</td>
                    <td class="font-bold text-dark">${log.plate}</td>
                    <td><span style="color: ${statusColor}; font-weight:800; font-size:0.85rem;">${displayStatus}</span></td>
                    <td class="text-slate-400 font-500" style="font-size:0.85rem;">${log.method || '-'}</td>
                `;
                
                row.onclick = () => openHistoryModal(actualIndex);
                tbody.appendChild(row);
            });

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
            
            let isUnregistered = !log.owner || !log.owner.f_name;
            let ownerDetails = `<div style="text-align: center; padding: 1.5rem; background: #fee2e2; color: #991b1b; border-radius: 0.75rem; font-weight: 700; border: 1px solid #fecaca;"><i class="fas fa-exclamation-triangle"></i> UNREGISTERED VEHICLE</div>`;
            
            if (!isUnregistered) {
                ownerDetails = `
                    <div class="detail-item">
                        <label>Owner Name</label>
                        <span class="text-dark font-700">${log.owner.f_name} ${log.owner.l_name}</span>
                    </div>
                    <div class="detail-item">
                        <label>Contact Number</label>
                        <span class="text-dark font-700">${log.owner.contact_number || 'N/A'}</span>
                    </div>
                `;
            }

            let timeInStr = log.time_in ? new Date(log.time_in).toLocaleString() : 'N/A';
            let timeOutStr = log.time_out ? new Date(log.time_out).toLocaleString() : '--';

            let methodBadgeColor = log.method === 'RFID' ? "background:var(--primary-light); color:var(--primary-dark);" : "background:var(--slate-100); color:var(--blue-500);";

            // Task 1: Add report button if unregistered in history details
            let reportBtnHtml = '';
            if (isUnregistered) {
                reportBtnHtml = `
                    <div style="margin-top: 1.5rem; border-top: 1px dashed var(--slate-200); padding-top: 1rem;">
                        <button onclick="reportVehicle('${log.plate}')" class="btn btn-red w-full" style="justify-content:center;">
                            <i class="fas fa-bullhorn"></i> Report Unregistered Vehicle
                        </button>
                    </div>
                `;
            }

            modalBody.innerHTML = `
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div class="plate-large" style="font-size: 3rem !important;">${log.plate}</div>
                    <div style="margin-top: 0.5rem;">
                        <span style="${methodBadgeColor} padding:0.4rem 1rem; border-radius:1rem; font-size:0.8rem; font-weight:800; text-transform:uppercase;">Detected via ${log.method || 'PLATE'}</span>
                    </div>
                </div>
                
                <h4 style="font-size:0.75rem; color:var(--slate-400); text-transform:uppercase; margin-bottom: 1rem; border-bottom: 1px solid var(--slate-100); padding-bottom: 0.5rem; letter-spacing:0.05em;">Owner Information</h4>
                <div style="margin-bottom: 1.5rem;">
                    ${ownerDetails}
                </div>

                <h4 style="font-size:0.75rem; color:var(--slate-400); text-transform:uppercase; margin-bottom: 1rem; border-bottom: 1px solid var(--slate-100); padding-bottom: 0.5rem; letter-spacing:0.05em;">Detection Details</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="text-dark font-700">${log.status || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <label>Vehicle Type</label>
                        <span class="text-dark font-700">${log.vehicle_type || 'Unknown'}</span>
                    </div>
                    <div class="detail-item" style="grid-column: span 2;">
                        <label>Time In</label>
                        <span class="text-dark font-700">${timeInStr}</span>
                    </div>
                    <div class="detail-item" style="grid-column: span 2;">
                        <label>Time Out</label>
                        <span class="text-dark font-700">${timeOutStr}</span>
                    </div>
                </div>
                ${reportBtnHtml}
            `;
            
            historyModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        // Task 1: Custom Report Confirmation
        window.reportVehicle = function(plate) {
            plateToReport = plate;
            document.getElementById('reportPlateDisplay').innerText = plate;
            reportModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        window.confirmReport = async function() {
            if (!plateToReport) return;
            
            closeCustomModal('reportConfirmationModal');
            
             try {
                const response = await fetch(reportUrl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                    body: JSON.stringify({ plate_number: plateToReport })
                });
                const d = await response.json();
                if(response.ok) {
                    alert("Success: " + d.message);
                } else {
                    alert("Error: " + d.message);
                }
            } catch (e) { 
                console.error(e); 
                alert("System Error occurred while reporting.");
            } finally {
                plateToReport = null;
            }
        }

        setInterval(fetchLatestDetection, 3000);
    </script>
</body>
</html>