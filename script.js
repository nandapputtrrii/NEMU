const judulUtama = document.querySelector("#beranda h1");

if (judulUtama) {
    judulUtama.textContent = "NEMU UMM";
}


const subJudul = document.querySelector("#beranda h2");

if (subJudul) {
    subJudul.textContent = "Pusat Lost & Found Kampus UMM";
}


const deskripsiBeranda = document.querySelector("#beranda p");

if (deskripsiBeranda) {
    deskripsiBeranda.textContent =
        "Tempat resmi mahasiswa UMM melapor dan mencari barang hilang di area kampus.";
}


const tombolLapor = document.querySelector(
    '#beranda a[href="lapor-hilang.html"]'
);

if (tombolLapor) {
    tombolLapor.textContent = "Buat Laporan Baru";
}


const tombolCari = document.querySelector(
    '#beranda a[href="#cari"]'
);

if (tombolCari) {
    tombolCari.textContent = "Cari Barang Sekarang";
}

const API_URL = "http://localhost:3000/barang";

const laporForm = document.querySelector("#lapor-form");

if (laporForm) {
    const pesanLaporan = document.querySelector("#lapor-pesan");
    const tombolKirimLaporan = document.querySelector("#kirim-laporan");
    const loadingOverlay = document.querySelector("#loading-overlay");
    let laporanTerakhir = null;

    laporForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        const nilai = new FormData(laporForm);
        const kategori = String(nilai.get("kategori")).trim();
        laporanTerakhir = {
            nama: String(nilai.get("namaBarang")).trim(),
            kategori: kategori.charAt(0).toUpperCase() + kategori.slice(1),
            lokasi: String(nilai.get("lokasi")).trim(),
            tanggal: String(nilai.get("tanggal")),
            jam: String(nilai.get("jam")),
            deskripsi: String(nilai.get("deskripsi")).trim(),
            status: "Hilang",
            namaPelapor: String(nilai.get("namaPelapor")).trim(),
            emailPelapor: String(nilai.get("emailPelapor")).trim(),
            teleponPelapor: String(nilai.get("teleponPelapor")).trim()
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

        try {
            const response = await fetch(API_URL, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(laporan)
            });

            if (!response.ok) {
                throw new Error(`Server menolak laporan (HTTP ${response.status})`);
            }

            const dataTersimpan = await response.json();
            laporForm.reset();
            laporanTerakhir = null;
            pesanLaporan.className = "md:col-span-2 rounded-xl bg-green-50 p-4 text-sm text-green-800";

            const judulBerhasil = document.createElement("p");
            judulBerhasil.className = "font-semibold";
            judulBerhasil.textContent = "Laporan berhasil dikirim dan tersimpan.";

            const detailBerhasil = document.createElement("p");
            detailBerhasil.className = "mt-1";
            detailBerhasil.textContent =
                `Data tersimpan: ${dataTersimpan.nama} — status ${dataTersimpan.status}` +
                (dataTersimpan.id ? ` (ID: ${dataTersimpan.id})` : "");

            pesanLaporan.replaceChildren(judulBerhasil, detailBerhasil);
        } catch (error) {
            console.error("Gagal mengirim laporan:", error);
            pesanLaporan.className = "md:col-span-2 rounded-xl bg-red-50 p-4 text-sm text-red-800";

            const pesanError = document.createElement("p");
            pesanError.textContent =
                "Laporan gagal dikirim. Periksa koneksi dan server data, lalu coba lagi.";

            const tombolCobaLagi = document.createElement("button");
            tombolCobaLagi.id = "coba-kirim-lagi";
            tombolCobaLagi.type = "button";
            tombolCobaLagi.className =
                "mt-3 rounded-lg bg-red-700 px-4 py-2 font-semibold text-white transition hover:bg-red-800";
            tombolCobaLagi.textContent = "Coba lagi";

            pesanLaporan.replaceChildren(pesanError, tombolCobaLagi);
        } finally {
            tombolKirimLaporan.disabled = false;
            tombolKirimLaporan.removeAttribute("aria-busy");
            tombolKirimLaporan.textContent = "Kirim Laporan";
            loadingOverlay.classList.remove("flex");
            loadingOverlay.classList.add("hidden");
        }
    }
}

