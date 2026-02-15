<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<!-- Font Awesome CSS (Use CDN, not SCSS imports) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    :root {
        --primary-color: #58bc82; 
        --guard-primary: #1e293b;
        --primary-dark: #3aa868;
        --primary-light: #e3f9eb; 
        --text-dark: #1f2937;
        --text-gray: #6b7280;
        --bg-body: #f3f4f6;       
        --white: #ffffff;
        --sidebar-width: 260px;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background-color: var(--bg-body);
        color: var(--text-dark);
        display: flex;
        min-height: 100vh;
    }

    /* --- SIDEBAR --- */
    .sidebar {
        width: var(--sidebar-width);
        background-color: var(--white);
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        z-index: 50;
        transition: transform 0.3s;
    }

    .sidebar-header {
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .sidebar-header i {
        font-size: 1.5rem;
        color: var(--primary-color);
    }

    .sidebar-header h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        letter-spacing: -0.025em;
    }

    .sidebar-nav {
        flex: 1;
        padding: 1.5rem 1rem;
        overflow-y: auto;
    }

    .nav-section-title {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #9ca3af;
        font-weight: 700;
        margin-bottom: 0.75rem;
        padding-left: 0.75rem;
        letter-spacing: 0.05em;
        margin-top: 1.5rem;
    }

    .nav-section-title:first-child {
        margin-top: 0;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: var(--text-gray);
        text-decoration: none;
        border-radius: 0.5rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .nav-link i {
        width: 1.25rem;
        margin-right: 0.75rem;
        font-size: 1rem;
        text-align: center;
        transition: color 0.2s;
    }

    .nav-link:hover {
        background-color: var(--primary-light);
        color: var(--primary-color);
    }

    .nav-link.active {
        background-color: var(--primary-color);
        color: var(--white);
    }

    .nav-link.active i {
        color: var(--white);
    }
    
    .sidebar-footer {
        padding: 1rem;
        border-top: 1px solid #f3f4f6;
    }

    .logout-btn {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background-color: #fee2e2; 
        color: #ef4444; 
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .logout-btn:hover {
        background-color: #fca5a5;
        color: #7f1d1d;
    }

    /* --- MAIN CONTENT --- */
    .main-wrapper {
        margin-left: var(--sidebar-width);
        flex: 1;
        padding: 2rem;
        width: calc(100% - var(--sidebar-width));
        transition: margin-left 0.3s, width 0.3s;
    }

    .page-header { margin-bottom: 2rem; }
    .page-header h1 { font-size: 1.75rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem; }
    .page-header p { color: var(--text-gray); }

    .dashboard-container { padding: 2rem; width: 100%; }

    /* --- CARDS & GRID --- */
    .card {
        background-color: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow);
        padding: 1.5rem;
        border: 1px solid #f3f4f6;
        margin-bottom: 1.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background-color: var(--white);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #f3f4f6;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    
    .stat-info h3 { font-size: 0.85rem; text-transform: uppercase; color: var(--text-gray); font-weight: 600; margin-bottom: 0.5rem; }
    .stat-info .value { font-size: 2rem; font-weight: 700; color: var(--text-dark); }
    
    .stat-icon {
        width: 3.5rem; height: 3.5rem; border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
    }
    .bg-green { background-color: #def7ec; color: #27ae60; }
    .bg-blue { background-color: #e1effe; color: #3b82f6; }
    .bg-orange { background-color: #fef3c7; color: #d97706; }

    /* --- TABLES --- */
    .table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .table th {
        text-align: left; padding: 1rem; background-color: #f9fafb;
        color: var(--text-gray); font-weight: 600; font-size: 0.85rem;
        text-transform: uppercase; border-bottom: 1px solid #e5e7eb;
    }
    .table td { padding: 1rem; border-bottom: 1px solid #f3f4f6; color: var(--text-dark); }
    .table tr:last-child td { border-bottom: none; }

    /* --- FORMS & INPUTS --- */
    .input-span { display: flex; flex-direction: column; margin-bottom: 15px; }
    .label { font-weight: 600; margin-bottom: 5px; color: var(--secondary-color); }

    input[type="text"], input[type="email"], input[type="password"], select {
        width: 100%; height: 44px;
        background-color: #05060f0a; border-radius: 0.5rem;
        padding: 0 1rem; border: 2px solid transparent; font-size: 1rem;
        transition: all 0.3s;
    }
    input:focus, select:focus { outline: none; border-color: var(--primary-color); background-color: #fff; }

    /* --- BUTTONS --- */
    .submit, .delete, .rfid-btn, .back-btn {
        padding: 0.6rem 1.25rem; border-radius: 2rem; border: none;
        font-weight: 600; cursor: pointer; text-decoration: none;
        display: inline-block; font-size: 0.9rem; transition: all 0.2s;
        text-align: center;
    }
    
    .submit { background-color: var(--primary-color); color: white; }
    .submit:hover { background-color: var(--primary-dark); }
    
    .delete { background-color: #ef4444; color: white; }
    .delete:hover { background-color: #dc2626; }
    
    .rfid-btn { background-color: #8b5cf6; color: white; border-radius: 8px; }
    .rfid-btn:hover { background-color: #7c3aed; }

    .back-btn { background-color: #333; color: #58bc82; }
    .back-btn:hover { background-color: #58bc82; color: #333; }

    /* --- GUARD DASHBOARD LAYOUT --- */
    .guard-body {
        background-color: var(--bg-body); color: var(--text-dark);
        min-height: 100vh; display: flex; flex-direction: column;
    }
    .navbar-guard {
        display: flex; justify-content: space-between; align-items: center;
        background-color: var(--guard-primary); padding: 1rem 2rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; width: 100%;
    }
    .navbar-guard .logo { color: #fff; font-weight: 700; font-size: 1.4rem; margin: 0; }
    
    .dashboard-container-grid {
        display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;
        padding: 2rem; max-width: 1600px; margin: 0 auto; align-items: start;
    }

    /* Video Feed */
    .live-stream-container {
        width: 100%; background-color: #000; min-height: 480px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 0 0 1rem 1rem; overflow: hidden;
    }
    .live-stream-container img { width: 100%; height: auto; display: block; object-fit: contain; max-height: 700px; }

    /* Detection Details */
    .detection-details-container { padding: 1.5rem; }
    .detail-item { margin-bottom: 1.25rem; border-bottom: 1px solid #f5f5f5; padding-bottom: 1rem; }
    .detail-item:last-child { border-bottom: none; margin-bottom: 0; }
    .detail-item label { display: block; font-size: 0.8rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; }
    .detail-item span { font-size: 1.4rem; font-weight: 600; color: var(--text-dark); display: flex; align-items: center; gap: 10px; }

    /* Status Badges */
    .status-badge { padding: 0.4rem 1rem; border-radius: 2rem; font-size: 0.9rem; font-weight: 700; }
    .status-authorized { background-color: #dcfce7; color: #166534; }
    .status-unauthorized { background-color: #fee2e2; color: #991b1b; }

    /* RFID Input */
    .rfid-content { padding: 1.5rem; display: flex; flex-direction: column; align-items: center; gap: 10px; }
    .input-group { display: flex; width: 100%; gap: 10px; }
    .rfid-input {
        flex: 1; padding: 12px; border: 2px solid #cbd5e1; border-radius: 8px;
        font-size: 1rem; text-align: center; font-weight: 600; letter-spacing: 1px;
        background-color: #f8fafc;
    }
    .rfid-input:focus { border-color: #8b5cf6; background-color: #fff; }

    /* --- MODALS --- */
    .custom-modal {
        display: none; 
        position: fixed; 
        z-index: 2000; 
        left: 0; top: 0; 
        width: 100%; height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }
    
    .modal-content {
        background-color: #fefefe; 
        margin: 10% auto; 
        padding: 25px;
        border: 1px solid #888; 
        width: 90%; max-width: 500px;
        border-radius: 12px; 
        position: relative; 
        animation: slideDown 0.3s ease-out;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;
    }
    
    .close-btn { color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; transition: 0.2s; }
    .close-btn:hover { color: #333; }

    .custom-full-width-modal {
        max-width: 95% !important; width: 95% !important; margin: 1.75rem auto;
    }

    @keyframes slideDown {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .sidebar { transform: translateX(-100%); }
        .main-wrapper { margin-left: 0; width: 100%; }
        .dashboard-container-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto Caps Lock for all text inputs (excluding email/password)
        const inputs = document.querySelectorAll('input[type="text"]');
        
        inputs.forEach(input => {
            input.style.textTransform = "uppercase";
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    });
</script>