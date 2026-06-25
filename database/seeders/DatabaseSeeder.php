<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Kost;
use App\Models\KostPhoto;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name'       => 'Admin KostKu',
            'email'      => 'admin@kostku.id',
            'phone'      => '08001234567',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'gender'     => 'Laki-laki',
            'occupation' => 'Administrator',
            'status'     => 'active',
        ]);

        // Sample users
        $users = [];
        $userData = [
            ['name' => 'Budi Santoso',   'email' => 'user@kostku.id',   'gender' => 'Laki-laki',  'occupation' => 'Mahasiswa'],
            ['name' => 'Sari Dewi',      'email' => 'sari@email.com',   'gender' => 'Perempuan',  'occupation' => 'Karyawan Swasta'],
            ['name' => 'Ahmad Fauzi',    'email' => 'ahmad@email.com',  'gender' => 'Laki-laki',  'occupation' => 'Freelancer'],
            ['name' => 'Rina Marlina',   'email' => 'rina@email.com',   'gender' => 'Perempuan',  'occupation' => 'Mahasiswa'],
            ['name' => 'Doni Pratama',   'email' => 'doni@email.com',   'gender' => 'Laki-laki',  'occupation' => 'Karyawan Swasta'],
        ];

        foreach ($userData as $ud) {
            $users[] = User::create([
                'name'       => $ud['name'],
                'email'      => $ud['email'],
                'phone'      => '08' . rand(100000000, 999999999),
                'password'   => Hash::make('password'),
                'role'       => 'user',
                'gender'     => $ud['gender'],
                'occupation' => $ud['occupation'],
                'status'     => 'active',
            ]);
        }

        // Sample Kost data
        $kostData = [
            [
                'name'            => 'Kost Melati Indah',
                'description'     => 'Kost nyaman dan bersih di lokasi strategis dekat kampus dan pusat perbelanjaan. Lingkungan aman dengan keamanan 24 jam. Cocok untuk mahasiswa dan karyawan.',
                'address'         => 'Jl. Margonda Raya No. 45',
                'city'            => 'Depok',
                'province'        => 'Jawa Barat',
                'postal_code'     => '16424',
                'type'            => 'Putri',
                'price_monthly'   => 1200000,
                'price_yearly'    => 12000000,
                'total_rooms'     => 20,
                'available_rooms' => 5,
                'facilities'      => ['AC', 'Kasur', 'Lemari', 'Meja Belajar', 'WiFi', 'Kamar Mandi Dalam'],
                'shared_facilities'=> ['Dapur Bersama', 'Parkir Motor', 'CCTV', 'Laundry'],
                'owner_name'      => 'Bu Siti Rahayu',
                'owner_phone'     => '08123456789',
                'status'          => 'active',
                'is_featured'     => true,
                'min_stay'        => 1,
                'allow_cooking'   => true,
                'allow_pets'      => false,
                'allow_guest'     => true,
                'entry_time'      => '06:00',
                'exit_time'       => '22:00',
                'rules'           => ['Jam malam pukul 22.00', 'Dilarang merokok di dalam kamar', 'Wajib menjaga kebersihan'],
            ],
            [
                'name'            => 'Kost Mahkota Jaya',
                'description'     => 'Kost eksklusif dengan fasilitas lengkap di jantung kota Jakarta. Tersedia AC, WiFi berkecepatan tinggi, dan kamar mandi dalam setiap kamar.',
                'address'         => 'Jl. Sudirman No. 88, Karet Tengsin',
                'city'            => 'Jakarta',
                'province'        => 'DKI Jakarta',
                'postal_code'     => '10250',
                'type'            => 'Campur',
                'price_monthly'   => 2500000,
                'price_yearly'    => 25000000,
                'total_rooms'     => 30,
                'available_rooms' => 8,
                'facilities'      => ['AC', 'Kasur', 'Lemari', 'TV', 'WiFi', 'Kamar Mandi Dalam', 'Water Heater', 'Kulkas Mini'],
                'shared_facilities'=> ['Parkir Motor', 'Parkir Mobil', 'CCTV', 'Satpam', 'Gym', 'Laundry'],
                'owner_name'      => 'Pak Hendra Gunawan',
                'owner_phone'     => '08234567890',
                'status'          => 'active',
                'is_featured'     => true,
                'min_stay'        => 3,
                'allow_cooking'   => false,
                'allow_pets'      => false,
                'allow_guest'     => true,
                'entry_time'      => '07:00',
                'exit_time'       => '23:00',
                'rules'           => ['Tamu diizinkan hingga pukul 22.00', 'Dilarang merokok di area kost'],
            ],
            [
                'name'            => 'Kost Putra Mandiri',
                'description'     => 'Kost khusus putra dengan harga terjangkau. Lokasi strategis dekat dengan kampus UGM dan pusat kota Yogyakarta.',
                'address'         => 'Jl. Kaliurang KM 5, Sleman',
                'city'            => 'Yogyakarta',
                'province'        => 'DI Yogyakarta',
                'postal_code'     => '55281',
                'type'            => 'Putra',
                'price_monthly'   => 800000,
                'price_yearly'    => 8400000,
                'total_rooms'     => 15,
                'available_rooms' => 3,
                'facilities'      => ['Kipas Angin', 'Kasur', 'Lemari', 'Meja Belajar', 'WiFi'],
                'shared_facilities'=> ['Dapur Bersama', 'Parkir Motor', 'Mushola', 'Jemuran'],
                'owner_name'      => 'Pak Bambang Susilo',
                'owner_phone'     => '08345678901',
                'status'          => 'active',
                'is_featured'     => false,
                'min_stay'        => 1,
                'allow_cooking'   => true,
                'allow_pets'      => false,
                'allow_guest'     => false,
                'entry_time'      => '06:00',
                'exit_time'       => '21:00',
                'rules'           => ['Jam malam pukul 21.00', 'Wajib sholat Subuh berjemaah (opsional)'],
            ],
            [
                'name'            => 'Kost Green Garden',
                'description'     => 'Kost dengan konsep taman hijau yang asri. Suasana nyaman dan segar, cocok untuk yang ingin tinggal jauh dari kebisingan kota.',
                'address'         => 'Jl. Buah Batu No. 210, Lengkong',
                'city'            => 'Bandung',
                'province'        => 'Jawa Barat',
                'postal_code'     => '40265',
                'type'            => 'Campur',
                'price_monthly'   => 1500000,
                'price_yearly'    => 15000000,
                'total_rooms'     => 25,
                'available_rooms' => 10,
                'facilities'      => ['AC', 'Kasur', 'Lemari', 'WiFi', 'Kamar Mandi Dalam', 'Balkon'],
                'shared_facilities'=> ['Taman', 'Parkir Motor', 'Laundry', 'Dapur Bersama', 'CCTV'],
                'owner_name'      => 'Bu Ratna Dewi',
                'owner_phone'     => '08456789012',
                'status'          => 'active',
                'is_featured'     => true,
                'min_stay'        => 3,
                'allow_cooking'   => true,
                'allow_pets'      => true,
                'allow_guest'     => true,
                'entry_time'      => '06:00',
                'exit_time'       => '23:00',
                'rules'           => ['Jaga kebersihan taman', 'Hewan peliharaan harus divaksin'],
            ],
        ];

        foreach ($kostData as $kd) {
            $kost = Kost::create(array_merge($kd, [
                'slug'       => Str::slug($kd['name']) . '-' . Str::random(6),
                'created_by' => $admin->id,
                'latitude'   => -6.2 + (rand(-10, 10) * 0.01),
                'longitude'  => 106.8 + (rand(-10, 10) * 0.01),
            ]));

            // Add rooms
            for ($i = 1; $i <= min(5, $kd['total_rooms']); $i++) {
                Room::create([
                    'kost_id'     => $kost->id,
                    'room_number' => 'A' . $i,
                    'floor'       => ceil($i / 5),
                    'size'        => rand(12, 25),
                    'price'       => $kd['price_monthly'],
                    'status'      => $i <= $kd['available_rooms'] ? 'available' : 'occupied',
                    'facilities'  => $kd['facilities'],
                ]);
            }

            // Add sample bookings
            foreach (array_slice($users, 0, 2) as $user) {
                $booking = Booking::create([
                    'user_id'        => $user->id,
                    'kost_id'        => $kost->id,
                    'check_in_date'  => now()->subMonths(rand(1, 6)),
                    'duration_months'=> rand(1, 6),
                    'total_price'    => $kd['price_monthly'] * rand(1, 6),
                    'deposit'        => $kd['price_monthly'],
                    'payment_status' => 'paid',
                    'booking_status' => 'active',
                    'payment_method' => 'transfer_bca',
                    'paid_at'        => now()->subMonths(rand(1, 3)),
                    'confirmed_at'   => now()->subMonths(rand(1, 3)),
                ]);

                // Add review
                Review::create([
                    'user_id'    => $user->id,
                    'kost_id'    => $kost->id,
                    'booking_id' => $booking->id,
                    'rating'     => rand(4, 5),
                    'comment'    => 'Kost yang sangat nyaman dan bersih. Pemilik ramah dan responsif. Fasilitas lengkap dan harga terjangkau. Sangat direkomendasikan!',
                    'is_approved'=> true,
                ]);
            }
        }
    }
}
