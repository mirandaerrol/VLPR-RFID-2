@extends('layouts.master')

@section('title', 'Database Overview')

@section('content')
    <h2 class="section-title"><i class="fas fa-database"></i> Database Overview</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
        <a onclick="openMasterModal('ownersModal')" class="stat-card-link" style="text-decoration: none; display: block; border-radius: 1rem; cursor: pointer;">
            <div class="stat-card" style="background-color: #fff; border-radius: 1rem; padding: 1.5rem; display: flex; align-items: center; border: 1px solid #f3f4f6; height: 100%;">
                <div class="stat-icon bg-blue" style="width: 4rem; height: 4rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-right: 1.5rem; background-color: #e0f2fe; color: #2563eb;"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3 style="font-size: 0.9rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.25rem;">Total Owners</h3>
                    <div class="value" style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($totalOwners) }}</div>
                </div>
            </div>
        </a>
        
        <a onclick="openMasterModal('vehiclesModal')" class="stat-card-link" style="text-decoration: none; display: block; border-radius: 1rem; cursor: pointer;">
            <div class="stat-card" style="background-color: #fff; border-radius: 1rem; padding: 1.5rem; display: flex; align-items: center; border: 1px solid #f3f4f6; height: 100%;">
                <div class="stat-icon bg-purple" style="width: 4rem; height: 4rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-right: 1.5rem; background-color: #f3e8ff; color: #7e22ce;"><i class="fas fa-car"></i></div>
                <div class="stat-info">
                    <h3 style="font-size: 0.9rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.25rem;">Reg. Vehicles</h3>
                    <div class="value" style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($totalVehicles) }}</div>
                </div>
            </div>
        </a>

        <a onclick="openMasterModal('guardsModal')" class="stat-card-link" style="text-decoration: none; display: block; border-radius: 1rem; cursor: pointer;">
            <div class="stat-card" style="background-color: #fff; border-radius: 1rem; padding: 1.5rem; display: flex; align-items: center; border: 1px solid #f3f4f6; height: 100%;">
                <div class="stat-icon bg-orange" style="width: 4rem; height: 4rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-right: 1.5rem; background-color: #ffedd5; color: #d97706;"><i class="fas fa-shield-alt"></i></div>
                <div class="stat-info">
                    <h3 style="font-size: 0.9rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.25rem;">Active Guards</h3>
                    <div class="value" style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($totalGuards) }}</div>
                </div>
            </div>
        </a>

        <a onclick="openMasterModal('unregisteredModal')" class="stat-card-link" style="text-decoration: none; display: block; border-radius: 1rem; cursor: pointer;">
            <div class="stat-card" style="background-color: #fff; border-radius: 1rem; padding: 1.5rem; display: flex; align-items: center; border: 1px solid #f3f4f6; height: 100%;">
                <div class="stat-icon bg-red" style="width: 4rem; height: 4rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-right: 1.5rem; background-color: #fee2e2; color: #dc2626;"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-info">
                    <h3 style="font-size: 0.9rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.25rem;">Unregistered Veh.</h3>
                    <div class="value" style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ number_format($totalUnregistered) }}</div>
                </div>
            </div>
        </a>

        <a href="{{ route('master.schedule_management') }}" class="stat-card-link" style="text-decoration: none; display: block; border-radius: 1rem; cursor: pointer;">
            <div class="stat-card" style="background-color: #fff; border-radius: 1rem; padding: 1.5rem; display: flex; align-items: center; border: 1px solid #f3f4f6; height: 100%;">
                <div class="stat-icon bg-green" style="width: 4rem; height: 4rem; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-right: 1.5rem; background-color: #dcfce7; color: #16a34a;"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-info">
                    <h3 style="font-size: 0.9rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.25rem;">Duty Assignments</h3>
                    <div class="value" style="font-size: 2.25rem; font-weight: 800; color: #0f172a; line-height: 1;">{{ $dutyAssignmentsCount }}</div>
                </div>
            </div>
        </a>
    </div>

    <div class="chart-container" style="margin-top: 2rem;">
        <h3 style="margin-bottom: 1rem; color: #334155;"><i class="fas fa-info-circle text-blue"></i> Database Overview Information</h3>
        <p style="color: #64748b; line-height: 1.6;">
            This section provides a high-level summary of the system's registered entities. 
            Click on any of the cards above to view detailed lists of owners, vehicles, guards, and unregistered detections.
        </p>
    </div>
@endsection

@section('modals')
    <!-- 1. Owners Modal -->
    <div id="ownersModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-users text-blue"></i> Registered Owners Directory</h3>
                <span class="close-btn" onclick="closeMasterModal('ownersModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Contact</th>
                            <th>RFID Code</th>
                            <th>Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ownersList as $owner)
                        <tr>
                            <td style="font-weight: 700; color: #1e293b;">{{ $owner->f_name }} {{ $owner->l_name }}</td>
                            <td><span class="badge-auth" style="background:#e0f2fe; color:#2563eb;">{{ ucfirst($owner->type_of_owner) }}</span></td>
                            <td>{{ $owner->contact_number }}</td>
                            <td style="font-family: monospace; color: #64748b;">{{ $owner->rfid_code ?? 'Not Set' }}</td>
                            <td>{{ $owner->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center; padding:15px;">No owners found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Vehicles Modal -->
    <div id="vehiclesModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-car text-purple"></i> Registered Vehicles</h3>
                <span class="close-btn" onclick="closeMasterModal('vehiclesModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Plate Number</th>
                            <th>Vehicle Type</th>
                            <th>Owner Name</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehiclesList as $vehicle)
                        <tr>
                            <td style="font-weight: 800; font-size: 1.1rem; color: #1e293b;">{{ $vehicle->plate_number }}</td>
                            <td>{{ $vehicle->vehicle_type ?? 'N/A' }}</td>
                            <td style="font-weight: 600;">{{ $vehicle->owner ? $vehicle->owner->f_name.' '.$vehicle->owner->l_name : 'No Owner' }}</td>
                            <td>{{ $vehicle->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center; padding:15px;">No vehicles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. Guards Modal -->
    <div id="guardsModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-shield-alt text-orange"></i> Active Guard Accounts</h3>
                <span class="close-btn" onclick="closeMasterModal('guardsModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Guard Name</th>
                            <th>Email Address</th>
                            <th>Account Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guardsList as $guard)
                        <tr>
                            <td style="font-weight: 700; color: #1e293b;"><i class="fas fa-user-shield" style="color:#cbd5e1; margin-right:5px;"></i> {{ $guard->name }}</td>
                            <td>{{ $guard->email }}</td>
                            <td>{{ $guard->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; padding:15px;">No guards found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. Unregistered Vehicles Modal -->
    <div id="unregisteredModal" class="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="margin:0; font-size: 1.25rem;"><i class="fas fa-exclamation-triangle text-red"></i> Unregistered Vehicles Log</h3>
                <span class="close-btn" onclick="closeMasterModal('unregisteredModal')">&times;</span>
            </div>
            <div class="modal-table-wrapper">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>Detected Plate</th>
                            <th>Total Detections</th>
                            <th>Last Seen Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unregisteredList as $unreg)
                        <tr>
                            <td style="font-weight: 800; font-size: 1.1rem; color: #dc2626;">{{ $unreg->detected_plate_number }}</td>
                            <td><span class="badge-unauth" style="background:#f1f5f9; color:#475569;">{{ $unreg->total_detections }} times</span></td>
                            <td>{{ Carbon\Carbon::parse($unreg->last_seen)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; padding:15px;">No unregistered vehicles logged.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection