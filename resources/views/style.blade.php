<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    @import '@fortawesome/fontawesome-free/scss/fontawesome.scss';
    @import '@fortawesome/fontawesome-free/scss/solid.scss';
    @import '@fortawesome/fontawesome-free/scss/brands.scss';
    /* npm install @fortawesome/fontawesome-free */
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



    /*SIDEBAR*/
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
    }

    /*Logo*/
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

    /*Navigation*/
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

    /*Hover & Active States*/
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

    /*MAIN CONTENT--- */
    .main-wrapper {
        margin-left: var(--sidebar-width);
        flex: 1;
        padding: 2rem;
        width: calc(100% - var(--sidebar-width));
    }

    /*PAGE HEADER*/
    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.25rem;
    }

    .page-header p {
        color: var(--text-gray);
    }

    .dashboard-container {
        padding: 2rem;
        width: 100%;
    }

    /* --- ADMIN MONITOR GRID (NEW) --- */
    .admin-monitor-grid {
        display: grid;
        grid-template-columns: 1.8fr 1.2fr; /* Video gets more space, Details get less */
        gap: 1.5rem;
        margin-bottom: 2rem;
        align-items: start;
    }

    /* --- VIDEO & DETECTION CARDS --- */
    .card { background: var(--white); border-radius: 1rem; box-shadow: var(--shadow); overflow: hidden; border: 1px solid #e2e8f0; }
    
    .card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8fafc;
    }
    .card-header h3 { font-size: 1.1rem; font-weight: 600; color: var(--text-dark); margin: 0; display: flex; align-items: center; gap: 0.75rem; }

    .live-stream-container { width: 100%; background-color: #000; min-height: 350px; display: flex; align-items: center; justify-content: center; }
    .live-stream-container img { width: 100%; height: auto; display: block; max-height: 500px; object-fit: contain; }

    /* --- DETECTION DETAILS --- */
    .detection-details-container { padding: 1.5rem; }
    .detail-item { margin-bottom: 1.25rem; border-bottom: 1px solid #f5f5f5; padding-bottom: 1rem; }
    .detail-item:last-child { border-bottom: none; margin-bottom: 0; }
    .detail-item label { display: block; font-size: 0.8rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; margin-bottom: 0.4rem; }
    .detail-item span { font-size: 1.2rem; font-weight: 600; color: var(--text-dark); display: flex; align-items: center; gap: 10px; }

    /* Badges */
    .status-badge { padding: 0.4rem 1rem; border-radius: 2rem; font-size: 0.9rem; display: inline-flex; align-items: center; }
    .status-authorized { background-color: #dcfce7; color: #166534; }
    .status-unauthorized { background-color: #fee2e2; color: #991b1b; }

    /* --- STATS GRID --- */
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

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-info h3 {
        font-size: 0.85rem;
        text-transform: uppercase;
        color: var(--text-gray);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .stat-info .value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .stat-icon {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /*Icon Background Colors*/
    .bg-green { background-color: #def7ec; color: #27ae60; }
    .bg-blue { background-color: #e1effe; color: #3b82f6; }
    .bg-orange { background-color: #fef3c7; color: #d97706; }

    /*Quick Actions*/
    .section-header { margin-bottom: 1.5rem; }
    .section-header h3 { font-size: 1.25rem; font-weight: 600; color: #1f2937; }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
    }
    .action-link { text-decoration: none; display: block; height: 100%; }

    .action-card {
        background: var(--white);
        padding: 2rem;
        border-radius: 1rem;
        text-align: center;
        border: 1px solid #e5e7eb;
        box-shadow: var(--shadow);
        transition: 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .action-card:hover {
        transform: translateY(-3px);
        border-color: var(--primary-color);
    }

    .action-card i { font-size: 2rem; margin-bottom: 1rem; }
    .action-card h4 { color: #333; font-weight: 600; margin: 0; }

    .card {
        background-color: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow);
        padding: 1.5rem;
        border: 1px solid #f3f4f6;
    }

    /*Tables*/
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .table th {
        text-align: left;
        padding: 1rem;
        background-color: #f9fafb;
        color: var(--text-gray);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-bottom: 1px solid #e5e7eb;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
        color: var(--text-dark);
    }
    
    .table tr:last-child td {
        border-bottom: none;
    }

    /* Buttons */
    .submit {
        background-color: var(--primary-color);
        color: white;
        padding: 0.6rem 1.25rem;
        border-radius: 2rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 0.9rem;
        transition: background 0.2s;
    }
    .submit:hover {
        background-color: #2ecc71;
        color: #333;
    }

    .delete {
        background-color: #ef4444;
        color: white;
        padding: 0.6rem 1.25rem;
        border-radius: 2rem;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 0.9rem;
        transition: background 0.2s;
    }
    .delete:hover {
        background-color: #c10f0fff;
        color: #333;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
        .main-wrapper { margin-left: 0; width: 100%; }
    }

.card {
  background: #fff;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  flex: 1;
  min-width: 320px;
  
}

/* Table Style */
.vehicle-owner-table, .table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.vehicle-owner-table th, .vehicle-owner-table td, .table th, .table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #ddd;
}


.vehicle-owner-table th,  .table th{
  background-color: #58bc82;
  color: white;
  font-weight: bold;
}

.vehicle-owner-table tr:nth-child(even) {
  background-color: #f9f9f9;
}

.vehicle-owner-table tr:hover {
  background-color: #f1f1f1;
}


.back-btn{
  padding: 1rem 0.75rem;
  border-radius: 3rem;
  background-color: #333;
  color: #58bc82;
  border: none;
  cursor: pointer;
  transition: all 300ms;
  font-weight: 600;
  font-size: 0.9rem;
  text-decoration: none;
}
.back-btn:hover{
  background-color: #58bc82;
  color: #333;
}


input {
  max-width: 500px;
  height: 44px;
  background-color: #05060f0a;
  border-radius: 10rem;
  padding: 0 1rem;
  border: 2px solid transparent;
  font-size: 1rem;
  transition: border-color .3s cubic-bezier(.25,.01,.25,1) 0s, color .3s cubic-bezier(.25,.01,.25,1) 0s,background .2s cubic-bezier(.25,.01,.25,1) 0s;
}

select{
  max-width: 300px;
  height: 44px;
  background-color: #05060f0a;
  border-radius: 10rem;
  padding: 0 1rem;
  border: 2px solid transparent;
  font-size: 1rem;
  transition: border-color .3s cubic-bezier(.25,.01,.25,1) 0s, color .3s cubic-bezier(.25,.01,.25,1) 0s,background .2s cubic-bezier(.25,.01,.25,1) 0s;
}

option{
  display: block;
  margin-bottom: .3rem;
  font-size: .9rem;
  font-weight: bold;
  color: #58bc82;
  transition: color .3s cubic-bezier(.25,.01,.25,1) 0s;
}

select:focus{
  border-color: #05060f0a;
  outline: none;
  background-color: #fff;
}

.input-span .label {
  display: block;
  margin-bottom: .3rem;
  font-size: .9rem;
  font-weight: bold;
  color: #58bc82;
  transition: color .3s cubic-bezier(.25,.01,.25,1) 0s;
}

.input:hover, .input:focus, .input-span:hover .input {
  outline: none;
  border-color: #58bc82;
}
form{
  style="display: flex; 
  flex-direction: column; 
  gap: 15px;"
}

.live-stream-container img {
  width: 840px;
  height: 740px;
}
.reports{
  color: #c0392b; 
  background: #fadbd8; 
  padding: 5px 10px; 
  border-radius: 4px;
}
p{
  color: #666; 
  margin-bottom: 20px;
}

/*PAGINATION*/
nav[role="navigation"] {
        display: flex;
        justify-content: center;
        width: 100%;
    }
 
    nav[role="navigation"] > div:first-child {
        display: none; 
    }
    
    nav ul.pagination {
        display: flex !important;           
        flex-direction: row !important;     
        gap: 15px !important;               
        list-style: none !important;
        padding: 0;
        margin: 0;
        justify-content: center;
    }
    
    nav ul.pagination li.page-item {
        display: inline-block !important;   
    }

    nav ul.pagination li.page-item a.page-link,
    nav ul.pagination li.page-item span.page-link {
        display: inline-block;
        padding: 0.6rem 1.2rem;
        background-color: #fff;
        border: 1px solid #ddd;
        color: #333;
        text-decoration: none;
        border-radius: 30px;
        font-weight: 600;
        cursor: pointer;
    }

    nav ul.pagination li.page-item.disabled span.page-link {
        background-color: #f5f5f5;
        color: #aaa;
        cursor: not-allowed;
        border-color: #eee;
    }

    nav ul.pagination li.page-item:not(.disabled) .page-link:hover {
        background-color: #58bc82;
        color: white;
        border-color: #58bc82;
    }

    
    /* --- GUARD DASHBOARD GRID --- */
    .guard-body{
      background-color: var(--bg-body);
        color: var(--text-dark);
        min-height: 100vh;
        
        display: flex;
        flex-direction: column; 
    }
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: var(--primary-color);
        padding: 1rem 2rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
    }
     .navbar-guard {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: var(--guard-primary);
        padding: 1rem 2rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
    }

    .navbar .logo, .navbar-guard .logo {
        color: #fff;
        font-weight: 700;
        font-size: 1.4rem;
        margin: 0;
    }
    .dashboard-container-grid {
        display: grid;
        grid-template-columns: 2fr 1fr; /* Video takes 2 parts, Info takes 1 part */
        gap: 2rem;
        padding: 2rem;
        max-width: 1600px;
        margin: 0 auto;
        align-items: start;
    }

    /* Cards */
    .card {
        background: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow);
        overflow: hidden;
        border: 1px solid #eee;
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #fafafa;
    }

    .card-header h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-header p {
        font-size: 0.85rem;
        color: var(--text-light);
        margin: 0;
    }

    /* Video Section */
    .live-stream-container {
        width: 100%;
        background-color: #000;
        min-height: 480px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .live-stream-container img {
        width: 100%;
        height: auto;
        display: block;
        max-height: 700px;
        object-fit: contain;
    }

    /* Info Column & Detection Details */
    .info-column {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .detection-details-container {
        padding: 1.5rem;
    }

    .detail-item {
        margin-bottom: 1.25rem;
        border-bottom: 1px solid #f5f5f5;
        padding-bottom: 1rem;
    }

    .detail-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .detail-item label {
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #888;
        font-weight: 600;
        margin-bottom: 0.4rem;
    }

    .detail-item span {
        font-size: 1.1rem;
        font-weight: 500;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.8rem;
        border-radius: 2rem;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .status-authorized {
        background-color: #def7ec;
        color: #27ae60;
    }

    .status-unauthorized {
        background-color: #fde8e8;
        color: #c0392b;
    }

    /* Placeholders */
    .placeholder-text {
        text-align: center;
        padding: 3rem 1rem;
        color: #aaa;
    }
    
    .placeholder-text i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #ddd;
    }
     /* --- RFID INPUT SECTION (UPDATED) --- */
    .rfid-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    /* Container to hold input + button side-by-side */
    .input-group {
        display: flex;
        width: 100%;
        gap: 10px;
    }

    .rfid-input {
        flex: 1;
        padding: 12px;
        border: 2px solid #cbd5e1;
        border-radius: 8px;
        font-size: 1rem;
        text-align: center;
        font-weight: 600;
        letter-spacing: 1px;
        outline: none;
        transition: all 0.3s ease;
        background-color: #f8fafc;
    }

    .rfid-input:focus {
        border-color: #8b5cf6;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }

    /* New Check Button */
    .rfid-btn {
        padding: 0 1.5rem;
        background-color: #8b5cf6;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .rfid-btn:hover { background-color: #7c3aed; }
    
    .custom-modal {
        display: none; 
        position: fixed; 
        z-index: 2000; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }

    .custom-full-width-modal {
        max-width: 95% !important;
        width: 95% !important;
        margin: 1.75rem auto;
    }
    
    @media (max-width: 768px) {
        .sidebar {
            width: 0;
            overflow: hidden;
        }
        .main-content {
            margin-left: 0;
        }
        /* You might want a toggle button for mobile */
    }
    }
    .modal-content {
        background-color: #fefefe; 
        margin: 5% auto; 
        padding: 25px;
        border: 1px solid #888; 
        width: 90%; 
        max-width: 500px;
        border-radius: 12px; 
        position: relative; 
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        margin-bottom: 20px; 
        border-bottom: 1px solid #eee; 
        padding-bottom: 10px;
    }
    .close-btn {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer; 
    }
    .close-btn:hover { 
        color: black; 
    }
    .input { 
        width: 100%; 
        padding: 10px; 
        border: 1px solid #ddd; 
        border-radius: 5px; 
        font-size: 1rem; 
    }

    .selection-modal {
        display: none; 
        position: fixed; 
        z-index: 2000; 
        left: 0; top: 0; 
        width: 50%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }

</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto Caps Lock for all text inputs (excluding email/password)
        const inputs = document.querySelectorAll('input[type="text"]');
        
        inputs.forEach(input => {
            // Apply text-transform for visual feedback
            input.style.textTransform = "uppercase";
            
            // Force value to uppercase on input
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    });
</script>