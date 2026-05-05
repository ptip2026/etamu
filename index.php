<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$user = currentUser();
$canMonthly = canMonthly();
$isAdmin = isAdmin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<title>e-Tamu v4.0 | PN Jakarta Selatan Kelas 1A Khusus</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/docx@8.5.0/build/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Lato', sans-serif; background: #0A1628; color: #F5F0E8; min-height: 100vh; overflow-x: hidden; }
  body::before { content: ''; position: fixed; inset: 0; background: radial-gradient(ellipse at 20% 20%, rgba(201,168,76,0.06) 0%, transparent 50%), repeating-linear-gradient(0deg, transparent, transparent 60px, rgba(201,168,76,0.02) 60px); z-index: -1; }
  :root { --gold: #C9A84C; --gold-light: #E8C97A; --gold-dark: #8B6914; --navy: #0A1628; --navy-mid: #112240; --white: #F5F0E8; --gray: #8899AA; --border: rgba(201,168,76,0.25); --glass: rgba(17,34,64,0.85); }
  header { background: linear-gradient(135deg, #0A1628, #112240); border-bottom: 2px solid var(--gold); position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 30px rgba(0,0,0,0.5); }
  .header-inner { display: flex; align-items: center; gap: 20px; padding: 12px 28px; max-width: 1600px; margin: 0 auto; }
  .kop-logo-cell { flex-shrink: 0; }
  .kop-logo-cell img { max-width: 80px; height: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3)); background: white; border-radius: 50%; padding: 4px; }
  .header-text { flex: 1; }
  .header-text h1 { font-family: 'Cinzel', serif; font-size: 18px; font-weight: 700; color: var(--gold-light); letter-spacing: 1px; }
  .header-text p { font-size: 11px; color: var(--gray); margin-top: 4px; }
  .clock-display { font-family: 'Cinzel', serif; font-size: 22px; color: var(--gold); }
  .date-display { font-size: 11px; color: var(--gray); }
  .visitor-count-badge { background: var(--gold); color: var(--navy); padding: 2px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
  .user-badge { background: rgba(17,34,64,0.9); border: 1px solid var(--border); border-radius: 20px; padding: 4px 14px; font-size: 12px; color: var(--gold-light); display: flex; align-items: center; gap: 6px; }
  .tab-bar { background: var(--navy-mid); border-bottom: 1px solid var(--border); display: flex; gap: 0; max-width: 1600px; margin: 0 auto; padding: 0 28px; overflow-x: auto; }
  .tab-btn { padding: 14px 22px; background: none; border: none; color: var(--gray); font-weight: 700; font-size: 13px; cursor: pointer; border-bottom: 3px solid transparent; transition: 0.2s; white-space: nowrap; }
  .tab-btn.active, .tab-btn:hover { color: var(--gold); border-bottom-color: var(--gold); }
  .main-content { max-width: 1600px; margin: 0 auto; padding: 28px; }
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }
  .card { background: var(--glass); border: 1px solid var(--border); border-radius: 12px; padding: 24px; backdrop-filter: blur(10px); margin-bottom: 20px; }
  .card-title { font-family: 'Cinzel', serif; font-size: 14px; color: var(--gold); border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
  .reg-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }
  .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
  label { font-size: 11px; font-weight: 700; color: var(--gold); text-transform: uppercase; letter-spacing: 1px; }
  input, select, textarea { background: rgba(10,22,40,0.8); border: 1px solid var(--border); border-radius: 8px; padding: 11px 14px; color: var(--white); font-size: 14px; width: 100%; font-family: 'Lato', sans-serif; }
  input:focus, select:focus, textarea:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(201,168,76,0.15); }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .nik-wrapper { position: relative; }
  .nik-input { padding-right: 50px; font-family: monospace; letter-spacing: 1px; }
  .nik-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 22px; color: var(--gold); pointer-events: none; }
  .nik-status { font-size: 11px; height: 18px; margin-top: 4px; }
  .nik-valid { color: #27AE60; } .nik-invalid { color: #E74C3C; }
  .visitor-scan-section { background: linear-gradient(135deg, rgba(39,174,96,0.08), rgba(26,52,96,0.4)); border: 2px dashed rgba(39,174,96,0.4); border-radius: 12px; padding: 20px; margin-bottom: 16px; }
  .visitor-scan-section .card-title { color: #5EB88A; border-color: rgba(94,184,138,0.3); }
  .visitor-number-input-row { display: flex; gap: 10px; align-items: center; }
  .visitor-number-input-row input { flex: 1; font-family: monospace; font-size: 18px; letter-spacing: 3px; font-weight: 700; }
  .scan-indicator { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; color: #5EB88A; margin-top: 6px; }
  .scan-indicator .dot { width: 8px; height: 8px; border-radius: 50%; background: #5EB88A; animation: blink 1s infinite; }
  @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }
  .visitor-ocr-drop { border: 1px solid rgba(94,184,138,0.3); border-radius: 10px; background: rgba(10,22,40,0.6); display: flex; flex-direction: column; align-items: center; padding: 14px; cursor: pointer; transition: 0.2s; margin-top: 12px; }
  .visitor-ocr-drop:hover { border-color: #5EB88A; background: rgba(39,174,96,0.08); }
  .ocr-section { background: linear-gradient(135deg, rgba(201,168,76,0.08), rgba(26,52,96,0.4)); border: 2px dashed rgba(201,168,76,0.4); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
  .ocr-drop-area { border: 1px solid var(--border); border-radius: 10px; background: rgba(10,22,40,0.6); display: flex; flex-direction: column; align-items: center; padding: 20px; cursor: pointer; transition: 0.2s; }
  .ocr-drop-area:hover { border-color: var(--gold); background: rgba(201,168,76,0.06); }
  .ocr-preview-img { max-width: 100%; max-height: 140px; border-radius: 8px; border: 1px solid var(--gold); margin-top: 8px; }
  .ocr-progress { display: none; margin-top: 12px; }
  .ocr-progress.show { display: block; }
  .ocr-progress-bar { height: 6px; background: linear-gradient(90deg, var(--gold-dark), var(--gold-light)); width: 0%; border-radius: 10px; transition: width 0.2s; }
  .ocr-result-box { display: none; background: rgba(26,122,74,0.1); border: 1px solid rgba(94,184,138,0.3); border-radius: 8px; padding: 12px; margin-top: 12px; }
  .ocr-result-box.show { display: block; }
  .btn { border: none; border-radius: 8px; padding: 10px 18px; font-weight: 700; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; }
  .btn-primary { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--navy); }
  .btn-primary:hover { opacity: 0.85; }
  .btn-secondary { background: transparent; border: 1px solid var(--gold); color: var(--gold); }
  .btn-secondary:hover { background: rgba(201,168,76,0.1); }
  .btn-success { background: #1A7A4A; color: white; }
  .btn-danger { background: #C0392B; color: white; }
  .btn-info { background: #1A5276; color: white; border: 1px solid #2E86C1; }
  .btn-word { background: #1A5276; color: white; border: 1px solid #2E86C1; }
  .btn-sm { padding: 6px 12px; font-size: 11px; }
  .submit-section { display: flex; gap: 12px; align-items: center; margin-top: 8px; }
  .webcam-panel .cam-box { width: 100%; aspect-ratio: 4/3; background: #000; border-radius: 10px; overflow: hidden; position: relative; border: 2px solid var(--border); margin-bottom: 12px; }
  #webcam, #capturedPhoto { width: 100%; height: 100%; object-fit: cover; }
  .badge-in { background: rgba(26,122,74,0.2); color: #5EB88A; border: 1px solid rgba(94,184,138,0.3); padding: 3px 10px; border-radius: 20px; font-size: 10px; }
  .badge-out { background: rgba(136,153,170,0.15); color: var(--gray); border: 1px solid var(--border); padding: 3px 10px; border-radius: 20px; font-size: 10px; }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid rgba(201,168,76,0.1); }
  th { color: var(--gold); font-size: 11px; text-transform: uppercase; background: rgba(10,22,40,0.5); }
  .table-wrapper { overflow-x: auto; }
  .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 24px; }
  .stat-card { background: var(--glass); border: 1px solid var(--border); border-radius: 10px; padding: 18px; text-align: center; }
  .stat-value { font-family: 'Cinzel', serif; font-size: 28px; color: var(--gold); }
  .stat-label { font-size: 12px; color: var(--gray); margin-top: 4px; }
  .autosave-banner { display: none; background: rgba(52,152,219,0.15); border: 1px solid #3498DB; border-radius: 10px; padding: 14px; margin-bottom: 18px; align-items: center; gap: 12px; }
  .autosave-banner.show { display: flex; }
  .toast { position: fixed; bottom: 30px; right: 30px; background: var(--navy-mid); border-left: 4px solid var(--gold); padding: 12px 20px; border-radius: 10px; z-index: 9999; transform: translateX(200%); transition: 0.3s; display: flex; align-items: center; gap: 8px; max-width: 320px; }
  .toast.show { transform: translateX(0); }
  .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 1000; }
  .modal-overlay.flex { display: flex; }
  .modal { background: var(--navy-mid); border: 1px solid var(--gold); border-radius: 16px; padding: 24px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
  .pagination { display: flex; gap: 8px; margin-top: 12px; justify-content: center; flex-wrap: wrap; }
  .page-btn { background: transparent; border: 1px solid var(--border); color: var(--gray); padding: 6px 12px; border-radius: 6px; cursor: pointer; }
  .page-btn.active { background: var(--gold); color: var(--navy); border-color: var(--gold); }
  .report-filter-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 20px; }
  .report-filter-bar .form-group { margin-bottom: 0; }
  .report-action-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
  .report-summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 20px; }
  .report-summary-card { background: rgba(10,22,40,0.6); border: 1px solid var(--border); border-radius: 10px; padding: 14px; text-align: center; }
  .report-summary-value { font-family: 'Cinzel', serif; font-size: 24px; color: var(--gold); }
  .report-summary-label { font-size: 11px; color: var(--gray); }
  .daily-report-header { font-family: 'Cinzel', serif; font-size: 16px; color: var(--gold-light); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border); }
  .date-nav { display: flex; gap: 8px; align-items: center; }
  .date-nav input[type=date] { max-width: 180px; }
  .locked-section { position:relative; }
  .locked-overlay { position:absolute; inset:0; background:rgba(10,22,40,0.85); border-radius:12px; display:flex; flex-direction:column; align-items:center; justify-content:center; z-index:10; }
  .locked-overlay span { font-size:48px; margin-bottom:12px; }
  .locked-overlay p { color:var(--gray); font-size:14px; }
  @media print {
    body { background: white !important; color: black !important; }
    body::before { display: none; }
    header, .tab-bar, .btn, .pagination, .report-action-bar, .report-filter-bar { display: none !important; }
    .main-content { padding: 0; max-width: 100%; }
    .tab-panel { display: none !important; }
    #print-area { display: block !important; }
    .card { background: white !important; border: 1px solid #ccc !important; }
    table { font-size: 11px; }
    th { background: #f0f0f0 !important; color: black !important; }
    td { color: black !important; }
    .print-header { text-align: center; margin-bottom: 20px; }
    .print-header h2 { font-size: 16px; font-weight: bold; }
    .badge-in, .badge-out { border: 1px solid #ccc; color: black !important; }
    img { max-width: 35px !important; }
  }
  #print-area { display: none; }
  .filter-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; align-items: flex-end; }
  .filter-row input, .filter-row select { flex: 1; min-width: 130px; }
  .db-badge { display:inline-flex; align-items:center; gap:6px; font-size:11px; color:#5EB88A; background:rgba(26,122,74,0.1); border:1px solid rgba(94,184,138,0.3); border-radius:8px; padding:6px 12px; }
  @media (max-width: 900px) { .reg-grid { grid-template-columns: 1fr; } .stats-grid { grid-template-columns: repeat(2,1fr); } .header-inner { padding: 10px 16px; } .main-content { padding: 16px; } }
</style>
</head>
<body>

<header>
  <div class="header-inner">
    <div class="kop-logo-cell">
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJ3aw1Om4FHEN0_g7RLXQ2E0omsMjO-vfBjg&s" alt="Logo Mahkamah Agung RI">
    </div>
    <div class="header-text">
      <h1>PENGADILAN NEGERI JAKARTA SELATAN KELAS 1A KHUSUS</h1>
      <p>SISTEM MANAJEMEN TAMU ELEKTRONIK (e-TAMU) · v4.0 · Database MySQL Permanen · Server-Side</p>
    </div>
    <div class="header-right" style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:4px">
      <div class="clock-display" id="clockDisplay"></div>
      <div class="date-display" id="dateDisplay"></div>
      <div class="visitor-count-badge" id="todayBadge">0 Tamu</div>
      <div class="user-badge">
        👤 <?= htmlspecialchars($user['display_name'] ?: $user['username']) ?>
        <?php if ($isAdmin): ?>
          <a href="admin.php" style="color:var(--gold-light);font-size:10px;text-decoration:none;border:1px solid var(--border);border-radius:10px;padding:2px 8px">⚙️ CP</a>
        <?php endif; ?>
        <a href="logout.php" style="color:#F1948A;font-size:10px;text-decoration:none;border:1px solid rgba(231,76,60,0.3);border-radius:10px;padding:2px 8px">🚪</a>
      </div>
    </div>
  </div>
  <div class="tab-bar">
    <button class="tab-btn active" onclick="switchTab('register',this)">📋 Registrasi</button>
    <button class="tab-btn" onclick="switchTab('list',this)">👥 Daftar Tamu</button>
    <button class="tab-btn" onclick="switchTab('report',this)">📊 Laporan</button>
    <button class="tab-btn" onclick="switchTab('import',this)">📂 Import CSV</button>
    <?php if ($isAdmin): ?>
    <button class="tab-btn" onclick="switchTab('settings',this)">⚙️ Pengaturan</button>
    <?php endif; ?>
  </div>
</header>

<div class="main-content">

  <!-- ==================== REGISTRASI TAB ==================== -->
  <div class="tab-panel active" id="tab-register">
    <div class="autosave-banner" id="autosaveBanner">
      <div>💾</div>
      <div><strong>Data formulir tersimpan sebelumnya</strong><br><small id="autosaveBannerTime"></small></div>
      <button class="btn btn-sm btn-success" onclick="restoreAutosave()">Pulihkan</button>
      <button class="btn btn-sm btn-danger" onclick="discardAutosave()">Hapus</button>
    </div>

    <div class="reg-grid">
      <div class="reg-left">
        <!-- VISITOR NUMBER SCAN -->
        <div class="visitor-scan-section">
          <div class="card-title">🎫 SCAN / INPUT NOMOR VISITOR</div>
          <p style="font-size:12px;color:var(--gray);margin-bottom:12px">Scan barcode kartu visitor menggunakan scanner <strong style="color:#5EB88A">Datalogic QW2100</strong>, atau ketik manual, atau gunakan OCR kamera/foto.</p>
          <div class="visitor-number-input-row">
            <input type="text" id="visitorNumberInput" placeholder="Scan atau ketik No. Visitor..." oninput="onVisitorNumberInput(this.value)" onkeydown="onVisitorNumberKeydown(event)">
            <button class="btn btn-sm" style="background:#1A7A4A;color:white;white-space:nowrap;" onclick="openVisitorOcrCamera()">📷 OCR</button>
            <button class="btn btn-sm btn-secondary" onclick="clearVisitorNumber()">✖</button>
          </div>
          <div class="scan-indicator" id="scanIndicator" style="display:none;"><span class="dot"></span> Scanner aktif</div>
          <div id="visitorNumberStatus" style="font-size:12px;margin-top:6px;color:var(--gray);"></div>
          <div class="visitor-ocr-drop" id="visitorOcrDrop" onclick="document.getElementById('visitorOcrFileInput').click()" ondragover="event.preventDefault()" ondrop="handleVisitorOcrDrop(event)">
            <div style="font-size:11px;color:var(--gray)">📄 Drag & drop / klik untuk upload foto kartu visitor (OCR nomor)</div>
            <img id="visitorOcrPreview" style="display:none;max-height:80px;margin-top:6px;border-radius:6px;border:1px solid #5EB88A;">
          </div>
          <input type="file" id="visitorOcrFileInput" accept="image/*" style="display:none" onchange="handleVisitorOcrFile(event)">
          <div class="ocr-progress" id="visitorOcrProgress"><div class="ocr-progress-bar" id="visitorOcrProgressBar"></div></div>
        </div>

        <!-- OCR KTP -->
        <div class="ocr-section">
          <div class="card-title">🪪 SCAN KTP OTOMATIS (OCR)</div>
          <div class="ocr-drop-area" id="ocrDropArea" onclick="document.getElementById('ktpFileInput').click()" ondragover="event.preventDefault()" ondrop="handleDrop(event)">
            <div>📄 Klik atau drag & drop foto KTP</div>
            <div style="font-size:11px;color:var(--gray)">NIK, Nama, TTL, Alamat akan terbaca otomatis</div>
            <img id="ocrPreviewImg" class="ocr-preview-img" style="display:none;">
          </div>
          <input type="file" id="ktpFileInput" accept="image/*" style="display:none" onchange="handleKtpFile(event)">
          <div class="ocr-progress" id="ocrProgress"><div class="ocr-progress-bar" id="ocrProgressBar"></div><div id="ocrProgressText" style="font-size:11px;margin-top:4px;color:var(--gray)">Memproses OCR...</div></div>
          <div class="ocr-result-box" id="ocrResultBox"><div id="ocrResultContent"></div>
            <div style="margin-top:8px;display:flex;gap:8px">
              <button class="btn btn-sm btn-success" onclick="applyOcrResult()">✅ Terapkan ke Form</button>
              <button class="btn btn-sm btn-secondary" onclick="resetOcr()">🔄 Reset OCR</button>
            </div>
          </div>
          <button class="btn btn-secondary btn-sm" style="margin-top:8px;" onclick="openOcrCamera()">📷 Ambil Foto KTP via Kamera</button>
        </div>

        <!-- FORM DATA TAMU -->
        <div class="card">
          <div class="card-title">📋 DATA TAMU</div>
          <div class="form-group"><label>No. Visitor / Kartu Tamu</label>
            <input type="text" id="visitorNumberDisplay" placeholder="Terisi dari scan di atas" style="font-family:monospace;letter-spacing:2px;background:rgba(39,174,96,0.08);border-color:rgba(94,184,138,0.4);" readonly>
          </div>
          <div class="form-group"><label>NIK * (16 digit)</label>
            <div class="nik-wrapper"><input type="text" id="nikInput" class="nik-input" placeholder="Scan / ketik 16 digit NIK" maxlength="16" oninput="onNikInput(this.value)"><span class="nik-icon">🔍</span></div>
            <div class="nik-status" id="nikStatus"></div>
          </div>
          <div class="form-group"><label>Nama Lengkap *</label><input type="text" id="namaInput" placeholder="Nama sesuai KTP"></div>
          <div class="form-row">
            <div class="form-group"><label>No HP / WA *</label><input type="tel" id="hpInput" placeholder="08xxxxxxxx"></div>
            <div class="form-group"><label>Jenis Kelamin</label><select id="genderSelect"><option value="">Pilih</option><option>Laki-laki</option><option>Perempuan</option></select></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>Tempat Lahir</label><input type="text" id="tempatLahirInput"></div>
            <div class="form-group"><label>Tgl Lahir (DD-MM-YYYY)</label><input type="text" id="tglLahirInput" placeholder="DD-MM-YYYY"></div>
          </div>
          <div class="form-group"><label>Alamat</label><input type="text" id="alamatInput"></div>
          <div class="form-row">
            <div class="form-group"><label>Keperluan / Tujuan *</label>
              <select id="keperluanSelect">
                <option value="">-- Pilih --</option>
                <option>Sidang Perdata</option><option>Sidang Pidana</option><option>Sidang Tipikor</option>
                <option>Pendaftaran Perkara</option><option>Konsultasi Hukum</option><option>Legalisir Dokumen</option>
                <option>Informasi Perkara</option><option>Kunjungan Resmi</option><option>Media/Pers</option><option>Lainnya</option>
              </select>
            </div>
            <div class="form-group"><label>Nomor Perkara</label><input type="text" id="noPerkara" placeholder="Opsional"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>Instansi / Kantor</label><input type="text" id="instansiInput"></div>
            <div class="form-group"><label>Tujuan / Bagian *</label>
              <select id="bagianSelect">
                <option value="">Pilih Bagian</option>
                <option>Ketua Pengadilan</option><option>Panitera</option><option>Sekretariat</option>
                <option>Kepaniteraan Perdata</option><option>Kepaniteraan Pidana</option><option>Ruang Sidang</option><option>Bagian Umum</option>
              </select>
            </div>
          </div>
          <div class="form-group"><label>Catatan</label><textarea id="catatanInput" rows="2"></textarea></div>
          <div class="submit-section">
            <button class="btn btn-primary" style="flex:1" onclick="registerVisitor()">✅ DAFTARKAN TAMU</button>
            <button class="btn btn-secondary" onclick="clearForm()">Reset</button>
          </div>
        </div>
      </div>

      <!-- WEBCAM PANEL -->
      <div class="webcam-panel">
        <div class="card">
          <div class="card-title">📸 FOTO WAJAH TAMU</div>
          <div class="cam-box"><video id="webcam" autoplay playsinline></video><canvas id="photoCanvas" style="display:none;"></canvas><img id="capturedPhoto" style="display:none;width:100%;height:100%;object-fit:cover;"></div>
          <div style="display:flex;gap:8px;margin:12px 0">
            <button class="btn btn-primary" id="captureBtn" onclick="capturePhoto()">📸 Ambil Foto</button>
            <button class="btn btn-secondary" id="retakeBtn" onclick="retakePhoto()" style="display:none;">🔄 Ulang</button>
          </div>
          <div class="card-title" style="margin-top:10px;">📡 Scan Terakhir</div>
          <div id="lastScanInfo" style="font-size:12px;color:var(--gray)">Belum ada scan</div>
        </div>
        <div class="card"><div class="card-title">📊 Statistik Hari Ini</div><div id="quickStats" style="font-size:13px;">0 tamu</div></div>
        <div class="card" style="background:rgba(39,174,96,0.05);border-color:rgba(94,184,138,0.3);">
          <div class="card-title" style="color:#5EB88A;border-color:rgba(94,184,138,0.3);">🗄️ Status Database</div>
          <div id="storageStatus" style="font-size:12px;line-height:1.8;color:var(--gray)">Memuat...</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ==================== DAFTAR TAMU TAB ==================== -->
  <div class="tab-panel" id="tab-list">
    <div class="stats-grid" id="statsGrid"></div>
    <div class="card">
      <div class="filter-row">
        <input type="text" id="searchInput" placeholder="🔍 Cari nama/NIK/No.Visitor..." style="flex:2;min-width:160px" oninput="filterVisitors()">
        <input type="date" id="filterDate" onchange="filterVisitors()" title="Filter per tanggal">
        <select id="filterStatus" onchange="filterVisitors()"><option value="">Semua Status</option><option value="in">Masuk</option><option value="out">Keluar</option></select>
        <button class="btn btn-secondary btn-sm" onclick="clearListFilter()">🔄 Reset Filter</button>
        <button class="btn btn-secondary btn-sm" onclick="exportToday()">📎 Export CSV</button>
        <button class="btn btn-word btn-sm" onclick="exportTodayWord()">📄 Export Word</button>
      </div>
      <div id="listInfo" style="font-size:12px;color:var(--gray);margin-bottom:10px;"></div>
      <div class="table-wrapper">
        <table id="visitorTable">
          <thead><tr>
            <th>#</th><th>Foto</th><th>No. Visitor</th><th>NIK</th><th>Nama</th><th>HP</th>
            <th>Keperluan</th><th>Bagian</th><th>Tgl Masuk</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Status</th><th>Aksi</th>
          </tr></thead>
          <tbody id="visitorTableBody"><tr><td colspan="13" style="text-align:center;color:var(--gray)">Memuat data...</td></tr></tbody>
        </table>
      </div>
      <div id="pagination" class="pagination"></div>
    </div>
  </div>

  <!-- ==================== LAPORAN TAB ==================== -->
  <div class="tab-panel" id="tab-report">
    <div class="card">
      <div class="card-title">📅 LAPORAN HARIAN (Per Tanggal)</div>
      <div class="report-filter-bar">
        <div class="form-group"><label>Pilih Tanggal</label>
          <div class="date-nav">
            <button class="btn btn-sm btn-secondary" onclick="shiftReportDate(-1)">◀</button>
            <input type="date" id="dailyReportDate" style="min-width:160px" onchange="generateDailyReport()">
            <button class="btn btn-sm btn-secondary" onclick="shiftReportDate(1)">▶</button>
          </div>
        </div>
        <button class="btn btn-primary" onclick="generateDailyReport()">🔍 Tampilkan</button>
      </div>
      <div class="report-action-bar">
        <button class="btn btn-secondary" onclick="printDailyReport()">🖨️ Print</button>
        <button class="btn btn-secondary" onclick="exportDailyCSV()">📎 CSV</button>
        <button class="btn btn-word" onclick="exportDailyWord()">📄 Word</button>
      </div>
      <div id="dailyReportContent"></div>
    </div>

    <!-- LAPORAN BULANAN — hanya jika punya akses -->
    <?php if ($canMonthly): ?>
    <div class="card">
      <div class="card-title">📈 LAPORAN BULANAN</div>
      <div class="report-filter-bar">
        <div class="form-group"><label>Bulan</label><select id="reportMonth" onchange="generateMonthlyReport()" style="min-width:100px"></select></div>
        <div class="form-group"><label>Tahun</label><select id="reportYear" onchange="generateMonthlyReport()" style="min-width:90px"></select></div>
        <button class="btn btn-primary" onclick="generateMonthlyReport()">🔍 Tampilkan</button>
      </div>
      <div class="report-action-bar">
        <button class="btn btn-secondary" onclick="printMonthlyReport()">🖨️ Print</button>
        <button class="btn btn-secondary" onclick="exportMonthlyCSV()">📎 CSV</button>
        <button class="btn btn-word" onclick="exportMonthlyWord()">📄 Word</button>
      </div>
      <div id="monthlyReportContent"></div>
    </div>
    <?php else: ?>
    <div class="card locked-section" style="min-height:200px">
      <div class="locked-overlay">
        <span>🔒</span>
        <p>Akses Rekap Bulanan tidak diizinkan untuk akun Anda.</p>
        <p style="font-size:12px;margin-top:4px">Hubungi Administrator untuk mendapatkan akses.</p>
      </div>
      <div class="card-title">📈 LAPORAN BULANAN</div>
    </div>
    <?php endif; ?>
  </div>

  <!-- ==================== IMPORT CSV TAB ==================== -->
  <div class="tab-panel" id="tab-import">
    <div class="card">
      <div class="card-title">📂 IMPORT DATABASE CSV</div>
      <p style="font-size:13px;color:var(--gray);margin-bottom:16px">
        Import data tamu dari file CSV. Kolom wajib: <strong style="color:var(--gold)">Nama, HP</strong>. Data duplikat akan dilewati.
      </p>
      <div id="csvDropArea" style="border:2px dashed rgba(30,132,73,0.5);border-radius:10px;background:rgba(20,90,50,0.1);padding:24px;text-align:center;cursor:pointer;" onclick="document.getElementById('csvFileInput').click()" ondragover="event.preventDefault()" ondrop="handleCsvDrop(event)">
        <div style="font-size:32px;margin-bottom:8px">📊</div>
        <div style="font-size:16px;font-weight:700;color:var(--gold)">Klik atau drag & drop file CSV</div>
        <div style="font-size:12px;color:var(--gray);margin-top:4px">Format: UTF-8 CSV dengan header di baris pertama</div>
      </div>
      <input type="file" id="csvFileInput" accept=".csv,text/csv" style="display:none" onchange="handleCsvFile(event)">
      <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap">
        <button class="btn btn-sm btn-secondary" onclick="downloadCsvTemplate()">⬇️ Download Template CSV</button>
      </div>
      <div id="importProgress" style="display:none;background:rgba(39,174,96,0.1);border:1px solid #27AE60;border-radius:8px;padding:12px;margin-top:12px;">
        <div id="importProgressText">Memproses...</div>
        <div style="background:rgba(201,168,76,0.2);border-radius:4px;height:8px;margin-top:8px"><div id="importProgressBar" style="height:100%;background:var(--gold);border-radius:4px;width:0%;transition:width 0.3s"></div></div>
      </div>
      <div id="csvPreviewArea" style="display:none;margin-top:16px;">
        <div class="card-title">🔍 Preview Data CSV (10 baris pertama)</div>
        <div style="max-height:300px;overflow-y:auto;overflow-x:auto"><table id="csvPreviewTable"><thead><tr id="csvPreviewHead"></tr></thead><tbody id="csvPreviewBody"></tbody></table></div>
        <div id="csvSummary" style="margin-top:12px;font-size:13px;color:var(--gold);"></div>
        <div style="margin-top:12px;display:flex;gap:10px">
          <button class="btn btn-success" onclick="confirmCsvImport()">✅ Import Data ke Database</button>
          <button class="btn btn-danger" onclick="cancelCsvImport()">❌ Batal</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==================== SETTINGS TAB (admin only) ==================== -->
  <?php if ($isAdmin): ?>
  <div class="tab-panel" id="tab-settings">
    <div class="card">
      <div class="card-title">⚙️ Pengaturan Sistem</div>
      <div class="db-badge" style="margin-bottom:16px">✅ Data tersimpan <strong>permanen</strong> di <strong>MySQL Server</strong></div>
      <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
        <button class="btn btn-secondary" onclick="exportAllDataJson()">💾 Backup Semua Data (JSON)</button>
        <a href="admin.php" class="btn btn-info">⚙️ Control Panel Admin</a>
        <button class="btn btn-danger" onclick="clearAllData()">⚠️ Hapus Semua Data</button>
      </div>
      <hr style="margin:16px 0;border-color:var(--border)">
      <div style="display:flex;flex-direction:column;gap:12px">
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer"><input type="checkbox" id="autoCapCheck" style="width:auto"> Auto-capture foto setelah scan NIK</label>
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer"><input type="checkbox" id="photoRequiredCheck" style="width:auto"> Foto wajah wajib diambil</label>
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer"><input type="checkbox" id="soundCheck" style="width:auto"> Suara notifikasi</label>
        <label style="display:flex;align-items:center;gap:10px;cursor:pointer"><input type="checkbox" id="scanModeCheck" style="width:auto"> Aktifkan mode scanner</label>
      </div>
      <button class="btn btn-primary" style="margin-top:16px" onclick="saveSettings()">Simpan Pengaturan</button>
      <hr style="margin:16px 0;border-color:var(--border)">
      <div id="dbInfo" style="font-size:12px;color:var(--gray)"></div>
    </div>
  </div>
  <?php endif; ?>

</div><!-- /main-content -->

<div id="print-area"></div>
<div class="modal-overlay" id="detailModalOverlay" onclick="if(event.target===this)this.classList.remove('flex')">
  <div class="modal" id="detailModalContent"></div>
</div>
<div id="toastMsg" class="toast">✅ <span id="toastText"></span></div>

<script>
// ==================== CONFIG ====================
const API = 'api.php';
const SETTINGS_KEY = 'pnjs_settings_v4';
const AUTOSAVE_KEY = 'etamu_autosave_form_v4';

let db = []; // client-side cache
let capturedPhotoData = null;
let settings = { autoCap: false, photoRequired: false, sound: true, scanMode: true };
let ocrRawResult = null;
let cameraStream = null;
let ocrCamStream = null;
let visitorOcrCamStream = null;
let csvParsedData = null;
let csvHeaders = [];
let activeTabId = 'register';
let filtered = [];
let currentPage = 1;
const PAGE_SIZE = 15;
let visitorNumberValue = '';

// ==================== API HELPER ====================
async function apiPost(action, data={}) {
  const r = await fetch(`${API}?action=${action}`, {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(data)
  });
  return r.json();
}

async function apiGet(action, params={}) {
  const q = new URLSearchParams(params);
  const r = await fetch(`${API}?action=${action}&${q}`);
  return r.json();
}

// ==================== LOAD DATA ====================
async function loadVisitors(params={}) {
  const res = await apiGet('getVisitors', {limit:1000, ...params});
  if (res.ok) {
    db = res.data;
    updateStats();
    filterVisitors();
  }
  return res;
}

async function loadStats() {
  const res = await apiGet('getStats');
  if (res.ok) {
    document.getElementById('todayBadge').innerHTML = `${res.today} Tamu Hari Ini`;
    const sg = document.getElementById('statsGrid');
    if(sg) sg.innerHTML = `
      <div class="stat-card"><div class="stat-value">${res.today}</div><div class="stat-label">Tamu Hari Ini</div></div>
      <div class="stat-card"><div class="stat-value">${res.in}</div><div class="stat-label">Masih Dalam</div></div>
      <div class="stat-card"><div class="stat-value">${res.out}</div><div class="stat-label">Sudah Keluar</div></div>
      <div class="stat-card"><div class="stat-value">${res.total}</div><div class="stat-label">Total Record</div></div>`;
    const qs = document.getElementById('quickStats');
    if(qs) qs.innerHTML = `Hari ini: <strong>${res.today}</strong> tamu | Dalam: <strong>${res.in}</strong> | Keluar: <strong>${res.out}</strong>`;
    const ss = document.getElementById('storageStatus');
    if(ss) ss.innerHTML = `✅ <strong>${res.total}</strong> record tersimpan<br>🗄️ MySQL Server (Permanen)<br>🕐 ${new Date().toLocaleTimeString('id-ID')}`;
  }
}

function updateStats() { loadStats(); }

// ==================== SETTINGS ====================
function loadSettings() {
  try { const s=localStorage.getItem(SETTINGS_KEY); if(s) Object.assign(settings,JSON.parse(s)); } catch(e){}
}
loadSettings();

function saveSettings() {
  settings.autoCap = document.getElementById('autoCapCheck')?.checked||false;
  settings.photoRequired = document.getElementById('photoRequiredCheck')?.checked||false;
  settings.sound = document.getElementById('soundCheck')?.checked||true;
  settings.scanMode = document.getElementById('scanModeCheck')?.checked||true;
  localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
  showToast('Pengaturan disimpan');
  updateScanMode();
}

function loadSettingsUI() {
  if(document.getElementById('autoCapCheck')) document.getElementById('autoCapCheck').checked = settings.autoCap;
  if(document.getElementById('photoRequiredCheck')) document.getElementById('photoRequiredCheck').checked = settings.photoRequired;
  if(document.getElementById('soundCheck')) document.getElementById('soundCheck').checked = settings.sound;
  if(document.getElementById('scanModeCheck')) document.getElementById('scanModeCheck').checked = settings.scanMode;
  const di = document.getElementById('dbInfo');
  if(di) di.innerHTML = `Penyimpanan: <strong style="color:#5EB88A">MySQL Server ✅ (Permanen)</strong><br>Total record: <strong>${db.length}</strong>`;
}

// ==================== CLOCK ====================
function updateClock() {
  const d = new Date();
  document.getElementById('clockDisplay').innerText = d.toLocaleTimeString('id-ID');
  document.getElementById('dateDisplay').innerText = d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
}
setInterval(updateClock,1000); updateClock();

// ==================== TOAST ====================
function showToast(msg) {
  const t=document.getElementById('toastMsg');
  document.getElementById('toastText').innerText=msg;
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),3500);
}

// ==================== NIK ====================
function onNikInput(val) {
  val = val.replace(/\D/g,'');
  document.getElementById('nikInput').value = val;
  const sd = document.getElementById('nikStatus');
  if(val.length===16) {
    sd.innerHTML='✅ NIK valid (16 digit)'; sd.className='nik-status nik-valid';
    // cek dari cache lokal
    const existing = db.filter(v=>v.nik===val).sort((a,b)=>b.timestamp-a.timestamp)[0];
    if(existing) {
      document.getElementById('namaInput').value=existing.nama;
      document.getElementById('hpInput').value=existing.hp;
      document.getElementById('alamatInput').value=existing.alamat||'';
      document.getElementById('instansiInput').value=existing.instansi||'';
      document.getElementById('genderSelect').value=existing.gender||'';
      document.getElementById('tempatLahirInput').value=existing.tempatLahir||'';
      document.getElementById('tglLahirInput').value=existing.tglLahir||'';
      showToast(`Data tamu lama: ${existing.nama} diisi otomatis`);
    }
    playBeep();
  } else if(val.length>0) { sd.innerHTML=`${val.length}/16 digit`; sd.className='nik-status nik-invalid'; }
  else sd.innerHTML='';
}

// ==================== VISITOR NUMBER ====================
function onVisitorNumberInput(val) {
  visitorNumberValue = val.trim().toUpperCase();
  document.getElementById('visitorNumberDisplay').value = visitorNumberValue;
  const status = document.getElementById('visitorNumberStatus');
  if(visitorNumberValue) {
    const today = new Date().toISOString().slice(0,10);
    const existing = db.find(v=>v.visitorNo===visitorNumberValue && v.dateKey===today && v.status==='in');
    if(existing) { status.innerHTML=`⚠️ No. Visitor <strong>${visitorNumberValue}</strong> dipakai: <strong>${existing.nama}</strong>`; status.style.color='#E74C3C'; }
    else { status.innerHTML=`✅ No. Visitor: <strong>${visitorNumberValue}</strong>`; status.style.color='#5EB88A'; }
    playBeep();
  } else status.innerHTML='';
}
function onVisitorNumberKeydown(e) {
  if(e.key==='Enter') { onVisitorNumberInput(document.getElementById('visitorNumberInput').value); setTimeout(()=>document.getElementById('nikInput').focus(),100); }
}
function clearVisitorNumber() {
  document.getElementById('visitorNumberInput').value='';
  document.getElementById('visitorNumberDisplay').value='';
  document.getElementById('visitorNumberStatus').innerHTML='';
  document.getElementById('visitorOcrPreview').style.display='none';
  visitorNumberValue='';
}
function updateScanMode() {
  const ind=document.getElementById('scanIndicator');
  if(settings.scanMode) { ind.style.display='flex'; document.getElementById('visitorNumberInput').focus(); }
  else ind.style.display='none';
}

// ==================== VISITOR OCR ====================
async function handleVisitorOcrFile(e) {
  const file=e.target.files[0]; if(!file) return;
  const reader=new FileReader();
  reader.onload=ev=>{ document.getElementById('visitorOcrPreview').src=ev.target.result; document.getElementById('visitorOcrPreview').style.display='block'; runVisitorOcr(ev.target.result); };
  reader.readAsDataURL(file);
}
function handleVisitorOcrDrop(e) { e.preventDefault(); const file=e.dataTransfer.files[0]; if(file&&file.type.startsWith('image/')) handleVisitorOcrFile({target:{files:[file]}}); }
async function openVisitorOcrCamera() {
  const modal=document.createElement('div'); modal.className='modal-overlay'; modal.style.display='flex';
  modal.innerHTML=`<div class="modal"><div style="color:var(--gold);font-family:Cinzel,serif;margin-bottom:12px;">📷 Ambil Foto Kartu Visitor</div><video id="visitorCamVideo" autoplay playsinline style="width:100%;border-radius:8px"></video><canvas id="visitorCamCanvas" style="display:none"></canvas><div style="margin-top:12px;display:flex;gap:8px"><button class="btn btn-primary" id="visitorCamCaptureBtn">📸 Ambil & OCR</button><button class="btn btn-secondary" onclick="this.closest('.modal-overlay').remove();if(visitorOcrCamStream)visitorOcrCamStream.getTracks().forEach(t=>t.stop())">Batal</button></div></div>`;
  document.body.appendChild(modal);
  try { const stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'environment'}}); document.getElementById('visitorCamVideo').srcObject=stream; visitorOcrCamStream=stream;
    document.getElementById('visitorCamCaptureBtn').onclick=async()=>{ const v=document.getElementById('visitorCamVideo'),c=document.getElementById('visitorCamCanvas'); c.width=v.videoWidth;c.height=v.videoHeight;c.getContext('2d').drawImage(v,0,0); const img=c.toDataURL('image/jpeg',0.9); if(visitorOcrCamStream) visitorOcrCamStream.getTracks().forEach(t=>t.stop()); modal.remove(); document.getElementById('visitorOcrPreview').src=img; document.getElementById('visitorOcrPreview').style.display='block'; runVisitorOcr(img); };
  } catch(e){ showToast('Gagal akses kamera'); modal.remove(); }
}
async function runVisitorOcr(imgData) {
  const prog=document.getElementById('visitorOcrProgress'); prog.classList.add('show'); document.getElementById('visitorOcrProgressBar').style.width='30%';
  try { const worker=await Tesseract.createWorker('eng'); document.getElementById('visitorOcrProgressBar').style.width='70%'; const{data:{text}}=await worker.recognize(imgData); await worker.terminate(); document.getElementById('visitorOcrProgressBar').style.width='100%'; prog.classList.remove('show');
    const numMatch=text.toUpperCase().replace(/\s+/g,' ').match(/\b([A-Z0-9]{3,15})\b/g);
    let visNo=''; if(numMatch) visNo=numMatch.sort((a,b)=>a.length-b.length)[0];
    if(visNo) { document.getElementById('visitorNumberInput').value=visNo; onVisitorNumberInput(visNo); showToast(`✅ OCR Visitor: ${visNo}`); }
    else showToast('⚠️ OCR tidak menemukan nomor');
  } catch(e){ prog.classList.remove('show'); showToast('Gagal OCR: '+e.message); }
}

// ==================== REGISTER ====================
async function registerVisitor() {
  const nik=document.getElementById('nikInput').value.replace(/\D/g,'');
  const nama=document.getElementById('namaInput').value.trim();
  const hp=document.getElementById('hpInput').value.trim();
  const keperluan=document.getElementById('keperluanSelect').value;
  const visitorNo=document.getElementById('visitorNumberInput').value.trim().toUpperCase();
  if(!nama||!hp||!keperluan){ showToast('Isi nama, HP, dan keperluan'); return; }
  if(settings.photoRequired&&!capturedPhotoData){ showToast('Foto wajah harus diambil'); return; }
  if(nik.length!==0&&nik.length!==16){ showToast('NIK harus 16 digit atau kosongkan'); return; }

  const now=new Date();
  const visitor={
    id: Date.now()+''+(Math.random()*9999|0),
    visitorNo, nik, nama, hp, keperluan,
    bagian: document.getElementById('bagianSelect').value,
    gender: document.getElementById('genderSelect').value,
    alamat: document.getElementById('alamatInput').value,
    instansi: document.getElementById('instansiInput').value,
    noPerkara: document.getElementById('noPerkara').value,
    tempatLahir: document.getElementById('tempatLahirInput').value,
    tglLahir: document.getElementById('tglLahirInput').value,
    catatan: document.getElementById('catatanInput').value,
    photo: capturedPhotoData,
    timestamp: now.getTime(),
    tanggal: now.toLocaleDateString('id-ID'),
    jamMasuk: now.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}),
    jamKeluar: null,
    status: 'in',
    dateKey: now.toISOString().slice(0,10)
  };

  const res = await apiPost('addVisitor', visitor);
  if(res.ok) {
    clearForm();
    showToast(`✅ ${nama} berhasil terdaftar${visitorNo?' | Visitor: '+visitorNo:''}`);
    playBeep();
    localStorage.removeItem(AUTOSAVE_KEY);
    document.getElementById('autosaveBanner').classList.remove('show');
    await loadVisitors();
  } else {
    showToast('❌ Gagal menyimpan: ' + (res.error||'?'));
  }
}

function clearForm() {
  ['nikInput','namaInput','hpInput','alamatInput','instansiInput','noPerkara','catatanInput','tempatLahirInput','tglLahirInput'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
  ['keperluanSelect','genderSelect','bagianSelect'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
  const ns=document.getElementById('nikStatus'); if(ns) ns.innerHTML='';
  clearVisitorNumber(); retakePhoto(); resetOcr();
  if(settings.scanMode) setTimeout(()=>document.getElementById('visitorNumberInput').focus(),200);
}

// ==================== LIST & FILTER ====================
function filterVisitors() {
  const search=(document.getElementById('searchInput')?.value||'').toLowerCase();
  const date=document.getElementById('filterDate')?.value||'';
  const status=document.getElementById('filterStatus')?.value||'';
  filtered=db.filter(v=>
    (v.nama?.toLowerCase().includes(search)||v.nik?.includes(search)||(v.visitorNo||'').toLowerCase().includes(search))&&
    (!date||v.dateKey===date)&&(!status||v.status===status)
  ).sort((a,b)=>b.timestamp-a.timestamp);
  currentPage=1;
  const info=document.getElementById('listInfo');
  if(info) info.innerText=`Menampilkan ${filtered.length} dari ${db.length} record${date?' (filter: '+formatDateID(date)+')':''}`;
  renderList();
}

function clearListFilter() {
  document.getElementById('searchInput').value='';
  document.getElementById('filterDate').value='';
  document.getElementById('filterStatus').value='';
  filterVisitors();
}

function renderList() {
  const tbody=document.getElementById('visitorTableBody'); if(!tbody) return;
  const start=(currentPage-1)*PAGE_SIZE;
  const page=filtered.slice(start,start+PAGE_SIZE);
  if(!page.length){ tbody.innerHTML='<tr><td colspan="13" style="text-align:center;color:var(--gray)">Tidak ada data</td></tr>'; renderPagination(); return; }
  tbody.innerHTML=page.map((v,i)=>`
    <tr>
      <td>${start+i+1}</td>
      <td>${v.photo?`<img src="${v.photo}" style="width:36px;height:36px;object-fit:cover;border-radius:6px">`:'👤'}</td>
      <td style="font-family:monospace;font-size:12px;color:#5EB88A;">${v.visitorNo||'-'}</td>
      <td style="font-family:monospace;font-size:11px">${v.nik?v.nik.slice(0,6)+'****'+v.nik.slice(-4):'-'}</td>
      <td><strong>${v.nama}</strong></td>
      <td>${v.hp}</td>
      <td>${v.keperluan}</td>
      <td style="font-size:11px;color:var(--gray)">${v.bagian||'-'}</td>
      <td style="font-size:11px">${v.tanggal||v.dateKey||'-'}</td>
      <td>${v.jamMasuk}</td>
      <td>${v.jamKeluar||'-'}</td>
      <td>${v.status==='in'?'<span class="badge-in">Dalam</span>':'<span class="badge-out">Keluar</span>'}</td>
      <td style="white-space:nowrap">
        <button class="btn btn-sm btn-secondary" onclick="showDetail('${v.id}')">Detail</button>
        ${v.status==='in'?`<button class="btn btn-sm btn-success" onclick="checkout('${v.id}')">Keluar</button>`:''}
        <button class="btn btn-sm btn-danger" onclick="deleteVisitor('${v.id}')">Hapus</button>
      </td>
    </tr>`).join('');
  renderPagination();
}

function renderPagination() {
  const total=Math.ceil(filtered.length/PAGE_SIZE);
  const pag=document.getElementById('pagination'); if(!pag) return;
  if(total<=1){pag.innerHTML='';return;}
  pag.innerHTML=Array.from({length:total},(_,i)=>`<button class="page-btn${currentPage===i+1?' active':''}" onclick="goPage(${i+1})">${i+1}</button>`).join('');
}
function goPage(p){currentPage=p;renderList();}

function showDetail(id) {
  const v=db.find(x=>x.id===id); if(!v) return;
  const overlay=document.getElementById('detailModalOverlay');
  document.getElementById('detailModalContent').innerHTML=`
    <div style="text-align:center;margin-bottom:16px">${v.photo?`<img src="${v.photo}" style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:2px solid var(--gold)">`:'<div style="width:100px;height:100px;border-radius:50%;background:var(--navy);border:2px solid var(--border);display:inline-flex;align-items:center;justify-content:center;font-size:40px">👤</div>'}</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px">
      <div style="grid-column:span 2;background:rgba(39,174,96,0.1);border:1px solid rgba(94,184,138,0.3);border-radius:8px;padding:10px;text-align:center"><div style="color:#5EB88A;font-size:10px;text-transform:uppercase">No. Visitor</div><div style="font-family:monospace;font-size:22px;letter-spacing:3px;color:#5EB88A;font-weight:700">${v.visitorNo||'-'}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Nama</div><div>${v.nama}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">NIK</div><div style="font-family:monospace">${v.nik||'-'}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">No HP</div><div>${v.hp}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Gender</div><div>${v.gender||'-'}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Keperluan</div><div>${v.keperluan}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Bagian</div><div>${v.bagian||'-'}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Tanggal</div><div>${v.tanggal}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Jam Masuk</div><div>${v.jamMasuk}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Jam Keluar</div><div>${v.jamKeluar||'Belum checkout'}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Status</div><div>${v.status==='in'?'<span class="badge-in">Masih Dalam</span>':'<span class="badge-out">Sudah Keluar</span>'}</div></div>
      <div><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Instansi</div><div>${v.instansi||'-'}</div></div>
      <div style="grid-column:span 2"><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Alamat</div><div>${v.alamat||'-'}</div></div>
      <div style="grid-column:span 2"><div style="color:var(--gold);font-size:10px;text-transform:uppercase">Catatan</div><div>${v.catatan||'-'}</div></div>
    </div>
    <div style="margin-top:16px;display:flex;gap:8px">
      ${v.status==='in'?`<button class="btn btn-success" onclick="checkout('${v.id}');document.getElementById('detailModalOverlay').classList.remove('flex')">🚪 Checkout</button>`:''}
      <button class="btn btn-secondary" onclick="document.getElementById('detailModalOverlay').classList.remove('flex')">Tutup</button>
    </div>`;
  overlay.classList.add('flex');
}

async function checkout(id) {
  const res = await apiPost('checkout',{id});
  if(res.ok){ showToast('Checkout berhasil'); await loadVisitors(); }
}

async function deleteVisitor(id) {
  if(!confirm('Hapus data tamu ini?')) return;
  const res = await apiPost('deleteVisitor',{id});
  if(res.ok){ showToast('Data dihapus'); await loadVisitors(); }
}

// ==================== EXPORT ====================
async function exportToday() {
  const today=new Date().toISOString().slice(0,10);
  const res=await apiGet('getVisitors',{date:today,limit:9999});
  if(res.ok) exportCSV(res.data,`tamu_${today}`);
}
async function exportTodayWord() {
  const today=new Date().toISOString().slice(0,10);
  const res=await apiGet('getVisitors',{date:today,limit:9999});
  if(res.ok&&res.data.length) generateWordReport(res.data.sort((a,b)=>a.timestamp-b.timestamp),`Laporan Harian Tamu - ${formatDateID(today)}`,today,null,null);
  else showToast('Tidak ada data tamu hari ini');
}
async function exportDailyCSV() {
  const date=document.getElementById('dailyReportDate').value;
  if(!date){showToast('Pilih tanggal dulu');return;}
  const res=await apiGet('getVisitors',{date,limit:9999});
  if(res.ok) exportCSV(res.data,`tamu_harian_${date}`);
}
async function exportMonthlyCSV() {
  const month=document.getElementById('reportMonth')?.value;
  const year=document.getElementById('reportYear')?.value;
  if(month===undefined||!year) return;
  const res=await apiGet('getVisitors',{month,year,limit:99999});
  if(res.ok) exportCSV(res.data,`laporan_${year}_${String(parseInt(month)+1).padStart(2,'0')}`);
}
function exportCSV(arr,name) {
  if(!arr.length){showToast('Tidak ada data');return;}
  const headers=['No','No.Visitor','Nama','NIK','HP','Gender','Keperluan','Bagian','Instansi','No Perkara','Tanggal','Jam Masuk','Jam Keluar','Status','Alamat','Catatan'];
  const rows=arr.map((v,i)=>[i+1,v.visitorNo||'',v.nama,v.nik||'',v.hp,v.gender||'',v.keperluan,v.bagian||'',v.instansi||'',v.noPerkara||'',v.tanggal,v.jamMasuk,v.jamKeluar||'-',v.status==='in'?'Masih Dalam':'Sudah Keluar',v.alamat||'',v.catatan||'']);
  const csv=[headers,...rows].map(r=>r.map(c=>'"'+String(c||'').replace(/"/g,'""')+'"').join(',')).join('\n');
  const blob=new Blob(['\uFEFF'+csv],{type:'text/csv'});
  const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=`${name}.csv`;a.click();
  showToast(`✅ CSV ${arr.length} record diekspor`);
}

// ==================== LAPORAN ====================
function shiftReportDate(delta) {
  const inp=document.getElementById('dailyReportDate');
  let d=inp.value?new Date(inp.value+'T12:00:00'):new Date();
  d.setDate(d.getDate()+delta); inp.value=d.toISOString().slice(0,10); generateDailyReport();
}

async function generateDailyReport() {
  const date=document.getElementById('dailyReportDate').value;
  if(!date){document.getElementById('dailyReportContent').innerHTML='<p style="color:var(--gray)">Pilih tanggal.</p>';return;}
  const res=await apiGet('getVisitors',{date,limit:9999});
  if(!res.ok) return;
  const data=res.data.sort((a,b)=>a.timestamp-b.timestamp);
  const inCount=data.filter(v=>v.status==='in').length;
  const outCount=data.filter(v=>v.status==='out').length;
  const kepSum={};
  data.forEach(v=>{kepSum[v.keperluan]=(kepSum[v.keperluan]||0)+1;});
  let html=`<div class="daily-report-header">📅 ${formatDateID(date)} — Total: ${data.length} Tamu</div>`;
  html+=`<div class="report-summary-grid">
    <div class="report-summary-card"><div class="report-summary-value">${data.length}</div><div class="report-summary-label">Total Tamu</div></div>
    <div class="report-summary-card"><div class="report-summary-value">${inCount}</div><div class="report-summary-label">Masih Dalam</div></div>
    <div class="report-summary-card"><div class="report-summary-value">${outCount}</div><div class="report-summary-label">Sudah Keluar</div></div>
    ${Object.entries(kepSum).slice(0,3).map(([k,v])=>`<div class="report-summary-card"><div class="report-summary-value">${v}</div><div class="report-summary-label">${k}</div></div>`).join('')}
  </div>`;
  if(!data.length) html+='<p style="color:var(--gray);text-align:center;padding:20px">Tidak ada tamu.</p>';
  else html+=`<div class="table-wrapper"><table>
    <thead><tr><th>#</th><th>No.Visitor</th><th>Nama</th><th>NIK</th><th>HP</th><th>Keperluan</th><th>Bagian</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Status</th></tr></thead>
    <tbody>${data.map((v,i)=>`<tr><td>${i+1}</td><td style="font-family:monospace;color:#5EB88A;font-weight:700">${v.visitorNo||'-'}</td><td><strong>${v.nama}</strong></td><td style="font-family:monospace;font-size:11px">${v.nik?v.nik.slice(0,6)+'****'+v.nik.slice(-4):'-'}</td><td>${v.hp}</td><td>${v.keperluan}</td><td style="color:var(--gray);font-size:12px">${v.bagian||'-'}</td><td>${v.jamMasuk}</td><td>${v.jamKeluar||'-'}</td><td>${v.status==='in'?'<span class="badge-in">Dalam</span>':'<span class="badge-out">Keluar</span>'}</td></tr>`).join('')}</tbody>
  </table></div>`;
  document.getElementById('dailyReportContent').innerHTML=html;
}

async function generateMonthlyReport() {
  const mEl=document.getElementById('reportMonth');
  const yEl=document.getElementById('reportYear');
  if(!mEl||!yEl) return;
  const month=parseInt(mEl.value);
  const year=parseInt(yEl.value);
  const res=await apiGet('getVisitors',{month,year,limit:99999});
  if(!res.ok) return;
  const data=res.data.sort((a,b)=>a.timestamp-b.timestamp);
  const namaBulan=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][month];
  const inCount=data.filter(v=>v.status==='in').length;
  const outCount=data.filter(v=>v.status==='out').length;
  const byDate={};
  data.forEach(v=>{if(!byDate[v.dateKey])byDate[v.dateKey]=[];byDate[v.dateKey].push(v);});
  const kepSum={};
  data.forEach(v=>{kepSum[v.keperluan]=(kepSum[v.keperluan]||0)+1;});
  let html=`<div class="daily-report-header">📈 ${namaBulan} ${year} — Total: ${data.length} Tamu</div>`;
  html+=`<div class="report-summary-grid">
    <div class="report-summary-card"><div class="report-summary-value">${data.length}</div><div class="report-summary-label">Total Tamu</div></div>
    <div class="report-summary-card"><div class="report-summary-value">${Object.keys(byDate).length}</div><div class="report-summary-label">Hari Aktif</div></div>
    <div class="report-summary-card"><div class="report-summary-value">${inCount}</div><div class="report-summary-label">Masih Dalam</div></div>
    <div class="report-summary-card"><div class="report-summary-value">${outCount}</div><div class="report-summary-label">Sudah Keluar</div></div>
    ${Object.entries(kepSum).sort((a,b)=>b[1]-a[1]).slice(0,4).map(([k,v])=>`<div class="report-summary-card"><div class="report-summary-value">${v}</div><div class="report-summary-label">${k}</div></div>`).join('')}
  </div>`;
  if(!data.length) html+='<p style="color:var(--gray);text-align:center;padding:20px">Tidak ada data.</p>';
  else {
    html+=`<div style="margin-bottom:16px"><div class="card-title">📆 Rekap Per Tanggal</div><div class="table-wrapper"><table><thead><tr><th>Tanggal</th><th>Jumlah</th><th>Dalam</th><th>Keluar</th></tr></thead><tbody>${Object.keys(byDate).sort().map(dk=>{const dv=byDate[dk];const din=dv.filter(v=>v.status==='in').length;return`<tr><td>${formatDateID(dk)}</td><td><strong>${dv.length}</strong></td><td>${din}</td><td>${dv.length-din}</td></tr>`;}).join('')}</tbody></table></div></div>`;
    html+=`<div style="margin-bottom:16px"><div class="card-title">📋 Rekap Keperluan</div><div class="table-wrapper"><table><thead><tr><th>Keperluan</th><th>Jumlah</th><th>%</th></tr></thead><tbody>${Object.entries(kepSum).sort((a,b)=>b[1]-a[1]).map(([k,v])=>`<tr><td>${k}</td><td>${v}</td><td>${((v/data.length)*100).toFixed(1)}%</td></tr>`).join('')}</tbody></table></div></div>`;
  }
  document.getElementById('monthlyReportContent').innerHTML=html;
}

async function printDailyReport() {
  const date=document.getElementById('dailyReportDate').value; if(!date){showToast('Pilih tanggal');return;}
  const res=await apiGet('getVisitors',{date,limit:9999});
  if(res.ok) doPrint(res.data.sort((a,b)=>a.timestamp-b.timestamp),`Laporan Harian Tamu - ${formatDateID(date)}`,date,null,null);
}
async function printMonthlyReport() {
  const month=parseInt(document.getElementById('reportMonth')?.value||0);
  const year=parseInt(document.getElementById('reportYear')?.value||new Date().getFullYear());
  const res=await apiGet('getVisitors',{month,year,limit:99999});
  const namaBulan=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][month];
  if(res.ok) doPrint(res.data.sort((a,b)=>a.timestamp-b.timestamp),`Laporan Bulanan Tamu - ${namaBulan} ${year}`,null,month,year);
}

function doPrint(data,judul,tanggal,month,year) {
  const printArea=document.getElementById('print-area');
  const rows=data.map((v,i)=>`<tr><td style="text-align:center">${i+1}</td><td style="font-family:monospace;font-weight:bold">${v.visitorNo||'-'}</td><td>${v.nama}</td><td>${v.nik||'-'}</td><td>${v.hp}</td><td>${v.keperluan}</td><td>${v.bagian||'-'}</td><td>${v.tanggal}</td><td>${v.jamMasuk}</td><td>${v.jamKeluar||'-'}</td><td>${v.status==='in'?'Masih Dalam':'Sudah Keluar'}</td></tr>`).join('');
  const inCount=data.filter(v=>v.status==='in').length;
  const outCount=data.filter(v=>v.status==='out').length;
  const kepSum={};data.forEach(v=>{kepSum[v.keperluan]=(kepSum[v.keperluan]||0)+1;});
  const kepRows=Object.entries(kepSum).sort((a,b)=>b[1]-a[1]).map(([k,v])=>`<tr><td>${k}</td><td style="text-align:center">${v}</td><td style="text-align:center">${((v/data.length)*100).toFixed(1)}%</td></tr>`).join('');
  printArea.innerHTML=`
    <div class="print-header" style="text-align:center;margin-bottom:20px;font-family:serif">
      <div style="font-size:14px;font-weight:bold;text-transform:uppercase">PENGADILAN NEGERI JAKARTA SELATAN KELAS 1A KHUSUS</div>
      <div style="font-size:12px">Jl. Ampera Raya No. 133, Jakarta Selatan</div>
      <hr style="margin:8px 0;border-top:2px solid black">
      <div style="font-size:15px;font-weight:bold;margin:6px 0">${judul}</div>
      <div style="font-size:11px">Dicetak: ${new Date().toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'})} pukul ${new Date().toLocaleTimeString('id-ID')}</div>
    </div>
    <table style="width:100%;margin-bottom:12px;border-collapse:collapse;font-size:12px"><tr style="background:#f0f0f0"><td style="padding:4px 8px;font-weight:bold">Total Tamu</td><td style="padding:4px 8px">${data.length}</td><td style="padding:4px 8px;font-weight:bold">Masih Dalam</td><td style="padding:4px 8px">${inCount}</td><td style="padding:4px 8px;font-weight:bold">Sudah Keluar</td><td style="padding:4px 8px">${outCount}</td></tr></table>
    <table border="1" style="width:100%;border-collapse:collapse;font-size:10px;margin-bottom:16px">
      <thead><tr style="background:#ddd"><th style="padding:5px">No</th><th>No.Visitor</th><th>Nama</th><th>NIK</th><th>No HP</th><th>Keperluan</th><th>Bagian</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Status</th></tr></thead>
      <tbody>${rows||'<tr><td colspan="11" style="text-align:center">Tidak ada data</td></tr>'}</tbody>
    </table>
    ${kepRows?`<div style="font-size:12px;font-weight:bold;margin-bottom:6px">Rekap Keperluan:</div><table border="1" style="width:50%;border-collapse:collapse;font-size:11px"><thead><tr style="background:#ddd"><th style="padding:4px 8px">Keperluan</th><th>Jumlah</th><th>%</th></tr></thead><tbody>${kepRows}</tbody></table>`:''}
    <div style="margin-top:40px;display:flex;justify-content:flex-end"><div style="text-align:center"><div style="font-size:12px">Jakarta, ${new Date().toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'})}</div><div style="font-size:12px">Petugas Registrasi</div><div style="height:60px"></div><div style="font-size:12px">(_______________________)</div></div></div>`;
  window.print();
}

// ==================== WORD EXPORT ====================
async function exportDailyWord() {
  const date=document.getElementById('dailyReportDate').value; if(!date){showToast('Pilih tanggal');return;}
  const res=await apiGet('getVisitors',{date,limit:9999});
  if(res.ok) generateWordReport(res.data.sort((a,b)=>a.timestamp-b.timestamp),`Laporan Harian Tamu - ${formatDateID(date)}`,date,null,null);
}
async function exportMonthlyWord() {
  const month=parseInt(document.getElementById('reportMonth')?.value||0);
  const year=parseInt(document.getElementById('reportYear')?.value||new Date().getFullYear());
  const res=await apiGet('getVisitors',{month,year,limit:99999});
  const namaBulan=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][month];
  if(res.ok) generateWordReport(res.data.sort((a,b)=>a.timestamp-b.timestamp),`Laporan Bulanan Tamu - ${namaBulan} ${year}`,null,month,year);
}

async function generateWordReport(data,judul,tanggal,month,year) {
  showToast('⏳ Membuat dokumen Word...');
  try {
    const{Document,Packer,Paragraph,TextRun,Table,TableRow,TableCell,AlignmentType,BorderStyle,WidthType,ShadingType,VerticalAlign}=docx;
    const navyMid="1A3A5C",goldColor="8B6914",white="FFFFFF",lightGray="F7F9FC";
    const darkBorder={style:BorderStyle.SINGLE,size:1,color:"AAAAAA"};
    const borders={top:darkBorder,bottom:darkBorder,left:darkBorder,right:darkBorder};
    const noBorder={style:BorderStyle.NONE,size:0,color:"FFFFFF"};
    const noBorders={top:noBorder,bottom:noBorder,left:noBorder,right:noBorder};
    const cellM={top:80,bottom:80,left:120,right:120};
    const namaBulan=month!==null?['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][month]:null;
    const inCount=data.filter(v=>v.status==='in').length;
    const outCount=data.filter(v=>v.status==='out').length;
    const kepSum={};data.forEach(v=>{kepSum[v.keperluan]=(kepSum[v.keperluan]||0)+1;});
    const byDate={};data.forEach(v=>{if(!byDate[v.dateKey])byDate[v.dateKey]=[];byDate[v.dateKey].push(v);});
    const mkPar=(text,opts={})=>new Paragraph({children:[new TextRun({text,font:"Arial",...opts})],alignment:opts.align||AlignmentType.LEFT,spacing:opts.spacing||{after:80}});
    const mkCell=(text,opts={})=>new TableCell({borders,margins:cellM,width:{size:opts.w||1000,type:WidthType.DXA},shading:{fill:opts.fill||white,type:ShadingType.CLEAR},verticalAlign:VerticalAlign.CENTER,children:[new Paragraph({alignment:opts.align||AlignmentType.LEFT,children:[new TextRun({text:String(text||'-'),font:"Arial",size:opts.size||18,bold:opts.bold||false,color:opts.color||"222222"})]})]});
    const mkHCell=(text,w)=>mkCell(text,{w,fill:navyMid,color:white,bold:true,size:17,align:AlignmentType.CENTER});
    const colW=[400,900,2000,1200,900,1600,1000,700,700,960];
    const mainTable=new Table({width:{size:colW.reduce((a,b)=>a+b,0),type:WidthType.DXA},columnWidths:colW,rows:[
      new TableRow({children:['#','No.Visitor','Nama Lengkap','NIK','No HP','Keperluan','Bagian / Tujuan','Jam Masuk','Jam Keluar','Status'].map((h,i)=>mkHCell(h,colW[i]))}),
      ...data.map((v,i)=>new TableRow({children:[String(i+1),v.visitorNo||'-',v.nama,v.nik||'-',v.hp,v.keperluan,v.bagian||'-',v.jamMasuk,v.jamKeluar||'-',v.status==='in'?'Dalam':'Keluar'].map((val,ci)=>mkCell(val,{w:colW[ci],fill:i%2===0?white:lightGray,align:ci===0?AlignmentType.CENTER:AlignmentType.LEFT}))}))
    ]});
    const doc=new Document({styles:{default:{document:{run:{font:"Arial",size:20}}}},sections:[{properties:{page:{size:{width:15840,height:12240},margin:{top:1080,right:1080,bottom:1080,left:1440}}},children:[
      mkPar('PENGADILAN NEGERI JAKARTA SELATAN KELAS 1A KHUSUS',{bold:true,size:26,align:AlignmentType.CENTER,spacing:{after:60}}),
      mkPar('Jl. Ampera Raya No. 133, Jakarta Selatan 12550',{size:18,align:AlignmentType.CENTER,color:'444444',spacing:{after:0}}),
      new Paragraph({border:{bottom:{style:BorderStyle.THICK,size:12,color:navyMid,space:4}},children:[],spacing:{after:160}}),
      mkPar(judul.toUpperCase(),{bold:true,size:28,align:AlignmentType.CENTER,color:"0A1628",spacing:{before:160,after:80}}),
      mkPar(`Dicetak: ${new Date().toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'})} pukul ${new Date().toLocaleTimeString('id-ID')}`,{size:17,align:AlignmentType.CENTER,color:'666666',spacing:{after:240}}),
      mkPar('DETAIL DATA TAMU',{bold:true,size:22,color:navyMid,spacing:{before:80,after:120}}),
      data.length===0?mkPar('Tidak ada data tamu pada periode ini.',{color:'888888',align:AlignmentType.CENTER}):mainTable,
      new Paragraph({children:[]}),
      new Table({width:{size:9360,type:WidthType.DXA},columnWidths:[4680,4680],rows:[new TableRow({children:[
        new TableCell({borders:noBorders,width:{size:4680,type:WidthType.DXA},margins:cellM,children:[mkPar('Mengetahui,',{align:AlignmentType.CENTER}),mkPar('Panitera / Sekretaris',{bold:true,align:AlignmentType.CENTER,spacing:{after:160}}),new Paragraph({alignment:AlignmentType.CENTER,children:[new TextRun({text:'(_____________________________)',font:'Arial',size:18})],spacing:{after:60}}),mkPar('NIP. ____________________',{align:AlignmentType.CENTER})]}),
        new TableCell({borders:noBorders,width:{size:4680,type:WidthType.DXA},margins:cellM,children:[mkPar(`Jakarta, ${new Date().toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'})}`,{align:AlignmentType.CENTER}),mkPar('Petugas Registrasi Tamu',{bold:true,align:AlignmentType.CENTER,spacing:{after:160}}),new Paragraph({alignment:AlignmentType.CENTER,children:[new TextRun({text:'(_____________________________)',font:'Arial',size:18})],spacing:{after:60}}),mkPar('NIP. ____________________',{align:AlignmentType.CENTER})]})
      ]})]}),
    ]}]});
    const buffer=await Packer.toBuffer(doc);
    const blob=new Blob([buffer],{type:"application/vnd.openxmlformats-officedocument.wordprocessingml.document"});
    const filename=tanggal?`Laporan_Harian_Tamu_${tanggal}.docx`:`Laporan_Bulanan_Tamu_${year}_${String(month+1).padStart(2,'0')}.docx`;
    saveAs(blob,filename);
    showToast('✅ Dokumen Word berhasil diunduh!');
  } catch(e) { console.error(e); showToast('❌ Gagal Word: '+e.message); }
}

// ==================== IMPORT CSV ====================
function handleCsvDrop(e){e.preventDefault();const file=e.dataTransfer.files[0];if(file)processCsvFile(file);}
function handleCsvFile(e){const file=e.target.files[0];if(file)processCsvFile(file);}
function processCsvFile(file){const reader=new FileReader();reader.onload=ev=>parseCsvData(ev.target.result);reader.readAsText(file,'UTF-8');}
function parseCsvData(text){
  if(text.charCodeAt(0)===0xFEFF)text=text.slice(1);
  const lines=text.trim().split('\n');if(lines.length<2){showToast('File CSV kosong');return;}
  csvHeaders=parseCSVLine(lines[0]);
  const rows=lines.slice(1).map(l=>parseCSVLine(l)).filter(r=>r.length>0&&r.some(c=>c.trim()));
  csvParsedData=rows.map(r=>{const obj={};csvHeaders.forEach((h,i)=>{obj[h.trim()]=r[i]?.trim()||'';});return obj;});
  document.getElementById('csvPreviewHead').innerHTML=csvHeaders.map(h=>`<th>${h}</th>`).join('');
  document.getElementById('csvPreviewBody').innerHTML=csvParsedData.slice(0,10).map(row=>`<tr>${csvHeaders.map(h=>`<td style="font-size:12px">${row[h]||''}</td>`).join('')}</tr>`).join('');
  document.getElementById('csvSummary').innerHTML=`✅ Ditemukan <strong>${csvParsedData.length}</strong> baris data.`;
  document.getElementById('csvPreviewArea').style.display='block';
}
function parseCSVLine(line){const result=[];let cur='';let inQ=false;for(let i=0;i<line.length;i++){const c=line[i];if(c==='"'&&!inQ){inQ=true;continue;}if(c==='"'&&inQ&&line[i+1]==='"'){cur+='"';i++;continue;}if(c==='"'&&inQ){inQ=false;continue;}if(c===','&&!inQ){result.push(cur);cur='';continue;}cur+=c;}result.push(cur);return result;}
const COL_MAP={visitorNo:['visitorno','visitor no','no visitor','no.visitor','nomor visitor'],nama:['nama','name','nama lengkap'],nik:['nik','ktp'],hp:['hp','no hp','nomor hp','telp','telepon','phone','wa'],keperluan:['keperluan','tujuan','purpose'],bagian:['bagian','department'],gender:['gender','jenis kelamin'],alamat:['alamat','address'],instansi:['instansi','kantor','perusahaan'],noPerkara:['noperkara','no perkara','nomor perkara'],tempatLahir:['tempat lahir','tempatlahir'],tglLahir:['tgl lahir','tgllahir','tanggal lahir'],catatan:['catatan','notes'],tanggal:['tanggal','date','tgl'],jamMasuk:['jam masuk','jammasuk','time in'],jamKeluar:['jam keluar','jamkeluar','time out'],status:['status']};
function mapColumn(headers,field){const keys=COL_MAP[field]||[field];for(const k of keys)for(const h of headers)if(h.toLowerCase().trim()===k)return h;return null;}
async function confirmCsvImport(){
  if(!csvParsedData||!csvParsedData.length){showToast('Tidak ada data');return;}
  const colNama=mapColumn(csvHeaders,'nama');const colHp=mapColumn(csvHeaders,'hp');
  if(!colNama||!colHp){showToast('Kolom Nama dan HP wajib');return;}
  document.getElementById('importProgress').style.display='block';
  const records=csvParsedData.map((row,idx)=>{
    const nama=(row[colNama]||'').trim();const hp=(row[mapColumn(csvHeaders,'hp')]||'').trim();
    if(!nama||!hp)return null;
    const tanggalRaw=(row[mapColumn(csvHeaders,'tanggal')]||'').trim();
    let dateKey='',tanggal='';
    if(tanggalRaw){const d=new Date(tanggalRaw);if(!isNaN(d)){dateKey=d.toISOString().slice(0,10);tanggal=d.toLocaleDateString('id-ID');}else{dateKey=tanggalRaw;tanggal=tanggalRaw;}}
    else{const now=new Date();dateKey=now.toISOString().slice(0,10);tanggal=now.toLocaleDateString('id-ID');}
    return{visitorNo:(row[mapColumn(csvHeaders,'visitorNo')]||'').trim().toUpperCase(),nik:(row[mapColumn(csvHeaders,'nik')]||'').trim(),nama,hp,keperluan:row[mapColumn(csvHeaders,'keperluan')]||'Lainnya',bagian:row[mapColumn(csvHeaders,'bagian')]||'',gender:row[mapColumn(csvHeaders,'gender')]||'',alamat:row[mapColumn(csvHeaders,'alamat')]||'',instansi:row[mapColumn(csvHeaders,'instansi')]||'',noPerkara:row[mapColumn(csvHeaders,'noPerkara')]||'',tempatLahir:row[mapColumn(csvHeaders,'tempatLahir')]||'',tglLahir:row[mapColumn(csvHeaders,'tglLahir')]||'',catatan:row[mapColumn(csvHeaders,'catatan')]||'',photo:null,timestamp:new Date(dateKey).getTime()||Date.now(),tanggal,dateKey,jamMasuk:(row[mapColumn(csvHeaders,'jamMasuk')]||new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})).trim(),jamKeluar:(row[mapColumn(csvHeaders,'jamKeluar')]||'').trim()||null,status:((row[mapColumn(csvHeaders,'status')]||'out').trim().toLowerCase()==='in')?'in':'out'};
  }).filter(Boolean);
  document.getElementById('importProgressBar').style.width='50%';
  const res=await apiPost('bulkImport',{records});
  document.getElementById('importProgressBar').style.width='100%';
  if(res.ok){showToast(`✅ Import: ${res.imported} masuk, ${res.skipped} dilewati`);cancelCsvImport();await loadVisitors();}
  else{showToast('❌ Import gagal: '+(res.error||'?'));}
  document.getElementById('importProgress').style.display='none';
}
function cancelCsvImport(){csvParsedData=null;csvHeaders=[];document.getElementById('csvPreviewArea').style.display='none';document.getElementById('importProgress').style.display='none';}
function downloadCsvTemplate(){const headers=['No.Visitor','Nama','NIK','HP','Keperluan','Bagian','Gender','Alamat','Instansi','NoPerkara','TempatLahir','TglLahir','Catatan','Tanggal','JamMasuk','JamKeluar','Status'];const sample=[['V001','Budi Santoso','3201010101010001','08123456789','Sidang Perdata','Kepaniteraan Perdata','Laki-laki','Jl. Merdeka No.1','PT Contoh','123/Pdt.G/2024','Jakarta','01-01-1985','','2024-01-15','08:30','10:45','out']];const csv=[headers,...sample].map(r=>r.map(c=>'"'+c+'"').join(',')).join('\n');const blob=new Blob(['\uFEFF'+csv],{type:'text/csv'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='template_import_tamu_v4.csv';a.click();}

// ==================== JSON BACKUP ====================
async function exportAllDataJson(){
  const res=await apiGet('getVisitors',{limit:999999});
  if(res.ok){const blob=new Blob([JSON.stringify(res.data,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=`backup_etamu_${Date.now()}.json`;a.click();showToast('Backup JSON berhasil');}
}
async function clearAllData(){
  if(!confirm('⚠️ HAPUS SEMUA DATA TAMU?\nTindakan ini PERMANEN dan tidak bisa dibatalkan!'))return;
  const res=await apiPost('clearAll');
  if(res.ok){showToast('Semua data dihapus');await loadVisitors();}
}

// ==================== OCR KTP ====================
async function runOcrOnImage(imgData){
  const prog=document.getElementById('ocrProgress');prog.classList.add('show');document.getElementById('ocrProgressBar').style.width='30%';
  try{const worker=await Tesseract.createWorker('ind');document.getElementById('ocrProgressBar').style.width='60%';const{data:{text}}=await worker.recognize(imgData);await worker.terminate();document.getElementById('ocrProgressBar').style.width='100%';prog.classList.remove('show');
    const parsed=parseKtpTextAdvanced(text);ocrRawResult=parsed;document.getElementById('ocrResultBox').classList.add('show');
    let html=`<div>🆔 NIK: <strong>${parsed.nik||'❌ tidak terbaca'}</strong></div><div>👤 Nama: ${parsed.nama||'-'}</div><div>📅 TTL: ${parsed.tempatLahir?parsed.tempatLahir+', ':''}${parsed.tglLahir||'-'}</div><div>📍 Alamat: ${(parsed.alamat||'').substring(0,80)}</div>`;
    document.getElementById('ocrResultContent').innerHTML=html;
    if(parsed.nik&&parsed.nik.length===16)showToast('✅ NIK berhasil terbaca!');else showToast('⚠️ OCR selesai, NIK tidak terbaca sempurna.');
  }catch(e){prog.classList.remove('show');showToast('Gagal OCR: '+e.message);}
}
function parseKtpTextAdvanced(raw){let text=raw.toUpperCase().replace(/[|]/g,'I').replace(/\n+/g,'\n');let nikText=text.replace(/O/g,'0').replace(/l/g,'1');let nik='';const nikMatch=nikText.match(/\b(\d{16})\b/);if(nikMatch)nik=nikMatch[1];else{const ld=text.replace(/\s/g,'').match(/\d{15,18}/);if(ld)nik=ld[0].slice(0,16);}let nama='';const namaMatch=text.match(/NAMA\s*[:.]?\s*([A-Z][A-Z\s\.]+)/);if(namaMatch)nama=namaMatch[1].trim().replace(/\s+/g,' ').substring(0,60);let tglLahir='',tempatLahir='';const ttlMatch=text.match(/TEMPAT\s*TGL\s*LAHIR\s*[:.]?\s*([A-Z\s]+)\s*(\d{2}[-\/]\d{2}[-\/]\d{4})/i);if(ttlMatch){tempatLahir=ttlMatch[1].trim();tglLahir=ttlMatch[2];}else{const tglMatch=text.match(/(\d{2}[-\/]\d{2}[-\/]\d{4})/);if(tglMatch)tglLahir=tglMatch[1];}let alamat='';const alamatMatch=text.match(/ALAMAT\s*[:.]?\s*([^\n]{10,80})/i);if(alamatMatch)alamat=alamatMatch[1].trim();return{nik,nama,tglLahir,tempatLahir,alamat};}
function applyOcrResult(){if(!ocrRawResult)return;if(ocrRawResult.nik){document.getElementById('nikInput').value=ocrRawResult.nik;onNikInput(ocrRawResult.nik);}if(ocrRawResult.nama)document.getElementById('namaInput').value=ocrRawResult.nama;if(ocrRawResult.tglLahir)document.getElementById('tglLahirInput').value=ocrRawResult.tglLahir;if(ocrRawResult.tempatLahir)document.getElementById('tempatLahirInput').value=ocrRawResult.tempatLahir;if(ocrRawResult.alamat)document.getElementById('alamatInput').value=ocrRawResult.alamat;showToast('Data OCR diterapkan');document.getElementById('ocrResultBox').classList.remove('show');}
function resetOcr(){ocrRawResult=null;document.getElementById('ocrResultBox').classList.remove('show');document.getElementById('ocrPreviewImg').style.display='none';}
function handleKtpFile(e){const file=e.target.files[0];if(file){const reader=new FileReader();reader.onload=ev=>{document.getElementById('ocrPreviewImg').src=ev.target.result;document.getElementById('ocrPreviewImg').style.display='block';runOcrOnImage(ev.target.result);};reader.readAsDataURL(file);}}
function handleDrop(e){e.preventDefault();const file=e.dataTransfer.files[0];if(file&&file.type.startsWith('image/'))handleKtpFile({target:{files:[file]}});}
async function openOcrCamera(){const modal=document.createElement('div');modal.className='modal-overlay';modal.style.display='flex';modal.innerHTML=`<div class="modal"><div style="color:var(--gold);font-family:Cinzel,serif;margin-bottom:12px;">📷 Ambil Foto KTP</div><video id="ocrCamVideo" autoplay playsinline style="width:100%;border-radius:8px"></video><canvas id="ocrCamCanvas" style="display:none"></canvas><div style="margin-top:12px;display:flex;gap:8px"><button class="btn btn-primary" id="ocrCamCaptureBtn">📸 Ambil & OCR</button><button class="btn btn-secondary" onclick="this.closest('.modal-overlay').remove();if(ocrCamStream)ocrCamStream.getTracks().forEach(t=>t.stop())">Batal</button></div></div>`;document.body.appendChild(modal);try{const stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'environment'}});document.getElementById('ocrCamVideo').srcObject=stream;ocrCamStream=stream;document.getElementById('ocrCamCaptureBtn').onclick=async()=>{const v=document.getElementById('ocrCamVideo'),c=document.getElementById('ocrCamCanvas');c.width=v.videoWidth;c.height=v.videoHeight;c.getContext('2d').drawImage(v,0,0);const imgData=c.toDataURL('image/jpeg',0.9);if(ocrCamStream)ocrCamStream.getTracks().forEach(t=>t.stop());modal.remove();document.getElementById('ocrPreviewImg').src=imgData;document.getElementById('ocrPreviewImg').style.display='block';runOcrOnImage(imgData);};}catch(e){showToast('Gagal akses kamera');modal.remove();}}

// ==================== WEBCAM ====================
async function initCamera(){try{const stream=await navigator.mediaDevices.getUserMedia({video:{width:640,height:480}});document.getElementById('webcam').srcObject=stream;cameraStream=stream;}catch(e){console.warn('Kamera tidak tersedia:',e);}}
function capturePhoto(){const video=document.getElementById('webcam'),canvas=document.getElementById('photoCanvas');canvas.width=video.videoWidth;canvas.height=video.videoHeight;canvas.getContext('2d').drawImage(video,0,0);capturedPhotoData=canvas.toDataURL('image/jpeg',0.8);document.getElementById('capturedPhoto').src=capturedPhotoData;document.getElementById('capturedPhoto').style.display='block';video.style.display='none';document.getElementById('retakeBtn').style.display='inline-flex';document.getElementById('captureBtn').innerHTML='✅ Tersimpan';playBeep();}
function retakePhoto(){capturedPhotoData=null;const w=document.getElementById('webcam'),c=document.getElementById('capturedPhoto');if(w)w.style.display='block';if(c)c.style.display='none';const rb=document.getElementById('retakeBtn'),cb=document.getElementById('captureBtn');if(rb)rb.style.display='none';if(cb)cb.innerHTML='📸 Ambil Foto';}
function playBeep(){if(settings.sound)try{const ctx=new AudioContext();const osc=ctx.createOscillator();osc.connect(ctx.destination);osc.frequency.value=880;osc.start();setTimeout(()=>osc.stop(),120);}catch(e){}}

// ==================== AUTOSAVE FORM ====================
function autosaveFormFields(){try{const data={visitorNo:document.getElementById('visitorNumberInput')?.value||'',nik:document.getElementById('nikInput')?.value||'',nama:document.getElementById('namaInput')?.value||'',hp:document.getElementById('hpInput')?.value||'',keperluan:document.getElementById('keperluanSelect')?.value||'',bagian:document.getElementById('bagianSelect')?.value||'',alamat:document.getElementById('alamatInput')?.value||'',instansi:document.getElementById('instansiInput')?.value||'',gender:document.getElementById('genderSelect')?.value||'',tempatLahir:document.getElementById('tempatLahirInput')?.value||'',tglLahir:document.getElementById('tglLahirInput')?.value||'',noPerkara:document.getElementById('noPerkara')?.value||'',catatan:document.getElementById('catatanInput')?.value||'',savedAt:Date.now()};if(data.nama||data.nik||data.visitorNo)localStorage.setItem(AUTOSAVE_KEY,JSON.stringify(data));}catch(e){}}
function restoreAutosave(){try{const raw=localStorage.getItem(AUTOSAVE_KEY);if(!raw)return;const d=JSON.parse(raw);const fieldMap={visitorNo:'visitorNumberInput',nik:'nikInput',nama:'namaInput',hp:'hpInput',alamat:'alamatInput',instansi:'instansiInput',tempatLahir:'tempatLahirInput',tglLahir:'tglLahirInput',noPerkara:'noPerkara',catatan:'catatanInput'};const selectMap={keperluan:'keperluanSelect',gender:'genderSelect',bagian:'bagianSelect'};Object.entries(fieldMap).forEach(([key,id])=>{const el=document.getElementById(id);if(el&&d[key])el.value=d[key];});Object.entries(selectMap).forEach(([key,id])=>{const el=document.getElementById(id);if(el&&d[key])el.value=d[key];});if(d.visitorNo){visitorNumberValue=d.visitorNo;onVisitorNumberInput(d.visitorNo);}if(d.nik)onNikInput(d.nik);showToast('✅ Data form dipulihkan');document.getElementById('autosaveBanner').classList.remove('show');}catch(e){showToast('Gagal pulihkan autosave');}}
function discardAutosave(){localStorage.removeItem(AUTOSAVE_KEY);document.getElementById('autosaveBanner').classList.remove('show');}
function checkAutosave(){try{const raw=localStorage.getItem(AUTOSAVE_KEY);if(!raw)return;const d=JSON.parse(raw);if(d&&(d.nama||d.nik||d.visitorNo)){const banner=document.getElementById('autosaveBanner');const timeEl=document.getElementById('autosaveBannerTime');if(timeEl&&d.savedAt){const t=new Date(d.savedAt);timeEl.innerText=`Tersimpan: ${t.toLocaleDateString('id-ID')} ${t.toLocaleTimeString('id-ID')} | Nama: ${d.nama||'-'}`;}banner.classList.add('show');}}catch(e){}}
setInterval(autosaveFormFields,5000);

// ==================== TABS ====================
function switchTab(tab,btn){
  document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById(`tab-${tab}`).classList.add('active');
  if(btn)btn.classList.add('active');
  activeTabId=tab;
  if(tab==='list') loadVisitors();
  if(tab==='report') initReportSelectors();
  if(tab==='settings'){loadSettingsUI();}
  if(tab==='register'&&settings.scanMode) setTimeout(()=>document.getElementById('visitorNumberInput').focus(),200);
}

function initReportSelectors(){
  const d=new Date();
  const mSel=document.getElementById('reportMonth');
  const ySel=document.getElementById('reportYear');
  if(mSel&&!mSel.innerHTML){mSel.innerHTML=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'].map((m,i)=>`<option value="${i}" ${i===d.getMonth()?'selected':''}>${m}</option>`).join('');ySel.innerHTML=[d.getFullYear(),d.getFullYear()-1,d.getFullYear()-2].map(y=>`<option>${y}</option>`).join('');}
  if(!document.getElementById('dailyReportDate').value)document.getElementById('dailyReportDate').value=d.toISOString().slice(0,10);
  generateDailyReport();
  if(mSel) generateMonthlyReport();
}

function formatDateID(dateStr){if(!dateStr)return '-';try{const d=new Date(dateStr+'T12:00:00');return d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});}catch(e){return dateStr;}}

// ==================== INIT ====================
(async function init(){
  loadSettings();
  initCamera();
  checkAutosave();
  updateScanMode();
  await loadVisitors();
  await loadStats();
  loadSettingsUI();
  setInterval(loadStats,30000);
  console.log('e-Tamu v4.0 PHP loaded');
})();
</script>
</body>
</html>
