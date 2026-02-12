<?php
session_start();
if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
    header('Location: home.php');
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Status | ASPORD</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --bg-main: #f8fafc;
            --surface: #ffffff;
            --text-dark: #0f172a;
            --text-body: #475569;
            --text-muted: #94a3b8;
            --border-soft: #e2e8f0;
            --accent: #2563eb;
            --success: #10b981;
            --font-head: 'Outfit', sans-serif;
            --font-body: 'Inter', sans-serif;
            /* Legacy Support */
            --obsidian: #0f172a;
            --gold: #2563eb;
            --secondary: #2563eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg-main);
            color: var(--text-body);
            padding-bottom: 140px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            padding: 0 20px;
        }

        /* ===== STATUS HEADER ===== */
        .status-header {
            background: var(--surface);
            padding: 80px 20px 60px;
            text-align: center;
            border-bottom: 1px solid var(--border-soft);
            margin-bottom: 0;
        }

        .status-icon {
            width: 80px;
            height: 80px;
            background: #ecfdf5;
            color: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 24px;
            border-radius: 50%;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.1);
        }

        .status-title {
            font-family: var(--font-head);
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 10px;
            color: var(--text-dark);
            letter-spacing: -1.5px;
        }

        .status-subtitle {
            font-size: 15px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* ===== ACCORDION SUMMARY ===== */
        .order-summary-box {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .summary-toggle {
            width: 100%;
            padding: 20px 24px;
            background: white;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            color: var(--obsidian);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .summary-toggle:after {
            content: '\f078'; /* FontAwesome chevron-down */
            content: '\f078';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 12px;
            transition: transform 0.3s;
        }

        .summary-toggle.active:after { transform: rotate(180deg); }

        .summary-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            background: #f8fafc;
        }

        .summary-content.active { max-height: 1000px; }

        .summary-inner { padding: 30px 24px; }

        .summary-item {
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            margin-bottom: 12px;
            color: var(--text-body);
            font-weight: 500;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            font-size: 20px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 2px solid var(--text-dark);
            color: var(--text-dark);
            font-family: var(--font-head);
        }

        .receipt-btn {
            display: block;
            width: 100%;
            margin-top: 30px;
            padding: 18px;
            background: var(--text-dark);
            color: white;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }

        .receipt-btn:hover {
            background: var(--accent);
            transform: translateY(-2px);
        }

        .status-pill {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pill.pending { background: #fff7ed; color: #c2410c; }
        .status-pill.done { background: #f0fdf4; color: #15803d; }

        /* ===== STANDARD INDIAN RECEIPT CARD ===== */
        .receipt-card {
            background: #ffffff;
            border: 1px solid #ddd;
            width: 100%;
            margin: 0;
            padding: 30px;
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .receipt-header h2 {
            margin: 0;
            font-size: 26px; /* Increased font-size */
            font-weight: 800;
            text-transform: uppercase;
        }

        .receipt-header p {
            margin: 4px 0;
            font-size: 14px; /* Increased font-size */
            line-height: 1.4;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px; /* Increased font-size */
            margin-bottom: 8px;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px; /* Increased font-size */
            margin: 20px 0;
        }

        .receipt-table th {
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
            padding: 10px 0;
            text-align: left;
        }

        .receipt-table td {
            padding: 8px 0;
        }

        .receipt-total-section {
            border-top: 2px dashed #000;
            padding-top: 15px;
            font-size: 15px; /* Increased font-size */
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .total-row.grand-total {
            font-weight: 800;
            font-size: 20px; /* Increased font-size */
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 10px 0;
            margin-top: 10px;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            border-top: 2px dashed #000;
            padding-top: 15px;
        }

        .payment-summary {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 13px;
        }

        .payment-summary h4 {
            margin: 0 0 5px;
            font-size: 14px;
            text-decoration: underline;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .download-bar {
            padding: 20px;
            text-align: center;
        }

        .download-btn {
            background: var(--text-dark);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        @media print {
            body * { visibility: hidden; }
            #digitalBill, #digitalBill * { visibility: visible; }
            #digitalBill {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: none;
                margin: 0;
                box-shadow: none;
                border: none;
                padding: 0;
            }
            .download-bar { display: none !important; }
            .receipt-card { border: none; width: 100% !important; max-width: none !important; }
        }

        /* ===== CART (COLLAPSIBLE SYNCED WITH HOME.PHP) ===== */
        .cart-bar {
            position: fixed;
            bottom: 20px;
            left: 16px;
            right: 16px;
            background: var(--text-dark);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border-radius: 16px;
            display: none;
            z-index: 2000;
            color: white;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cart-header {
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            background: var(--text-dark);
            position: relative;
            z-index: 10;
        }

        .cart-summary {
            display: flex;
            flex-direction: column;
        }

        .cart-count {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .cart-total {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-toggle-icon {
            font-size: 14px;
            color: var(--text-muted);
            transition: transform 0.3s;
        }

        .cart-bar.open .cart-toggle-icon {
            transform: rotate(180deg);
        }

        .cart-list-container {
            max-height: 0;
            overflow-y: auto;
            background: #1e293b;
            transition: max-height 0.3s ease;
        }

        .cart-bar.open .cart-list-container {
            max-height: 40vh;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .cart-item-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 20px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .cart-item-row:last-child {
            border-bottom: none;
        }

        .place-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 99px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ===== MODAL & AUTH (SYNCED WITH HOME.PHP) ===== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 3000;
            backdrop-filter: blur(4px);
        }

        .modal-card {
            background: var(--surface);
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.15);
            border: 1px solid var(--border-soft);
            position: relative;
            animation: modalFadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-muted);
            cursor: pointer;
        }

        @keyframes modalFadeUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-card h3 {
            font-family: var(--font-head);
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 10px;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        .modal-card p {
            color: var(--text-muted);
            margin-bottom: 30px;
            font-size: 15px;
        }

        .input-group {
            position: relative;
            margin-bottom: 16px;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .input-group input {
            width: 100%;
            padding: 16px 16px 16px 45px;
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .modal-btn {
            width: 100%;
            padding: 16px;
            background: var(--text-dark);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .modal-btn:hover {
            background: var(--accent);
            transform: translateY(-2px);
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .otp-digit {
            width: 45px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 1px solid var(--border-soft);
            border-radius: 10px;
            background: #f8fafc;
            outline: none;
        }

        .otp-digit:focus {
            border-color: var(--accent);
            background: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }

        /* ===== CUSTOM POPUP ===== */
        #customPopup {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            background: white;
            padding: 12px 25px;
            border-radius: 999px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 4000;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        #customPopup.show { transform: translateX(-50%) translateY(0); }

        #customPopup .icon {
            width: 24px;
            height: 24px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .divider-dashed {
            border-bottom: 1px dashed var(--border-soft);
            margin: 0 20px;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* ===== SEARCH (SYNCED FROM HOME.PHP) ===== */
        .search-container {
            margin-top: 16px;
        }

        .search-box {
            background: var(--surface);
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            position: relative;
        }

        .search-box:focus-within {
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
        }

        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            height: 54px;
            padding: 0 20px 0 52px;
            font-size: 15px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .search-box i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
        }

        @media (max-width: 640px) {
            .search-container {
                margin-top: 12px !important;
                margin-bottom: 12px !important;
                padding: 0 16px !important;
                background: var(--bg-main);
            }

            .search-box {
                border-radius: 10px;
                background: white;
            }

            .search-box input {
                height: 48px;
                padding: 0 16px 0 44px;
                font-size: 14px;
            }

            .search-box i {
                left: 16px;
                font-size: 14px;
            }
        }

        /* SEARCH SUGGESTIONS STYLING */
        .suggestion-item {
            padding: 12px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-soft);
            transition: all 0.2s ease;
            text-align: left;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item:hover {
            background: #f8fafc;
        }

        .suggestion-item .item-info {
            display: flex;
            flex-direction: column;
        }

        .suggestion-item .item-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
        }

        .suggestion-item .item-cat {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== VIEW BILL TRIGGER ===== */
        .view-bill-banner {
            background: #fff;
            margin: 20px 16px;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid var(--border-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideUp 0.6s ease-out;
        }

        .view-bill-info h4 {
            margin: 0;
            font-size: 18px;
            color: var(--text-dark);
            font-family: var(--font-head);
        }

        .view-bill-info p {
            margin: 4px 0 0;
            font-size: 13px;
            color: var(--text-muted);
        }

        .view-bill-btn {
            background: var(--text-dark);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .view-bill-btn:hover {
            background: var(--accent);
            transform: scale(1.02);
        }

        /* ===== BILL MODAL ===== */
        .bill-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
            z-index: 5000;
            display: none;
            align-items: flex-start;
            justify-content: center;
            overflow-y: auto;
            padding: 20px;
        }

        .bill-modal-content {
            width: 100%;
            max-width: 500px;
            position: relative;
            margin: 40px auto;
            animation: modalFadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .close-bill-modal {
            position: absolute;
            top: -50px;
            right: 0;
            background: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--text-dark);
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            border: none;
            z-index: 5001;
        }

        /* ===== UPSELL & MENU (SYNCED WITH HOME.PHP) ===== */
        .upsell-header {
            padding: 80px 20px 40px;
            text-align: center;
        }

        .upsell-header h3 {
            font-family: var(--font-head);
            font-size: 36px;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: -1px;
        }

        .upsell-header p {
            font-size: 15px;
            color: var(--text-muted);
            margin-top: 8px;
            font-weight: 500;
        }

        .categories-wrapper {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--border);
        }

        .categories {
            display: flex;
            gap: 12px;
            padding: 16px 20px;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .categories::-webkit-scrollbar { display: none; }

        .categories span {
            flex-shrink: 0;
            padding: 10px 22px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 700;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text-dark);
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .categories span.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .menu-grid {
            display: flex;
            flex-direction: column;
            gap: 0;
            padding-bottom: 40px;
            background: var(--sand);
        }

        .menu-item-z {
            display: flex;
            justify-content: space-between;
            padding: 30px 20px;
            background: transparent;
            gap: 20px;
            border-bottom: 1px solid var(--border);
            position: relative;
        }

        .menu-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            min-width: 0;
        }

        .menu-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .diet-icon {
            width: 16px;
            height: 16px;
            border: 1.5px solid;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 8px;
        }

        .diet-icon.veg { border-color: #24963f; color: #24963f; }
        .diet-icon.nonveg { border-color: #dc2626; color: #dc2626; }

        .spicy-icon {
            color: #ea580c;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
        }

        .tag-bestseller {
            font-size: 10px;
            font-weight: 800;
            color: var(--gold);
            background: var(--obsidian);
            padding: 2px 8px;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu-title {
            font-family: var(--font-head);
            font-size: 20px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 4px;
        }

        .menu-price {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin-top: 8px;
        }

        .menu-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 6px;
        }

        .stars {
            background: #f1f5f9;
            color: #64748b;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stars i { color: #f59e0b; }

        .votes { font-size: 12px; font-weight: 500; color: var(--text-muted); }

        .menu-desc {
            font-size: 13px;
            color: var(--text-body);
            margin-top: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .menu-right {
            width: 130px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .img-wrapper {
            width: 120px;
            height: 120px;
            position: relative;
            margin-bottom: 12px;
        }

        .img-wrapper img {
            width: 100%;
            height: 100%;
            border-radius: 16px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .add-action-area {
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 90px;
            height: 36px;
            z-index: 5;
        }

        .add-btn-z {
            width: 100%;
            height: 100%;
            background: var(--text-dark);
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
        }

        .add-btn-z:hover {
            background: var(--accent);
            transform: translateY(-2px);
        }

        .qty-control-z {
            width: 100%;
            height: 100%;
            background: var(--accent);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
        }

        .qty-control-z button {
            width: 28px;
            height: 100%;
            background: transparent;
            border: none;
            color: white;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .qty-control-z span { font-weight: 700; font-size: 15px; }
    </style>
</head>

<body>

    <!-- STATUS HEADER -->
    <div class="status-header">
        <div class="status-icon"><i class="fas fa-check"></i></div>
        <h1 class="status-title">Order Placed!</h1>
        <div class="status-subtitle">Kitchen has received your order</div>
    </div>


    <!-- VIEW BILL TRIGGER (RECTANGULAR BOX) -->
    <div class="view-bill-banner">
        <div class="view-bill-info">
            <h4>Total Consolidated Bill</h4>
            <p>View all orders and payment breakdown</p>
        </div>
        <button class="view-bill-btn" onclick="toggleBillModal(true)">
            <i class="fas fa-receipt"></i> View Bill
        </button>
    </div>

    <!-- BILL POPUP MODAL -->
    <div id="billModal" class="bill-modal-overlay">
      <div class="bill-modal-content">
        <button class="close-bill-modal" onclick="toggleBillModal(false)">&times;</button>
        
        <div class="receipt-card" id="digitalBill">
            <div class="receipt-header">
                <h2>ASPORD RESTAURANT</h2>
                <p>123 Food Street, Cyber Hub, Gurgaon</p>
                <p>Tel: +91 98765 43210</p>
                <p>GSTIN: 06AAACA1234A1Z5 | FSSAI: 12345678901234</p>
            </div>
            
            <div class="receipt-info">
                <span>Bill No: <span id="billNo">#1001</span></span>
                <span>Date: <span id="billDateOnly"></span></span>
            </div>
            <div class="receipt-info">
                <span>Table: <span id="billTableNo">TABLE1</span></span>
                <span>Time: <span id="billTimeOnly"></span></span>
            </div>
            <div class="receipt-info">
                <span>Customer: <span id="custName">Guest</span></span>
            </div>

            <table class="receipt-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Item</th>
                        <th style="width: 15%; text-align: center;">Qty</th>
                        <th style="width: 15%; text-align: right;">Rate</th>
                        <th style="width: 20%; text-align: right;">Amt</th>
                    </tr>
                </thead>
                <tbody id="receiptItems">
                    <!-- Items injected here -->
                </tbody>
            </table>

            <div class="receipt-total-section">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span id="subTotal">₹0.00</span>
                </div>
                <div class="total-row">
                    <span>CGST (2.5%)</span>
                    <span id="cgstAmt">₹0.00</span>
                </div>
                <div class="total-row">
                    <span>SGST (2.5%)</span>
                    <span id="sgstAmt">₹0.00</span>
                </div>
                <div class="total-row grand-total">
                    <span>GRAND TOTAL</span>
                    <span id="grandTotalAmt">₹0.00</span>
                </div>
            </div>

            <div class="payment-summary" id="paymentBreakdown">
                <!-- Payment breakdown injected here -->
            </div>

            <div class="receipt-footer">
                <p>THANK YOU! VISIT AGAIN</p>
                <p>aspord</p>
            </div>

            <div class="download-bar">
                <button class="download-btn" onclick="downloadBill()">
                    <i class="fas fa-print"></i> PRINT RECEIPT
                </button>
            </div>
        </div>
      </div>
    </div>

    <!-- UPSELL HEADER -->
    <div class="container upsell-header">
        <h3>Still Hungry?</h3>
        <p>Add more items to your table immediately.</p>
    </div>


    <!-- CATEGORIES -->
    <div class="categories-wrapper">
        <div class="categories container"></div>
    </div>

    <!-- SEARCH BELOW CATEGORIES -->
    <div class="search-container container" style="margin-top: 16px; margin-bottom: 16px;">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search for dishes...">
            <div id="searchSuggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: white; z-index: 1000; border: 1px solid var(--border-soft); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: none; margin-top: 8px; overflow: hidden;"></div>
        </div>
    </div>

    <!-- MENU GRID -->
    <div class="menu-grid container" id="menu"></div>

    <!-- CART BAR (SYNCED WITH HOME.PHP) -->
    <div class="cart-bar" id="cartBar">
      <div class="cart-header" onclick="toggleCart()">
        <div class="cart-summary">
            <span class="cart-count"><span id="cartCount">0</span> ITEMS</span>
            <div class="cart-total">
                <span id="cartTotalDisplay">₹0</span>
                <i class="fas fa-chevron-up cart-toggle-icon"></i>
            </div>
        </div>
        <button class="place-btn" onclick="placeOrder(event)">
            Place Order <i class="fas fa-arrow-right"></i>
        </button>
      </div>
      <div class="cart-list-container" id="cartList">
        <!-- Content injected via JS -->
      </div>
    </div>


    <!-- SCRIPTS -->
    <script>
        // 1. RENDER FUTURISTIC BILL
        const historyJson = sessionStorage.getItem("aspord_order_history");
        const userJson = localStorage.getItem("aspordUser");
        
        const compactContainer = document.getElementById("compactItems");
        const tableBody = document.getElementById("billTableBody");
        const dateElement = document.getElementById("billDate");

        if (historyJson) {
            try {
                const history = JSON.parse(historyJson);
                const user = userJson ? JSON.parse(userJson) : { name: "Guest", email: "N/A" };
                const now = new Date();
                
                document.getElementById("billDateOnly").textContent = now.toLocaleDateString();
                document.getElementById("billTimeOnly").textContent = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                document.getElementById("custName").textContent = user.name;
                document.getElementById("billTableNo").textContent = sessionStorage.getItem('aspord_table') || "TABLE1";

                let subtotal = 0;
                let allItems = {};
                let lastOrderId = "#1001";
                let paymentDetails = []; // To store breakdown
                let methodGroups = {}; // { UPI: { items: { name: qty }, subtotal: amount }, CASH: ... }

                history.forEach(order => {
                    const method = order.method || "CASH";
                    lastOrderId = "#" + (order.id || "1001");
                    
                    if (!methodGroups[method]) {
                        methodGroups[method] = { items: {}, subtotal: 0 };
                    }
                    
                    Object.keys(order.items).forEach(name => {
                        const item = order.items[name];
                        
                        // Global items for main receipt table
                        if(!allItems[name]) allItems[name] = { qty: 0, price: item.price };
                        allItems[name].qty += item.qty;
                        
                        // Aggregated items per payment method
                        if (!methodGroups[method].items[name]) methodGroups[method].items[name] = 0;
                        methodGroups[method].items[name] += item.qty;
                        
                        methodGroups[method].subtotal += item.qty * item.price;
                    });
                });

                // Generate payment details from groups
                Object.keys(methodGroups).forEach(m => {
                    let itemStrings = [];
                    Object.keys(methodGroups[m].items).forEach(name => {
                        itemStrings.push(`${name} x${methodGroups[m].items[name]}`);
                    });
                    paymentDetails.push({
                        method: m,
                        items: itemStrings.join(", "),
                        amount: methodGroups[m].subtotal
                    });
                });

                document.getElementById("billNo").textContent = lastOrderId;

                const itemsContainer = document.getElementById("receiptItems");
                Object.keys(allItems).forEach(name => {
                    const item = allItems[name];
                    const itemAmt = item.qty * item.price;
                    subtotal += itemAmt;

                    itemsContainer.innerHTML += `
                        <tr>
                            <td>${name}</td>
                            <td style="text-align: center;">${item.qty}</td>
                            <td style="text-align: right;">${item.price}</td>
                            <td style="text-align: right;">${itemAmt.toFixed(2)}</td>
                        </tr>
                    `;
                });

                const cgst = subtotal * 0.025;
                const sgst = subtotal * 0.025;
                const grandTotal = subtotal + cgst + sgst;

                document.getElementById("subTotal").textContent = "₹" + subtotal.toFixed(2);
                document.getElementById("cgstAmt").textContent = "₹" + cgst.toFixed(2);
                document.getElementById("sgstAmt").textContent = "₹" + sgst.toFixed(2);
                document.getElementById("grandTotalAmt").textContent = "₹" + grandTotal.toFixed(2);

                // Populate Payment Breakdown
                const breakdownEl = document.getElementById("paymentBreakdown");
                if(breakdownEl) {
                    let breakdownHtml = '<h4>PAYMENT SUMMARY</h4>';
                    paymentDetails.forEach(p => {
                        breakdownHtml += `
                            <div class="payment-row" style="align-items: flex-start; margin-bottom: 6px;">
                                <span style="flex: 1; padding-right: 15px; font-size: 11px; line-height: 1.3;">
                                    ${p.items} (${p.method})
                                </span>
                                <span style="white-space: nowrap;">₹${p.amount.toFixed(2)}</span>
                            </div>
                        `;
                    });
                    breakdownEl.innerHTML = breakdownHtml;
                }

            } catch (e) {
                console.error("Error building bill:", e);
            }
        }

        function toggleDetails(btn) {
            btn.classList.toggle("active");
            const content = document.getElementById("billCollapse");
            content.classList.toggle("active");
        }

        function toggleBillModal(show) {
            const modal = document.getElementById('billModal');
            if (show) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Prevent background scroll
            } else {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Restore scroll
            }
        }

        function downloadBill() {
            const element = document.getElementById('digitalBill');
            if (!element) return;

            // Get Bill Number and Current Date
            const billNo = document.getElementById('billNo').innerText.replace('#', '').trim() || '1001';
            const now = new Date();
            const dateStr = now.toLocaleDateString('en-IN').replace(/\//g, '-'); // DD-MM-YYYY
            const filename = `ASPORD-Restaurant-Bill-${dateStr}-${billNo}.pdf`;

            // Temporarily hide the download bar
            const downloadBar = element.querySelector('.download-bar');
            if (downloadBar) downloadBar.style.display = 'none';

            const opt = {
                margin: [10, 5, 10, 5],
                filename: filename,
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { 
                    scale: 2, 
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    scrollY: 0,
                    windowWidth: document.documentElement.offsetWidth,
                    windowHeight: document.documentElement.offsetHeight
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Generate PDF
            html2pdf().set(opt).from(element).save().then(() => {
                if (downloadBar) downloadBar.style.display = 'block';
                if (typeof showPopup === 'function') {
                    showPopup("Bill Downloaded Successfully!", '<i class="fas fa-file-pdf"></i>');
                }
            }).catch(err => {
                console.error("PDF download error:", err);
                if (downloadBar) downloadBar.style.display = 'block';
                alert("Could not generate PDF. Please try again.");
            });
        }
    </script>

    <!-- REUSE APP LOGIC FOR MENU -->

    <!-- AUTH MODAL (SYNCED WITH HOME.PHP) -->
    <div id="authModal" class="modal-overlay" style="display: none;">
      <div class="modal-card">
        <button class="modal-close" onclick="document.getElementById('authModal').style.display='none'">&times;</button>
        <div id="authDetailsSection">
          <h3>Customer Details</h3>
          <p>Please provide your details to proceed with the order</p>
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" id="custNameInput" placeholder="Enter Your Name">
          </div>
          <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" id="custEmailInput" placeholder="Enter Your Email">
          </div>
          <p style="font-size: 11px; color: #64748b; margin-top: 10px;">
            <i class="fas fa-info-circle"></i> We'll send an OTP to verify your email.
          </p>
          <div id="authError" style="color: #dc2626; font-size: 14px; margin: 10px 0; display: none;"></div>
          <button class="modal-btn" onclick="sendEmailOTP()">
            <span id="authBtnText">Send OTP</span>
            <span id="authBtnLoader" style="display: none;">Sending...</span>
          </button>
        </div>

        <div id="otpSection" style="display: none;">
          <h3>Verify Email</h3>
          <p id="otpMessage">Enter the 6-digit code sent to your email</p>
          <div class="otp-inputs">
            <input type="number" class="otp-digit" maxlength="1" oninput="moveFocus(this, 0)">
            <input type="number" class="otp-digit" maxlength="1" oninput="moveFocus(this, 1)">
            <input type="number" class="otp-digit" maxlength="1" oninput="moveFocus(this, 2)">
            <input type="number" class="otp-digit" maxlength="1" oninput="moveFocus(this, 3)">
            <input type="number" class="otp-digit" maxlength="1" oninput="moveFocus(this, 4)">
            <input type="number" class="otp-digit" maxlength="1" oninput="moveFocus(this, 5)">
          </div>
          <div id="otpError" style="color: #dc2626; font-size: 14px; margin: 10px 0; display: none;"></div>
          <button class="modal-btn" id="verifyOtpBtn" onclick="verifyEmailOTP()">
            <span id="verifyOtpText">Verify & Proceed</span>
            <span id="verifyOtpLoader" style="display: none;">Verifying...</span>
          </button>
          <button class="modal-btn" id="resendOtpBtn" onclick="sendEmailOTP()"
            style="background: #6b7280; margin-top: 10px; display: none;">
            <span id="resendText">Resend Code</span>
            <span id="resendCountdown"></span>
          </button>
        </div>
      </div>
    </div>

    <!-- CUSTOM POPUP (SYNCED WITH HOME.PHP) -->
    <div id="customPopup">
      <div class="icon"><i class="fas fa-check"></i></div>
      <div class="msg">Message here</div>
    </div>

    <script src="./auth.js?v=<?php echo time(); ?>"></script>
    <script>
      window.isVerified = <?php echo isset($_SESSION['verified']) && $_SESSION['verified'] === true ? 'true' : 'false'; ?>;
    </script>
    <script src="./otp-api.js?v=<?php echo time(); ?>"></script>
    <script src="./app.js?v=<?php echo time(); ?>-FINAL-V4"></script>

</body>

</html>