<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #58bc82; 
        --primary-dark: #3aa868;
        --primary-light: #e3f9eb; 
        --text-dark: #1f2937;
        --text-gray: #6b7280;
        --bg-body: #f3f4f6;       
        --white: #ffffff;
        --sidebar-width: 260px;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --guard-primary: #1e293b;
        --master-bg: #0f172a;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --blue-500: #3b82f6;
        --orange-500: #f59e0b;
        --purple-500: #8b5cf6;
        --red-500: #ef4444;
        --green-500: #22c55e;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
    body { background-color: var(--bg-body); color: var(--text-dark); min-height: 100vh; }

    /* --- LAYOUT --- */
    .sidebar { width: var(--sidebar-width); background-color: var(--white); border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; position: fixed; height: 100vh; left: 0; top: 0; z-index: 50; }
    .sidebar-header { padding: 1.5rem; display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid #f3f4f6; }
    .sidebar-header i { font-size: 1.5rem; color: var(--primary-color); }
    .sidebar-header h2 { font-size: 1.1rem; font-weight: 700; color: var(--text-dark); letter-spacing: -0.025em; }
    .sidebar-nav { flex: 1; padding: 1.5rem 1rem; overflow-y: auto; }
    .nav-section-title { font-size: 0.7rem; text-transform: uppercase; color: #9ca3af; font-weight: 700; margin-bottom: 0.75rem; padding-left: 0.75rem; letter-spacing: 0.05em; margin-top: 1.5rem; }
    .nav-link { display: flex; align-items: center; padding: 0.75rem 1rem; color: var(--text-gray); text-decoration: none; border-radius: 0.5rem; margin-bottom: 0.25rem; font-weight: 500; font-size: 0.9rem; transition: all 0.2s; }
    .nav-link:hover { background-color: var(--primary-light); color: var(--primary-color); }
    .nav-link.active { background-color: var(--primary-color); color: var(--white); }
    .sidebar-footer { padding: 1rem; border-top: 1px solid #f3f4f6; }
    .main-wrapper { margin-left: var(--sidebar-width); flex: 1; padding: 2rem; width: calc(100% - var(--sidebar-width)); }

    /* --- COMPONENTS --- */
    .card { background: var(--white); border-radius: 1rem; box-shadow: var(--shadow); overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; }
    .card-padding { padding: 1.5rem; }
    .card-header { padding: 1rem 1.5rem; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; background-color: var(--slate-50); }
    .card-header h3 { font-size: 1.1rem; font-weight: 600; color: var(--text-dark); margin: 0; display: flex; align-items: center; gap: 0.75rem; }
    
    .status-badge { padding: 0.4rem 1rem; border-radius: 2rem; font-size: 0.85rem; display: inline-flex; align-items: center; font-weight: 600; gap: 0.5rem; }
    .status-authorized { background-color: #dcfce7; color: #166534; }
    .status-unauthorized { background-color: #fee2e2; color: #991b1b; }
    .status-pending { background-color: #fef3c7; color: #92400e; }
    
    .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .table th { text-align: left; padding: 1rem; background-color: var(--slate-50); color: var(--text-gray); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; border-bottom: 2px solid var(--slate-100); }
    .table td { padding: 1rem; border-bottom: 1px solid var(--slate-100); color: var(--text-dark); font-size: 0.95rem; }
    .table tr:last-child td { border-bottom: none; }
    .table-responsive { overflow-x: auto; width: 100%; }

    /* --- DASHBOARD STATS --- */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: var(--white); border-radius: 1rem; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--slate-200); transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-info h4 { font-size: 0.9rem; text-transform: uppercase; color: var(--text-gray); font-weight: 600; margin-bottom: 0.5rem; }
    .stat-info h2 { font-size: 2rem; font-weight: 800; color: var(--text-dark); }
    .stat-icon { width: 3.5rem; height: 3.5rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background-color: var(--slate-50); }

    /* --- ADMIN DASHBOARD --- */
    .admin-monitor-grid { display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 1.5rem; margin-bottom: 2rem; align-items: start; }
    .live-stream-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 1rem; }
    .gate-label { text-align: center; padding: 0.5rem 0; font-weight: 700; font-size: 0.85rem; border-radius: 0.5rem; margin-bottom: 0.5rem; }
    .entry-label { color: var(--green-500); background-color: #f0fdf4; }
    .exit-label { color: var(--red-500); background-color: #fef2f2; }
    .stream-img { width: 100%; border-radius: 0.75rem; border: 1px solid var(--slate-200); }
    
    .detection-details-container { padding: 1.5rem; }
    .detail-item { margin-bottom: 1.25rem; border-bottom: 1px solid var(--slate-100); padding-bottom: 1rem; }
    .detail-item:last-child { border-bottom: none; margin-bottom: 0; }
    .detail-item label { display: block; font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700; margin-bottom: 0.4rem; letter-spacing: 0.05em; }
    .detail-item span { font-size: 1.1rem; font-weight: 600; color: var(--text-dark); display: flex; align-items: center; gap: 0.75rem; }
    .plate-large { font-size: 2rem !important; font-weight: 800 !important; color: #111827 !important; }

    /* --- QUICK ACTIONS --- */
    .quick-actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding: 1.5rem; }
    .action-card { background: var(--white); padding: 1.5rem; border-radius: 1rem; text-align: center; border: 1px solid var(--slate-200); transition: all 0.2s; height: 100%; display: flex; flex-direction: column; align-items: center; gap: 1rem; }
    .action-card:hover { border-color: var(--primary-color); background-color: var(--primary-light); transform: translateY(-3px); }
    .action-card i { font-size: 2rem; }
    .action-card h4 { font-size: 1rem; color: var(--text-dark); font-weight: 700; }

    /* --- BUTTONS --- */
    .btn { padding: 0.6rem 1.25rem; border-radius: 0.75rem; font-weight: 600; cursor: pointer; transition: 0.2s; font-size: 0.9rem; border: none; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; }
    .btn-primary { background-color: var(--primary-color); color: white; }
    .btn-primary:hover { background-color: var(--primary-dark); }
    .btn-blue { background-color: var(--blue-500); color: white; }
    .btn-blue:hover { background-color: #2563eb; }
    .btn-red { background-color: var(--red-500); color: white; }
    .btn-red:hover { background-color: #dc2626; }
    .btn-outline { background-color: transparent; border: 2px solid var(--slate-200); color: var(--text-gray); }
    .btn-outline:hover { border-color: var(--slate-300); color: var(--text-dark); }
    
    .logout-btn { width: 100%; display: flex; justify-content: center; align-items: center; gap: 0.5rem; padding: 0.75rem; background-color: #fee2e2; color: #ef4444; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .logout-btn:hover { background-color: #fca5a5; }

    /* --- FORMS --- */
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-size: 0.9rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.5rem; }
    .form-control { width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 2px solid var(--slate-200); font-size: 1rem; transition: 0.2s; outline: none; background-color: var(--slate-50); }
    .form-control:focus { border-color: var(--primary-color); background-color: white; box-shadow: 0 0 0 4px var(--primary-light); }
    .form-control[readonly] { background-color: var(--slate-100); cursor: not-allowed; }

    /* --- UTILITIES --- */
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-4 { margin-bottom: 1rem; }
    .mb-6 { margin-bottom: 1.5rem; }
    .mb-8 { margin-bottom: 2rem; }
    .text-gray { color: var(--text-gray); }
    .text-slate-400 { color: var(--slate-400); }
    .text-slate-500 { color: var(--slate-500); }
    .text-dark { color: var(--text-dark); }
    .font-bold { font-weight: 700; }
    .font-800 { font-weight: 800; }
    .flex-between { display: flex; justify-content: space-between; align-items: center; }
    .flex-center { display: flex; justify-content: center; align-items: center; }
    .gap-2 { gap: 0.5rem; }
    .gap-4 { gap: 1rem; }
    .w-full { width: 100%; }
    .text-2xl { font-size: 1.5rem; }
    .text-3xl { font-size: 1.875rem; }

    /* --- MODALS --- */
    .custom-modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); }
    .modal-content { background: white; margin: 5% auto; padding: 0; border-radius: 1.25rem; width: 90%; max-width: 600px; position: relative; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .modal-header { padding: 1.5rem; border-bottom: 1px solid var(--slate-100); background-color: var(--slate-50); display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 1.5rem; }
    .modal-footer { padding: 1.25rem 1.5rem; border-top: 1px solid var(--slate-100); background-color: var(--slate-50); display: flex; justify-content: flex-end; gap: 1rem; }
    .close-btn { color: var(--slate-400); font-size: 1.5rem; cursor: pointer; transition: 0.2s; line-height: 1; }
    .close-btn:hover { color: var(--text-dark); }

    /* --- RESPONSIVE --- */
    @media (max-width: 1024px) {
        .admin-monitor-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .live-stream-grid { grid-template-columns: 1fr; }
        .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
        .main-wrapper { margin-left: 0; width: 100%; }
    }
</style>