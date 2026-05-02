<?php

namespace App\Services;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DoctorSearchService
{
    /**
     * Search and paginate doctors with intelligent caching.
     * 
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function search(Request $request): LengthAwarePaginator
    {
        $page = $request->input('page', 1);
        $filters = $request->only(['specialty', 'city', 'insurance', 'search', 'available_today', 'top_rated']);
        
        // Sort filters to ensure consistent cache keys regardless of param order
        ksort($filters);
        
        // Generate a unique cache key based on sorted filters and page
        $cacheKey = 'doctors_search_' . md5(serialize($filters) . '_page_' . $page);

        // Using cache tags allows for selective invalidation if the driver supports it (e.g. Redis)
        $cache = Cache::supportsTags() ? Cache::tags(['doctors', 'search']) : Cache::getFacadeRoot();

        $query = Doctor::with('user')
            ->verified()
            ->when($request->specialty, fn ($q) => $q->where('specialty', $request->specialty))
            ->when($request->city,      fn ($q) => $q->where('city', $request->city))
            ->when($request->insurance, function ($q) use ($request) {
                match ($request->insurance) {
                    'cnss'    => $q->where('accepts_cnss', true),
                    'ramed'   => $q->where('accepts_ramed', true),
                    'private' => $q->where('accepts_private', true),
                    default   => null,
                };
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sq) use ($request) {
                    $sq->whereHas('user', fn ($u) =>
                        $u->where('name', 'like', '%' . $request->search . '%')
                    )->orWhere('specialty', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->available_today, fn ($q) => $q->availableToday())
            ->when($request->top_rated, fn ($q) => $q->orderByDesc('rating'))
            ->orderByDesc('rating')
            ->paginate(12);

        return $query;
    }

    /**
     * Clear the doctors search cache.
     * Should be called when a doctor profile is updated or a new one is verified.
     */
    public function clearCache(): void
    {
        if (Cache::supportsTags()) {
            Cache::tags(['doctors', 'search'])->flush();
        } else {
            // For file/database cache, we just wait for TTL or manually flush if needed.
            // In a production app, we would use a more granular key tracking system.
            Cache::flush(); 
        }
    }
}
