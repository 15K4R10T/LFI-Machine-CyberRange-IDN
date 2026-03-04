# Lab LFI Injection — IDN-CyberRange

<div align="center">

Lingkungan praktik Local File Inclusion (LFI) berbasis Docker dengan enam modul bertingkat — dari basic file read hingga eskalasi ke Remote Code Execution.

[![Port](https://img.shields.io/badge/Port-8083-2496ED)](#cara-menjalankan)
[![Modules](https://img.shields.io/badge/Modules-6-22c55e)](#modul)
[![Flags](https://img.shields.io/badge/Flags-6-e63946)](#flags--challenges)
[![Stack](https://img.shields.io/badge/Stack-PHP%208.1%20%2B%20MySQL-777BB4)](#arsitektur)

</div>

---

## Daftar Isi

- [Tentang Lab](#tentang-lab)
- [Modul](#modul)
- [Arsitektur](#arsitektur)
- [Cara Menjalankan](#cara-menjalankan)
- [Struktur File](#struktur-file)
- [Database](#database)
- [Target Files](#target-files)
- [Flags & Challenges](#flags--challenges)
- [Commands](#commands)
- [Disclaimer](#disclaimer)

---

## Tentang Lab

Lab ini menyimulasikan kerentanan Local File Inclusion yang terjadi ketika aplikasi web menggunakan input pengguna untuk menentukan file yang akan dimuat tanpa validasi yang memadai. Enam modul mencakup skenario dari yang paling sederhana hingga teknik lanjutan yang mengarah ke eksekusi kode arbitrer di server.

---

## Modul

### Basic Series

| # | Nama | Path | Tingkat | Deskripsi |
|---|------|------|---------|-----------|
| 1 | Basic LFI | `/basic-1/` | Basic | Parameter `?page=` langsung digunakan di `file_get_contents()` tanpa validasi |
| 2 | LFI + Null Byte | `/basic-2/` | Basic | Bypass penambahan ekstensi `.php` otomatis menggunakan null byte (`%00`) |
| 3 | LFI + Path Traversal | `/basic-3/` | Basic | Keluar dari base directory menggunakan sequence `../` yang tidak divalidasi |

### Advanced Series

| # | Nama | Path | Tingkat | Deskripsi |
|---|------|------|---------|-----------|
| 4 | LFI + Filter Bypass | `/advanced-1/` | Advanced | Melewati filter blacklist `../` dengan encoding dan nested payload (3 level) |
| 5 | LFI + Log Poisoning | `/advanced-2/` | Advanced | Injeksi PHP code ke Apache access log via User-Agent, kemudian include log |
| 6 | LFI to RCE | `/advanced-3/` | Advanced | Eskalasi ke RCE menggunakan PHP wrappers: `php://filter`, `data://`, `php://input` |

---

## Arsitektur

```
Container: lab-lfi  (port 8083)
├── Supervisor (process manager)
│   ├── Apache 2 + PHP 8.1  -->  port 8083
│   └── MySQL 8.0           -->  internal only
└── /var/www/html/
    ├── /basic-1/
    ├── /basic-2/
    ├── /basic-3/
    ├── /advanced-1/
    ├── /advanced-2/
    └── /advanced-3/
```

Port mapping seluruh lab (tidak ada konflik):

| Lab | Container | Port |
|-----|-----------|------|
| LFI Injection | `lab-lfi` | `8083` |

---

## Cara Menjalankan

### Prasyarat

```bash
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER && newgrp docker
```

### Opsi A — Build dari Source Code

```bash
git clone https://github.com/15K4R10T/IDN-CyberRange.git
cd IDN-CyberRange/lab-lfi
chmod +x run.sh
./run.sh
```

### Opsi B — Load dari Docker Image

```bash
files tar (https://drive.google.com/file/d/1lR-Qj_3yd0ZWHqSAXXtg6NivofHcfz4W/view?usp=sharing)
docker load < lab-lfi-image.tar.gz
docker run -d --name lab-lfi -p 8083:80 --restart unless-stopped lab-lfi
```

### Akses Lab

```
http://localhost:8083
http://<IP-VM>:8083
```

---

## Struktur File

```
lab-lfi/
├── Dockerfile                 Termasuk pembuatan file target sensitif
├── run.sh                     Build & deploy otomatis (port 8083)
├── entrypoint.sh
├── supervisord.conf
├── apache.conf
├── init.sql                   Schema database + access log dummy
└── web/
    ├── index.php              Dashboard
    ├── basic-1/
    │   ├── index.php          Modul 1: Basic LFI
    │   └── pages/             File HTML contoh (home, about, contact)
    ├── basic-2/
    │   └── index.php          Modul 2: Null Byte Bypass
    ├── basic-3/
    │   ├── index.php          Modul 3: Path Traversal
    │   └── pages/             File teks contoh
    ├── advanced-1/
    │   └── index.php          Modul 4: Filter Bypass (3 level)
    ├── advanced-2/
    │   └── index.php          Modul 5: Log Poisoning
    ├── advanced-3/
    │   └── index.php          Modul 6: LFI to RCE via Wrappers
    └── includes/
        ├── db.php
        ├── shared_css.php
        ├── nav.php
        └── footer.php
```

---

## Database

```
Host     : 127.0.0.1
Database : lablfi
User     : labuser
Password : labpass123
```

| Tabel | Keterangan |
|-------|-----------|
| `users` | Data akun pengguna |
| `access_log` | Log akses request — digunakan pada modul Log Poisoning |

---

## Target Files

File-file berikut dibuat di dalam container sebagai target challenge:

| Path | Keterangan |
|------|-----------|
| `/etc/passwd` | Daftar user sistem Linux |
| `/etc/hosts` | Konfigurasi jaringan |
| `/var/private/flag.txt` | File flag utama |
| `/var/private/config.env` | Konfigurasi aplikasi + kredensial database |
| `/home/labuser/.secret` | File tersembunyi di home directory |
| `/var/log/apache2/access.log` | Apache access log (target log poisoning) |

---

## Flags & Challenges

| Flag | Modul | Cara Mendapatkan |
|------|-------|-----------------|
| `FLAG{lfi_basic_file_read}` | Basic 1 | Baca `/var/private/flag.txt` langsung |
| `FLAG{lfi_config_exposed}` | Basic 1 | Baca `/var/private/config.env` |
| `FLAG{lfi_home_dir_traversal}` | Basic 3 | Baca `/home/labuser/.secret` via traversal |
| `FLAG{lfi_filter_bypass_success}` | Advanced 1 | Bypass semua 3 level filter |
| `FLAG{lfi_log_poisoning_rce}` | Advanced 2 | Eksekusi PHP code via log poisoning |
| `FLAG{lfi_rce_wrapper_php_input}` | Advanced 3 | RCE via `data://` atau `php://input` wrapper |

---

## Commands

```bash
docker logs -f lab-lfi
docker stop lab-lfi
docker start lab-lfi
docker exec -it lab-lfi bash
docker rm -f lab-lfi && ./run.sh
```

---

## Disclaimer

> Lab ini dibuat **hanya untuk keperluan edukasi dan pelatihan keamanan siber** di lingkungan yang terisolasi.
> Jangan gunakan teknik yang dipelajari pada sistem, jaringan, atau aplikasi tanpa izin tertulis dari pemiliknya.
> ID-Networkers tidak bertanggung jawab atas segala bentuk penyalahgunaan materi dalam repositori ini.

---

<div align="center">
  <sub>Dibuat oleh <strong>ID-Networkers</strong> — Indonesian IT Expert Factory</sub>
</div>
