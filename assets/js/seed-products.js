import { db } from "./firebase-config.js";
import {
    collection,
    addDoc
} from "firebase/firestore";

const products = [
    {
        nama_produk: "Snack Box Premium",
        kategori: "Snack",
        harga: 7000,
        deskripsi: "Paket snack lengkap untuk berbagai acara.",
        foto: "assets/images/paket-premium.jpg",
        stok: 100
    },
    {
        nama_produk: "Snack Box Hemat",
        kategori: "Snack",
        harga: 5000,
        deskripsi: "Pilihan snack sederhana dengan harga terjangkau.",
        foto: "assets/images/paket-hemat.jpg",
        stok: 100
    },
    {
        nama_produk: "Lontong",
        kategori: "Snack",
        harga: 1500,
        deskripsi: "Olahan beras.",
        foto: "assets/images/lontong.jpg",
        stok: 100
    },
    {
        nama_produk: "Risol",
        kategori: "Snack",
        harga: 1500,
        deskripsi: "isian mayo.",
        foto: "assets/images/risol.jpg",
        stok: 100
    },
    {
        nama_produk: "Agar-Agar",
        kategori: "Snack",
        harga: 500,
        deskripsi: "Olahan Agar.",
        foto: "assets/images/agar.jpg",
        stok: 100
    },
    {
        nama_produk: "Nasi Kotak Ayam",
        kategori: "Nasi Kotak",
        harga: 20000,
        deskripsi: "Nasi kotak lengkap dengan lauk ayam.",
        foto: "assets/images/nasi-kotak.jpg",
        stok: 100
    },
    {
        nama_produk: "Nasi Kotak Premium",
        kategori: "Nasi Kotak",
        harga: 30000,
        deskripsi: "Paket nasi kotak dengan menu lebih lengkap.",
        foto: "assets/images/nasi-kotakP.jpg",
        stok: 100
    },
    {
        nama_produk: "Ayam Ingkung",
        kategori: "Ayam Ingkung",
        harga: 150000,
        deskripsi: "Ayam ingkung untuk acara keluarga dan perayaan.",
        foto: "assets/images/ayam-ingkung.jpg",
        stok: 50
    },
    {
        nama_produk: "Ayam Ingkung Premium",
        kategori: "Ayam Ingkung",
        harga: 200000,
        deskripsi: "Ayam ingkung dengan sajian lengkap.",
        foto: "assets/images/ayam-ingkung.jpg",
        stok: 50
    }
];

async function tambahProduk() {

    for (const product of products) {

        await addDoc(
            collection(db, "products"),
            product
        );

        console.log(
            "Produk berhasil ditambahkan:",
            product.nama_produk
        );
    }

    console.log("SEMUA PRODUK BERHASIL DITAMBAHKAN!");

}

tambahProduk();