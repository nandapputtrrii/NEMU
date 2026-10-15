const API_URL = "http://localhost:3000/barang";

/* ============================================================
   PENGATURAN FOTO
   ============================================================ */
const FOTO_TIPE_DIIZINKAN = ["image/jpeg", "image/png", "image/webp"];
const FOTO_UKURAN_MAKS = 5 * 1024 * 1024; // 5 MB sebelum dikecilkan
const FOTO_SISI_MAKS = 800; // px, sisi terpanjang setelah dikecilkan
const FOTO_KUALITAS = 0.7;

/* Kecilkan foto di browser lalu ubah jadi data URL (base64) */
function kompresFoto(file) {
    return new Promise(function (resolve, reject) {
        const urlSementara = URL.createObjectURL(file);
        const gambar = new Image();

        gambar.onload = function () {
            URL.revokeObjectURL(urlSementara);
            const skala = Math.min(1, FOTO_SISI_MAKS / Math.max(gambar.width, gambar.height));
            const canvas = document.createElement("canvas");
            canvas.width = Math.round(gambar.width * skala);
            canvas.height = Math.round(gambar.height * skala);

            const ctx = canvas.getContext("2d");
            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(gambar, 0, 0, canvas.width, canvas.height);
            resolve(canvas.toDataURL("image/jpeg", FOTO_KUALITAS));
        };

        gambar.onerror = function () {
            URL.revokeObjectURL(urlSementara);
            reject(new Error("Foto tidak bisa dibaca."));
        };

        gambar.src = urlSementara;
    });
}

/* Ubah 08xxx / +628xxx / 8xxx jadi 628xxx (format wa.me). Kosong jika tidak valid. */
function normalisasiNomorWa(nomor) {
    const digit = String(nomor || "").replace(/\D/g, "");
    let hasil = "";

    if (digit.startsWith("62")) {
        hasil = digit;
    } else if (digit.startsWith("0")) {
        hasil = "62" + digit.slice(1);
    } else if (digit.startsWith("8")) {
        hasil = "62" + digit;
    }

    return hasil.length >= 10 && hasil.length <= 15 ? hasil : "";
}

/* ============================================================
   FORM LAPOR BARANG (lapor-hilang.html & lapor-temuan.html)
   ============================================================ */
const laporForm = document.querySelector("#lapor-form");

