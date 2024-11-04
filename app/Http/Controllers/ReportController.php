<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\InspectionCategory;
use App\Models\Inspector;
use App\Models\Nationality;
use App\Models\Report;
use App\Models\Visit;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function manageReport()
    {
        $nationalities = Nationality::withTrashed()->whereIn('id', function ($query) {
            $query->select('nationality_id')
                ->from('inspectors')
                ->join('inspections', 'inspectors.id', '=', 'inspections.inspector_id')
                ->distinct();
        })->orderBy('country_name')
            ->get();

        $categories = InspectionCategory::withTrashed()->get();

        $categoryWiseInspectors = Inspection::with(['inspector' => function ($query) {
            $query->withTrashed();
        }, 'category'])
            ->select('category_id', 'inspector_id')
            ->join('inspectors', 'inspections.inspector_id', '=', 'inspectors.id')
            ->join('nationalities', 'inspectors.nationality_id', '=', 'nationalities.id')
            ->withTrashed()
            ->select(
                'nationalities.country_name as country',
                'inspections.category_id',
                DB::raw('count(inspections.inspector_id) as total')
            )
            ->groupBy('nationalities.country_name', 'inspections.category_id')
            ->get();

        $finalData = [];
        foreach ($nationalities as $nationality) {
            $row = ['country' => $nationality->country_name];

            foreach ($categories as $category) {

                $inspectorCount = $categoryWiseInspectors->firstWhere(function ($item) use ($nationality, $category) {
                    return $item->country === $nationality->country_name && $item->category_id === $category->id;
                });


                $row[$category->category_name] = (int)($inspectorCount->total ?? 0);
            }


            $finalData[] = $row;
        }

        return view('manage-report', compact('finalData', 'categories'));
    }




    public function showByCountry($country)
    {
        $inspections = Inspection::with(['inspector' => function ($query) {
            $query->withTrashed();
        }, 'category' => function ($query) {
            $query->withTrashed();
        }])
            ->join('inspectors', 'inspections.inspector_id', '=', 'inspectors.id')
            ->join('nationalities', 'inspectors.nationality_id', '=', 'nationalities.id')
            ->where('nationalities.country_name', $country)
            ->select('inspections.*')
            ->get();

        return view('inspections_by_country', compact('inspections', 'country'));
    }
    public function listInspectors()
    {
        $inspectors = Inspector::with([
            'inspections' => function ($query) {
                $query->withTrashed()->with(['category', 'status']); // Eager load category and status
            }, 
            'visits' => function ($query) {
                $query->withTrashed(); 
            }, 
            'gender', 
            'rank', 
            'nationality'
        ]) 
        ->withTrashed()
        ->get();
    
            $inspectorsData = $inspectors->map(function ($inspector) {
                return [
                    'id' => $inspector->id,
                    'name' => $inspector->name ?? 'N/A',
                    'gender' => $inspector->gender->gender_name ?? 'N/A',
                    'dob' => $inspector->dob ?? 'N/A',
                    'nationality' => $inspector->nationality ?? 'N/A',
                    'passport_number' => $inspector->passport_number ?? 'N/A',
                    'unlp_number' => $inspector->unlp_number ?? 'N/A',
                    'rank' => $inspector->rank->rank_name ?? 'N/A',
                    'qualifications' => $inspector->qualifications ?? 'N/A',
                    'professional_experience' => $inspector->professional_experience ?? 'N/A',
                    'clearance_certificate' => $inspector->clearance_certificate ?? 'N/A',
                    'remarks' => $inspector->remarks ?? 'N/A',
                    'is_active' => $inspector->is_active ?? false,
                    'inspections' => $inspector->inspections,
                    'visits' => $inspector->visits,
                ];
            });
            

        // dd($inspectorsData);
    
        return view('list_inspectors', compact('inspectorsData'));
    }
    
}
