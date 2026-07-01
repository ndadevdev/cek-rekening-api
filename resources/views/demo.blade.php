<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Rekening Bank/E-Wallet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Work+Sans:wght@400;500;600;700&family=Space+Mono&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Work Sans', -apple-system, 'Segoe UI', Helvetica, sans-serif;
            background: #FAFAFA;
            color: #0A0A0A;
            min-height: 100vh;
        }
        .display { font-family: 'Archivo Black', Impact, 'Arial Black', sans-serif; font-weight: 400; }
        .mono { font-family: 'Space Mono', 'Courier New', Consolas, monospace; font-size: 13px; }

        .header {
            background: #0A0A0A; color: #FAFAFA; padding: 0 32px;
            display: flex; justify-content: space-between; align-items: center; height: 56px;
        }
        .header-logo { font-size: 18px; letter-spacing: -0.02em; }
        .header-right { display: flex; align-items: center; gap: 16px; }
        .header-token { font-size: 11px; color: #A3A3A3; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .header div:last-child { display: flex; align-items: center; gap: 12px; }

        .container { max-width: 880px; margin: 0 auto; padding: 48px 24px; }

        .hero { margin-bottom: 40px; }
        .hero .overline { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: #A3A3A3; margin-bottom: 4px; }
        .hero h1 { font-size: 38px; letter-spacing: -0.02em; line-height: 1.1; margin-bottom: 8px; }
        .hero p { font-size: 16px; color: #525252; line-height: 1.6; max-width: 560px; }
        .hero-stats { display: flex; gap: 32px; margin-top: 16px; }
        .hero-stats span { font-size: 12px; color: #A3A3A3; }
        .hero-stats strong { color: #0A0A0A; font-weight: 600; }

        .card { border: 2px solid #E5E5E5; padding: 24px; margin-bottom: 24px; }
        .card-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #A3A3A3; margin-bottom: 16px; }
        .card-accent { border-top: 4px solid #EF4444; }

        select optgroup { font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; color: #A3A3A3; padding: 4px 0; }
        select optgroup option { padding-left: 12px; font-weight: 400; text-transform: none; letter-spacing: 0; font-size: 14px; color: #0A0A0A; }

        .form-row { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
        .form-row .field { flex: 1; min-width: 180px; margin-bottom: 0; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #0A0A0A; margin-bottom: 6px; }
        .field input, .field select {
            height: 44px; border: 2px solid #D4D4D4; background: #FAFAFA; padding: 8px 14px;
            font-family: 'Work Sans', sans-serif; font-size: 14px; color: #0A0A0A; width: 100%;
            outline: none;
        }
        .field input:focus, .field select:focus { border-color: #0A0A0A; }
        .field input::placeholder { color: #A3A3A3; }
        .field select {
            -webkit-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='%230A0A0A'%3E%3Cpath d='M6 8L0 0h12z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 14px center; padding-right: 38px; cursor: pointer;
        }
        .field select option { background: #FAFAFA; color: #0A0A0A; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            height: 44px; padding: 0 24px; border: 2px solid #0A0A0A;
            font-family: 'Work Sans', sans-serif; font-size: 13px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            cursor: pointer; transition: background .15s, color .15s;
        }
        .btn-primary { background: #0A0A0A; color: #FAFAFA; }
        .btn-primary:hover { background: #EF4444; border-color: #EF4444; }
        .btn-ghost { background: transparent; color: #525252; border-color: #D4D4D4; }
        .btn-ghost:hover { background: #0A0A0A; color: #FAFAFA; border-color: #0A0A0A; }
        .btn-sm { height: 34px; padding: 0 16px; font-size: 11px; }



        .error-msg { font-size: 12px; color: #EF4444; margin-top: 8px; }
        .error-msg svg { vertical-align: -2px; margin-right: 4px; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(10,10,10,0.5); display: flex; align-items: center; justify-content: center; z-index: 60; visibility: hidden; opacity: 0; transition: opacity .2s ease, visibility .2s ease; }
        .modal-overlay.show { visibility: visible; opacity: 1; }
        .modal-box { background: #FAFAFA; border: 2px solid #0A0A0A; max-width: 420px; width: 90%; }
        .modal-head { padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #E5E5E5; }
        .modal-head h3 { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
        .modal-close { width: 28px; height: 28px; border: 2px solid #D4D4D4; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .15s; }
        .modal-close:hover { border-color: #0A0A0A; }
        .modal-body { padding: 24px 20px; }
        .modal-name { font-family: 'Archivo Black', Impact, 'Arial Black', sans-serif; font-size: 24px; letter-spacing: -0.02em; line-height: 1.2; margin-bottom: 16px; }
        .modal-details { }
        .modal-details div { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #E5E5E5; font-size: 14px; }
        .modal-details div:last-child { border-bottom: none; }
        .modal-details .label { color: #A3A3A3; }
        .modal-details .value { font-weight: 600; text-align: right; }
        .modal-valid .modal-name { color: #16A34A; }
        .modal-valid .modal-head h3 { color: #16A34A; }
        .modal-invalid .modal-name { color: #EF4444; }
        .modal-invalid .modal-head h3 { color: #EF4444; }
        .modal-invalid .modal-details div { justify-content: center; color: #525252; border: none; font-size: 14px; }

        .login-box { max-width: 380px; margin: 40px auto 0; padding: 32px; text-align: center; }
        .login-box h2 { font-size: 24px; margin-bottom: 4px; }
        .login-box p { font-size: 14px; color: #525252; margin-bottom: 24px; }
        .login-box .field { text-align: left; }

        .loading { position: fixed; inset: 0; background: rgba(10,10,10,0.3); display: none; align-items: center; justify-content: center; z-index: 50; }
        .loading.show { display: flex; }
        .loading-box { background: #FAFAFA; border: 2px solid #0A0A0A; padding: 24px 32px; display: flex; align-items: center; gap: 12px; }
        .spinner { width: 16px; height: 16px; border: 2px solid #E5E5E5; border-top-color: #0A0A0A; border-radius: 50%; animation: spin .6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-text { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }

        .hidden { display: none !important; }
        .mt-4 { margin-top: 16px; }
        .w-full { width: 100%; }

        .history-item {
            display:flex;align-items:center;gap:12px;padding:12px 0;
            border-bottom:1px solid #E5E5E5;font-size:13px;
        }
        .history-item:last-child { border-bottom:none; }
        .history-check { width:16px;height:16px;border:2px solid #D4D4D4;cursor:pointer;flex-shrink:0;background:#FAFAFA; }
        .history-check.checked { background:#0A0A0A;border-color:#0A0A0A;position:relative; }
        .history-check.checked::after { content:'';position:absolute;top:2px;left:4px;width:4px;height:8px;border:solid #FAFAFA;border-width:0 2px 2px 0;transform:rotate(45deg); }
        .history-info { flex:1;min-width:0; }
        .history-info .h-name { font-weight:600; }
        .history-info .h-meta { font-size:11px;color:#A3A3A3; }
        .history-info .h-meta span { margin-right:12px; }
        .history-status { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:3px 8px;border:2px solid;flex-shrink:0; }
        .history-status.valid { border-color:#16A34A;color:#16A34A; }
        .history-status.invalid { border-color:#EF4444;color:#EF4444; }
        .history-actions { display:flex;gap:6px;flex-shrink:0;position:relative; }
        .history-actions button { min-width:28px;height:28px;border:2px solid #D4D4D4;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;padding:0 8px;transition:all .15s; }
        .history-actions button:hover { border-color:#0A0A0A; }
        .dl-dropdown { position:absolute;top:100%;right:0;margin-top:4px;background:#FAFAFA;border:2px solid #0A0A0A;z-index:20;min-width:100px;display:none; }
        .dl-dropdown.show { display:block; }
        .dl-dropdown button { display:block;width:100%;height:auto;padding:8px 14px;border:none;border-bottom:1px solid #E5E5E5;font-size:12px;justify-content:flex-start;gap:8px; }
        .dl-dropdown button:last-child { border-bottom:none; }
        .dl-dropdown button:hover { background:#F5F5F5;border-color:transparent; }

        @media (max-width: 640px) {
            .header { padding: 0 16px; }
            .header-token { max-width: 100px; }
            .container { padding: 24px 16px; }
            .hero h1 { font-size: 26px; }
            .hero-stats { gap: 16px; flex-wrap: wrap; }
            .hero-stats span { width: 100%; }
            .history-item { flex-wrap:wrap; gap:8px; }
            .history-info { width:100%;order:-1; }
            .form-row .field { min-width: 100%; }
            .card { padding: 16px; }
            .modal-box { width: 95%; }
            .modal-name { font-size: 20px; }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-logo display">CEKREKENING</div>
        <div class="header-right">
            <span class="header-token mono" id="navToken" style="font-size:10px;color:#A3A3A3">demo mode</span>
        </div>
    </div>

    <div class="container">

        <div id="appSection">

            <div class="hero">
                <div class="overline">API Demo</div>
                <h1 class="display">Cek Rekening<br>Bank &amp; E-Wallet</h1>
                <p>Verifikasi nama pemilik rekening bank dan e-wallet Indonesia. Cukup masukkan kode bank dan nomor rekening.</p>
                <div class="hero-stats">
                    <span>Bank: <strong>12</strong></span>
                    <span>E-Wallet: <strong>5</strong></span>
                    <span>Rate limit: <strong>3.000/hari</strong></span>
                </div>
            </div>

            <div class="card card-accent" id="section-cek">
                <div class="card-title">Cek Rekening</div>
                <div class="form-row">
                    <div class="field">
                        <label>Bank / E-Wallet</label>
                        <select id="bankCode"><option value="">— Pilih —</option></select>
                    </div>
                    <div class="field">
                        <label>Nomor Rekening</label>
                        <input id="accountNumber" type="text" placeholder="contoh: 0888123456">
                    </div>
                    <button onclick="cekRekening()" class="btn btn-primary" style="margin-bottom:0">Cari</button>
                </div>
                <div id="cekError" class="error-msg hidden"></div>
            </div>

            <div class="card" id="historySection" style="display:none">
                <div class="card-title" style="display:flex;align-items:center;justify-content:space-between">
                    <span>Riwayat Pencarian</span>
                    <div style="display:flex;gap:8px">
                        <button onclick="deleteSelected()" class="btn btn-sm btn-ghost" id="btnDeleteSelected" style="display:none">Hapus</button>
                    </div>
                </div>
                <div id="historyList"></div>
            </div>

            <div style="text-align:center;margin-top:32px;font-size:12px;color:#A3A3A3;text-transform:uppercase;letter-spacing:0.04em">
                CekRekening API &middot; Laravel 12 &middot; Sanctum &middot; SQLite
            </div>

        </div>
    </div>

    <div class="modal-overlay" id="confirmModal" onclick="if(event.target===this)closeConfirm()">
        <div class="modal-box" style="max-width:340px">
            <div class="modal-head">
                <h3 id="confirmTitle">Konfirmasi</h3>
                <button onclick="closeConfirm()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body" style="text-align:center">
                <p id="confirmMsg" style="font-size:14px;color:#525252;margin-bottom:20px"></p>
                <div style="display:flex;gap:8px;justify-content:center">
                    <button onclick="closeConfirm()" class="btn btn-sm btn-ghost">Batal</button>
                    <button id="confirmBtn" class="btn btn-sm btn-primary" style="background:#EF4444;border-color:#EF4444">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="resultModal" onclick="if(event.target===this)closeModal()">
        <div class="modal-box" id="modalBox">
            <div class="modal-head">
                <h3 id="modalTitle">Hasil</h3>
                <button onclick="closeModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

    <div class="loading" id="loading">
        <div class="loading-box">
            <div class="spinner"></div>
            <span class="loading-text">Memproses</span>
        </div>
    </div>

    <script>
        const BASE = '/api';
        let token = '{!! session('api_token') !!}';

        try {
            loadBanks();
            renderHistory();
        } catch (e) { console.error(e); }

        function showLoad() { document.getElementById('loading').classList.add('show'); }
        function hideLoad() { document.getElementById('loading').classList.remove('show'); }

        function selectBank(code) {
            document.getElementById('bankCode').value = code;
            document.getElementById('accountNumber').focus();
        }

        let confirmCallback = null;

        function showConfirm(msg, btnText, cb) {
            document.getElementById('confirmMsg').textContent = msg;
            document.getElementById('confirmBtn').textContent = btnText || 'Hapus';
            confirmCallback = cb;
            document.getElementById('confirmModal').classList.add('show');
        }

        function closeConfirm() {
            document.getElementById('confirmModal').classList.remove('show');
            confirmCallback = null;
        }

        const confirmBtn = document.getElementById('confirmBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (confirmCallback) confirmCallback();
                closeConfirm();
            });
        }

        setTimeout(function() { hideLoad(); }, 8000);

        async function loadBanks() {
            showLoad();
            try {
                const res = await fetch(BASE + '/banks');
                const data = await res.json();
                const sel = document.getElementById('bankCode');
                sel.innerHTML = '<option value="">— Pilih —</option>';
                const banks = data.data.filter(b => b.type === 'bank');
                const ewallets = data.data.filter(b => b.type === 'ewallet');
                if (banks.length) {
                    let g = document.createElement('optgroup');
                    g.label = 'Bank';
                    banks.forEach(b => { g.innerHTML += `<option value="${b.bank_code}">${b.bank_name}</option>`; });
                    sel.appendChild(g);
                }
                if (ewallets.length) {
                    let g = document.createElement('optgroup');
                    g.label = 'E-Wallet';
                    ewallets.forEach(b => { g.innerHTML += `<option value="${b.bank_code}">${b.bank_name}</option>`; });
                    sel.appendChild(g);
                }
            } catch (e) {
                document.getElementById('cekError').textContent = 'Gagal memuat data bank';
                document.getElementById('cekError').classList.remove('hidden');
            } finally { hideLoad(); }
        }

        function closeModal() {
            document.getElementById('resultModal').classList.remove('show');
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        let lastResult = null;

        function showModal(html, valid) {
            const box = document.getElementById('modalBox');
            box.className = 'modal-box ' + (valid ? 'modal-valid' : 'modal-invalid');
            document.getElementById('modalTitle').textContent = valid ? 'Rekening Ditemukan' : 'Rekening Tidak Ditemukan';
            document.getElementById('modalBody').innerHTML = html + `<div style="margin-top:16px;padding-top:16px;border-top:1px solid #E5E5E5;text-align:right">
                <button onclick="downloadLastCSV()" class="btn btn-sm btn-ghost" style="font-size:10px">CSV</button>
                <button onclick="downloadLastPNG()" class="btn btn-sm btn-ghost" style="font-size:10px">PNG</button>
                <button onclick="downloadLastPDF()" class="btn btn-sm btn-ghost" style="font-size:10px">PDF</button>
            </div>`;
            document.getElementById('resultModal').classList.add('show');
        }

        function downloadLastCSV() {
            if (!lastResult) return;
            downloadCSV([lastResult], `cek-rekening-${(lastResult.reference_id||'').slice(0,8)||'result'}.csv`);
        }

        function downloadLastPNG() {
            if (!lastResult) return;
            const el = document.createElement('div');
            el.style.cssText = 'padding:32px;background:#FAFAFA;font-family:Work Sans,sans-serif;width:480px';
            el.innerHTML = buildReceiptHTML(lastResult);
            document.body.appendChild(el);
            html2canvas(el, { scale: 2, backgroundColor: '#FAFAFA' }).then(canvas => {
                const a = document.createElement('a');
                a.href = canvas.toDataURL('image/png');
                a.download = `cek-rekening-${(lastResult.reference_id||'').slice(0,8)||'result'}.png`;
                a.click();
                document.body.removeChild(el);
            }).catch(() => document.body.removeChild(el));
        }

        function downloadLastPDF() {
            if (!lastResult) return;
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ unit: 'mm', format: 'a4' });
            pdfReceipt(doc, lastResult);
            doc.save(`cek-rekening-${(lastResult.reference_id||'').slice(0,8)||'result'}.pdf`);
        }

        function getHistory() {
            try { return JSON.parse(localStorage.getItem('history') || '[]'); } catch { return []; }
        }

        function saveHistory(h) {
            localStorage.setItem('history', JSON.stringify(h));
        }

        function renderHistory() {
            try {
                const h = getHistory();
                const el = document.getElementById('historySection');
                const list = document.getElementById('historyList');
                if (!h.length || !el || !list) { if (el) el.style.display = 'none'; return; }
                el.style.display = '';
                list.innerHTML = h.map((item, i) => {
                    const name = item.account_name || 'Tidak Ditemukan';
                    const bank = item.bank_name || '';
                    const acc = item.account_number || '';
                    const ref = item.reference_id ? item.reference_id.slice(0,8)+'...' : '';
                    const cls = item.status === 'valid' ? 'Valid' : 'Invalid';
                    return `<div class="history-item" data-idx="${i}">
                        <div class="history-check" onclick="toggleCheck(${i})"></div>
                        <div class="history-info">
                            <div class="h-name">${name}</div>
                            <div class="h-meta">
                                <span>${bank}</span>
                                <span>${acc}</span>
                                <span class="mono" style="font-size:10px">${ref}</span>
                            </div>
                        </div>
                        <div class="history-status ${item.status}">${cls}</div>
                        <div class="history-actions">
                            <button onclick="toggleDl(${i})" title="Download">Download</button>
                            <div class="dl-dropdown" id="dl-${i}">
                                <button onclick="downloadItemCSV(${i})">CSV</button>
                                <button onclick="downloadItemPNG(${i})">PNG</button>
                                <button onclick="downloadItemPDF(${i})">PDF</button>
                            </div>
                            <button onclick="deleteItem(${i})" title="Hapus">&times;</button>
                        </div>
                    </div>`;
                }).join('');
                updateDeleteBtn();
            } catch (e) { console.error(e); }
        }

        function toggleCheck(idx) {
            const cb = document.querySelector(`.history-item[data-idx="${idx}"] .history-check`);
            cb.classList.toggle('checked');
            updateDeleteBtn();
        }

        function getCheckedIdxs() {
            const els = document.querySelectorAll('.history-item .history-check.checked');
            return Array.from(els).map(el => parseInt(el.closest('.history-item').dataset.idx));
        }

        function updateDeleteBtn() {
            const btn = document.getElementById('btnDeleteSelected');
            btn.style.display = getCheckedIdxs().length > 0 ? '' : 'none';
        }

        function saveHistoryEntry(data) {
            const h = getHistory();
            h.unshift({
                status: data.status,
                bank_code: data.bank_code,
                bank_name: data.bank_name || '',
                type: data.type || '',
                account_number: data.account_number,
                account_name: data.account_name || null,
                message: data.message || null,
                reference_id: data.reference_id,
                validator: data.validator,
                timestamp: new Date().toISOString()
            });
            saveHistory(h);
            renderHistory();
        }

        function downloadJSON(data, filename) {
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            a.click();
            URL.revokeObjectURL(a.href);
        }

        function downloadCSV(data, filename) {
            const headers = ['Status','Bank','Type','No. Rekening','Nama','Referensi','Provider','Waktu'];
            const rows = data.map(d => [
                d.status, d.bank_name, d.type, d.account_number,
                d.account_name || d.message || '-', d.reference_id,
                d.validator, d.timestamp
            ]);
            const csv = [headers.join(','), ...rows.map(r => r.map(v => `"${v||''}"`).join(','))].join('\n');
            const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            a.click();
            URL.revokeObjectURL(a.href);
        }

        function toggleDl(idx) {
            const m = document.getElementById('dl-' + idx);
            document.querySelectorAll('.dl-dropdown.show').forEach(el => { if (el !== m) el.classList.remove('show'); });
            m.classList.toggle('show');
        }
        document.addEventListener('click', e => {
            if (!e.target.closest('.history-actions')) document.querySelectorAll('.dl-dropdown.show').forEach(el => el.classList.remove('show'));
        });

        function makeTableRows(items) {
            return items.map(d => {
                let ts = '-';
                if (d.timestamp) {
                    try { ts = new Date(d.timestamp).toLocaleString('id-ID'); } catch(e) { ts = d.timestamp; }
                }
                return [
                    d.status === 'valid' ? 'VALID' : 'TIDAK VALID',
                    d.bank_name || '-',
                    d.type === 'ewallet' ? 'E-Wallet' : 'Bank',
                    d.account_number || '-',
                    d.account_name || d.message || '-',
                    d.reference_id || '-',
                    d.validator || '-',
                    ts
                ];
            });
        }

        function downloadCSV(data, filename) {
            const headers = ['Status','Bank','Jenis','No. Rekening','Nama Pemilik','Referensi','Provider','Waktu'];
            const rows = makeTableRows(data);
            const tsv = [headers.join('\t'), ...rows.map(r => r.join('\t'))].join('\n');
            const blob = new Blob(['\uFEFF' + tsv], { type: 'text/csv;charset=utf-8;' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            a.click();
            URL.revokeObjectURL(a.href);
        }

        function downloadItemCSV(idx) {
            const item = getHistory()[idx];
            if (!item) return;
            downloadCSV([item], `cek-rekening-${(item.reference_id||'').slice(0,8)||idx}.csv`);
            document.querySelectorAll('.dl-dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function downloadItemPNG(idx) {
            const item = getHistory()[idx];
            if (!item) return;
            const el = document.createElement('div');
            el.style.cssText = 'padding:32px;background:#FAFAFA;font-family:Work Sans,sans-serif;width:480px';
            el.innerHTML = buildReceiptHTML(item);
            document.body.appendChild(el);
            html2canvas(el, { scale: 2, backgroundColor: '#FAFAFA' }).then(canvas => {
                const a = document.createElement('a');
                a.href = canvas.toDataURL('image/png');
                a.download = `cek-rekening-${(item.reference_id||'').slice(0,8)||idx}.png`;
                a.click();
                document.body.removeChild(el);
            }).catch(() => document.body.removeChild(el));
            document.querySelectorAll('.dl-dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function downloadItemPDF(idx) {
            const item = getHistory()[idx];
            if (!item) return;
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ unit: 'mm', format: 'a4' });
            pdfReceipt(doc, item);
            doc.save(`cek-rekening-${(item.reference_id||'').slice(0,8)||idx}.pdf`);
            document.querySelectorAll('.dl-dropdown.show').forEach(el => el.classList.remove('show'));
        }

        function pdfReceipt(doc, d) {
            const valid = d.status === 'valid';
            const pageW = doc.internal.pageSize.getWidth();
            let y = 20;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(10);
            doc.setTextColor(valid ? '#16A34A' : '#EF4444');
            doc.text(valid ? 'VALID' : 'TIDAK VALID', pageW / 2, y, { align: 'center' });
            y += 10;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(18);
            doc.setTextColor('#0A0A0A');
            doc.text(d.account_name || 'Tidak Ditemukan', pageW / 2, y, { align: 'center' });
            y += 14;

            const rows = [
                ['Bank/E-Wallet', d.bank_name || '-'],
                ['Jenis', d.type === 'ewallet' ? 'E-Wallet' : 'Bank'],
                ['No. Rekening', d.account_number || '-'],
            ];
            if (!valid) rows.push(['Pesan', d.message || '-']);
            rows.push(['Referensi', d.reference_id || '-']);
            rows.push(['Provider', d.validator || '-']);
            rows.push(['Waktu', d.timestamp ? new Date(d.timestamp).toLocaleString('id-ID') : '-']);

            doc.setFont('helvetica', 'normal');
            doc.setFontSize(10);
            rows.forEach(([label, val]) => {
                doc.setFont('helvetica', 'bold');
                doc.setTextColor('#525252');
                doc.setFontSize(9);
                doc.text(label, 20, y);
                doc.setFont('helvetica', 'normal');
                doc.setTextColor('#0A0A0A');
                doc.setFontSize(10);
                doc.text(val, 65, y);
                y += 7;
                doc.setDrawColor('#E5E5E5');
                doc.line(20, y, pageW - 20, y);
                y += 3;
            });

            y += 8;
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(8);
            doc.setTextColor('#A3A3A3');
            doc.text('Dicetak dari CekRekening API', pageW / 2, y, { align: 'center' });
        }

        function buildReceiptHTML(d) {
            const valid = d.status === 'valid';
            return `
                <div class="status ${valid ? 'valid' : 'invalid'}">${valid ? 'VALID' : 'TIDAK VALID'}</div>
                <h2>${d.account_name || 'Tidak Ditemukan'}</h2>
                <table>
                    <tr><td>Bank/E-Wallet</td><td>${d.bank_name || '-'}</td></tr>
                    <tr><td>Jenis</td><td>${d.type === 'ewallet' ? 'E-Wallet' : 'Bank'}</td></tr>
                    <tr><td>No. Rekening</td><td>${d.account_number || '-'}</td></tr>
                    ${!valid ? `<tr><td>Pesan</td><td>${d.message || '-'}</td></tr>` : ''}
                    <tr><td>Referensi</td><td style="font-family:monospace;font-size:12px">${d.reference_id || '-'}</td></tr>
                    <tr><td>Provider</td><td style="text-transform:capitalize">${d.validator || '-'}</td></tr>
                    <tr><td>Waktu</td><td>${d.timestamp ? new Date(d.timestamp).toLocaleString('id-ID') : '-'}</td></tr>
                </table>
            `;
        }

        function deleteItem(idx) {
            const h = getHistory();
            h.splice(idx, 1);
            saveHistory(h);
            renderHistory();
        }

        function deleteSelected() {
            const idxs = getCheckedIdxs().sort((a,b) => b - a);
            if (!idxs.length) return;
            const count = idxs.length;
            showConfirm(`Hapus ${count} item dari riwayat?`, `Hapus ${count}`, () => {
                const h = getHistory();
                idxs.forEach(i => h.splice(i, 1));
                saveHistory(h);
                renderHistory();
            });
        }

        async function cekRekening() {
            const bankCode = document.getElementById('bankCode').value;
            const accountNumber = document.getElementById('accountNumber').value;
            const ee = document.getElementById('cekError');
            ee.classList.add('hidden');
            if (!bankCode) { ee.textContent = 'Pilih bank terlebih dahulu'; ee.classList.remove('hidden'); return; }
            if (!accountNumber) { ee.textContent = 'Masukkan nomor rekening'; ee.classList.remove('hidden'); return; }
            showLoad();
            try {
                const res = await fetch(BASE + '/cek-rekening', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                    body: JSON.stringify({ bank_code: bankCode, account_number: accountNumber })
                });
                const data = await res.json();

                saveHistoryEntry(data.data);
                lastResult = data.data;

                if (data.success) {
                    const typeLabel = data.data.type === 'ewallet' ? 'E-Wallet' : 'Bank';
                    showModal(`
                        <div style="margin-bottom:12px"><span style="display:inline-block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:3px 10px;border:2px solid #16A34A;color:#16A34A">VALID</span></div>
                        <div class="modal-name">${data.data.account_name}</div>
                        <div class="modal-details">
                            <div><span class="label">${typeLabel}</span><span class="value">${data.data.bank_name}</span></div>
                            <div><span class="label">No. Rekening</span><span class="value mono">${data.data.account_number}</span></div>
                            <div><span class="label">Referensi</span><span class="value mono" style="font-size:11px;color:#A3A3A3">${data.data.reference_id}</span></div>
                            <div><span class="label">Provider</span><span class="value" style="text-transform:capitalize">${data.data.validator}</span></div>
                        </div>
                    `, true);
                } else {
                    showModal(`
                        <div style="margin-bottom:12px"><span style="display:inline-block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:3px 10px;border:2px solid #EF4444;color:#EF4444">TIDAK VALID</span></div>
                        <div class="modal-name">Tidak Ditemukan</div>
                        <div class="modal-details">
                            <div><span style="color:#525252">${data.message || 'Nomor rekening tidak terdaftar.'}</span></div>
                            <div><span class="label">Referensi</span><span class="value mono" style="font-size:11px;color:#A3A3A3">${data.data.reference_id}</span></div>
                            <div><span class="label">Provider</span><span class="value" style="text-transform:capitalize">${data.data.validator}</span></div>
                        </div>
                    `, false);
                }
            } catch (e) {
                ee.textContent = 'Terjadi kesalahan: ' + e.message;
                ee.classList.remove('hidden');
            } finally { hideLoad(); }
        }
    </script>
</body>
</html>