if (laporForm) {
    const pesanLaporan = document.querySelector("#lapor-pesan");
    const tombolKirimLaporan = document.querySelector("#kirim-laporan");
    const loadingOverlay = document.querySelector("#loading-overlay");
    const fotoInput = document.querySelector("#foto");
    const fotoPreview = document.querySelector("#foto-preview");
    const fotoPreviewWrap = document.querySelector("#foto-preview-wrap");
    const fotoError = document.querySelector("#foto-error");
    let laporanTerakhir = null;
    let urlPreview = null;

    function tampilkanFotoError(teks) {
        if (!fotoError) {
            return;
        }
        fotoError.textContent = teks;
        fotoError.classList.toggle("hidden", !teks);
    }

    function resetPreviewFoto() {
        if (urlPreview) {
            URL.revokeObjectURL(urlPreview);
            urlPreview = null;
        }
        if (fotoPreview) {
            fotoPreview.removeAttribute("src");
        }
        if (fotoPreviewWrap) {
            fotoPreviewWrap.classList.add("hidden");
        }
    }

    function tampilkanPesanError(teks) {
        pesanLaporan.className =
            "rounded-lg border-2 border-umm-700 bg-umm-100 p-4 text-sm text-umm-900 md:col-span-2";
        pesanLaporan.textContent = teks;
    }

    if (fotoInput) {
        fotoInput.addEventListener("change", function () {
            tampilkanFotoError("");
            resetPreviewFoto();

            const file = fotoInput.files[0];
            if (!file) {
                return;
            }

            if (!FOTO_TIPE_DIIZINKAN.includes(file.type)) {
                tampilkanFotoError("Format foto harus JPG, PNG, atau WebP.");
                fotoInput.value = "";
                return;
            }

            if (file.size > FOTO_UKURAN_MAKS) {
                tampilkanFotoError("Ukuran foto maksimal 5 MB.");
                fotoInput.value = "";
                return;
            }

            urlPreview = URL.createObjectURL(file);
            fotoPreview.src = urlPreview;
            fotoPreviewWrap.classList.remove("hidden");
        });
    }

    laporForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        const nilai = new FormData(laporForm);
        const kategori = String(nilai.get("kategori")).trim();

        const nomorWa = normalisasiNomorWa(nilai.get("teleponPelapor"));
        if (!nomorWa) {
            tampilkanPesanError("Nomor WhatsApp tidak valid. Contoh: 081234567890.");
            return;
        }

        let foto = "";
        const file = fotoInput ? fotoInput.files[0] : null;
        if (file) {
            try {
                foto = await kompresFoto(file);
            } catch (error) {
                console.error("Gagal memproses foto:", error);
                tampilkanFotoError("Foto tidak bisa diproses. Coba pilih foto lain.");
                return;
            }
        } else if (fotoInput && fotoInput.required) {
            tampilkanFotoError("Foto barang wajib dilampirkan.");
            return;
        }

        laporanTerakhir = {
            data: {
                nama: String(nilai.get("namaBarang")).trim(),
                kategori: kategori.charAt(0).toUpperCase() + kategori.slice(1),
                lokasi: String(nilai.get("lokasi")).trim(),
                tanggal: String(nilai.get("tanggal")),
                jam: String(nilai.get("jam")),
                deskripsi: String(nilai.get("deskripsi")).trim(),
                status: laporForm.dataset.status || "Hilang",
                namaPelapor: String(nilai.get("namaPelapor")).trim(),
                telepon: nomorWa,
                ...(foto ? { foto: foto } : {}),
                ...(nilai.get("penyimpanan")
                    ? { penyimpanan: String(nilai.get("penyimpanan")).trim() }
                    : {})
            }
        };

        await kirimLaporan(laporanTerakhir);
    });

    pesanLaporan.addEventListener("click", function (event) {
        if (event.target.closest("#coba-kirim-lagi") && laporanTerakhir) {
            kirimLaporan(laporanTerakhir);
        }
    });

    async function kirimLaporan(laporan) {
        tombolKirimLaporan.disabled = true;
        tombolKirimLaporan.setAttribute("aria-busy", "true");
        loadingOverlay.classList.remove("hidden");
        loadingOverlay.classList.add("flex");
        pesanLaporan.classList.add("hidden");
        tampilkanFotoError("");

        try {
            const response = await fetch(API_URL, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(laporan.data)
            });

            if (!response.ok) {
                throw new Error(`Server menolak laporan (HTTP ${response.status})`);
            }

            const dataTersimpan = await response.json();
            laporForm.reset();
            resetPreviewFoto();
            laporanTerakhir = null;
            pesanLaporan.className = "rounded-lg border-2 border-ink bg-gold-500/20 p-4 text-sm md:col-span-2";

            const judulBerhasil = document.createElement("p");
            judulBerhasil.className = "font-bold";
            judulBerhasil.textContent = "Laporan terkirim dan tersimpan.";

            const detailBerhasil = document.createElement("p");
            detailBerhasil.className = "mt-1";
            detailBerhasil.textContent =
                `Data tersimpan: ${dataTersimpan.nama}, status ${dataTersimpan.status}` +
                (dataTersimpan.id ? ` (ID: ${dataTersimpan.id})` : "");

            const detailKontak = document.createElement("p");
            detailKontak.className = "mt-2 text-ink/70";
            detailKontak.textContent =
                "Nomor WhatsApp kamu dipakai untuk tombol WhatsApp di detail barang. Tombol itu hanya aktif untuk pengguna yang sudah login.";

            pesanLaporan.replaceChildren(judulBerhasil, detailBerhasil, detailKontak);
        } catch (error) {
            console.error("Gagal mengirim laporan:", error);
            pesanLaporan.className = "rounded-lg border-2 border-umm-700 bg-umm-100 p-4 text-sm text-umm-900 md:col-span-2";

            const pesanError = document.createElement("p");
            pesanError.textContent =
                "Laporan gagal dikirim. Periksa koneksi dan pastikan JSON Server berjalan, lalu coba lagi.";

            const tombolCobaLagi = document.createElement("button");
            tombolCobaLagi.id = "coba-kirim-lagi";
            tombolCobaLagi.type = "button";
            tombolCobaLagi.className =
                "mt-3 rounded-lg bg-umm-700 px-4 py-2 font-bold text-white transition hover:bg-umm-800";
            tombolCobaLagi.textContent = "Coba lagi";

            pesanLaporan.replaceChildren(pesanError, tombolCobaLagi);
        } finally {
            tombolKirimLaporan.disabled = false;
            tombolKirimLaporan.removeAttribute("aria-busy");
            tombolKirimLaporan.textContent = "Kirim laporan";
            loadingOverlay.classList.remove("flex");
            loadingOverlay.classList.add("hidden");
        }
    }
}

