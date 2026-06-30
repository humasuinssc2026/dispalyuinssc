<?php
// admin.php
@ini_set('upload_max_filesize', '1000');
@ini_set('post_max_size', '1000');
@ini_set('memory_limit', '1000');
@ini_set('max_execution_time', '3000');
session_start();

$ADMIN_PASSWORD = 'admin@uinssc';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

if (isset($_POST['login'])) {
    if ($_POST['password'] === $ADMIN_PASSWORD) {
        $_SESSION['is_admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Password salah!";
    }
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Admin Display</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    </head>
    <body class="bg-gradient-to-br from-green-900 to-green-700 h-screen flex items-center justify-center" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="bg-white p-8 rounded-2xl shadow-2xl w-96">
            <div class="text-center mb-6">
                <img src="https://uinssc.ac.id/wp-content/uploads/2024/08/logo-uin-siber-cirebon.png" class="h-16 mx-auto mb-3" onerror="this.style.display='none'">
                <h2 class="text-2xl font-bold text-green-800">Admin Display</h2>
                <p class="text-gray-500 text-sm">UIN Siber Syekh Nurjati Cirebon</p>
            </div>
            <?php if(isset($error)) echo "<p class='text-red-500 mb-4 text-center text-sm bg-red-50 p-2 rounded'>$error</p>"; ?>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password Admin</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <button type="submit" name="login" class="w-full bg-green-700 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-green-800 transition">Masuk</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$dataFile = __DIR__ . '/data.json';
$uploadDir = __DIR__ . '/uploads/';

$data = [];
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true);
}

// Initialize missing arrays
if (!isset($data['kegiatans'])) $data['kegiatans'] = [];
if (!isset($data['arsip_kegiatans'])) $data['arsip_kegiatans'] = [];
if (!isset($data['agenda_pimpinan'])) $data['agenda_pimpinan'] = [];
if (!isset($data['layanan_publik'])) $data['layanan_publik'] = [
    ['title' => 'Permohonan Informasi', 'link' => '#'],
    ['title' => 'Pengaduan', 'link' => '#'],
    ['title' => 'E-PPID', 'link' => '#'],
    ['title' => 'SOP Pelayanan', 'link' => '#'],
];
if (!isset($data['link_cepat'])) $data['link_cepat'] = [
    ['title' => 'PPID Online', 'url' => 'https://ppid.uinssc.ac.id'],
    ['title' => 'Website UIN', 'url' => 'https://uinssc.ac.id'],
    ['title' => 'Jurnal UIN', 'url' => 'https://jurnal.uinssc.ac.id'],
    ['title' => 'PMB Online', 'url' => 'https://pmb.uinssc.ac.id'],
];
if (!isset($data['settings'])) $data['settings'] = ['custom_running_text' => ''];
if (!isset($data['settings']['promos'])) $data['settings']['promos'] = [];
if (!isset($data['settings']['videos'])) $data['settings']['videos'] = [];
if (!isset($data['settings']['logo_url'])) $data['settings']['logo_url'] = '';
if (!isset($data['settings']['youtube_url'])) $data['settings']['youtube_url'] = '';

function getYouTubeId($url) {
    if (empty($url)) return '';
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/live\/)([^"&?\/\s]{11})/i', $url, $matches);
    return isset($matches[1]) ? $matches[1] : $url;
}

// Migrate old promo_url
if (isset($data['settings']['promo_url']) && !empty($data['settings']['promo_url'])) {
    $data['settings']['promos'][] = $data['settings']['promo_url'];
    unset($data['settings']['promo_url']);
}

// Auto-Archive Logic
$today = date('Y-m-d');
$needsSave = false;
foreach ($data['kegiatans'] as $k => $v) {
    if (isset($v['tanggal_sistem']) && !empty($v['tanggal_sistem']) && $v['tanggal_sistem'] < $today) {
        $data['arsip_kegiatans'][] = $v;
        unset($data['kegiatans'][$k]);
        $needsSave = true;
    }
}
if ($needsSave) {
    $data['kegiatans'] = array_values($data['kegiatans']);
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}

// Export CSV Logic
if (isset($_GET['export']) && $_GET['export'] === 'arsip') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Arsip_Kegiatan_' . date('Y-m-d_H-i') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Tanggal (Sistem)', 'Teks Tanggal', 'Waktu', 'Nama Kegiatan', 'Lokasi', 'Tipe']);
    foreach ($data['arsip_kegiatans'] as $row) {
        fputcsv($output, [
            $row['id'] ?? '',
            $row['tanggal_sistem'] ?? '',
            $row['tanggal'] ?? '',
            $row['waktu'] ?? '',
            $row['nama'] ?? '',
            $row['lokasi'] ?? '',
            $row['tipe'] ?? 'hari_ini'
        ]);
    }
    fclose($output);
    exit;
}

