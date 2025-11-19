<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportMedia;
use App\Services\BarangayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected $barangayService;

    public function __construct(BarangayService $barangayService)
    {
        $this->barangayService = $barangayService;
    }

    /**
     * Display a listing of the reports
     */
    public function index(Request $request)
    {
        $query = Report::with(['user', 'location', 'media']);
        
        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['pending', 'investigating', 'resolved'])) {
            $query->where('status', $request->status);
        }

        // Filter by station if user is a police officer
        if (auth()->user() && auth()->user()->role === 'police') {
            $officer = auth()->user()->officer;
            if ($officer && $officer->station_id) {
                $query->where('assigned_station_id', $officer->station_id);
            }
        }
        
        $reports = $query->orderBy('created_at', 'desc')->paginate(10);
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
                'crime_type' => 'required|string',
                'location_id' => 'required|exists:locations,location_id',
                'is_anonymous' => 'boolean',
            ]);

            $report = Report::create($request->only([
                'user_id',
                'title',
                'description',
                'crime_type',
                'location_id',
                'is_anonymous',
            ]));

            // Automatically assign to the correct police station based on location
            $this->barangayService->assignReportToStation($report);

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