/* ============================================================
   DAFTAR BARANG + DETAIL (nemu.html)
   ============================================================ */
const barangContainer = document.querySelector("#barang-container");
const tombolLihatSemua = document.querySelector("#lihat-semua-barang");
let dataBarang = [];
let dataBarangSudahDimuat = false;
let tampilkanSemuaBarang = false;
let barangDetail = null;

const detailModal = document.querySelector("#detail-modal");

function adalahDitemukan(barang) {
    return String(barang.status || "").trim().toLocaleLowerCase("id") === "ditemukan";
}

function idBarang(barang) {
    return String(barang.id || barang.nama);
}

/* Foto barang, atau placeholder kalau tidak ada foto */
function buatFotoBarang(barang, kelasUkuran) {
    if (barang.foto) {
        const gambar = document.createElement("img");
        gambar.src = barang.foto;
        gambar.alt = `Foto ${barang.nama || "barang"}`;
        gambar.loading = "lazy";
        gambar.className = `${kelasUkuran} w-full object-cover`;
        return gambar;
    }

    const placeholder = document.createElement("div");
    placeholder.className =
        `${kelasUkuran} flex w-full items-center justify-center bg-paper font-mono text-xs text-ink/50`;
    placeholder.textContent = "Tanpa foto";
    return placeholder;
}

/* Link wa.me dengan pesan awal. Null jika pelapor tidak punya nomor valid. */
function buatLinkWa(barang) {
    const nomor = normalisasiNomorWa(barang.telepon);
    if (!nomor) {
        return null;
    }

    const sapaan = barang.namaPelapor ? `Halo ${barang.namaPelapor}, ` : "Halo, ";
    const pesan = adalahDitemukan(barang)
        ? `${sapaan}saya melihat barang "${barang.nama}" yang kamu temukan di NEMU dan ingin mengklaimnya. Bisa dibantu?`
        : `${sapaan}saya melihat laporan kehilangan "${barang.nama}" di NEMU dan ingin membantu. Boleh kita bicara?`;

    return `https://wa.me/${nomor}?text=${encodeURIComponent(pesan)}`;
}

function tutupDetail() {
    if (!detailModal) {
        return;
    }
    detailModal.classList.add("hidden");
    detailModal.classList.remove("flex");
    barangDetail = null;
}

function bukaDetail(barang) {
    if (!detailModal) {
        return;
    }

    barangDetail = barang;
    const ditemukan = adalahDitemukan(barang);

    document.querySelector("#detail-foto-wrap")
        .replaceChildren(buatFotoBarang(barang, "aspect-video"));
    document.querySelector("#detail-label").textContent =
        `${barang.kategori || "Lainnya"}, ${String(barang.status || "status tidak diketahui").toLowerCase()}`;
    document.querySelector("#detail-title").textContent = barang.nama || "Barang tanpa nama";
    document.querySelector("#detail-deskripsi").textContent = barang.deskripsi || "Tidak ada deskripsi.";
    document.querySelector("#detail-meta").textContent =
        `Lokasi: ${barang.lokasi || "Tidak diketahui"}\n` +
        `Tanggal: ${barang.tanggal || "Tidak diketahui"}` +
        (barang.jam ? `\nJam: ${barang.jam}` : "") +
        (barang.penyimpanan ? `\nDisimpan di: ${barang.penyimpanan}` : "");

    const tombolAksi = document.querySelector("#detail-aksi");
    const info = document.querySelector("#detail-info");
    const adaNomor = Boolean(buatLinkWa(barang));

    tombolAksi.textContent = ditemukan
        ? "Klaim via WhatsApp"
        : "Hubungi pelapor via WhatsApp";
    tombolAksi.disabled = !adaNomor;

    if (!adaNomor) {
        info.textContent = "Pelapor belum mencantumkan nomor WhatsApp untuk barang ini.";
        info.classList.remove("hidden");
    } else if (!window.NemuAuth.isLoggedIn()) {
        info.textContent = "Login dengan akun UMM dulu untuk menghubungi pelapor lewat WhatsApp.";
        info.classList.remove("hidden");
    } else {
        info.classList.add("hidden");
    }

    detailModal.classList.remove("hidden");
    detailModal.classList.add("flex");
    tombolAksi.focus();
}

