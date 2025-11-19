<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
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