// Migrate old video_url
if (isset($data['settings']['video_url']) && !empty($data['settings']['video_url'])) {
    $data['settings']['videos'][] = $data['settings']['video_url'];
    unset($data['settings']['video_url']);
}

$message = '';
$messageType = 'green';

function handleUpload($fileInput, $allowedExts, $uploadDir) {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $name = basename($_FILES[$fileInput]['name']);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) return false;
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $newName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $name);
    $dest = $uploadDir . $newName;
    if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $dest)) {
        return 'uploads/' . $newName;
    }
    return false;
}

// SIMPAN SETTINGS (VIDEO/TEXT/PROMO)
if (isset($_POST['save_settings'])) {
    $data['settings']['custom_running_text'] = $_POST['custom_running_text'];
    
    if (isset($_POST['youtube_url'])) {
        $data['settings']['youtube_url'] = getYouTubeId($_POST['youtube_url']);
    }
    
    $videoResult = handleUpload('video_file', ['mp4', 'm4v', 'webm'], $uploadDir);
    if ($videoResult === false && isset($_FILES['video_file']) && $_FILES['video_file']['error'] !== 4) {
        $message = "Format video salah."; $messageType = 'red';
    } elseif ($videoResult) {
        $data['settings']['videos'][] = $videoResult;
    }
    
    $promoResult = handleUpload('promo_file', ['jpg', 'jpeg', 'png', 'webp'], $uploadDir);
    if ($promoResult === false && isset($_FILES['promo_file']) && $_FILES['promo_file']['error'] !== 4) {
        $message = "Format gambar promosi salah."; $messageType = 'red';
    } elseif ($promoResult) {
        $data['settings']['promos'][] = $promoResult;
    }
    
    $logoResult = handleUpload('logo_file', ['jpg', 'jpeg', 'png', 'webp'], $uploadDir);
    if ($logoResult === false && isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] !== 4) {
        $message = "Format gambar logo salah."; $messageType = 'red';
    } elseif ($logoResult) {
        $data['settings']['logo_url'] = $logoResult;
    }
    
    $message = "Pengaturan berhasil disimpan!";
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}

// HAPUS PROMO
if (isset($_GET['delete_promo']) && is_numeric($_GET['delete_promo'])) {
    $idx = (int)$_GET['delete_promo'];
    if (isset($data['settings']['promos'][$idx])) {
        array_splice($data['settings']['promos'], $idx, 1);
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        header('Location: admin.php');
        exit;
    }
}

// HAPUS VIDEO
if (isset($_GET['delete_video']) && is_numeric($_GET['delete_video'])) {
    $idx = (int)$_GET['delete_video'];
    if (isset($data['settings']['videos'][$idx])) {
        array_splice($data['settings']['videos'], $idx, 1);
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        header('Location: admin.php');
        exit;
    }
}

// HAPUS LAYANAN
if (isset($_GET['delete_layanan']) && is_numeric($_GET['delete_layanan'])) {
    $idx = (int)$_GET['delete_layanan'];
    if (isset($data['layanan_publik'][$idx])) {
        array_splice($data['layanan_publik'], $idx, 1);
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        header('Location: admin.php');
        exit;
    }
}

// TAMBAH LAYANAN PUBLIK
if (isset($_POST['add_layanan'])) {
    $layananIcon = handleUpload('layanan_icon', ['jpg', 'jpeg', 'png', 'webp', 'svg'], $uploadDir);
    if ($layananIcon === false && isset($_FILES['layanan_icon']) && $_FILES['layanan_icon']['error'] !== 4) {
        $message = "Format ikon layanan salah."; $messageType = 'red';
    } else {
        $data['layanan_publik'][] = [
            'title' => $_POST['layanan_title'],
            'link' => $_POST['layanan_link'],
            'icon_url' => $layananIcon ? $layananIcon : ''
        ];
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        $message = "Layanan publik berhasil ditambahkan!";
    }
}

// SIMPAN TAUTAN (LINK CEPAT)
if (isset($_POST['save_links'])) {
    for ($i = 0; $i < 4; $i++) {
        if (isset($_POST['linkcepat_title'][$i])) {
            $data['link_cepat'][$i]['title'] = $_POST['linkcepat_title'][$i];
            $data['link_cepat'][$i]['url'] = $_POST['linkcepat_url'][$i];
        }
    }
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    $message = "Tautan Cepat berhasil disimpan!";
}