if (detailModal) {
    document.querySelector("#detail-aksi").addEventListener("click", function () {
        if (!barangDetail) {
            return;
        }

        if (!window.NemuAuth.requireLogin(
            "claim",
            `nemu.html?claim=${encodeURIComponent(idBarang(barangDetail))}`
        )) {
            return;
        }

        const link = buatLinkWa(barangDetail);
        if (link) {
            window.open(link, "_blank", "noopener");
        }
    });

    document.querySelector("#detail-tutup").addEventListener("click", tutupDetail);

    detailModal.addEventListener("click", function (event) {
        if (event.target === detailModal) {
            tutupDetail();
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && !detailModal.classList.contains("hidden")) {
            tutupDetail();
        }
    });
}

if (tombolLihatSemua) {
    tombolLihatSemua.addEventListener("click", function () {
        tampilkanSemuaBarang = !tampilkanSemuaBarang;
        tombolLihatSemua.textContent = tampilkanSemuaBarang
            ? "Tampilkan lebih sedikit"
            : "Lihat semua";
        tampilkanBarang(dataBarang);
    });
}

async function ambilBarang() {
    // Halaman tanpa daftar barang (mis. lapor-hilang.html) tidak perlu memanggil API.
    if (!barangContainer) {
        return;
    }

    try {
        const data = await muatDataBarang();

        const jumlahBarangDitemukan = data.filter(adalahDitemukan).length;

        if (tombolLihatSemua) {
            tombolLihatSemua.disabled = jumlahBarangDitemukan <= 3;
        }
        tampilkanBarang(data);

        const searchInput = document.querySelector("#search");
        if (searchInput) {
            searchInput.dispatchEvent(new Event("input"));
        }

        // Setelah login dari tombol klaim, kembali ke detail barang yang tadi dibuka
        const claimId = new URLSearchParams(window.location.search).get("claim");
        if (claimId) {
            const barangKlaim = data.find(function (barang) {
                return idBarang(barang) === claimId;
            });
            if (barangKlaim) {
                bukaDetail(barangKlaim);
            }
            window.history.replaceState({}, document.title, `${window.location.pathname}${window.location.hash}`);
        }
    } catch (error) {
        console.error("Error:", error);

        barangContainer.innerHTML = `
            <p class="col-span-full text-center font-mono text-sm text-umm-700">
                Data barang gagal dimuat. Pastikan REST API (JSON Server) sedang berjalan.
            </p>
        `;
    }
}

async function muatDataBarang() {
    const response = await fetch(API_URL);

    if (!response.ok) {
        throw new Error(`Gagal mengambil data dari API (HTTP ${response.status})`);
    }

    const data = await response.json();
    if (!Array.isArray(data)) {
        throw new Error("Format data barang dari API tidak valid.");
    }

    dataBarang = data;
    dataBarangSudahDimuat = true;
    return data;
}

function tampilkanBarang(data) {
    if (!barangContainer) {
        return;
    }

    const barangDitemukan = data
        .filter(adalahDitemukan)
        .sort(function (barangA, barangB) {
            return String(barangB.tanggal || "").localeCompare(String(barangA.tanggal || ""));
        });
    const barangDitampilkan = tampilkanSemuaBarang
        ? barangDitemukan
        : barangDitemukan.slice(0, 3);

    barangContainer.replaceChildren();
    if (barangDitampilkan.length === 0) {
        const emptyMessage = document.createElement("p");
        emptyMessage.className = "col-span-full text-center font-mono text-sm text-ink/60";
        emptyMessage.textContent = "Belum ada barang yang ditemukan.";
        barangContainer.appendChild(emptyMessage);
        return;
    }

    barangDitampilkan.forEach(function (barang) {
        barangContainer.appendChild(buatKartuBarang(barang));
    });
}

