<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Buat 3 user contoh ──────────────────────────────────
        $users = [
            [
                'name'     => 'Andi Mahasiswa',
                'nim'      => '2021001001',
                'email'    => 'andi@mahasiswa.ac.id',
                'phone'    => '081234567890',
                'password' => Hash::make('password123'),
            ],
            [
                'name'     => 'Siti Rahayu',
                'nim'      => '2021001002',
                'email'    => 'siti@mahasiswa.ac.id',
                'phone'    => '082345678901',
                'password' => Hash::make('password123'),
            ],
            [
                'name'     => 'Budi Santoso',
                'nim'      => '2021001003',
                'email'    => 'budi@mahasiswa.ac.id',
                'phone'    => '083456789012',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // ── Buat postingan contoh ──────────────────────────────
        $items = [
            [
                'user_id'       => 1,
                'type'          => 'lost',
                'name'          => 'Kartu Mahasiswa',
                'description'   => 'KTM atas nama Andi NIM 2021001001. Kartu berwarna biru dengan foto di bagian kiri. Hilang di area perpustakaan lantai 2.',
                'location'      => 'Perpustakaan Lantai 2',
                'date_occurred' => '2024-03-10',
                'status'        => 'open',
            ],
            [
                'user_id'       => 2,
                'type'          => 'found',
                'name'          => 'Dompet Coklat',
                'description'   => 'Dompet kulit warna coklat, berisi beberapa kartu dan uang tunai. Ditemukan di kantin utama dekat meja nomor 5.',
                'location'      => 'Kantin Utama',
                'date_occurred' => '2024-03-11',
                'status'        => 'open',
            ],
            [
                'user_id'       => 3,
                'type'          => 'lost',
                'name'          => 'Charger Laptop ASUS',
                'description'   => 'Charger laptop ASUS 65W warna hitam, kepala bulat. Tertinggal di ruang kelas B207 setelah kuliah pukul 14.00.',
                'location'      => 'Ruang Kelas B207',
                'date_occurred' => '2024-03-12',
                'status'        => 'open',
            ],
            [
                'user_id'       => 1,
                'type'          => 'found',
                'name'          => 'Botol Minum Stanley',
                'description'   => 'Botol minum warna hijau merk Stanley ukuran 1L. Ada stiker kucing kecil di bagian bawah. Ditemukan di lobby gedung rektorat.',
                'location'      => 'Lobby Gedung Rektorat',
                'date_occurred' => '2024-03-13',
                'status'        => 'resolved',
            ],
            [
                'user_id'       => 2,
                'type'          => 'lost',
                'name'          => 'Kacamata Minus',
                'description'   => 'Kacamata minus dengan frame bulat warna hitam, lensa sudah agak baret. Hilang saat olahraga di lapangan basket.',
                'location'      => 'Lapangan Basket',
                'date_occurred' => '2024-03-14',
                'status'        => 'open',
            ],
            [
                'user_id'       => 3,
                'type'          => 'found',
                'name'          => 'USB Flash Drive 32GB',
                'description'   => 'Flashdisk warna merah merek SanDisk 32GB. Berisi beberapa file dokumen. Ditemukan jatuh di dekat printer perpustakaan.',
                'location'      => 'Area Printer Perpustakaan',
                'date_occurred' => '2024-03-15',
                'status'        => 'open',
            ],
        ];

        foreach ($items as $itemData) {
            Item::create($itemData);
        }
    }
}
