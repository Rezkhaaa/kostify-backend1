<?php

namespace Database\Seeders;

use App\Models\{ActivityHistory, Billing, Booking, Complaint, Maintenance, Occupancy, Payment, Property, PaymentSetting, PropertyRegistrationRequest, TenantPropertyStatus, Unit, User};
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $kosMelati = Property::create([
            'name' => 'Kos Melati Residence',
            'slug' => 'kos-melati-' . strtolower(Str::random(4)),
            'owner_name' => 'Ibu Rina Melati',
            'phone' => '081234567890',
            'address' => 'Jl. Melati Indah No. 10, Bekasi',
            'gender_type' => 'putri',
            'status' => 'active',
            'package_name' => 'Standard',
            'max_units' => 30,
            'notes' => 'Kos demo utama untuk pengelolaan kamar, tagihan, komplain, dan maintenance.',
        ]);

        $kosMawar = Property::create([
            'name' => 'Kos Mawar Eksklusif',
            'slug' => 'kos-mawar-' . strtolower(Str::random(4)),
            'owner_name' => 'Pak Budi Santoso',
            'phone' => '081288877766',
            'address' => 'Jl. Mawar Raya No. 21, Jakarta Timur',
            'gender_type' => 'putra',
            'status' => 'active',
            'package_name' => 'Premium',
            'max_units' => null,
            'notes' => 'Kos demo kedua untuk menunjukkan fitur multi pemilik kos.',
        ]);

        $kosCempaka = Property::create([
            'name' => 'Kos Cempaka Campuran',
            'slug' => 'kos-cempaka-' . strtolower(Str::random(4)),
            'owner_name' => 'Ibu Lilis Cempaka',
            'phone' => '081399900011',
            'address' => 'Jl. Cempaka Raya No. 7, Tangerang',
            'gender_type' => 'campuran',
            'status' => 'active',
            'package_name' => 'Basic',
            'max_units' => 12,
            'notes' => 'Kos campuran demo agar filter putra, putri, dan campuran terlihat di mobile.',
        ]);

        PaymentSetting::create([
            'property_id' => null,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Kostify Residence',
            'instructions' => 'Transfer sesuai nominal tagihan. Setelah transfer, buka menu Tagihan dan upload bukti pembayaran.',
            'is_active' => true,
        ]);

        PaymentSetting::create([
            'property_id' => $kosMelati->id,
            'bank_name' => 'BCA',
            'account_number' => '1112223334',
            'account_name' => 'Kos Melati Residence',
            'instructions' => 'Transfer sesuai nominal tagihan ke rekening Kos Melati, lalu upload bukti pembayaran dari aplikasi.',
            'is_active' => true,
        ]);

        PaymentSetting::create([
            'property_id' => $kosMawar->id,
            'bank_name' => 'BRI',
            'account_number' => '5556667778',
            'account_name' => 'Kos Mawar Eksklusif',
            'instructions' => 'Transfer sesuai nominal tagihan ke rekening Kos Mawar, lalu upload bukti pembayaran dari aplikasi.',
            'is_active' => true,
        ]);

        PaymentSetting::create([
            'property_id' => $kosCempaka->id,
            'bank_name' => 'Mandiri',
            'account_number' => '9001234567',
            'account_name' => 'Kos Cempaka Campuran',
            'instructions' => 'Transfer sesuai nominal tagihan ke rekening Kos Cempaka, lalu upload bukti pembayaran dari aplikasi.',
            'is_active' => true,
        ]);

        $superAdmin = User::create([
            'name' => 'Super Admin Kostify',
            'email' => 'super@kostify.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '081100000001',
            'address' => 'Kantor Pusat Kostify',
            'status' => 'active',
        ]);

        $adminMelati = User::create([
            'property_id' => $kosMelati->id,
            'name' => 'Admin Kos Melati',
            'email' => 'admin@kostify.com',
            'password' => Hash::make('password'),
            'role' => 'property_admin',
            'phone' => '081234567891',
            'address' => 'Kantor Kos Melati Residence',
            'status' => 'active',
        ]);

        $adminMawar = User::create([
            'property_id' => $kosMawar->id,
            'name' => 'Admin Kos Mawar',
            'email' => 'admin@kosmawar.com',
            'password' => Hash::make('password'),
            'role' => 'property_admin',
            'phone' => '081288877767',
            'address' => 'Kantor Kos Mawar Eksklusif',
            'status' => 'active',
        ]);

        $adminCempaka = User::create([
            'property_id' => $kosCempaka->id,
            'name' => 'Admin Kos Cempaka',
            'email' => 'admin@koscempaka.com',
            'password' => Hash::make('password'),
            'role' => 'property_admin',
            'phone' => '081399900012',
            'address' => 'Kantor Kos Cempaka Campuran',
            'status' => 'active',
        ]);

        $tenants = collect([
            ['property' => $kosMelati, 'name' => 'Andi Pratama', 'email' => 'andi@kostify.com', 'phone' => '081300000101', 'address' => 'Kos Melati Residence Blok A', 'gender' => 'putri'],
            ['property' => $kosMelati, 'name' => 'Maya Lestari', 'email' => 'maya@kostify.com', 'phone' => '081300000102', 'address' => 'Kos Melati Residence Blok B', 'gender' => 'putri'],
            ['property' => $kosMawar, 'name' => 'Rizky Maulana', 'email' => 'rizky@kostify.com', 'phone' => '081300000103', 'address' => 'Kos Mawar Eksklusif Blok C', 'gender' => 'putra'],
            ['property' => $kosMawar, 'name' => 'Nadia Putri', 'email' => 'nadia@kostify.com', 'phone' => '081300000104', 'address' => 'Kos Mawar Eksklusif Blok D', 'gender' => 'putri'],
        ])->map(fn ($item) => User::create([
            'property_id' => $item['property']->id,
            'name' => $item['name'],
            'email' => $item['email'],
            'password' => Hash::make('password'),
            'role' => 'tenant',
            'gender' => $item['gender'],
            'phone' => $item['phone'],
            'address' => $item['address'],
            'status' => 'active',
        ]))->values();

        foreach ($tenants as $tenant) {
            TenantPropertyStatus::updateOrCreate(
                ['user_id' => $tenant->id, 'property_id' => $tenant->property_id],
                ['status' => 'active']
            );
        }

        $units = collect([
            ['property' => $kosMelati, 'name' => 'Kamar Melati A-01', 'price' => 1250000, 'status' => 'occupied', 'floor' => 1, 'area' => 12, 'address' => 'Lantai 1 No. A-01, dekat area parkir'],
            ['property' => $kosMelati, 'name' => 'Kamar Melati A-02', 'price' => 1150000, 'status' => 'available', 'floor' => 1, 'area' => 10, 'address' => 'Lantai 1 No. A-02, dekat dapur bersama'],
            ['property' => $kosMelati, 'name' => 'Kamar Melati B-07', 'price' => 1500000, 'status' => 'available', 'floor' => 2, 'area' => 16, 'address' => 'Lantai 2 No. B-07, dekat balkon'],
            ['property' => $kosMawar, 'name' => 'Kamar Mawar C-12', 'price' => 1000000, 'status' => 'occupied', 'floor' => 1, 'area' => 9, 'address' => 'Lantai 1 No. C-12, dekat ruang tamu'],
            ['property' => $kosMawar, 'name' => 'Kamar Mawar D-03', 'price' => 1750000, 'status' => 'maintenance', 'floor' => 2, 'area' => 18, 'address' => 'Lantai 2 No. D-03, posisi sudut'],
            ['property' => $kosMawar, 'name' => 'Kamar Mawar E-05', 'price' => 950000, 'status' => 'available', 'floor' => 1, 'area' => 8, 'address' => 'Lantai 1 No. E-05, dekat pintu keluar'],
            ['property' => $kosCempaka, 'name' => 'Kamar Cempaka A-03', 'price' => 1100000, 'status' => 'available', 'floor' => 1, 'area' => 11, 'address' => 'Lantai 1 No. A-03, akses dekat lobi'],
            ['property' => $kosCempaka, 'name' => 'Kamar Cempaka B-09', 'price' => 1350000, 'status' => 'available', 'floor' => 2, 'area' => 14, 'address' => 'Lantai 2 No. B-09, dekat ruang bersama'],
        ])->map(fn ($unit) => Unit::create([
            'property_id' => $unit['property']->id,
            'unit_code' => 'KOS-' . strtoupper(Str::random(6)),
            'name' => $unit['name'],
            'type' => 'standar',
            'description' => 'Kamar kos siap huni dengan fasilitas nyaman, akses mudah, dan didukung sistem pengelolaan digital Kostify.',
            'price' => $unit['price'],
            'price_period' => 'bulanan',
            'floor' => $unit['floor'],
            'area' => $unit['area'],
            'capacity' => 1,
            'facilities' => ['Kasur', 'Lemari', 'Meja Belajar', 'WiFi', 'Listrik', 'Air Bersih', 'CCTV Area'],
            'status' => $unit['status'],
            'address' => $unit['address'],
        ]))->values();

        $bookingApproved = Booking::create([
            'property_id' => $kosMelati->id,
            'booking_code' => 'BK-' . strtoupper(Str::random(8)),
            'user_id' => $tenants[0]->id,
            'unit_id' => $units[0]->id,
            'start_date' => Carbon::now()->subMonths(1)->startOfMonth(),
            'end_date' => Carbon::now()->addMonths(11)->endOfMonth(),
            'duration' => 12,
            'total_price' => $units[0]->price * 12,
            'status' => 'approved',
            'notes' => 'Sewa kamar kos bulanan untuk penghuni aktif.',
            'approved_by' => $adminMelati->id,
            'approved_at' => Carbon::now()->subMonth(),
        ]);

        Booking::create([
            'property_id' => $kosMelati->id,
            'booking_code' => 'BK-' . strtoupper(Str::random(8)),
            'user_id' => $tenants[1]->id,
            'unit_id' => $units[1]->id,
            'start_date' => Carbon::now()->addDays(7),
            'end_date' => Carbon::now()->addMonths(6)->addDays(7),
            'duration' => 6,
            'total_price' => $units[1]->price * 6,
            'status' => 'pending',
            'notes' => 'Pengajuan sewa kamar kos untuk calon penghuni.',
        ]);

        $bookingApproved2 = Booking::create([
            'property_id' => $kosMawar->id,
            'booking_code' => 'BK-' . strtoupper(Str::random(8)),
            'user_id' => $tenants[2]->id,
            'unit_id' => $units[3]->id,
            'start_date' => Carbon::now()->subMonths(2)->startOfMonth(),
            'end_date' => Carbon::now()->addMonths(10)->endOfMonth(),
            'duration' => 12,
            'total_price' => $units[3]->price * 12,
            'status' => 'approved',
            'notes' => 'Sewa kamar kos aktif.',
            'approved_by' => $adminMawar->id,
            'approved_at' => Carbon::now()->subMonths(2),
        ]);

        $occupancy1 = Occupancy::create([
            'property_id' => $kosMelati->id,
            'booking_id' => $bookingApproved->id,
            'user_id' => $tenants[0]->id,
            'unit_id' => $units[0]->id,
            'start_date' => $bookingApproved->start_date,
            'end_date' => $bookingApproved->end_date,
            'status' => 'active',
            'notes' => 'Penghuni aktif dengan kontrak 12 bulan.',
        ]);

        $occupancy2 = Occupancy::create([
            'property_id' => $kosMawar->id,
            'booking_id' => $bookingApproved2->id,
            'user_id' => $tenants[2]->id,
            'unit_id' => $units[3]->id,
            'start_date' => $bookingApproved2->start_date,
            'end_date' => $bookingApproved2->end_date,
            'status' => 'active',
            'notes' => 'Penghuni aktif.',
        ]);

        $billingPaid = Billing::create([
            'property_id' => $kosMelati->id,
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-001',
            'user_id' => $tenants[0]->id,
            'unit_id' => $units[0]->id,
            'occupancy_id' => $occupancy1->id,
            'title' => 'Sewa Kamar Melati A-01 ' . now()->translatedFormat('F Y'),
            'amount' => $units[0]->price,
            'tax' => 0,
            'total_amount' => $units[0]->price,
            'billing_period_start' => now()->startOfMonth(),
            'billing_period_end' => now()->endOfMonth(),
            'due_date' => now()->addDays(7),
            'status' => 'paid',
            'notes' => 'Tagihan sewa bulanan kamar kos.',
            'created_by' => $adminMelati->id,
        ]);

        Billing::create([
            'property_id' => $kosMelati->id,
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-002',
            'user_id' => $tenants[0]->id,
            'unit_id' => $units[0]->id,
            'occupancy_id' => $occupancy1->id,
            'title' => 'Service Charge & Kebersihan Area Kos',
            'amount' => 150000,
            'tax' => 0,
            'total_amount' => 150000,
            'billing_period_start' => now()->startOfMonth(),
            'billing_period_end' => now()->endOfMonth(),
            'due_date' => now()->addDays(5),
            'status' => 'unpaid',
            'notes' => 'Biaya operasional kebersihan dan keamanan.',
            'created_by' => $adminMelati->id,
        ]);

        Billing::create([
            'property_id' => $kosMawar->id,
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-003',
            'user_id' => $tenants[2]->id,
            'unit_id' => $units[3]->id,
            'occupancy_id' => $occupancy2->id,
            'title' => 'Sewa Kamar Mawar C-12 ' . now()->translatedFormat('F Y'),
            'amount' => $units[3]->price,
            'tax' => 0,
            'total_amount' => $units[3]->price,
            'billing_period_start' => now()->startOfMonth(),
            'billing_period_end' => now()->endOfMonth(),
            'due_date' => now()->addDays(8),
            'status' => 'unpaid',
            'created_by' => $adminMawar->id,
        ]);

        Payment::create([
            'property_id' => $kosMelati->id,
            'payment_code' => 'PAY-' . strtoupper(Str::random(10)),
            'billing_id' => $billingPaid->id,
            'user_id' => $tenants[0]->id,
            'amount' => $billingPaid->total_amount,
            'method' => 'transfer',
            'status' => 'success',
            'reference_number' => 'TRX-' . now()->format('Ymd') . '-A01',
            'notes' => 'Transfer bank untuk sewa bulan berjalan.',
            'paid_at' => now()->subDays(2),
            'confirmed_by' => $adminMelati->id,
            'confirmed_at' => now()->subDays(2),
        ]);

        Complaint::create([
            'property_id' => $kosMelati->id,
            'complaint_code' => 'CMP-' . strtoupper(Str::random(8)),
            'user_id' => $tenants[0]->id,
            'unit_id' => $units[0]->id,
            'title' => 'Lampu koridor depan kamar kos mati',
            'description' => 'Area depan kamar kos kurang terang pada malam hari sehingga mengganggu aktivitas penghuni.',
            'category' => 'keamanan',
            'priority' => 'medium',
            'status' => 'in_progress',
            'admin_response' => 'Tim operasional sudah dijadwalkan mengganti lampu koridor.',
            'handled_by' => $adminMelati->id,
        ]);

        Complaint::create([
            'property_id' => $kosMawar->id,
            'complaint_code' => 'CMP-' . strtoupper(Str::random(8)),
            'user_id' => $tenants[2]->id,
            'unit_id' => $units[3]->id,
            'title' => 'Parkir depan kamar kos sering tertutup kendaraan lama',
            'description' => 'Mohon penertiban parkir supaya akses penghuni lebih lancar.',
            'category' => 'keamanan',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        Maintenance::create([
            'property_id' => $kosMelati->id,
            'maintenance_code' => 'MNT-' . strtoupper(Str::random(8)),
            'user_id' => $tenants[0]->id,
            'unit_id' => $units[0]->id,
            'title' => 'Perbaikan pintu kamar',
            'description' => 'Pintu kamar terasa berat saat dibuka dan perlu pengecekan engsel.',
            'category' => 'structural',
            'priority' => 'high',
            'status' => 'approved',
            'admin_notes' => 'Teknisi dijadwalkan melakukan pengecekan engsel.',
            'scheduled_date' => now()->addDays(2),
            'cost' => 0,
            'cost_payer' => 'pengelola',
            'handled_by' => $adminMelati->id,
        ]);

        Maintenance::create([
            'property_id' => $kosMawar->id,
            'maintenance_code' => 'MNT-' . strtoupper(Str::random(8)),
            'user_id' => $tenants[2]->id,
            'unit_id' => $units[3]->id,
            'title' => 'Pengecekan stop kontak lantai 2',
            'description' => 'Beberapa stop kontak lantai 2 tidak stabil.',
            'category' => 'electrical',
            'priority' => 'medium',
            'status' => 'completed',
            'admin_notes' => 'MCB dan stop kontak telah dicek, satu titik diganti.',
            'completed_date' => now()->subDays(3),
            'cost' => 185000,
            'cost_payer' => 'pengelola',
            'handled_by' => $adminMawar->id,
        ]);

        ActivityHistory::create([
            'property_id' => $kosMelati->id,
            'user_id' => $tenants[0]->id,
            'action' => 'create',
            'module' => 'booking',
            'description' => 'Penghuni mengajukan dan menjalankan sewa Kamar Melati A-01',
            'subject_id' => $bookingApproved->id,
            'subject_type' => Booking::class,
        ]);

        ActivityHistory::create([
            'property_id' => $kosMelati->id,
            'user_id' => $adminMelati->id,
            'action' => 'approve',
            'module' => 'booking',
            'description' => 'Admin menyetujui booking Kamar Melati A-01',
            'subject_id' => $bookingApproved->id,
            'subject_type' => Booking::class,
        ]);

        PropertyRegistrationRequest::create([
            'owner_name' => 'Ibu Sari Wijaya',
            'email' => 'owner@kosanggrek.com',
            'phone' => '081355566677',
            'property_name' => 'Kos Anggrek Putri',
            'property_address' => 'Jl. Anggrek No. 8, Depok',
            'gender_type' => 'putri',
            'room_count' => 18,
            'password' => Hash::make('password'),
            'status' => 'pending',
        ]);
    }
}
