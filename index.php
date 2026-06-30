<?php
// index.php
$dataFile = __DIR__ . '/data.json';
$data = [];
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true);
}

$settings = $data['settings'] ?? [];
$kegiatans = $data['kegiatans'] ?? [];
$agenda_pimpinan = $data['agenda_pimpinan'] ?? [];
$layanan_publik = $data['layanan_publik'] ?? [
    ['title' => 'Permohonan Informasi', 'link' => '#'],
    ['title' => 'Pengaduan', 'link' => '#'],
    ['title' => 'E-PPID', 'link' => '#'],
    ['title' => 'SOP Pelayanan', 'link' => '#'],
];
$link_cepat = $data['link_cepat'] ?? [
    ['title' => 'PPID Online', 'url' => 'https://ppid.uinssc.ac.id'],
    ['title' => 'Website UIN', 'url' => 'https://uinssc.ac.id'],
    ['title' => 'Jurnal UIN', 'url' => 'https://jurnal.uinssc.ac.id'],
    ['title' => 'PMB Online', 'url' => 'https://pmb.uinssc.ac.id'],
];

$youtubeUrl = $settings['youtube_url'] ?? '';

$videos = $settings['videos'] ?? [];
if (empty($videos) && !empty($settings['video_url'])) $videos[] = $settings['video_url'];

$promos = $settings['promos'] ?? [];
if (empty($promos) && !empty($settings['promo_url'])) $promos[] = $settings['promo_url'];
if (empty($promos)) $promos[] = 'https://upload.wikimedia.org/wikipedia/commons/2/23/Logo-UINSSC-696x858.png';
$customText = $settings['custom_running_text'] ?? '';
$headerLogo = !empty($settings['logo_url']) ? $settings['logo_url'] : 'https://upload.wikimedia.org/wikipedia/commons/2/23/Logo-UINSSC-696x858.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Informasi UIN Siber Syekh Nurjati Cirebon</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#052e16',
                        secondary: '#0f3c20',
                        accent: '#fbbf24',
                        light: '#e2e8f0',
                    }
                }
            }
        }
    </script>
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #052614; /* Very dark green */
            color: white;
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
        }

        .panel {
            background-color: rgba(10, 48, 25, 0.6);
            border: 1px solid rgba(251, 191, 36, 0.2);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .panel-header {
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid rgba(251, 191, 36, 0.2);
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .marquee-container {
            overflow: hidden;
            white-space: nowrap;
            flex-grow: 1;
        }
        .marquee-content {
            display: inline-block;
            animation: marquee 60s linear infinite;
            padding-left: 100%;
        }
        @keyframes marquee {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-100%, 0); }
        }

        .news-card-img {
            aspect-ratio: 16/9;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }
    </style>