const barangContainer = document.querySelector("#barang-container");
const tombolLihatSemua = document.querySelector("#lihat-semua-barang");
let dataBarang = [];
let tampilkanSemuaBarang = false;

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

    try {

        const response = await fetch(API_URL);

        if (!response.ok) {
            throw new Error("Gagal mengambil data dari API");
        }

        const data = await response.json();

        console.log("Data dari API:", data);

        dataBarang = data;
        const jumlahBarangDitemukan = data.filter(function (barang) {
            return barang.status.toLowerCase() === "ditemukan";
        }).length;

        if (tombolLihatSemua) {
            tombolLihatSemua.disabled = jumlahBarangDitemukan <= 3;
        }
        tampilkanBarang(data);

    } catch (error) {

        console.error("Error:", error);

        if (barangContainer) {
            barangContainer.innerHTML = `
                <p class="col-span-full text-center text-red-500">
                    Gagal mengambil data barang.
                    Pastikan REST API sedang berjalan.
                </p>
            `;
        }
    }
}


function tampilkanBarang(data) {

    if (!barangContainer) {
        return;
    }

    const barangDitemukan = data
        .filter(function (barang) {
            return barang.status.toLowerCase() === "ditemukan";
        })
        .sort(function (barangA, barangB) {
            return barangB.tanggal.localeCompare(barangA.tanggal);
        });
    const barangDitampilkan = tampilkanSemuaBarang
        ? barangDitemukan
        : barangDitemukan.slice(0, 3);

    barangContainer.innerHTML = "";

    // Jika tidak ada data
    if (barangDitampilkan.length === 0) {

        barangContainer.innerHTML = `
            <p class="col-span-full text-center text-slate-500">
                Belum ada barang yang ditemukan.
            </p>
        `;

        return;
    }


    // Membuat card untuk setiap barang
    barangDitampilkan.forEach(function (barang) {

        const card = document.createElement("article");

        card.className =
            "rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-md";


        card.innerHTML = `
            <div class="mb-4 flex h-24 items-center justify-center rounded-xl bg-gradient-to-br from-violet-100 to-indigo-200 text-4xl">
                ${getIcon(barang.kategori)}
            </div>

            <p class="text-sm font-semibold text-violet-600">
                ${barang.kategori}
            </p>

            <h3 class="mt-1 text-xl font-bold text-slate-800">
                ${barang.nama}
            </h3>

            <p class="mt-2 text-sm text-slate-600">
                ${barang.deskripsi}
            </p>

            <p class="mt-3 text-sm font-medium text-slate-500">
                📍 ${barang.lokasi}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Status: ${barang.status}
            </p>
        `;


        barangContainer.appendChild(card);

    });
}

function getIcon(kategori) {

    switch (kategori.toLowerCase()) {

        case "elektronik":
            return "💻";

        case "dokumen":
            return "📄";

        case "aksesoris":
            return "🔑";

        case "tas":
            return "🎒";

        case "pakaian":
            return "👕";

        default:
            return "📦";
    }
}


// Jalankan fungsi API
ambilBarang();

const searchForm = document.querySelector("#search-form");

if (searchForm) {

    searchForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        const searchInput = document.querySelector("#search");

        const keyword = searchInput.value.trim();

        if (keyword === "") {
            ambilBarang();
            return;
        }

        try {

            const response = await fetch(API_URL);

            if (!response.ok) {
                throw new Error("Gagal mengambil data");
            }

            const data = await response.json();


            // Filter berdasarkan nama barang
            const hasil = data.filter(function (barang) {

                return barang.nama
                    .toLowerCase()
                    .includes(keyword.toLowerCase());

            });


            tampilkanHasilPencarian(hasil);


        } catch (error) {

            console.error(error);

        }

    });
}

function tampilkanHasilPencarian(data) {

    const searchResults =
        document.querySelector("#search-results");

    if (!searchResults) {
        return;
    }


    searchResults.innerHTML = "";


    if (data.length === 0) {

        searchResults.innerHTML = `
            <div class="rounded-xl bg-white p-6 text-center shadow">
                <p class="text-slate-500">
                    Barang tidak ditemukan.
                </p>
            </div>
        `;

        return;
    }


    data.forEach(function (barang) {

        const card = document.createElement("article");

        card.className =
            "mb-4 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200";


        card.innerHTML = `
            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-semibold text-violet-600">
                        ${barang.kategori}
                    </p>

                    <h3 class="mt-1 text-xl font-bold">
                        ${barang.nama}
                    </h3>

                    <p class="mt-2 text-sm text-slate-600">
                        ${barang.deskripsi}
                    </p>

                    <p class="mt-2 text-sm text-slate-500">
                        📍 ${barang.lokasi}
                    </p>

                </div>

                <span class="rounded-full bg-violet-100 px-3 py-1 text-sm font-semibold text-violet-700">
                    ${barang.status}
                </span>

            </div>
        `;


        searchResults.appendChild(card);

    });
}