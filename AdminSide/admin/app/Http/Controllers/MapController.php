<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Location;
use Carbon\Carbon;

class MapController extends Controller
{
    public function index()
    {
        return view('view-map');
    }
    
    public function getReports(Request $request)
    {
        $query = Report::with(['location', 'user']);
        
        // Apply date filters if provided
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('date_reported', $request->year);
        }
        
        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('date_reported', $request->month);
        }
        
        if ($request->has('day') && $request->day != '') {
            $query->whereDay('date_reported', $request->day);
        }
        
        // Apply date range filter if provided
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('date_reported', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('date_reported', '<=', $request->date_to);
        }
        
        // Apply status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Apply report type filter
        if ($request->has('report_type') && $request->report_type != '') {
            $query->where('report_type', $request->report_type);
        }
        
        $reports = $query->orderBy('date_reported', 'desc')->get();
        
        // Transform data for map display
        $mapData = $reports->map(function ($report) {
            return [
                'id' => $report->report_id,
                'title' => $report->report_type,
                'description' => $report->description,
                'latitude' => $report->location->latitude,
                'longitude' => $report->location->longitude,
                'location_name' => $report->location->barangay,
                'status' => $report->status,
                'date_reported' => $report->date_reported->timezone('Asia/Manila')->format('Y-m-d H:i:s'),
                'reporter' => $report->user->firstname . ' ' . $report->user->lastname,
                // Determine risk level based on report type or status
                'risk_level' => $this->determineRiskLevel($report)
            ];
        });
        
        return response()->json([
            'reports' => $mapData,
            'total_count' => $reports->count(),
            'stats' => $this->getReportStats($reports)
        ]);
    }
    
    private function determineRiskLevel($report)
    {
        // Define risk levels based on report type or other criteria
        $highRiskTypes = ['emergency', 'violence', 'accident', 'fire'];
        $mediumRiskTypes = ['theft', 'vandalism', 'suspicious'];
        
        if (in_array(strtolower($report->report_type), $highRiskTypes)) {
            return 'high';
        } elseif (in_array(strtolower($report->report_type), $mediumRiskTypes)) {
            return 'medium';
        } else {
            return 'low';
        }
    }
    
    private function getReportStats($reports)
    {
        $stats = [
            'high' => 0,
            'medium' => 0,
            'low' => 0
        ];
        
        foreach ($reports as $report) {
            $riskLevel = $this->determineRiskLevel($report);
            $stats[$riskLevel]++;
        }
        
        return $stats;
    }
}
