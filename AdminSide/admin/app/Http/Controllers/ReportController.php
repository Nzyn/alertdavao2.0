<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportMedia;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Assign a report to the correct police station based on its location
     */
    private function assignReportToStation(Report $report)
    {
        try {
            if (!$report->location_id || !$report->location) {
                \Log::warning('Cannot assign report: no location', ['report_id' => $report->report_id]);
                return;
            }

            $location = $report->location;
            
            // Validate location has coordinates
            if (!$location->latitude || !$location->longitude) {
                \Log::warning('Cannot assign report: no coordinates', ['location_id' => $location->location_id]);
                return;
            }

            // Find barangay by coordinates (using proximity search)
            // ±0.01 degrees ≈ 1.1 km at equator
            $barangay = Barangay::whereBetween('latitude', [$location->latitude - 0.01, $location->latitude + 0.01])
                ->whereBetween('longitude', [$location->longitude - 0.01, $location->longitude + 0.01])
                ->first();

            if ($barangay && $barangay->station_id) {
                $report->assigned_station_id = $barangay->station_id;
                $report->save();
                \Log::info('Report assigned to station', [
                    'report_id' => $report->report_id,
                    'station_id' => $barangay->station_id,
                    'barangay' => $barangay->name ?? 'Unknown'
                ]);
            } else {
                \Log::warning('No barangay found for coordinates', [
                    'report_id' => $report->report_id,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error assigning report to station: ' . $e->getMessage(), [
                'report_id' => $report->report_id
            ]);
        }
    }

    /**
     * Display a listing of the reports
     */
    public function index(Request $request)
    {
        $query = Report::with(['user', 'location', 'media'])
            ->join('locations', 'reports.location_id', '=', 'locations.location_id');
        
        // Exclude reports without a location - must have valid coordinates
        $query->where('locations.latitude', '!=', 0)
              ->where('locations.longitude', '!=', 0)
              ->where('locations.latitude', '!=', null)
              ->where('locations.longitude', '!=', null);
        
        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['pending', 'investigating', 'resolved'])) {
            $query->where('reports.status', $request->status);
        }

        // Filter by station if user is a police officer
        if (auth()->user() && auth()->user()->role === 'police') {
            $userStationId = auth()->user()->station_id;
            if ($userStationId) {
                // For police officers: show reports assigned to their station AND have valid location
                // Do NOT show unassigned reports if they don't have a location
                $query->where('reports.assigned_station_id', $userStationId);
            }
        }
        // For admin users, show ALL reports (excluding those without valid location)
        
        $reports = $query->select('reports.*')
                         ->orderBy('reports.created_at', 'desc')
                         ->paginate(10);
        return view('reports', compact('reports'));
    }

    /**
     * Update the status of a report
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,investigating,resolved'
            ]);

            $report = Report::findOrFail($id);
            $report->status = $request->status;
            $report->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a new report with automatic station assignment
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'report_type' => 'required|string',
                'location_id' => 'required|exists:locations,location_id',
                'is_anonymous' => 'boolean',
            ]);

            $report = Report::create(array_merge($request->only([
                'user_id',
                'title',
                'description',
                'report_type',
                'location_id',
                'is_anonymous',
            ]), [
                'date_reported' => now(),
            ]));

            // Automatically assign to the correct police station based on location
            $this->assignReportToStation($report);

            return response()->json([
                'success' => true,
                'message' => 'Report created and assigned to the appropriate police station',
                'data' => $report->load(['user', 'location']),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create report: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get report details for modal display
     */
    public function getDetails($id)
    {
        try {
            $report = Report::with(['user', 'location', 'media'])->findOrFail($id);
            return response()->json(['success' => true, 'data' => $report]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load report details: ' . $e->getMessage()], 500);
        }
    }
}