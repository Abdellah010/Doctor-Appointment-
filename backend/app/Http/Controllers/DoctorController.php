<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Services\SlotService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoctorController extends Controller
{
    public function __construct(
        private readonly SlotService $slotService,
        private readonly \App\Services\DoctorSearchService $searchService
    ) {}

    /**
     * GET /doctors — list verified doctors with filters
     */
    public function index(Request $request)
    {
        $doctors = $this->searchService->search($request);

        $specialties = [
            'Cardiology', 'Dermatology', 'Endocrinology', 'Gastroenterology', 
            'General Practice', 'Gynecology', 'Neurology', 'Oncology', 
            'Ophthalmology', 'Orthopedics', 'Pediatrics', 'Psychiatry', 
            'Pulmonology', 'Radiology', 'Rheumatology', 'Urology', 'Dentistry'
        ];

        $cities = [
            'Casablanca', 'Rabat', 'Marrakech', 'Fes', 'Tangier', 
            'Agadir', 'Meknes', 'Oujda', 'Kenitra', 'Tetouan', 
            'Safi', 'Mohammedia', 'Khouribga', 'El Jadida', 'Beni Mellal',
            'Nador', 'Taza', 'Settat'
        ];

        return Inertia::render('Doctors/Index', [
            'doctors'    => $doctors,
            'filters'    => $request->only(['specialty', 'city', 'insurance', 'search']),
            'specialties'=> collect($specialties)->sort()->values(),
            'cities'     => collect($cities)->sort()->values(),
        ]);
    }

    /**
     * GET /doctors/{doctor} — doctor profile + calendar
     */
    public function show(Doctor $doctor, Request $request)
    {
        $doctor->load('user');
        $month       = $request->input('month', now()->format('Y-m'));
        $datesWithSlots = $this->slotService->getDatesWithSlots($doctor, $month);

        return Inertia::render('Doctors/Show', [
            'doctor'         => $doctor,
            'datesWithSlots' => $datesWithSlots,
            'month'          => $month,
        ]);
    }

    /**
     * GET /doctors/{doctor}/slots?date=YYYY-MM-DD
     */
    public function slots(Doctor $doctor, Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $slots = $this->slotService->getAvailableSlots($doctor, $request->date);

        return response()->json($slots);
    }
}