</head>
<body x-data="uinsscDisplay()">

    <header class="h-20 w-full flex items-center justify-between px-6 bg-white border-b-4 border-accent z-10 shrink-0 relative">
        <div class="flex items-center gap-4 h-full py-2">
            <img src="<?php echo htmlspecialchars($headerLogo); ?>" alt="Logo UIN SSC" class="h-full object-contain drop-shadow-sm">
            <div class="border-l-2 border-gray-300 pl-4 h-full flex flex-col justify-center">
                <h1 class="text-3xl font-extrabold text-[#0a2e1f] tracking-tight leading-none uppercase">PPID</h1>
                <p class="text-[#dda239] font-bold text-sm tracking-widest uppercase mt-0.5">Pusat Layanan Informasi Publik</p>
            </div>
        </div>
        
        <!-- Center Title -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center">
            <h2 class="text-xl lg:text-2xl font-black text-[#0a2e1f] tracking-wider uppercase text-center opacity-90 drop-shadow-sm">UIN Siber Syekh Nurjati Cirebon</h2>
        </div>
        
        <div class="flex items-center gap-8 h-full py-2 text-primary">
            <div class="text-right">
                <div class="text-4xl font-extrabold tracking-tight text-accent" x-text="currentTime">00:00:00</div>
                <div class="text-sm font-bold uppercase tracking-wide text-primary" x-text="currentDate">HARI, DD BULAN YYYY</div>
            </div>
            <div class="flex items-center gap-3">
                <svg class="w-10 h-10 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.759-2.159 4.5 4.5 0 11-1.385 8.948A.5.5 0 0111 16H5.5z"></path></svg>
                <div class="text-right border-r-2 border-gray-300 pr-4 mr-1">
                    <div class="text-xl font-bold">28°C</div>
                    <div class="text-xs font-semibold">Cirebon</div>
                </div>
                <!-- Fullscreen Button -->
                <button @click="toggleFullscreen()" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full text-primary transition shadow-sm" title="Toggle Fullscreen">
                    <svg x-show="!isFullscreen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                    <svg x-show="isFullscreen" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h4v4m0-4l-5 5m15-15l-5 5m0 0V4m0 4h4M8 10l-5-5m13 5h4m-4 0v-4"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="flex-1 flex gap-4 p-4 min-h-0 overflow-hidden">
        
        <!-- Left Column (Grid spans) -->
        <div class="w-[65%] flex flex-col gap-4 h-full">
            
            <!-- Top Row: Video & Promosi -->
            <div class="flex gap-4 h-[55%]">
                <!-- Video Player / YouTube Live -->
                <div class="panel flex-1 relative overflow-hidden rounded-xl border border-accent/40 shadow-lg bg-black">
                    <!-- YouTube Player -->
                    <iframe x-show="youtubeUrl" class="w-full h-full absolute inset-0 z-10" :src="youtubeUrl ? 'https://www.youtube.com/embed/' + youtubeUrl + '?autoplay=1&mute=0&loop=1&playlist=' + youtubeUrl + '&controls=0&showinfo=0&rel=0' : ''" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    
                    <!-- LIVE Badge -->
                    <div x-show="youtubeUrl" class="absolute top-4 left-4 z-30 flex items-center gap-2 bg-red-600/90 backdrop-blur-sm px-3 py-1.5 rounded-md shadow-lg border border-red-500/50">
                        <div class="w-2.5 h-2.5 bg-white rounded-full animate-pulse"></div>
                        <span class="text-white font-bold tracking-widest uppercase text-xs">LIVE</span>
                    </div>
                    
                    <!-- Local Video Player -->
                    <video x-show="!youtubeUrl" x-ref="mainVid" id="mainVideo" class="w-full h-full object-cover absolute inset-0" autoplay playsinline src="<?php echo !empty($videos) ? htmlspecialchars($videos[0]) : ''; ?>" :src="videos[activeVideoIndex]" @ended="nextVideo()" x-init="setTimeout(() => { $el.play().catch(e => console.log('Autoplay error:', e)) }, 500); $el.volume = videoVolume / 100;">
                        Your browser does not support the video tag.
                    </video>

                    <!-- Speaker Overlay -->
                    <div class="absolute bottom-4 right-4 z-20 group flex items-center gap-2" x-data="{ showVol: false }" @mouseenter="showVol = true" @mouseleave="showVol = false">
                        <input type="range" min="0" max="100" x-model="videoVolume" x-show="showVol" x-transition @input="$refs.mainVid.volume = videoVolume / 100; if(videoVolume > 0) videoMuted = false;" class="w-24 h-1 bg-white rounded-lg appearance-none cursor-pointer accent-accent">
                        <button @click="videoMuted = !videoMuted; $refs.mainVid.muted = videoMuted" class="w-10 h-10 bg-black/60 backdrop-blur rounded-full flex items-center justify-center cursor-pointer hover:bg-black/80 transition shadow-lg">
                            <svg x-show="!videoMuted && videoVolume > 0" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"></path></svg>
                            <svg x-show="videoMuted || videoVolume == 0" class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 0L15 8.586l1.293-1.293a1 1 0 111.414 1.414L16.414 10l1.293 1.293a1 1 0 01-1.414 1.414L15 11.414l-1.293 1.293a1 1 0 01-1.414-1.414L13.586 10l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Popup Promosi / Flayer -->
                <div class="w-[280px] aspect-[1080/1350] shrink-0 relative my-auto hover:scale-105 transition-transform duration-500 overflow-hidden rounded-2xl border-4 border-accent shadow-[0_20px_50px_rgba(0,0,0,0.7)] bg-white p-1">
                    <template x-for="(promo, index) in promos" :key="index">
                        <img :src="promo" 
                             x-show="activePromoIndex === index"
                             x-transition:enter="transition ease-out duration-700"
                             x-transition:enter-start="opacity-0 transform translate-x-full"
                             x-transition:enter-end="opacity-100 transform translate-x-0"
                             x-transition:leave="transition ease-in duration-700 absolute inset-0"
                             x-transition:leave-start="opacity-100 transform translate-x-0"
                             x-transition:leave-end="opacity-0 transform -translate-x-full"
                             class="w-full h-full object-cover rounded-xl"
                             style="position: absolute; top: 4px; left: 4px; width: calc(100% - 8px); height: calc(100% - 8px);">
                    </template>
                </div>
            </div>

            <!-- Middle Row (Deskripsi Kegiatan & Layanan Publik) -->
            <div class="flex gap-4 h-[25%]">
                <!-- Deskripsi Kegiatan -->
                <div class="panel p-4 flex-1 flex flex-col">
                    <div class="panel-header">
                        <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                        <h2 class="text-sm font-bold tracking-wide uppercase">Deskripsi Kegiatan Hari Ini</h2>
                    </div>
                    <div class="flex-1 flex gap-4">
                        <div class="flex-1 overflow-hidden" x-show="kegiatans.length > 0">
                            <h3 class="text-xl font-bold text-accent mb-1 line-clamp-1" x-text="kegiatans[0]?.nama">Nama Kegiatan</h3>
                            <p class="text-sm text-gray-300 mb-2 line-clamp-2" x-text="kegiatans[0]?.deskripsi || 'Tidak ada deskripsi.'"></p>
                            <p class="text-xs text-gray-400">Kegiatan berlangsung pukul <span x-text="kegiatans[0]?.waktu"></span> di <span x-text="kegiatans[0]?.lokasi"></span>.</p>
                        </div>
                        <div class="w-24 shrink-0 flex items-center justify-center" x-show="kegiatans.length > 0">
                            <!-- SVG Notebook Icon -->
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div x-show="kegiatans.length === 0" class="flex-1 flex items-center justify-center text-sm text-gray-400 italic">
                            Tidak ada kegiatan hari ini.
                        </div>
                    </div>
                </div>

                <!-- Layanan Publik -->
                <div class="panel p-4 flex-1 flex flex-col relative">
                    <div class="panel-header">
                        <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                        <h2 class="text-sm font-bold tracking-wide uppercase">Layanan Publik</h2>
                    </div>
                    <div class="flex-1 grid grid-cols-4 gap-2">
                        <a :href="layananPublik[0]?.link" class="flex flex-col items-center justify-center text-center gap-1 border border-accent/20 rounded-lg p-2 bg-black/20 hover:bg-accent/10 transition">
                            <div class="bg-white rounded-full p-2 mb-1"><svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg></div>
                            <span class="text-[10px] font-bold" x-text="layananPublik[0]?.title"></span>
                        </a>
                        <a :href="layananPublik[1]?.link" class="flex flex-col items-center justify-center text-center gap-1 border border-accent/20 rounded-lg p-2 bg-black/20 hover:bg-accent/10 transition">
                            <div class="bg-white rounded-full p-2 mb-1"><svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"></path></svg></div>
                            <span class="text-[10px] font-bold" x-text="layananPublik[1]?.title"></span>
                        </a>
                        <a :href="layananPublik[2]?.link" class="flex flex-col items-center justify-center text-center gap-1 border border-accent/20 rounded-lg p-2 bg-black/20 hover:bg-accent/10 transition">
                            <div class="bg-white rounded-lg p-2 mb-1"><svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path></svg></div>
                            <span class="text-[10px] font-bold" x-text="layananPublik[2]?.title"></span>
                        </a>
                        <a :href="layananPublik[3]?.link" class="flex flex-col items-center justify-center text-center gap-1 border border-accent/20 rounded-lg p-2 bg-black/20 hover:bg-accent/10 transition">
                            <div class="bg-white rounded-lg p-2 mb-1"><svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg></div>
                            <span class="text-[10px] font-bold" x-text="layananPublik[3]?.title"></span>
                        </a>
                    </div>
                    <div class="absolute -bottom-3 left-1/2 -translate-x-1/2">
                        <span class="bg-accent text-primary text-xs font-bold px-3 py-1 rounded-full shadow-lg border border-accent whitespace-nowrap">Selengkapnya →</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Row (Berita Pimpinan) -->
            <div class="panel p-4 h-[20%] flex flex-col">
                <div class="panel-header justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path></svg>
                        <h2 class="text-sm font-bold tracking-wide uppercase">Berita Pimpinan & Kelembagaan</h2>
                    </div>
                    <a href="#" class="text-xs text-accent hover:text-white transition">Lihat Semua →</a>
                </div>
                
                <div class="flex-1 overflow-hidden relative group">
                    <template x-if="newsItems.length === 0">
                        <div class="absolute inset-0 flex items-center justify-center text-sm text-gray-400">Memuat berita...</div>
                    </template>
                    <div class="flex h-full absolute animate-marquee-news items-center group-hover:[animation-play-state:paused]"
                         x-show="newsItems.length > 0">
                        <!-- Set 1 -->
                        <div class="flex gap-4 pr-4 shrink-0 h-full items-center">
                            <template x-for="(news, index) in newsItems" :key="'orig-'+index">
                                <div class="bg-black/30 rounded-lg p-2 flex gap-3 border border-accent/10 h-[90%] w-[350px] shrink-0 overflow-hidden hover:border-accent/30 transition cursor-pointer">
                                    <img :src="news.image" class="w-1/3 object-cover rounded shadow-sm bg-black" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/2/23/Logo-UINSSC-696x858.png'">
                                    <div class="flex-1 flex flex-col py-1">
                                        <div class="text-[9px] text-gray-400 mb-0.5" x-text="news.date"></div>
                                        <h3 class="text-xs font-bold leading-tight mb-1 line-clamp-3 group-hover:text-accent transition" x-text="news.title"></h3>
                                        <div class="mt-auto text-[9px] text-accent font-semibold inline-flex items-center">
                                            Baca Selengkapnya <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <!-- Set 2 for seamless loop -->
                        <div class="flex gap-4 pr-4 shrink-0 h-full items-center">
                            <template x-for="(news, index) in newsItems" :key="'dup-'+index">
                                <div class="bg-black/30 rounded-lg p-2 flex gap-3 border border-accent/10 h-[90%] w-[350px] shrink-0 overflow-hidden hover:border-accent/30 transition cursor-pointer">
                                    <img :src="news.image" class="w-1/3 object-cover rounded shadow-sm bg-black" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/2/23/Logo-UINSSC-696x858.png'">
                                    <div class="flex-1 flex flex-col py-1">
                                        <div class="text-[9px] text-gray-400 mb-0.5" x-text="news.date"></div>
                                        <h3 class="text-xs font-bold leading-tight mb-1 line-clamp-3 group-hover:text-accent transition" x-text="news.title"></h3>
                                        <div class="mt-auto text-[9px] text-accent font-semibold inline-flex items-center">
                                            Baca Selengkapnya <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Grid spans) -->
        <div class="w-[35%] flex flex-col gap-4 h-full">
            
            <!-- Kalender & Agenda Top Section -->
            <div class="panel p-4 h-[55%] flex flex-col overflow-hidden">
                <div class="flex gap-4 h-full">
                    
                    <!-- Left: Kalender -->
                    <div class="w-1/2 flex flex-col border-r border-accent/20 pr-4">
                        <div class="panel-header mb-2">
                            <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                            <h2 class="text-xs font-bold tracking-wide uppercase">Kalender Kegiatan</h2>
                        </div>
                        
                        <!-- Calendar Widget -->
                        <div class="bg-black/20 rounded-lg p-3 flex-1 flex flex-col">
                            <div class="flex justify-between items-center mb-3">
                                <button class="w-6 h-6 rounded-full bg-accent/20 text-accent flex items-center justify-center hover:bg-accent/40"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                                <div class="text-sm font-bold uppercase tracking-widest" x-text="calendarMonth">JUNI 2026</div>
                                <button class="w-6 h-6 rounded-full bg-accent/20 text-accent flex items-center justify-center hover:bg-accent/40"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold mb-2">
                                <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center text-xs flex-1">
                                <template x-for="day in calendarDays">
                                    <div class="flex items-center justify-center rounded-md p-1 font-semibold"
                                         :class="{
                                            'text-gray-500': !day.isCurrentMonth,
                                            'bg-accent text-primary shadow-sm': day.isToday,
                                            'hover:bg-white/10': !day.isToday && day.isCurrentMonth
                                         }"
                                         x-text="day.date">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right: Agenda List -->
                    <div class="w-1/2 flex flex-col pl-2">
                        <div class="panel-header mb-2">
                            <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                            <h2 class="text-xs font-bold tracking-wide uppercase">Agenda Kegiatan</h2>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto pr-1 custom-scrollbar flex flex-col">
                            <!-- Hari Ini -->
                            <div class="flex items-center gap-2 mb-2">
                                <div class="h-px bg-accent/30 w-4"></div>
                                <div class="text-[10px] text-accent font-semibold">Hari Ini</div>
                            </div>
                            <div class="space-y-3 mb-4">
                                <template x-for="kegiatan in kegiatans.filter(k => k.tipe !== 'mendatang')" :key="'hariini-'+kegiatan.id">
                                    <div class="relative pl-4">
                                        <div class="absolute left-0 top-1 w-2 h-2 rounded-full border-2 border-accent bg-transparent"></div>
                                        <div class="absolute left-[3px] top-3 bottom-[-12px] w-px border-l border-dashed border-accent/50 last-of-type:hidden"></div>
                                        <div class="text-[10px] font-bold text-gray-300" x-text="kegiatan.waktu"></div>
                                        <div class="text-xs font-bold text-white leading-tight" x-text="kegiatan.nama"></div>
                                        <div class="text-[9px] text-gray-400" x-text="kegiatan.lokasi"></div>
                                    </div>
                                </template>
                                <template x-if="kegiatans.filter(k => k.tipe !== 'mendatang').length === 0">
                                    <div class="text-xs text-gray-500 italic pl-4">Belum ada agenda hari ini.</div>
                                </template>
                            </div>
                            
                            <!-- Agenda Mendatang -->
                            <div class="flex items-center gap-2 mb-2 mt-2">
                                <div class="h-px bg-accent/30 w-4"></div>
                                <div class="text-[10px] text-accent font-semibold">Agenda Mendatang</div>
                            </div>
                            <div class="space-y-3 pb-2">
                                <template x-for="kegiatan in kegiatans.filter(k => k.tipe === 'mendatang')" :key="'mendatang-'+kegiatan.id">
                                    <div class="flex gap-2 items-start">
                                        <div class="bg-black/30 border border-white/10 rounded-md text-center p-1 w-10 flex-shrink-0 flex items-center justify-center shadow-inner">
                                            <span class="text-[9px] font-bold text-white leading-none text-center" x-html="formatDateBox(kegiatan.tanggal)"></span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-xs font-bold text-white leading-tight mb-0.5" x-text="kegiatan.nama"></div>
                                            <div class="text-[9px] text-gray-400 flex items-center gap-1">
                                                <svg class="w-2.5 h-2.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span x-text="kegiatan.waktu"></span>
                                            </div>
                                            <div class="text-[9px] text-gray-400 flex items-center gap-1 mt-0.5">
                                                <svg class="w-2.5 h-2.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                <span x-text="kegiatan.lokasi"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="kegiatans.filter(k => k.tipe === 'mendatang').length === 0">
                                    <div class="text-xs text-gray-500 italic pl-2">Belum ada agenda mendatang.</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agenda Pimpinan -->
            <div class="panel p-4 h-[25%] flex flex-col">
                <div class="panel-header">
                    <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    <h2 class="text-sm font-bold tracking-wide uppercase">Agenda Pimpinan</h2>
                </div>
                <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
                    <!-- Hari Ini -->
                    <div>
                        <div class="text-[10px] text-accent font-semibold mb-2 uppercase tracking-wide border-b border-accent/20 pb-1">Hari Ini</div>
                        <div class="space-y-2">
                            <template x-for="agenda in agendaPimpinan.filter(a => a.tipe !== 'mendatang')" :key="'p-hariini-'+agenda.id">
                                <div class="flex items-start gap-2">
                                    <div class="mt-1 w-1.5 h-1.5 rounded-full bg-accent shrink-0"></div>
                                    <div>
                                        <div class="text-xs font-bold text-white" x-text="agenda.jabatan"></div>
                                        <div class="text-[10px] text-gray-300 leading-tight" x-text="agenda.kegiatan + ' - ' + agenda.lokasi"></div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="agendaPimpinan.filter(a => a.tipe !== 'mendatang').length === 0">
                                <div class="text-xs text-gray-500 italic pl-3.5">Belum ada agenda pimpinan hari ini.</div>
                            </template>
                        </div>
                    </div>

                    <!-- Agenda Mendatang -->
                    <div>
                        <div class="text-[10px] text-accent font-semibold mb-2 uppercase tracking-wide border-b border-accent/20 pb-1">Agenda Mendatang</div>
                        <div class="space-y-2">
                            <template x-for="agenda in agendaPimpinan.filter(a => a.tipe === 'mendatang')" :key="'p-mendatang-'+agenda.id">
                                <div class="flex items-start gap-2 opacity-80">
                                    <div class="mt-1 w-1.5 h-1.5 rounded-full bg-white/40 shrink-0"></div>
                                    <div>
                                        <div class="text-xs font-bold text-white" x-text="agenda.jabatan"></div>
                                        <div class="text-[10px] text-gray-400 leading-tight" x-text="agenda.kegiatan + ' - ' + agenda.lokasi"></div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="agendaPimpinan.filter(a => a.tipe === 'mendatang').length === 0">
                                <div class="text-xs text-gray-500 italic pl-3.5">Belum ada agenda pimpinan mendatang.</div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Link Cepat & QR -->
            <div class="panel p-4 h-[20%] flex flex-col">
                <div class="panel-header mb-2">
                    <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path></svg>
                    <h2 class="text-sm font-bold tracking-wide uppercase">Link Cepat & Informasi</h2>
                </div>
                <div class="flex-1 grid grid-cols-4 gap-2 min-h-0">
                    <template x-for="(link, i) in linkCepat.slice(0, 4)" :key="i">
                        <a :href="link.url" target="_blank" class="bg-white rounded p-1.5 flex flex-col items-center justify-between text-center border-b-2 border-accent hover:bg-gray-100 transition overflow-hidden h-full">
                            <div class="text-[8px] font-bold text-primary mb-1 line-clamp-1 shrink-0" x-text="link.title"></div>
                            <div class="flex-1 min-h-0 flex items-center justify-center w-full">
                                <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' + encodeURIComponent(link.url)" class="h-full w-auto max-w-full object-contain">
                            </div>
                        </a>
                    </template>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer: Info Terkini -->
    <footer class="h-10 w-full bg-primary border-t border-accent/40 z-10 flex items-center shrink-0">
        <div class="bg-accent h-full px-6 flex items-center justify-center font-bold text-primary uppercase text-sm whitespace-nowrap z-20 relative">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
            INFO TERKINI
            <div class="w-0 h-0 border-t-[20px] border-t-transparent border-b-[20px] border-b-transparent border-l-[15px] border-l-accent absolute right-[-15px] top-0 hidden md:block"></div>
        </div>
        <div class="flex-1 marquee-container ml-6 text-sm font-semibold tracking-wide flex items-center h-full">
            <div class="marquee-content text-gray-200">
                <span x-html="marqueeHtml"></span>
            </div>
        </div>
        <div class="bg-accent h-full px-6 flex items-center justify-center font-bold text-primary text-xs whitespace-nowrap z-20 rounded-tl-xl border-l-2 border-white">
            Selengkapnya di www.uinssc.ac.id
        </div>
    </footer>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(251,191,36,0.5); border-radius: 4px; }

        @keyframes marquee-news {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee-news {
            animation: marquee-news 40s linear infinite;
        }
    </style>

    <script>
        const initialKegiatans = <?php echo json_encode($kegiatans); ?>;
        const initialAgendaPimpinan = <?php echo json_encode($agenda_pimpinan); ?>;
        const initialLayananPublik = <?php echo json_encode($layanan_publik); ?>;
        const initialLinkCepat = <?php echo json_encode($link_cepat); ?>;
        const initialPromos = <?php echo json_encode($promos); ?>;
        const initialVideos = <?php echo json_encode($videos); ?>;
        const initialYoutubeUrl = <?php echo json_encode($youtubeUrl); ?>;
        const customRunningText = <?php echo json_encode($customText); ?>;

        function uinsscDisplay() {
            return {
                currentTime: '',
                currentDate: '',
                kegiatans: initialKegiatans,
                agendaPimpinan: initialAgendaPimpinan,
                layananPublik: initialLayananPublik,
                linkCepat: initialLinkCepat,
                promos: initialPromos,
                activePromoIndex: 0,
                videos: initialVideos,
                activeVideoIndex: 0,
                videoVolume: 100,
                videoMuted: false,
                youtubeUrl: initialYoutubeUrl,
                marqueeHtml: '<span class="mx-4 text-accent">◆</span> Memuat berita...',
                newsItems: [],
                calendarDays: [],
                calendarMonth: '',
                isFullscreen: false,
                
                init() {
                    this.updateTime();
                    this.generateCalendar();
                    setInterval(() => this.updateTime(), 1000);
                    this.fetchNews();
                    setInterval(() => this.fetchNews(), 3600000); // 1 hour
                    
                    // Cek pembaharuan data tiap 5 detik
                    setInterval(() => this.pollData(), 5000); 

                    // Interval rotasi flayer (cek secara dinamis)
                    setInterval(() => {
                        if (this.promos.length > 1) {
                            this.activePromoIndex = (this.activePromoIndex + 1) % this.promos.length;
                        } else {
                            this.activePromoIndex = 0;
                        }
                    }, 5000);

                    // Sync fullscreen state
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = !!document.fullscreenElement;
                    });
                },

                pollData() {
                    fetch('data.json?t=' + new Date().getTime())
                        .then(res => res.json())
                        .then(data => {
                            if (data) {
                                this.kegiatans = data.kegiatans || [];
                                this.agendaPimpinan = data.agenda_pimpinan || [];
                                this.layananPublik = data.layanan_publik || [];
                                this.linkCepat = data.link_cepat || [];
                                
                                const settings = data.settings || {};
                                this.promos = settings.promos || [];
                                this.youtubeUrl = settings.youtube_url || '';
                                this.customText = settings.custom_running_text || '';
                                
                                let newVideos = settings.videos || [];
                                if (newVideos.length === 0 && settings.video_url) newVideos.push(settings.video_url);
                                
                                if (JSON.stringify(this.videos) !== JSON.stringify(newVideos)) {
                                    this.videos = newVideos;
                                    this.activeVideoIndex = 0;
                                }
                            }
                        })
                        .catch(err => console.log('Poll error', err));
                },
                
                formatDateBox(tanggal) {
                    if(!tanggal) return '-';
                    const parts = tanggal.split(' ');
                    if(parts.length >= 2) {
                        return `${parts[0]}<br><span class="text-accent">${parts[1].substring(0,3).toUpperCase()}</span>`;
                    }
                    return tanggal.substring(0,6);
                },

                nextVideo() {
                    if (this.videos.length > 1) {
                        this.activeVideoIndex = (this.activeVideoIndex + 1) % this.videos.length;
                        this.$nextTick(() => {
                            const v = document.getElementById('mainVideo');
                            if(v) v.play();
                        });
                    } else {
                        const v = document.getElementById('mainVideo');
                        if(v) {
                            v.currentTime = 0;
                            v.play();
                        }
                    }
                },

                generateCalendar() {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = today.getMonth();
                    
                    const months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
                    this.calendarMonth = `${months[month]} ${year}`;
                    
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
                    
                    let startDayOfWeek = firstDay.getDay() - 1;
                    if (startDayOfWeek === -1) startDayOfWeek = 6;
                    
                    const days = [];
                    
                    const prevMonthLastDay = new Date(year, month, 0).getDate();
                    for (let i = startDayOfWeek - 1; i >= 0; i--) {
                        days.push({ date: prevMonthLastDay - i, isCurrentMonth: false, isToday: false });
                    }
                    
                    for (let i = 1; i <= lastDay.getDate(); i++) {
                        const isToday = (i === today.getDate() && month === new Date().getMonth() && year === new Date().getFullYear());
                        days.push({ date: i, isCurrentMonth: true, isToday: isToday });
                    }
                    
                    let nextDay = 1;
                    while (days.length < 42) {
                        days.push({ date: nextDay++, isCurrentMonth: false, isToday: false });
                    }
                    
                    this.calendarDays = days;
                },

                async fetchNews() {
                    try {
                        const apiUrl = 'https://info.uinssc.ac.id/wp-json/wp/v2/posts?_embed&per_page=10'; 
                        const response = await fetch(apiUrl);
                        const posts = await response.json();
                        
                        let html = '';
                        let fetchedNews = [];
                        
                        if (customRunningText.trim() !== '') {
                            html += `<span class="mx-4 text-accent">◆</span> ${customRunningText} `;
                        }
                        
                        for(let i=0; i < posts.length; i++) {
                            const post = posts[i];
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = post.title.rendered;
                            const cleanTitle = tempDiv.textContent || tempDiv.innerText || "";
                            html += `<span class="mx-4 text-accent">◆</span> ${cleanTitle} `;
                            
                            const dateObj = new Date(post.date);
                            const dateStr = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                            
                            let image = 'https://uinssc.ac.id/wp-content/uploads/2024/08/logo-uin-siber-cirebon.png';
                            if (post._embedded && post._embedded['wp:featuredmedia'] && post._embedded['wp:featuredmedia'].length > 0) {
                                image = post._embedded['wp:featuredmedia'][0].source_url;
                            }
                            
                            fetchedNews.push({ title: cleanTitle, date: dateStr, image: image });
                        }

                        if(html) this.marqueeHtml = html;
                        if(fetchedNews.length > 0) this.newsItems = fetchedNews;
                        
                    } catch (e) {
                        console.error('Error fetching news:', e);
                        this.marqueeHtml = `<span class="mx-4 text-accent">◆</span> Selamat datang di UIN Siber Syekh Nurjati Cirebon ${customRunningText}`;
                    }
                },

                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch((err) => {
                            console.error(`Error attempting to enable fullscreen: ${err.message}`);
                        });
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        }
                    }
                },

                updateTime() {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    this.currentTime = `${hours}:${minutes}:${seconds}`;
                    
                    const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
                    const months = ['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'];
                    
                    const dayName = days[now.getDay()];
                    const date = now.getDate();
                    const monthName = months[now.getMonth()];
                    const year = now.getFullYear();
                    
                    this.currentDate = `${dayName}, ${date} ${monthName} ${year}`;
                }
            }
        }
    </script>
</body>
</html>