// TAMBAH KEGIATAN
if (isset($_POST['add_kegiatan'])) {
    $newId = count($data['kegiatans']) > 0 ? max(array_column($data['kegiatans'], 'id')) + 1 : 1;
    $newKegiatan = [
        'id'            => $newId,
        'tanggal_sistem'=> $_POST['tanggal_sistem'] ?? '',
        'tanggal'       => $_POST['tanggal'],
        'waktu'         => $_POST['waktu'],
        'nama'          => $_POST['nama'],
        'lokasi'        => $_POST['lokasi'],
        'tipe'          => $_POST['tipe'] ?? 'hari_ini'
    ];
    $data['kegiatans'][] = $newKegiatan;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    $message = "Kegiatan berhasil ditambahkan!";
}
if (isset($_GET['delete_kegiatan'])) {
    $id = (int)$_GET['delete_kegiatan'];
    $data['kegiatans'] = array_values(array_filter($data['kegiatans'], fn($k) => $k['id'] !== $id));
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    header("Location: admin.php?msg=deleted");
    exit;
}
if (isset($_GET['clear_arsip'])) {
    $data['arsip_kegiatans'] = [];
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    header("Location: admin.php?msg=arsip_cleared");
    exit;
}

// TAMBAH AGENDA PIMPINAN
if (isset($_POST['add_agenda'])) {
    $newId = count($data['agenda_pimpinan']) > 0 ? max(array_column($data['agenda_pimpinan'], 'id')) + 1 : 1;
    $data['agenda_pimpinan'][] = [
        'id'      => $newId,
        'jabatan' => $_POST['jabatan'],
        'kegiatan'=> $_POST['kegiatan'],
        'lokasi'  => $_POST['lokasi'],
        'tipe'    => $_POST['tipe_agenda'] ?? 'hari_ini'
    ];
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    $message = "Agenda Pimpinan berhasil ditambahkan!";
}
if (isset($_GET['delete_agenda'])) {
    $id = (int)$_GET['delete_agenda'];
    $data['agenda_pimpinan'] = array_values(array_filter($data['agenda_pimpinan'], fn($a) => $a['id'] !== $id));
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
    header("Location: admin.php?msg=deleted");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-green-800 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <?php $adminLogo = !empty($data['settings']['logo_url']) ? $data['settings']['logo_url'] : 'https://upload.wikimedia.org/wikipedia/commons/2/23/Logo-UINSSC-696x858.png'; ?>
                <img src="<?php echo htmlspecialchars($adminLogo); ?>" class="h-10 bg-white rounded-lg px-1 object-contain">
                <div>
                    <h1 class="text-lg font-bold leading-none">Admin Display Informasi</h1>
                    <p class="text-green-300 text-xs">UIN Siber Syekh Nurjati Cirebon</p>
                </div>
            </div>
            <div class="space-x-3">
                <a href="index.php" target="_blank" class="bg-yellow-500 hover:bg-yellow-400 text-green-900 font-bold px-4 py-2 rounded-lg text-sm transition">▶ Lihat Display</a>
                <a href="?logout=true" class="bg-red-600 hover:bg-red-500 px-4 py-2 rounded-lg font-semibold text-sm transition">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <?php if($message): ?>
        <div class="bg-<?php echo $messageType; ?>-100 border-l-4 border-<?php echo $messageType; ?>-500 text-<?php echo $messageType; ?>-700 p-4 mb-6 rounded shadow-sm">
            <p class="font-semibold"><?php echo $message; ?></p>
        </div>
        <?php endif; ?>

        <!-- ROW 1: Settings & Links -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <!-- Video & Running Text -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="w-1.5 h-5 bg-blue-500 rounded-full"></span> Video & Teks Berjalan</h2>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tautan YouTube Live / Video (Prioritas Utama)</label>
                        <input type="text" name="youtube_url" value="<?php echo htmlspecialchars($data['settings']['youtube_url'] ?? ''); ?>" placeholder="Contoh: https://youtube.com/live/xxxxxx" class="w-full text-sm border p-2 rounded-lg mb-4 text-red-600 font-semibold focus:outline-red-500">
                        <p class="text-xs text-gray-500 mb-4 -mt-2">Kosongkan kolom ini jika ingin kembali memutar daftar video (MP4) di bawah ini.</p>
                        
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tambah Video Layar Utama (MP4)</label>
                        <input type="file" name="video_file" accept="video/mp4,video/webm" class="w-full text-sm border p-2 rounded-lg mb-2">
                        
                        <?php if(!empty($data['settings']['videos'])): ?>
                            <div class="space-y-2 mt-2">
                                <p class="text-xs font-semibold text-gray-500">Video Tersimpan (Akan berputar otomatis jika YouTube kosong):</p>
                                <?php foreach($data['settings']['videos'] as $idx => $video): ?>
                                <div class="flex items-center justify-between bg-gray-50 p-2 rounded border text-xs">
                                    <span class="truncate w-3/4" title="<?php echo $video; ?>">#<?php echo $idx + 1; ?> - <?php echo basename($video); ?></span>
                                    <a href="?delete_video=<?php echo $idx; ?>" class="text-red-500 hover:text-red-700 font-bold" onclick="return confirm('Hapus video ini?')">Hapus</a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Logo Header (Kiri Atas)</label>
                        <?php if(!empty($data['settings']['logo_url'])): ?>
                            <div class="mb-2 bg-gray-100 p-2 rounded-lg inline-block">
                                <img src="<?php echo htmlspecialchars($data['settings']['logo_url']); ?>" class="h-10 object-contain">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="logo_file" accept="image/jpeg,image/png,image/webp" class="w-full text-sm border p-2 rounded-lg mb-4">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tambah Flayer Promosi (JPG/PNG)</label>
                        <input type="file" name="promo_file" accept="image/jpeg,image/png,image/webp" class="w-full text-sm border p-2 rounded-lg mb-2">
                        
                        <?php if(!empty($data['settings']['promos'])): ?>
                            <div class="space-y-2 mt-2">
                                <p class="text-xs font-semibold text-gray-500">Flayer Tersimpan (Akan berputar otomatis):</p>
                                <?php foreach($data['settings']['promos'] as $idx => $promo): ?>
                                <div class="flex items-center justify-between bg-gray-50 p-2 rounded border text-xs">
                                    <span class="truncate w-3/4" title="<?php echo $promo; ?>">#<?php echo $idx + 1; ?> - <?php echo basename($promo); ?></span>
                                    <a href="?delete_promo=<?php echo $idx; ?>" class="text-red-500 hover:text-red-700 font-bold" onclick="return confirm('Hapus flayer ini?')">Hapus</a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Teks Berjalan Khusus</label>
                        <textarea name="custom_running_text" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm"><?php echo htmlspecialchars($data['settings']['custom_running_text']); ?></textarea>
                    </div>
                    <button type="submit" name="save_settings" class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700">Simpan Pengaturan</button>
                </form>
            </div>

            <!-- Tautan (Link Cepat) -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="w-1.5 h-5 bg-purple-500 rounded-full"></span> Link Cepat / QR Code</h2>
                <form method="POST" class="grid grid-cols-1 gap-4">
                    <?php for($i=0; $i<4; $i++): ?>
                    <div class="bg-gray-50 p-3 rounded border">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">Slot <?php echo $i+1; ?></label>
                        <input type="text" name="linkcepat_title[]" value="<?php echo htmlspecialchars($data['link_cepat'][$i]['title']); ?>" class="w-full text-xs p-1.5 border rounded mt-1 mb-1" placeholder="Judul QR">
                        <input type="text" name="linkcepat_url[]" value="<?php echo htmlspecialchars($data['link_cepat'][$i]['url']); ?>" class="w-full text-xs p-1.5 border rounded" placeholder="URL Target QR">
                    </div>
                    <?php endfor; ?>
                    <button type="submit" name="save_links" class="w-full bg-purple-600 text-white font-bold py-2.5 rounded-lg hover:bg-purple-700">Simpan Link Cepat</button>
                </form>
            </div>

            <!-- Layanan Publik Dinamis -->
            <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="w-1.5 h-5 bg-teal-500 rounded-full"></span> Layanan Publik (Ber-Ikon)</h2>
                
                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-3 mb-6 bg-teal-50 p-4 rounded-lg border border-teal-100">
                    <input type="text" name="layanan_title" required placeholder="Judul Layanan (cth: PPID)" class="p-2 border rounded text-sm">
                    <input type="text" name="layanan_link" placeholder="URL Target (Opsional)" class="p-2 border rounded text-sm">
                    <div class="col-span-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase block mb-1">Ikon Layanan (PNG transparan / SVG disarankan)</label>
                        <input type="file" name="layanan_icon" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="w-full text-sm border p-2 rounded bg-white">
                    </div>
                    <button type="submit" name="add_layanan" class="col-span-2 bg-teal-600 text-white font-bold py-2 rounded hover:bg-teal-700 transition">Tambah Layanan</button>
                </form>
                
                <div class="flex-1 overflow-y-auto max-h-[300px] border rounded-lg">
                    <?php if(empty($data['layanan_publik'])): ?>
                        <div class="p-4 text-center text-sm text-gray-400">Belum ada layanan.</div>
                    <?php else: ?>
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b sticky top-0">
                                <tr>
                                    <th class="p-3 font-semibold">Ikon</th>
                                    <th class="p-3 font-semibold">Layanan</th>
                                    <th class="p-3 font-semibold text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['layanan_publik'] as $idx => $lay): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3">
                                        <?php if(!empty($lay['icon_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($lay['icon_url']); ?>" class="h-8 w-8 object-contain">
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">Tanpa Ikon</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3">
                                        <div class="font-bold"><?php echo htmlspecialchars($lay['title']); ?></div>
                                        <div class="text-[10px] text-gray-500 truncate max-w-[150px]"><?php echo htmlspecialchars($lay['link'] ?? ''); ?></div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <a href="?delete_layanan=<?php echo $idx; ?>" class="text-red-500 hover:text-red-700 font-bold" onclick="return confirm('Hapus layanan ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ROW 2: Kegiatan & Agenda Pimpinan -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Jadwal Kegiatan -->
            <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="w-1.5 h-5 bg-green-500 rounded-full"></span> Jadwal Kegiatan (Kalender)</h2>
                
                <form method="POST" class="grid grid-cols-2 gap-3 mb-6 bg-green-50 p-4 rounded-lg border border-green-100">
                    <div class="col-span-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">Tgl Berakhir (Patokan Arsip Otomatis)</label>
                        <input type="date" name="tanggal_sistem" required class="w-full p-2 border rounded text-sm bg-white mt-1">
                    </div>
                    <input type="text" name="tanggal" required placeholder="Teks Tgl (Tampilan, cth: 10 Ags)" class="p-2 border rounded text-sm">
                    <input type="text" name="waktu" required placeholder="Waktu (cth: 08:00 - Selesai)" class="p-2 border rounded text-sm">
                    <input type="text" name="nama" required placeholder="Nama Kegiatan Utama" class="col-span-2 p-2 border rounded text-sm">
                    <input type="text" name="lokasi" required placeholder="Lokasi (cth: Aula Utama)" class="p-2 border rounded text-sm">
                    <select name="tipe" class="p-2 border rounded text-sm bg-white">
                        <option value="hari_ini">Hari Ini</option>
                        <option value="mendatang">Agenda Mendatang</option>
                    </select>
                    <button type="submit" name="add_kegiatan" class="col-span-2 bg-green-600 text-white font-bold py-2 rounded">Tambah Kegiatan</button>
                </form>
                
                <div class="flex-1 overflow-y-auto max-h-[300px] border rounded-lg">
                    <?php if(empty($data['kegiatans'])): ?>
                        <div class="p-4 text-center text-sm text-gray-400">Belum ada kegiatan.</div>
                    <?php else: ?>
                        <?php foreach(array_reverse($data['kegiatans']) as $k): ?>
                        <div class="flex justify-between items-center p-3 border-b hover:bg-gray-50">
                            <div>
                                <div class="font-bold text-sm text-gray-900">
                                    <?php if(isset($k['tipe']) && $k['tipe'] === 'mendatang'): ?>
                                        <span class="bg-blue-100 text-blue-800 text-[10px] px-2 py-0.5 rounded mr-1">Mendatang</span>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($k['nama']); ?>
                                </div>
                                <div class="text-xs text-gray-600"><?php echo htmlspecialchars($k['tanggal']); ?> | <?php echo htmlspecialchars($k['waktu']); ?> | <?php echo htmlspecialchars($k['lokasi']); ?></div>
                            </div>
                            <a href="?delete_kegiatan=<?php echo $k['id']; ?>" class="text-red-500 text-xs font-bold px-2 py-1 bg-red-50 hover:bg-red-100 rounded ml-2">Hapus</a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Agenda Pimpinan -->
            <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="w-1.5 h-5 bg-yellow-500 rounded-full"></span> Agenda Pimpinan Hari Ini</h2>
                
                <form method="POST" class="grid grid-cols-1 gap-3 mb-6 bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                    <input type="text" name="jabatan" required placeholder="Jabatan (cth: Rektor, Wakil Rektor I)" class="p-2 border rounded text-sm">
                    <input type="text" name="kegiatan" required placeholder="Nama Agenda Pimpinan" class="p-2 border rounded text-sm">
                    <input type="text" name="lokasi" required placeholder="Lokasi Ruangan/Tempat" class="p-2 border rounded text-sm">
                    <select name="tipe_agenda" class="p-2 border rounded text-sm bg-white">
                        <option value="hari_ini">Hari Ini</option>
                        <option value="mendatang">Agenda Mendatang</option>
                    </select>
                    <button type="submit" name="add_agenda" class="bg-yellow-600 text-white font-bold py-2 rounded">Tambah Agenda Pimpinan</button>
                </form>
                
                <div class="flex-1 overflow-y-auto max-h-[300px] border rounded-lg">
                    <?php if(empty($data['agenda_pimpinan'])): ?>
                        <div class="p-4 text-center text-sm text-gray-400">Belum ada agenda pimpinan.</div>
                    <?php else: ?>
                        <?php foreach($data['agenda_pimpinan'] as $a): ?>
                        <div class="flex justify-between items-center p-3 border-b hover:bg-gray-50">
                            <div>
                                <div class="font-bold text-sm text-gray-900">
                                    <?php if(isset($a['tipe']) && $a['tipe'] === 'mendatang'): ?>
                                        <span class="bg-blue-100 text-blue-800 text-[10px] px-2 py-0.5 rounded mr-1">Mendatang</span>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($a['jabatan']); ?>
                                </div>
                                <div class="text-xs text-gray-600"><?php echo htmlspecialchars($a['kegiatan']); ?> - <?php echo htmlspecialchars($a['lokasi']); ?></div>
                            </div>
                            <a href="?delete_agenda=<?php echo $a['id']; ?>" class="text-red-500 text-xs font-bold px-2 py-1 bg-red-50 hover:bg-red-100 rounded ml-2">Hapus</a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
                </div>
            </div>

        </div>

        <!-- ROW 3: Arsip Kegiatan -->
        <div class="mt-6 bg-white rounded-xl shadow-sm border p-6 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold flex items-center gap-2"><span class="w-1.5 h-5 bg-gray-500 rounded-full"></span> 🗃️ Arsip Kegiatan (Sudah Lewat)</h2>
                <div class="flex gap-2">
                    <a href="?export=arsip" class="bg-blue-600 text-white text-xs font-bold py-2 px-4 rounded hover:bg-blue-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ekspor Excel
                    </a>
                    <a href="?clear_arsip=1" class="bg-red-100 text-red-600 text-xs font-bold py-2 px-4 rounded hover:bg-red-200" onclick="return confirm('Yakin ingin menghapus permanen semua arsip?')">Bersihkan Arsip</a>
                </div>
            </div>
            
            <div class="overflow-x-auto border rounded-lg max-h-[300px]">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 sticky top-0 border-b">
                        <tr>
                            <th class="p-3 font-semibold text-gray-600">ID</th>
                            <th class="p-3 font-semibold text-gray-600">Patokan Tgl</th>
                            <th class="p-3 font-semibold text-gray-600">Kegiatan</th>
                            <th class="p-3 font-semibold text-gray-600">Lokasi & Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php if(empty($data['arsip_kegiatans'])): ?>
                            <tr><td colspan="4" class="p-4 text-center text-gray-400">Arsip kosong.</td></tr>
                        <?php else: ?>
                            <?php foreach(array_reverse($data['arsip_kegiatans']) as $arsip): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 text-gray-500">#<?php echo $arsip['id'] ?? '-'; ?></td>
                                <td class="p-3 font-semibold text-gray-700"><?php echo htmlspecialchars($arsip['tanggal_sistem'] ?? '-'); ?></td>
                                <td class="p-3">
                                    <div class="font-bold text-gray-900"><?php echo htmlspecialchars($arsip['nama'] ?? '-'); ?></div>
                                    <div class="text-[10px] text-gray-500"><?php echo htmlspecialchars($arsip['tanggal'] ?? '-'); ?></div>
                                </td>
                                <td class="p-3 text-xs text-gray-600">
                                    <?php echo htmlspecialchars($arsip['lokasi'] ?? '-'); ?><br>
                                    <span class="text-gray-400"><?php echo htmlspecialchars($arsip['waktu'] ?? '-'); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