/* Kartu barang berbentuk label/tag. Klik kartu untuk melihat detail. */
function buatKartuBarang(barang) {
    const ditemukan = adalahDitemukan(barang);

    const card = document.createElement("article");
    card.className =
        "relative cursor-pointer overflow-hidden rounded-xl border-2 border-ink bg-white transition hover:border-umm-600 focus:border-umm-600 focus:outline-none";
    card.tabIndex = 0;
    card.setAttribute("role", "button");
    card.setAttribute("aria-label", `Lihat detail ${barang.nama || "barang"}`);

    const fotoWrap = document.createElement("div");
    fotoWrap.className = "border-b-2 border-ink";
    fotoWrap.appendChild(buatFotoBarang(barang, "aspect-[4/3]"));

    const hole = document.createElement("span");
    hole.className = "absolute right-4 top-3 h-3 w-3 rounded-full border-2 border-ink bg-paper";
    hole.setAttribute("aria-hidden", "true");

    const body = document.createElement("div");
    body.className = "p-5";

    const label = document.createElement("p");
    label.className = "font-mono text-xs " + (ditemukan ? "text-umm-600" : "text-ink/60");
    label.textContent = `${barang.kategori || "Lainnya"}, ${String(barang.status || "status tidak diketahui").toLowerCase()}`;

    const name = document.createElement("h3");
    name.className = "mt-2 font-display text-2xl font-bold leading-tight";
    name.textContent = barang.nama || "Barang tanpa nama";

    const description = document.createElement("p");
    description.className = "mt-2 line-clamp-2 text-sm text-ink/70";
    description.textContent = barang.deskripsi || "Tidak ada deskripsi.";

    const divider = document.createElement("div");
    divider.className = "my-4 border-t-2 border-dashed border-ink/30";

    const meta = document.createElement("p");
    meta.className = "whitespace-pre-line font-mono text-xs leading-relaxed text-ink/70";
    meta.textContent =
        `Lokasi: ${barang.lokasi || "Tidak diketahui"}\nTanggal: ${barang.tanggal || "Tidak diketahui"}` +
        (barang.penyimpanan ? `\nDisimpan di: ${barang.penyimpanan}` : "");

    const petunjuk = document.createElement("p");
    petunjuk.className = "mt-4 font-mono text-xs font-bold text-umm-600";
    petunjuk.textContent = "Lihat detail";

    body.append(label, name, description, divider, meta, petunjuk);
    card.append(fotoWrap, hole, body);

    card.addEventListener("click", function () {
        bukaDetail(barang);
    });
    card.addEventListener("keydown", function (event) {
        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            bukaDetail(barang);
        }
    });

    return card;
}

// Jalankan fungsi API
ambilBarang();

/* ============================================================
   PENCARIAN
   ============================================================ */
const searchForm = document.querySelector("#search-form");

if (searchForm) {
    const searchInput = document.querySelector("#search");
    const categoryInput = document.querySelector("#kategori");
    const searchResults = document.querySelector("#search-results");

    function cariBarang() {
        if (!dataBarangSudahDimuat) {
            return;
        }

        const kataKunci = searchInput.value
            .trim()
            .toLocaleLowerCase("id")
            .split(/\s+/)
            .filter(Boolean);
        const kategori = categoryInput.value.trim().toLocaleLowerCase("id");

        if (kataKunci.length === 0 && !kategori) {
            searchResults.replaceChildren();
            return;
        }

        const hasil = dataBarang.filter(function (barang) {
            const informasiBarang = [
                barang.nama,
                barang.kategori,
                barang.lokasi,
                barang.tanggal,
                barang.status,
                barang.deskripsi
            ].map(function (nilai) {
                return String(nilai || "").toLocaleLowerCase("id");
            }).join(" ");
            const cocokKataKunci = kataKunci.every(function (kata) {
                return informasiBarang.includes(kata);
            });
            const cocokKategori = !kategori ||
                String(barang.kategori || "").trim().toLocaleLowerCase("id") === kategori;
            return cocokKataKunci && cocokKategori;
        });

        tampilkanHasilPencarian(hasil);
    }

    searchInput.addEventListener("input", cariBarang);
    categoryInput.addEventListener("change", cariBarang);

    searchForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        const loadingMessage = document.createElement("p");
        loadingMessage.className = "col-span-full font-mono text-sm text-paper/70";
        loadingMessage.textContent = "Sedang mencari barang...";
        searchResults.replaceChildren(loadingMessage);

        try {
            await muatDataBarang();
            cariBarang();
        } catch (error) {
            console.error("Gagal memuat data pencarian:", error);
            const errorMessage = document.createElement("p");
            errorMessage.className = "col-span-full rounded-lg bg-umm-100 p-4 text-umm-900";
            errorMessage.textContent =
                "Data barang gagal dimuat. Pastikan JSON Server berjalan, lalu coba lagi.";
            searchResults.replaceChildren(errorMessage);
        }
    });
}

function tampilkanHasilPencarian(data) {
    const searchResults = document.querySelector("#search-results");

    if (!searchResults) {
        return;
    }

    searchResults.replaceChildren();
    if (data.length === 0) {
        const emptyMessage = document.createElement("p");
        emptyMessage.className = "col-span-full font-mono text-sm text-paper/70";
        emptyMessage.textContent =
            "Barang tidak ditemukan. Coba kata kunci atau kategori lain.";
        searchResults.appendChild(emptyMessage);
        return;
    }

    data.forEach(function (barang) {
        searchResults.appendChild(buatKartuBarang(barang));
    });
}