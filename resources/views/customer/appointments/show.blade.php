@extends('_layout._layhome.home')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
/* Full-width background wrapper */
.full-bg-wrapper {
    min-height: 100vh;
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    background: linear-gradient(135deg, #FFD700 0%, #FFA500 50%, #FF8C00 100%);
    background-attachment: fixed;
    position: relative;
    overflow-x: hidden;
}

.full-bg-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="15" height="15" patternUnits="userSpaceOnUse"><path d="M 15 0 L 0 0 0 15" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    pointer-events: none;
    z-index: 0;
}

.full-bg-wrapper > * {
    position: relative;
    z-index: 1;
}

/* Appointment Container Styling */
.appointment-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 30px 20px;
    font-family: 'Poppins', sans-serif;
}

/* Page Header */
.page-header {
    text-align: center;
    margin-bottom: 50px;
    color: white;
    position: relative;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: rgba(255,255,255,0.3);
    border-radius: 2px;
}

.page-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 15px;
    text-shadow: 0 4px 8px rgba(0,0,0,0.2);
    letter-spacing: -0.5px;
}

.page-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0;
    font-weight: 300;
    max-width: 600px;
    margin: 0 auto;
}

/* Stats Cards */
.appointments-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 50px;
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
    margin-bottom: 50px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 6px 25px rgba(243, 156, 18, 0.2);
    border: 1px solid rgba(255, 215, 0, 0.3);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.4s ease;
}

.stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 35px rgba(255, 215, 0, 0.3);
    border-color: rgba(255, 215, 0, 0.5);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    position: relative;
    overflow: hidden;
}

.stat-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.3));
    border-radius: 50%;
}

