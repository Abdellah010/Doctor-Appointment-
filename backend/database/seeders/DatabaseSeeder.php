<?php

namespace Database\Seeders;

use App\Enums\DoctorStatus;
use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Slot;
use App\Models\User;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function __construct(private readonly SlotService $slotService) {}

    public function run(): void
    {
        // ── Admin ──
        $admin = User::create([
            'name'     => 'Admin DocAppoint',
            'email'    => 'admin@docappoint.ma',
            'password' => Hash::make('password'),
            'role'     => UserRole::Admin,
            'phone'    => '+212600000001',
        ]);

        // ── Verified Doctors ──
        $doctors = [
            ['Sara Alaoui',     'sara@docappoint.ma',     'Cardiology',    'Casablanca', 300, 4.9, 84, ['mon','tue','thu','fri']],
            ['Karim Mansouri',  'karim@docappoint.ma',    'Neurology',     'Rabat',      350, 4.7, 62, ['mon','wed','fri']],
            ['Leila Benali',    'leila@docappoint.ma',    'Dermatology',   'Casablanca', 280, 4.8, 71, ['tue','wed','thu','fri']],
            ['Youssef Razi',    'youssef@docappoint.ma',  'Orthopedics',   'Fes',        400, 4.6, 48, ['mon','tue','wed','thu']],
            ['Nadia El Fassi',  'nadia@docappoint.ma',    'General',       'Rabat',      200, 4.9, 120,['mon','tue','wed','thu','fri']],
        ];

        $doctorModels = [];
        foreach ($doctors as [$name, $email, $specialty, $city, $fee, $rating, $count, $days]) {
            $user = User::create([
                'name'     => "Dr. $name",
                'email'    => $email,
                'password' => Hash::make('password'),
                'role'     => UserRole::Doctor,
                'phone'    => '+212661' . rand(100000, 999999),
            ]);

            $doctor = Doctor::create([
                'user_id'          => $user->id,
                'specialty'        => $specialty,
                'city'             => $city,
                'license_number'   => 'MA-' . strtoupper(substr($specialty,0,3)) . '-2022-' . rand(1000,9999),
                'consultation_fee' => $fee,
                'status'           => DoctorStatus::Verified,
                'bio'              => "Board-certified {$specialty} specialist with over 10 years of experience.",
                'available_days'   => $days,
                'slot_duration'    => 30,
                'rating'           => $rating,
                'rating_count'     => $count,
                'accepts_cnss'     => true,
                'accepts_ramed'    => rand(0,1),
                'accepts_private'  => true,
            ]);

            $this->slotService->generateForDoctor($doctor, 30);
            $doctorModels[] = $doctor;
        }

        // ── Pending Doctors ──
        foreach ([
            ['Hassan Benkirane', 'hassan@mail.ma', 'Pediatrics',     'Casablanca'],
            ['Maryam Ouali',     'maryam@mail.ma', 'Ophthalmology',  'Rabat'],
            ['Rachid Tahiri',    'rachid@mail.ma', 'Gastroenterology','Fes'],
        ] as [$name, $email, $specialty, $city]) {
            $user = User::create([
                'name'     => "Dr. $name",
                'email'    => $email,
                'password' => Hash::make('password'),
                'role'     => UserRole::Doctor,
            ]);
            Doctor::create([
                'user_id'        => $user->id,
                'specialty'      => $specialty,
                'city'           => $city,
                'license_number' => 'MA-PND-2024-' . rand(1000,9999),
                'consultation_fee'=> rand(200,400),
                'status'         => DoctorStatus::Pending,
                'available_days' => ['mon','tue','wed','thu','fri'],
                'slot_duration'  => 30,
            ]);
        }

        // ── Patients ──
        $patients = [];
        foreach ([
            ['Ahmed Benali',    'ahmed@gmail.com',   '+212661234567', 'cnss'],
            ['Fatima Zahra',    'fatima@gmail.com',  '+212662234567', 'ramed'],
            ['Omar Idrissi',    'omar@gmail.com',    '+212663234567', 'private'],
            ['Hind Amrani',     'hind@gmail.com',    '+212664234567', 'cnss'],
            ['Younes Kabbaj',   'younes@gmail.com',  '+212665234567', 'none'],
        ] as [$name, $email, $phone, $insurance]) {
            $patients[] = User::create([
                'name'           => $name,
                'email'          => $email,
                'password'       => Hash::make('password'),
                'role'           => UserRole::Patient,
                'phone'          => $phone,
                'insurance_type' => $insurance,
            ]);
        }

        // ── Sample Appointments ──
        $firstDoctor = $doctorModels[0];
        $availableSlot = Slot::where('doctor_id', $firstDoctor->id)
            ->where('is_booked', false)
            ->where('date', '>=', now()->toDateString())
            ->first();

        if ($availableSlot && !empty($patients)) {
            Appointment::create([
                'patient_id'    => $patients[0]->id,
                'doctor_id'     => $firstDoctor->id,
                'slot_id'       => $availableSlot->id,
                'scheduled_at'  => Carbon::parse($availableSlot->date)->format('Y-m-d') . ' ' . $availableSlot->start_time,
                'duration_minutes' => 30,
                'status'        => 'confirmed',
                'reason'        => 'Routine cardiac checkup and ECG review',
                'insurance_type'=> 'cnss',
                'fee'           => $firstDoctor->consultation_fee,
                'sms_sent'      => true,
            ]);

            $availableSlot->update(['is_booked' => true]);
        }

        $this->command->info('DocAppoint seeded successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',   'admin@docappoint.ma', 'password'],
                ['Doctor',  'sara@docappoint.ma',  'password'],
                ['Patient', 'ahmed@gmail.com',     'password'],
            ]
        );
    }
}