.stat-icon.pending { background: linear-gradient(135deg, #FFD700, #FFA500); }
.stat-icon.confirmed { background: linear-gradient(135deg, #32CD32, #228B22); }
.stat-icon.completed { background: linear-gradient(135deg, #4169E1, #1E90FF); }
.stat-icon.cancelled { background: linear-gradient(135deg, #DC143C, #B22222); }

.stat-info h3 {
    margin: 0;
    font-size: 1.6rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-info p {
    margin: 3px 0 0 0;
    color: #7f8c8d;
    font-weight: 500;
    font-size: 0.9rem;
}

/* Section Headers */
.section-header {
    margin: 40px 0 30px 0;
    text-align: center;
    position: relative;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 10px;
    text-shadow: 0 4px 8px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.section-subtitle {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.9);
    font-weight: 300;
}

.section-divider {
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, rgba(255,255,255,0.3), rgba(255,215,0,0.5), rgba(255,255,255,0.3));
    margin: 20px auto;
    border-radius: 2px;
}

/* Appointments Grid */
.appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.appointment-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(15px);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 8px 25px rgba(243, 156, 18, 0.15);
    border: 2px solid rgba(255, 215, 0, 0.2);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
}

.appointment-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #f39c12, #ffd700, #e67e22);
    z-index: 1;
}

.appointment-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px rgba(255, 215, 0, 0.25);
    border-color: rgba(255, 215, 0, 0.4);
}

/* Priority indicators */
.appointment-card.priority-high::before {
    background: linear-gradient(90deg, #e74c3c, #ff6b6b, #c0392b);
}

.appointment-card.priority-medium::before {
    background: linear-gradient(90deg, #f39c12, #ffd700, #e67e22);
}

.appointment-card.priority-low::before {
    background: linear-gradient(90deg, #27ae60, #2ecc71, #16a085);
}

/* Upcoming vs History cards */
.appointment-card.upcoming {
    border-left: 5px solid #ffd700;
    background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(255,248,220,0.9));
}

.appointment-card.history {
    border-left: 5px solid #bdc3c7;
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,249,250,0.9));
    opacity: 0.9;
}

.appointment-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.appointment-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    line-height: 1.3;
    flex: 1;
    margin-right: 10px;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
    white-space: nowrap;
    flex-shrink: 0;
}

.status-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.status-badge:hover::before {
    left: 100%;
}

.status-badge.pending {
    background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
    color: #d63031;
    border-color: #fdcb6e;
    box-shadow: 0 3px 10px rgba(253, 203, 110, 0.3);
}

.status-badge.confirmed {
    background: linear-gradient(135deg, #55efc4, #00cec9);
    color: #00695c;
    border-color: #00cec9;
    box-shadow: 0 3px 10px rgba(0, 206, 201, 0.3);
}

.status-badge.completed {
    background: linear-gradient(135deg, #74b9ff, #0984e3);
    color: #0d47a1;
    border-color: #0984e3;
    box-shadow: 0 3px 10px rgba(116, 185, 255, 0.3);
}

.status-badge.cancelled {
    background: linear-gradient(135deg, #fab1a0, #e17055);
    color: #c62828;
    border-color: #e17055;
    box-shadow: 0 3px 10px rgba(225, 112, 85, 0.3);
}

/* Vietnamese status badges */
.status-badge.khởi-tạo {
    background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
    color: #d63031;
    border-color: #fdcb6e;
    box-shadow: 0 3px 10px rgba(253, 203, 110, 0.3);
}

.status-badge.đang-thực-hiện {
    background: linear-gradient(135deg, #55efc4, #00cec9);
    color: #00695c;
    border-color: #00cec9;
    box-shadow: 0 3px 10px rgba(0, 206, 201, 0.3);
}

.status-badge.hoàn-thành {
    background: linear-gradient(135deg, #74b9ff, #0984e3);
    color: #0d47a1;
    border-color: #0984e3;
    box-shadow: 0 3px 10px rgba(116, 185, 255, 0.3);
}

.status-badge.hủy-hẹn {
    background: linear-gradient(135deg, #fab1a0, #e17055);
    color: #c62828;
    border-color: #e17055;
    box-shadow: 0 3px 10px rgba(225, 112, 85, 0.3);
}

.appointment-description {
    color: #7f8c8d;
    margin-bottom: 15px;
    line-height: 1.5;
    font-size: 0.9rem;
}

.appointment-details {
    display: grid;
    gap: 8px;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    transition: background-color 0.3s ease;
}

.info-row:hover {
    background-color: rgba(255, 215, 0, 0.05);
    border-radius: 6px;
    padding-left: 8px;
    padding-right: 8px;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #2c3e50;
    min-width: 100px;
    font-size: 0.85rem;
}

.info-value {
    color: #34495e;
    flex: 1;
    font-size: 0.85rem;
}

.appointment-actions {
    margin-top: 15px;
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.btn {
    padding: 10px 16px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    position: relative;
    overflow: hidden;
    font-size: 0.85rem;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.6s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #8B4513;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
    font-weight: 700;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #FFA500, #FF8C00);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
    color: #654321;
}

.btn-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c0392b, #a93226);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #95a5a6, #7f8c8d);
    color: white;
    box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #7f8c8d, #6c7b7d);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(149, 165, 166, 0.4);
}

/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    backdrop-filter: blur(3px);
}

.modal-content {
    background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(255,248,220,0.95));
    backdrop-filter: blur(15px);
    margin: 3% auto;
    padding: 0;
    border-radius: 20px;
    width: 90%;
    max-width: 700px;
    max-height: 85vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(255, 215, 0, 0.3);
    border: 2px solid rgba(255, 215, 0, 0.2);
    animation: modalSlideIn 0.4s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #8B4513;
    padding: 25px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="5" cy="5" r="1" fill="rgba(139,69,19,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
    pointer-events: none;
}

.modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 10px;
}

.close {
    color: #8B4513;
    font-size: 32px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
}

.close:hover {
    color: #654321;
    background: rgba(255,255,255,0.4);
    transform: rotate(90deg) scale(1.1);
}

.modal-body {
    padding: 30px;
    line-height: 1.6;
    max-height: calc(85vh - 100px);
    overflow-y: auto;
}

.modal-info-grid {
    display: grid;
    gap: 20px;
}

.modal-info-row {
    display: flex;
    padding: 15px 20px;
    border-radius: 12px;
    background: rgba(255,255,255,0.7);
    border-left: 4px solid #FFD700;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(255,215,0,0.1);
}

.modal-info-row:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 20px rgba(255,215,0,0.2);
    border-left-color: #FFA500;
}

.modal-info-label {
    font-weight: 700;
    color: #8B4513;
    min-width: 140px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-info-value {
    color: #2c3e50;
    flex: 1;
    font-weight: 500;
}

.no-appointments {
    text-align: center;
    color: white;
    font-size: 1.2rem;
    padding: 60px 20px;
}

.no-appointments-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.7;
}

/* Back Button */
.back-to-home {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,248,220,0.95));
    backdrop-filter: blur(10px);
    color: #FFD700;
    padding: 15px 20px;
    border-radius: 50px;
    text-decoration: none;
    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
    border: 2px solid rgba(255, 215, 0, 0.3);
    transition: all 0.3s ease;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.back-to-home:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(255, 215, 0, 0.3);
    color: #FFA500;
    border-color: rgba(255, 215, 0, 0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
    .appointment-container {
        padding: 15px;
    }

    .page-title {
        font-size: 2rem;
    }

    .section-title {
        font-size: 1.8rem;
        flex-direction: column;
        gap: 10px;
    }

    .appointments-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .appointments-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .stat-card {
        padding: 15px;
        gap: 12px;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    .stat-info h3 {
        font-size: 1.3rem;
    }

    .stat-info p {
        font-size: 0.8rem;
    }

    .appointment-card {
        padding: 20px;
    }

    .appointments-stats {
        grid-template-columns: 1fr;
    }

    .modal-content {
        margin: 10% auto;
        padding: 20px;
        width: 95%;
    }

    .back-to-home {
        bottom: 20px;
        right: 20px;
    }

    .appointment-actions {
        flex-direction: column;
        gap: 8px;
    }

    .btn {
        width: 100%;
        justify-content: center;
        padding: 12px 16px;
        font-size: 0.8rem;
    }
}

/* Animation keyframes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.appointment-card {
    animation: fadeInUp 0.6s ease-out;
}

.appointment-card:nth-child(even) {
    animation-delay: 0.1s;
}

.appointment-card:nth-child(odd) {
    animation-delay: 0.2s;
}

/* Tab System Styling */
.appointments-tabs {
    margin-bottom: 40px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 8px;
    box-shadow: 0 8px 30px rgba(255, 215, 0, 0.15);
    border: 1px solid rgba(255, 215, 0, 0.2);
}

.tab-buttons {
    display: flex;
    gap: 8px;
}

.tab-btn {
    flex: 1;
    padding: 16px 24px;
    border: none;
    background: transparent;
    color: #8B4513;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.tab-btn:hover {
    background: rgba(255, 215, 0, 0.1);
    transform: translateY(-2px);
}

.tab-btn.active {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: white;
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.3);
    transform: translateY(-2px);
}

.tab-btn .tab-count {
    background: rgba(255, 255, 255, 0.3);
    padding: 4px 8px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 700;
}

.tab-btn.active .tab-count {
    background: rgba(255, 255, 255, 0.4);
    color: #8B4513;
}

.tab-content {
    display: none;
    animation: fadeInUp 0.4s ease-out;
}

.tab-content.active {
    display: block;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Table Styling */
.appointments-table {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(255, 215, 0, 0.15);
    border: 1px solid rgba(255, 215, 0, 0.2);
}

.table-header {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: white;
    padding: 25px 30px;
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
    overflow: hidden;
}

.table-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="5" cy="5" r="1" fill="rgba(255,255,255,0.2)"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
    pointer-events: none;
}

.table-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    position: relative;
    z-index: 1;
}

.table-header .table-count {
    background: rgba(255, 255, 255, 0.3);
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.table-responsive {
    overflow-x: auto;
    max-height: 600px;
    overflow-y: auto;
}

.appointments-data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.appointments-data-table thead th {
    background: rgba(255, 248, 220, 0.8);
    color: #8B4513;
    padding: 18px 16px;
    text-align: left;
    font-weight: 700;
    border-bottom: 2px solid rgba(255, 215, 0, 0.3);
    position: sticky;
    top: 0;
    z-index: 2;
}

.appointments-data-table tbody tr {
    border-bottom: 1px solid rgba(255, 215, 0, 0.1);
    transition: all 0.3s ease;
}

.appointments-data-table tbody tr:hover {
    background: rgba(255, 248, 220, 0.3);
    transform: scale(1.01);
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.1);
}

.appointments-data-table tbody td {
    padding: 16px;
    vertical-align: top;
    line-height: 1.5;
}

.appointment-title-cell {
    font-weight: 600;
    color: #8B4513;
    max-width: 200px;
}

.appointment-property {
    color: #666;
    font-size: 0.85rem;
    margin-top: 4px;
}

.appointment-people {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.person-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
}

.person-info i {
    color: #FFD700;
    width: 14px;
}

.appointment-datetime {
    white-space: nowrap;
    color: #555;
}

.datetime-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
    font-size: 0.85rem;
}

.datetime-row i {
    color: #FFD700;
    width: 14px;
}

.table-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-table {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    white-space: nowrap;
}

.btn-table:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #545b62);
    color: white;
}

/* Empty State for Table */
.table-empty {
    text-align: center;
    padding: 60px 30px;
    color: #8B4513;
}

.table-empty i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.table-empty h4 {
    margin-bottom: 10px;
    font-weight: 600;
}

.table-empty p {
    opacity: 0.7;
    margin-bottom: 0;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .tab-btn {
        font-size: 0.9rem;
        padding: 14px 16px;
    }

    .appointments-data-table {
        font-size: 0.8rem;
    }

    .appointments-data-table thead th,
    .appointments-data-table tbody td {
        padding: 12px 8px;
    }

    .table-actions {
        flex-direction: column;
    }

    .btn-table {
        font-size: 0.75rem;
        padding: 6px 12px;
    }
}

/* Scroll to top button */
.scroll-top {
    position: fixed;
    bottom: 30px;
    left: 30px;
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,248,220,0.95));
    backdrop-filter: blur(10px);
    color: #FFD700;
    padding: 12px;
    border-radius: 50%;
    border: 2px solid rgba(255, 215, 0, 0.3);
    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
    cursor: pointer;
    transition: all 0.3s ease;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: translateY(50px);
    pointer-events: none;
}

.scroll-top.show {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}

.scroll-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(255, 215, 0, 0.3);
    border-color: rgba(255, 215, 0, 0.5);
    color: #FFA500;
}
</style>
@endsection

@section('content')
<!-- Full-width background wrapper -->
<div class="full-bg-wrapper">
    <div class="appointment-container">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-calendar-check"></i> Quản Lý Lịch Hẹn</h1>
            <p class="page-subtitle">Theo dõi và quản lý tất cả các cuộc hẹn xem bất động sản của bạn</p>
        </div>        @if($appointments && $appointments->count() > 0)
        <!-- Stats Cards -->
        <div class="appointments-stats">
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $appointments->whereIn('Status', ['Khởi tạo', 'Đang Thực hiện'])->count() }}</h3>
                    <p>Cuộc hẹn sắp tới</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon confirmed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $appointments->where('Status', 'Đang Thực hiện')->count() }}</h3>
                    <p>Đang thực hiện</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $appointments->where('Status', 'Hoàn Thành')->count() }}</h3>
                    <p>Hoàn thành</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon cancelled">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $appointments->where('Status', 'Hủy Hẹn')->count() }}</h3>
                    <p>Đã hủy</p>
                </div>
            </div>
        </div>

        @php
            $upcomingAppointments = $appointments->filter(function($appointment) {
                return in_array($appointment->Status, ['Khởi tạo', 'Đang Thực hiện']);
            });
            $historyAppointments = $appointments->filter(function($appointment) {
                return in_array($appointment->Status, ['Hoàn Thành', 'Hủy Hẹn']);
            });
        @endphp

        <!-- Tabs Navigation -->
        <div class="appointments-tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="switchTab('upcoming')" id="upcomingTab">
                    <i class="fas fa-clock"></i>
                    Lịch Hẹn Sắp Diễn Ra
                    <span class="tab-count">{{ $upcomingAppointments->count() }}</span>
                </button>
                <button class="tab-btn" onclick="switchTab('history')" id="historyTab">
                    <i class="fas fa-archive"></i>
                    Lịch Sử Cuộc Hẹn
                    <span class="tab-count">{{ $historyAppointments->count() }}</span>
                </button>
            </div>
        </div>

        <!-- Upcoming Appointments Tab Content -->
        <div id="upcomingContent" class="tab-content active">
            <div class="appointments-table">
                <div class="table-header">
                    <i class="fas fa-clock"></i>
                    <h3>Lịch Hẹn Sắp Diễn Ra</h3>
                    <span class="table-count">{{ $upcomingAppointments->count() }} cuộc hẹn</span>
                </div>

                @if($upcomingAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="appointments-data-table">
                        <thead>
                            <tr>
                                <th>Thông tin cuộc hẹn</th>
                                <th>Người liên quan</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingAppointments as $appointment)
                            <tr>
                                <td>
                                    <div class="appointment-title-cell">
                                        {{ $appointment->TitleAppoint ?? 'Cuộc hẹn xem nhà' }}
                                    </div>
                                    <div class="appointment-property">
                                        <i class="fas fa-home"></i> {{ $appointment->property->Title ?? 'Không có thông tin' }}
                                    </div>
                                    @if($appointment->DescAppoint)
                                    <div class="appointment-property">
                                        <i class="fas fa-info-circle"></i> {{ Str::limit($appointment->DescAppoint, 80) }}
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="appointment-people">
                                        <div class="person-info">
                                            <i class="fas fa-user-tie"></i>
                                            <span>{{ $appointment->user_agent->Name ?? 'Chưa phân công' }}</span>
                                        </div>
                                        <div class="person-info">
                                            <i class="fas fa-user"></i>
                                            <span>{{ $appointment->user_owner->Name ?? 'Không có thông tin' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="appointment-datetime">
                                    <div class="datetime-row">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>{{ $appointment->AppointmentDateStart ? \Carbon\Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y H:i') : 'Chưa xác định' }}</span>
                                    </div>
                                    <div class="datetime-row">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $appointment->AppointmentDateEnd ? \Carbon\Carbon::parse($appointment->AppointmentDateEnd)->format('d/m/Y H:i') : 'Chưa xác định' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge {{ strtolower(str_replace(' ', '-', $appointment->Status ?? 'khoi-tao')) }}">
                                        {{ $appointment->Status ?? 'Khởi tạo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn-table btn-primary" onclick="showAppointmentDetails({{ $appointment->AppointmentID }})">
                                            <i class="fas fa-info-circle"></i> Chi tiết
                                        </button>
                                        @if($appointment->Status === 'Khởi tạo' || $appointment->Status === 'Đang Thực hiện')
                                        <button class="btn-table btn-danger" onclick="cancelAppointment({{ $appointment->AppointmentID }})">
                                            <i class="fas fa-times"></i> Hủy
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="table-empty">
                    <i class="fas fa-calendar-plus"></i>
                    <h4>Chưa có lịch hẹn sắp tới</h4>
                    <p>Bạn chưa có cuộc hẹn nào đang chờ thực hiện hoặc đang diễn ra</p>
                </div>
                @endif
            </div>
        </div>

        <!-- History Appointments Tab Content -->
        <div id="historyContent" class="tab-content">
            <div class="appointments-table">
                <div class="table-header">
                    <i class="fas fa-archive"></i>
                    <h3>Lịch Sử Cuộc Hẹn</h3>
                    <span class="table-count">{{ $historyAppointments->count() }} cuộc hẹn</span>
                </div>

                @if($historyAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="appointments-data-table">
                        <thead>
                            <tr>
                                <th>Thông tin cuộc hẹn</th>
                                <th>Người liên quan</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historyAppointments as $appointment)
                            <tr>
                                <td>
                                    <div class="appointment-title-cell">
                                        {{ $appointment->TitleAppoint ?? 'Cuộc hẹn xem nhà' }}
                                    </div>
                                    <div class="appointment-property">
                                        <i class="fas fa-home"></i> {{ $appointment->property->Title ?? 'Không có thông tin' }}
                                    </div>
                                    @if($appointment->DescAppoint)
                                    <div class="appointment-property">
                                        <i class="fas fa-info-circle"></i> {{ Str::limit($appointment->DescAppoint, 80) }}
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="appointment-people">
                                        <div class="person-info">
                                            <i class="fas fa-user-tie"></i>
                                            <span>{{ $appointment->user_agent->Name ?? 'Chưa phân công' }}</span>
                                        </div>
                                        <div class="person-info">
                                            <i class="fas fa-user"></i>
                                            <span>{{ $appointment->user_owner->Name ?? 'Không có thông tin' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="appointment-datetime">
                                    <div class="datetime-row">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>{{ $appointment->AppointmentDateStart ? \Carbon\Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y H:i') : 'Chưa xác định' }}</span>
                                    </div>
                                    <div class="datetime-row">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $appointment->AppointmentDateEnd ? \Carbon\Carbon::parse($appointment->AppointmentDateEnd)->format('d/m/Y H:i') : 'Chưa xác định' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge {{ strtolower(str_replace(' ', '-', $appointment->Status ?? 'completed')) }}">
                                        {{ $appointment->Status ?? 'Hoàn thành' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn-table btn-secondary" onclick="showAppointmentDetails({{ $appointment->AppointmentID }})">
                                            <i class="fas fa-info-circle"></i> Xem chi tiết
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="table-empty">
                    <i class="fas fa-history"></i>
                    <h4>Chưa có lịch sử cuộc hẹn</h4>
                    <p>Bạn chưa có cuộc hẹn nào đã hoàn thành hoặc bị hủy</p>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="no-appointments">
            <div class="no-appointments-icon">📅</div>
            <h2>Chưa có lịch hẹn nào</h2>
            <p>Bạn chưa có cuộc hẹn xem bất động sản nào. Hãy tìm kiếm và đặt lịch xem nhà ngay!</p>
            <div style="margin-top: 30px;">
                <a href="{{ url('/properties') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                    <i class="fas fa-search"></i> Tìm kiếm bất động sản
                </a>
            </div>
        </div>
    @endif

    <!-- Back to Home Button -->
    <a href="{{ url('/') }}" class="back-to-home">
        <i class="fas fa-home"></i> Về trang chủ
    </a>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i>
    </button>
</div>

<!-- Modal for Appointment Details -->
<div id="appointmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title"><i class="fas fa-calendar-check"></i> Chi Tiết Lịch Hẹn</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Tab switching function
function switchTab(tabName) {
    // Remove active class from all tabs and content
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    // Add active class to selected tab and content
    document.getElementById(tabName + 'Tab').classList.add('active');
    document.getElementById(tabName + 'Content').classList.add('active');
}

// Show/hide scroll to top button
window.addEventListener('scroll', function() {
    const scrollTop = document.querySelector('.scroll-top');
    if (window.pageYOffset > 300) {
        scrollTop.classList.add('show');
    } else {
        scrollTop.classList.remove('show');
    }
});

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show appointment details in modal
function showAppointmentDetails(appointmentId) {
    const appointments = @json($appointments ?? []);
    const appointment = appointments.find(app => app.AppointmentID == appointmentId);

    if (!appointment) {
        alert('Không tìm thấy thông tin lịch hẹn');
        return;
    }

    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <div class="modal-info-grid">
            <div class="modal-info-row">
                <span class="modal-info-label"><i class="fas fa-tag"></i> Tiêu đề:</span>
                <span class="modal-info-value">${appointment.TitleAppoint || 'Cuộc hẹn xem nhà'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label"><i class="fas fa-align-left"></i> Mô tả:</span>
                <span class="modal-info-value">${appointment.DescAppoint || 'Không có mô tả'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label"><i class="fas fa-home"></i> Bất động sản:</span>
                <span class="modal-info-value">${appointment.property?.Title || 'Không có thông tin'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label"><i class="fas fa-user-tie"></i> Agent phụ trách:</span>
                <span class="modal-info-value">${appointment.user_agent?.Name || 'Chưa phân công'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label"><i class="fas fa-user"></i> Chủ sở hữu:</span>
                <span class="modal-info-value">${appointment.user_owner?.Name || 'Không có thông tin'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label"><i class="fas fa-calendar-alt"></i> Thời gian bắt đầu:</span>
                <span class="modal-info-value">${appointment.AppointmentDateStart ? new Date(appointment.AppointmentDateStart).toLocaleString('vi-VN') : 'Chưa xác định'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label"><i class="fas fa-clock"></i> Thời gian kết thúc:</span>
                <span class="modal-info-value">${appointment.AppointmentDateEnd ? new Date(appointment.AppointmentDateEnd).toLocaleString('vi-VN') : 'Chưa xác định'}</span>
            </div>
            <div class="modal-info-row">
                <span class="modal-info-label"><i class="fas fa-info-circle"></i> Trạng thái:</span>
                <span class="modal-info-value">
                    <span class="status-badge ${appointment.Status?.toLowerCase().replace(' ', '-') || 'pending'}">
                        ${appointment.Status || 'Pending'}
                    </span>
                </span>
            </div>
        </div>
    `;

    document.getElementById('appointmentModal').style.display = 'block';
}

// Close modal
function closeModal() {
    document.getElementById('appointmentModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('appointmentModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Cancel appointment
function cancelAppointment(appointmentId) {
    if (confirm('Bạn có chắc chắn muốn hủy lịch hẹn này không?')) {
        fetch(`/appointments/${appointmentId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Hủy lịch hẹn thành công!');
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi hủy lịch hẹn. Vui lòng thử lại.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi hủy lịch hẹn. Vui lòng thử lại.');
        });
    }
}
</script>
@endsection
