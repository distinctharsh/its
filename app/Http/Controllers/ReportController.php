<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\EntryExitPoint;
use App\Models\EscortOfficer;
use App\Models\Inspection;
use App\Models\InspectionCategory;
use App\Models\InspectionCategoryType;
use App\Models\InspectionProperties;
use App\Models\InspectionType;
use App\Models\Inspector;
use App\Models\Nationality;
use App\Models\OpcwFax;
use App\Models\OtherStaff;
use App\Models\Rank;
use App\Models\SiteCode;
use App\Models\State;
use App\Models\Status;
use App\Models\Visit;
use App\Models\VisitCategory;
use App\Models\VisitSiteMapping;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{

    public function manageReport()
    {
        // $nationalities = Nationality::withTrashed()->whereIn('id', function ($query) {
        //     $query->select('nationality_id')
        //         ->from('inspectors')
        //         ->join('inspections', 'inspectors.id', '=', 'inspections.inspector_id')
        //         ->distinct();
        // })->orderBy('country_name')
        //     ->get();


       $nationalities = Nationality::withTrashed()
        ->whereIn('id', function ($query) {
            $query->select('nationality_id')
                ->from('inspectors')
                ->join('inspections', 'inspectors.id', '=', 'inspections.inspector_id')
                ->where(function($q) {
                    $q->whereNull('inspectors.deleted_at')
                      ->where('inspectors.is_draft', 0)
                      ->where('inspectors.is_reverted', 0);
                })
                ->whereNull('inspections.deleted_at') // Only check deleted_at for inspections
                ->distinct();
        })
        ->orderBy('country_name')
        ->get();


        $categories = InspectionCategory::withTrashed()->get();

        // $categoryWiseInspectors = Inspection::with(['inspector' => function ($query) {
        //     $query->withTrashed();
        // }, 'category'])
        //     ->select('category_id', 'inspector_id')
        //     ->join('inspectors', 'inspections.inspector_id', '=', 'inspectors.id')
        //     ->join('nationalities', 'inspectors.nationality_id', '=', 'nationalities.id')
        //     ->withTrashed()
        //     ->select(
        //         'nationalities.country_name as country',
        //         'inspections.category_id',
        //         DB::raw('count(inspections.inspector_id) as total')
        //     )
        //     ->groupBy('nationalities.country_name', 'inspections.category_id')
        //     ->get();



          $categoryWiseInspectors = Inspection::with(['inspector' => function ($query) {
            $query->whereNull('deleted_at')
                  ->where('is_draft', 0)
                  ->where('is_reverted', 0);
        }, 'category'])
        ->select('category_id', 'inspector_id')
        ->join('inspectors', 'inspections.inspector_id', '=', 'inspectors.id')
        ->join('nationalities', 'inspectors.nationality_id', '=', 'nationalities.id')
        ->whereNull('inspections.deleted_at')
        ->where(function($query) {
            $query->whereNull('inspectors.deleted_at')
                  ->where('inspectors.is_draft', 0)
                  ->where('inspectors.is_reverted', 0);
        })
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
            $totalInspectors = 0;

            foreach ($categories as $category) {

                $inspectorCount = $categoryWiseInspectors->firstWhere(function ($item) use ($nationality, $category) {
                    return $item->country === $nationality->country_name && $item->category_id === $category->id;
                });

                $count = (int)($inspectorCount->total ?? 0);
                $row[$category->category_name] = $count;
                $totalInspectors += $count; // Add to the total


                $row[$category->category_name] = (int)($inspectorCount->total ?? 0);
            }

            $row['total'] = $totalInspectors;
            $finalData[] = $row;
        }

        return view('manage-report', compact('finalData', 'categories'));
    }

    // public function yearwiseReport()
    // {
    //     // Fetch inspection types and categories
    //     $inspectionTypes = InspectionType::whereNull('deleted_at')->get();
    //     $categories = InspectionCategory::withTrashed()->get();

    //     // Fetch yearly data grouped by year and type of inspection using JOIN
    //     $visits = DB::table('visits')
    //         ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
    //         ->join('inspection_types', 'visit_site_mappings.inspection_category_id', '=', 'inspection_types.id')
    //         ->selectRaw("
    //         YEAR(visits.arrival_datetime) as year,
    //         SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END) as schedule_1,
    //         SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END) as schedule_2,
    //         SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END) as schedule_3,
    //         SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END) as ocpf,
    //         COUNT(visits.id) as total
    //     ")
    //         ->whereNotNull('visits.arrival_datetime')
    //         ->groupBy(DB::raw("YEAR(visits.arrival_datetime)"))
    //         ->orderBy('year', 'ASC')
    //         ->get() ?? collect();

    //     return view('year-wisereport', compact('inspectionTypes', 'visits', 'categories'));
    // }


    public function yearwiseReport()
    {
        // Fetch inspection types and categories
        $inspectionTypes = InspectionType::whereNull('deleted_at')->get();
        $categories = InspectionCategory::withTrashed()->get();

        // Fetch yearly data grouped by year and type of inspection using JOIN
        $visits = DB::table('visits')
            ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->join('inspection_types', 'visit_site_mappings.inspection_category_id', '=', 'inspection_types.id')
            ->selectRaw("
                YEAR(visits.arrival_datetime) as year,
                SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END) as schedule_1,
                SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END) as schedule_2,
                SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END) as schedule_3,
                SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END) as ocpf,
                COUNT(visits.id) as total
            ")
            ->whereNotNull('visits.arrival_datetime')
            ->where(function($query) {
                $query->whereNull('visits.deleted_at')
                    ->where('visits.is_draft', 0)
                    ->where('visits.is_reverted', 0);
            })
            ->whereNull('visit_site_mappings.deleted_at') // Add if visit_site_mappings has soft deletes
            ->groupBy(DB::raw("YEAR(visits.arrival_datetime)"))
            ->orderBy('year', 'ASC')
            ->get() ?? collect();

             // Calculate totals for each inspection category
    $categoryTotals = [
        'schedule_1' => $visits->sum('schedule_1'),
        'schedule_2' => $visits->sum('schedule_2'),
        'schedule_3' => $visits->sum('schedule_3'),
        'ocpf' => $visits->sum('ocpf'),
        'total' => $visits->sum('total')
    ];

        return view('year-wisereport', compact('inspectionTypes', 'visits', 'categories', 'categoryTotals'));
    }


    // public function stateWiseReport(Request $request)
    // {
    //     $states = State::withTrashed()->orderBy('state_name')->get();
    //     $inspectionTypes = InspectionType::withTrashed()->get();

    //     $dateOfArrivalFrom = $request->input('dateOfArrivalFrom');
    //     $dateOfArrivalTo = $request->input('dateOfArrivalTo');

    //     $visitQuery = Visit::withTrashed() 
    //         ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
    //         ->select(
    //             'visit_site_mappings.state_id',
    //             'visit_site_mappings.inspection_category_id',
    //             DB::raw('COUNT(visits.id) as total')
    //         )
    //         ->groupBy('visit_site_mappings.state_id', 'visit_site_mappings.inspection_category_id');

    //     if ($dateOfArrivalFrom && $dateOfArrivalTo) {
    //         $visitQuery->whereBetween('visits.arrival_datetime', [$dateOfArrivalFrom, $dateOfArrivalTo]);
    //     } elseif ($dateOfArrivalFrom) {
    //         $visitQuery->where('visits.arrival_datetime', '>=', $dateOfArrivalFrom);
    //     } elseif ($dateOfArrivalTo) {
    //         $visitQuery->where('visits.arrival_datetime', '<=', $dateOfArrivalTo);
    //     }

    //     $inspectionData = $visitQuery->get();

    //     $finalData = [];
    //     foreach ($states as $state) {
    //         $row = [
    //             'state' => $state->state_name,
    //             'state_id' => $state->id, 
    //         ];

    //         $totalVisits = 0;
    //         foreach ($inspectionTypes as $type) {
    //             $visitCount = $inspectionData->firstWhere(function ($item) use ($state, $type) {
    //                 return $item->state_id === $state->id && $item->inspection_category_id === $type->id;
    //             });
    //             $count = (int)($visitCount->total ?? 0);
    //             $row[$type->type_name] = $count;
    //             $row[$type->type_name.'_id'] = $type->id;
    //             $totalVisits += $count;
    //         }
    //         $row['total'] = $totalVisits;
    //         if ($totalVisits > 0) {
    //             $finalData[] = $row;
    //         }
    //     }

    //     return view('state-wisereport', compact('finalData', 'inspectionTypes', 'dateOfArrivalFrom', 'dateOfArrivalTo'));
    // }


    public function stateWiseReport(Request $request)
    {
        $states = State::withTrashed()->orderBy('state_name')->get();
        $inspectionTypes = InspectionType::withTrashed()->get();

        $dateOfArrivalFrom = $request->input('dateOfArrivalFrom');
        $dateOfArrivalTo = $request->input('dateOfArrivalTo');

        $visitQuery = Visit::query() // Changed from withTrashed()
            ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->select(
                'visit_site_mappings.state_id',
                'visit_site_mappings.inspection_category_id',
                DB::raw('COUNT(visits.id) as total')
            )
            ->where(function($query) {
                $query->whereNull('visits.deleted_at')
                    ->where('visits.is_draft', 0)
                    ->where('visits.is_reverted', 0);
            })
            ->whereNull('visit_site_mappings.deleted_at') // Add if visit_site_mappings has soft deletes
            ->groupBy('visit_site_mappings.state_id', 'visit_site_mappings.inspection_category_id');

        if ($dateOfArrivalFrom && $dateOfArrivalTo) {
            $visitQuery->whereBetween('visits.arrival_datetime', [$dateOfArrivalFrom, $dateOfArrivalTo]);
        } elseif ($dateOfArrivalFrom) {
            $visitQuery->where('visits.arrival_datetime', '>=', $dateOfArrivalFrom);
        } elseif ($dateOfArrivalTo) {
            $visitQuery->where('visits.arrival_datetime', '<=', $dateOfArrivalTo);
        }

        $inspectionData = $visitQuery->get();

        $finalData = [];
        foreach ($states as $state) {
            $row = [
                'state' => $state->state_name,
                'state_id' => $state->id, 
            ];

            $totalVisits = 0;
            foreach ($inspectionTypes as $type) {
                $visitCount = $inspectionData->firstWhere(function ($item) use ($state, $type) {
                    return $item->state_id === $state->id && $item->inspection_category_id === $type->id;
                });
                $count = (int)($visitCount->total ?? 0);
                $row[$type->type_name] = $count;
                $row[$type->type_name.'_id'] = $type->id;
                $totalVisits += $count;
            }
            $row['total'] = $totalVisits;
            if ($totalVisits > 0) {
                $finalData[] = $row;
            }
        }

        return view('state-wisereport', compact('finalData', 'inspectionTypes', 'dateOfArrivalFrom', 'dateOfArrivalTo'));
    }


//     public function nationalWiseInspectionReport(Request $request)
// {
//     $nationalities = Nationality::withTrashed()->orderBy('country_name')->get(); 
//     $inspectionTypes = InspectionType::withTrashed()->get();
//     $dateOfArrivalFrom = $request->input('dateOfArrivalFrom');
//     $dateOfArrivalTo = $request->input('dateOfArrivalTo');
    
//     $visitQuery = Visit::withTrashed() 
//         ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
//         ->join('inspectors', 'visits.inspector_id', '=', 'inspectors.id') 
//         ->withTrashed() 
//         ->select(
//             'inspectors.nationality_id',
//             'visit_site_mappings.inspection_category_id',
//             DB::raw('COUNT(visits.id) as total')
//         )
//         ->groupBy('inspectors.nationality_id', 'visit_site_mappings.inspection_category_id');

//     if ($dateOfArrivalFrom && $dateOfArrivalTo) {
//         $visitQuery->whereBetween('visits.arrival_datetime', [$dateOfArrivalFrom, $dateOfArrivalTo]);
//     } elseif ($dateOfArrivalFrom) {
//         $visitQuery->where('visits.arrival_datetime', '>=', $dateOfArrivalFrom);
//     } elseif ($dateOfArrivalTo) {
//         $visitQuery->where('visits.arrival_datetime', '<=', $dateOfArrivalTo);
//     }

//     $inspectionData = $visitQuery->get();
//     $finalData = [];
    
//     foreach ($nationalities as $nationality) {
//         $row = [
//             'nationality' => $nationality->country_name,
//             'nationality_id' => $nationality->id, 
//         ];
//         $totalVisits = 0;
//         $typeTotals = []; // Array to store total per type name
//         foreach ($inspectionTypes as $type) {
//             $visitCount = $inspectionData->firstWhere(function ($item) use ($nationality, $type) {
//                 return $item->nationality_id === $nationality->id && $item->inspection_category_id === $type->id;
//             });

//             $count = (int)($visitCount->total ?? 0);
//             $row[$type->type_name] = $count;
//             $row[$type->type_name.'_id'] = $type->id;
//             $totalVisits += $count;

//             // Add to total per type
//             if (!isset($typeTotals[$type->type_name])) {
//                 $typeTotals[$type->type_name] = 0;
//             }
//             $typeTotals[$type->type_name] += $count;
//         }

//         // Add type totals to the row
//         foreach ($typeTotals as $typeName => $total) {
//             $row[$typeName.'_total'] = $total;
//         }

//         if ($totalVisits > 0) {
//             $row['total'] = $totalVisits;
//             $finalData[] = $row;
//         }
//     }

//     // Calculate the totals for each inspection type
//     $inspectionTypeTotals = [];
//     foreach ($inspectionTypes as $type) {
//         // Sum the visit counts for the current type
//         $typeTotal = $inspectionData->where('inspection_category_id', $type->id)->sum('total');
//         $inspectionTypeTotals[$type->type_name] = $typeTotal;
//     }

//     return view('nationality-wisereport', compact('finalData', 'inspectionTypes', 'dateOfArrivalFrom', 'dateOfArrivalTo', 'inspectionTypeTotals'));
// }



    public function nationalWiseInspectionReport(Request $request)
    {
        $nationalities = Nationality::withTrashed()->orderBy('country_name')->get(); 
        $inspectionTypes = InspectionType::withTrashed()->get();
        $dateOfArrivalFrom = $request->input('dateOfArrivalFrom');
        $dateOfArrivalTo = $request->input('dateOfArrivalTo');
        
        $visitQuery = Visit::query() // Changed from withTrashed()
            ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->join('inspectors', 'visits.inspector_id', '=', 'inspectors.id')
            ->select(
                'inspectors.nationality_id',
                'visit_site_mappings.inspection_category_id',
                DB::raw('COUNT(visits.id) as total')
            )
            ->where(function($query) {
                $query->whereNull('visits.deleted_at')
                    ->where('visits.is_draft', 0)
                    ->where('visits.is_reverted', 0);
            })
            ->where(function($query) {
                $query->whereNull('inspectors.deleted_at')
                    ->where('inspectors.is_draft', 0)
                    ->where('inspectors.is_reverted', 0);
            })
            ->whereNull('visit_site_mappings.deleted_at')
            ->groupBy('inspectors.nationality_id', 'visit_site_mappings.inspection_category_id');

        if ($dateOfArrivalFrom && $dateOfArrivalTo) {
            $visitQuery->whereBetween('visits.arrival_datetime', [$dateOfArrivalFrom, $dateOfArrivalTo]);
        } elseif ($dateOfArrivalFrom) {
            $visitQuery->where('visits.arrival_datetime', '>=', $dateOfArrivalFrom);
        } elseif ($dateOfArrivalTo) {
            $visitQuery->where('visits.arrival_datetime', '<=', $dateOfArrivalTo);
        }

        $inspectionData = $visitQuery->get();
        $finalData = [];
        
        foreach ($nationalities as $nationality) {
            $row = [
                'nationality' => $nationality->country_name,
                'nationality_id' => $nationality->id, 
            ];
            $totalVisits = 0;
            $typeTotals = [];
            
            foreach ($inspectionTypes as $type) {
                $visitCount = $inspectionData->firstWhere(function ($item) use ($nationality, $type) {
                    return $item->nationality_id === $nationality->id && $item->inspection_category_id === $type->id;
                });

                $count = (int)($visitCount->total ?? 0);
                $row[$type->type_name] = $count;
                $row[$type->type_name.'_id'] = $type->id;
                $totalVisits += $count;

                if (!isset($typeTotals[$type->type_name])) {
                    $typeTotals[$type->type_name] = 0;
                }
                $typeTotals[$type->type_name] += $count;
            }

            foreach ($typeTotals as $typeName => $total) {
                $row[$typeName.'_total'] = $total;
            }

            if ($totalVisits > 0) {
                $row['total'] = $totalVisits;
                $finalData[] = $row;
            }
        }

        $inspectionTypeTotals = [];
        foreach ($inspectionTypes as $type) {
            $typeTotal = $inspectionData->where('inspection_category_id', $type->id)->sum('total');
            $inspectionTypeTotals[$type->type_name] = $typeTotal;
        }

        return view('nationality-wisereport', compact('finalData', 'inspectionTypes', 'dateOfArrivalFrom', 'dateOfArrivalTo', 'inspectionTypeTotals'));
    }


    


	// public function plantsitewiseReport(Request $request)
    // {
    //     $stateId = $request->input('state');
    //     $dateOfArrivalFrom = $request->input('dateOfArrivalFrom');
    //     $dateOfArrivalTo = $request->input('dateOfArrivalTo');
    //     $siteCodeId = $request->input('siteCode');
    //     $allStates  = State::withTrashed()->get();

    //     // Fetch all SiteCode
    //     $siteCodes = SiteCode::orderBy('site_code')->get();
	// 	/*$siteCodesQuery = SiteCode::orderBy('site_code');
    // if ($stateId) {
    //     if (is_array($stateId)) {
    //         // If multiple states selected, filter by whereIn
    //         $siteCodesQuery->whereIn('state_id', $stateId);
    //     } else {
    //         // If a single state selected, filter by where
    //         $siteCodesQuery->where('state_id', $stateId);
    //     }
    // }
	// $filteredsiteCodes = $siteCodesQuery->get();
	// */


    

    //             // Fetch all inspection types
    //     $inspectionTypes = InspectionType::all();

    //     // Fetch data from Visit and map with states and inspection types
    //     $inspectionDataQuery = Visit::join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
    //         ->select(
    //             'visit_site_mappings.site_code_id',
    //             'visit_site_mappings.inspection_category_id',
    //             DB::raw('COUNT(visits.id) as total')
    //         );

    //         // Filter by State ID
    //         if ($stateId) {
    //                 if (is_array($stateId)) {
    //                     // If it's an array (multiple states selected), use whereIn
    //                     $inspectionDataQuery->whereIn('visit_site_mappings.state_id', $stateId);
    //                 } else {
    //                     // If it's a single state, use where
    //                     $inspectionDataQuery->where('visit_site_mappings.state_id', '=', $stateId);
    //             }
    //         }
    //     if ($dateOfArrivalFrom && $dateOfArrivalTo) {
    //         // Filter by arrival_datetime range, including trashed inspections
    //         $inspectionDataQuery->whereBetween('arrival_datetime', [$dateOfArrivalFrom, $dateOfArrivalTo]);
    //     } elseif ($dateOfArrivalFrom) {
    //         // Filter by arrival_datetime from a specific date, including trashed visits
    //         $inspectionDataQuery->whereDate('arrival_datetime', '>=', $dateOfArrivalFrom);
    //     } elseif ($dateOfArrivalTo) {
    //         // Filter by arrival_datetime up to a specific date, including trashed visits
    //         $inspectionDataQuery->whereDate('arrival_datetime', '<=', $dateOfArrivalTo);
    //     }

		
    //     $inspectionData = $inspectionDataQuery
    //     ->groupBy('visit_site_mappings.site_code_id', 'visit_site_mappings.inspection_category_id')
    //     ->get();
		
	// 	$filteredSiteCodeIds = $inspectionData->pluck('site_code_id')->unique();

	// 	// Fetch only the site codes that match the filtered data
	// 	$filteredsiteCodes = SiteCode::whereIn('id', $filteredSiteCodeIds)->orderBy('site_code')->get();
	// 	//Log::info('Site Codes:', $filteredSiteCodeIds->toArray());
    //     // Prepare final data for the report
    //     $finalData = [];
    //     foreach ($filteredsiteCodes as $SiteCode) {
    //         $row = [
    //             'site_code' => $SiteCode->site_code,
    //             'id' => $SiteCode->id, // Include state_id for clickable links
    //         ];
    //         $totalVisits = 0;

    //         foreach ($inspectionTypes as $type) {
    //             $visitCount = $inspectionData->firstWhere(function ($item) use ($SiteCode, $type) {
    //                 return $item->site_code_id === $SiteCode->id && $item->inspection_category_id === $type->id;
    //             });

    //             $count = (int)($visitCount->total ?? 0);
    //             $row[$type->type_name] = $count;
    //             $row[$type->type_name.'_id'] = $type->id;
    //             $totalVisits += $count;
    //         }

    //         $row['total'] = $totalVisits;
    //         $finalData[] = $row;
    //     }

    //     return view('plantsite-wisereport', compact('finalData', 'inspectionTypes','allStates','siteCodes','stateId','siteCodeId','dateOfArrivalFrom','dateOfArrivalTo'));
    // }

    public function plantsitewiseReport(Request $request)
    {
        $stateId = $request->input('state');
        $dateOfArrivalFrom = $request->input('dateOfArrivalFrom');
        $dateOfArrivalTo = $request->input('dateOfArrivalTo');
        $siteCodeId = $request->input('siteCode');
        $allStates = State::withTrashed()->get();

        // Fetch all SiteCode
        $siteCodes = SiteCode::orderBy('site_code')->get();

        // Fetch all inspection types
        $inspectionTypes = InspectionType::all();

        // Fetch data from Visit and map with states and inspection types
        $inspectionDataQuery = Visit::join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->select(
                'visit_site_mappings.site_code_id',
                'visit_site_mappings.inspection_category_id',
                DB::raw('COUNT(visits.id) as total')
            )
            ->where(function($query) {
                $query->whereNull('visits.deleted_at')
                    ->where('visits.is_draft', 0)
                    ->where('visits.is_reverted', 0);
            })
            ->whereNull('visit_site_mappings.deleted_at');

        // Filter by State ID
        if ($stateId) {
            if (is_array($stateId)) {
                $inspectionDataQuery->whereIn('visit_site_mappings.state_id', $stateId);
            } else {
                $inspectionDataQuery->where('visit_site_mappings.state_id', '=', $stateId);
            }
        }

        // Filter by date range
        if ($dateOfArrivalFrom && $dateOfArrivalTo) {
            $inspectionDataQuery->whereBetween('arrival_datetime', [$dateOfArrivalFrom, $dateOfArrivalTo]);
        } elseif ($dateOfArrivalFrom) {
            $inspectionDataQuery->whereDate('arrival_datetime', '>=', $dateOfArrivalFrom);
        } elseif ($dateOfArrivalTo) {
            $inspectionDataQuery->whereDate('arrival_datetime', '<=', $dateOfArrivalTo);
        }

        $inspectionData = $inspectionDataQuery
            ->groupBy('visit_site_mappings.site_code_id', 'visit_site_mappings.inspection_category_id')
            ->get();

        $filteredSiteCodeIds = $inspectionData->pluck('site_code_id')->unique();

        // Fetch only the site codes that match the filtered data
        $filteredsiteCodes = SiteCode::whereIn('id', $filteredSiteCodeIds)
            ->orderBy('site_code')
            ->get();

        // Prepare final data for the report
        $finalData = [];
        foreach ($filteredsiteCodes as $SiteCode) {
            $row = [
                'site_code' => $SiteCode->site_code,
                'id' => $SiteCode->id,
            ];
            $totalVisits = 0;

            foreach ($inspectionTypes as $type) {
                $visitCount = $inspectionData->firstWhere(function ($item) use ($SiteCode, $type) {
                    return $item->site_code_id === $SiteCode->id && $item->inspection_category_id === $type->id;
                });

                $count = (int)($visitCount->total ?? 0);
                $row[$type->type_name] = $count;
                $row[$type->type_name.'_id'] = $type->id;
                $totalVisits += $count;
            }

            $row['total'] = $totalVisits;
            $finalData[] = $row;
        }

        return view('plantsite-wisereport', compact(
            'finalData', 
            'inspectionTypes',
            'allStates',
            'siteCodes',
            'stateId',
            'siteCodeId',
            'dateOfArrivalFrom',
            'dateOfArrivalTo'
        ));
    }


    // public function yearwiseBarGraph()
    // {
    //     // Get the current year
    //     $currentYear = date('Y');

    //     // Fetch the states and inspection types for the dropdown and bar graph
    //     $states = State::orderBy('state_name')->get();
    //     $inspectionTypes = InspectionType::whereNull('deleted_at')->get();

    //     // Fetch distinct years from visits table
    //     $years = DB::table('visits')
    //         ->selectRaw('YEAR(arrival_datetime) as year')
    //         ->groupBy(DB::raw('YEAR(arrival_datetime)'))
    //         ->orderBy('year', 'ASC')
    //         ->get();


    //         // dd($years);

    //     // Fetch year-wise visits grouped by year, state, and inspection types
    //     $visits = DB::table('visits')
    //         ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
    //         ->join('inspection_types', 'visit_site_mappings.inspection_category_id', '=', 'inspection_types.id')
    //         ->join('states', 'visit_site_mappings.state_id', '=', 'states.id') // Join with states table
    //         ->selectRaw("
    //             YEAR(visits.arrival_datetime) as year,
    //             visit_site_mappings.state_id,
    //             states.state_name,  /* Get state name */
    //             COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END), 0) as schedule_1,
    //             COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END), 0) as schedule_2,
    //             COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END), 0) as schedule_3,
    //             COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END), 0) as schedule_4
    //         ")
    //         ->groupBy(DB::raw("YEAR(visits.arrival_datetime), visit_site_mappings.state_id, states.state_name"))
    //         ->whereNotNull('visits.arrival_datetime')
    //         ->orderBy('year', 'ASC')
    //         ->get() ?? collect();

    //     return view('yearwise-bar-graph', compact('visits', 'states', 'currentYear', 'inspectionTypes', 'years'));
    // }


    public function yearwiseBarGraph()
    {
        // Get the current year
        $currentYear = date('Y');

        // Fetch the states and inspection types for the dropdown and bar graph
        $states = State::orderBy('state_name')->get();
        $inspectionTypes = InspectionType::whereNull('deleted_at')->get();

        // Fetch distinct years from visits table (only active records)
        $years = DB::table('visits')
            ->selectRaw('YEAR(arrival_datetime) as year')
            ->whereNull('deleted_at')
            ->where('is_draft', 0)
            ->where('is_reverted', 0)
            ->whereNotNull('arrival_datetime')
            ->groupBy(DB::raw('YEAR(arrival_datetime)'))
            ->orderBy('year', 'ASC')
            ->get();

        // Fetch year-wise visits grouped by year, state, and inspection types
        $visits = DB::table('visits')
            ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->join('inspection_types', 'visit_site_mappings.inspection_category_id', '=', 'inspection_types.id')
            ->join('states', 'visit_site_mappings.state_id', '=', 'states.id')
            ->selectRaw("
                YEAR(visits.arrival_datetime) as year,
                visit_site_mappings.state_id,
                states.state_name,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END), 0) as schedule_1,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END), 0) as schedule_2,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END), 0) as schedule_3,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END), 0) as schedule_4
            ")
            ->where(function($query) {
                $query->whereNull('visits.deleted_at')
                    ->where('visits.is_draft', 0)
                    ->where('visits.is_reverted', 0);
            })
            ->whereNull('visit_site_mappings.deleted_at')
            ->whereNotNull('visits.arrival_datetime')
            ->groupBy(DB::raw("YEAR(visits.arrival_datetime), visit_site_mappings.state_id, states.state_name"))
            ->orderBy('year', 'ASC')
            ->get() ?? collect();

        return view('yearwise-bar-graph', compact('visits', 'states', 'currentYear', 'inspectionTypes', 'years'));
    }


    // public function yearwisePieChart()
    // {
    //     $currentYear = date('Y');

    //     // Fetch distinct years from visits table (last 5 years)
    //     $years = DB::table('visits')
    //         ->selectRaw('YEAR(arrival_datetime) as year')
    //         ->groupBy(DB::raw('YEAR(arrival_datetime)'))
    //         ->orderBy('year', 'ASC')
    //         ->whereRaw('YEAR(arrival_datetime) >= ?', [date('Y') - 5]) // Last 5 years
    //         ->get();

    //     // Fetch year-wise visits grouped by year and inspection types for pie chart
    //     $pieChartData = DB::table('visits')
    //         ->join('inspection_types', 'visit_site_mappings.inspection_category_id', '=', 'inspection_types.id')
    //         ->selectRaw("
    //             YEAR(visits.arrival_datetime) as year,
    //             COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END), 0) as schedule_1,
    //             COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END), 0) as schedule_2,
    //             COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END), 0) as schedule_3,
    //             COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END), 0) as schedule_4
    //         ")
    //         ->groupBy(DB::raw("YEAR(visits.arrival_datetime)"))
    //         ->whereNotNull('visits.arrival_datetime')
    //         ->whereRaw('YEAR(visits.arrival_datetime) >= ?', [date('Y') - 5]) // Last 5 years
    //         ->orderBy('year', 'ASC')
    //         ->get() ?? collect();

    //     return view('yearwise-pie-chart', compact('pieChartData', 'years', 'currentYear'));
    // }


    public function yearwisePieChart()
    {
        $currentYear = date('Y');

        // Fetch distinct years from visits table (last 5 years, only active records)
        $years = DB::table('visits')
            ->selectRaw('YEAR(arrival_datetime) as year')
            ->whereNull('deleted_at')
            ->where('is_draft', 0)
            ->where('is_reverted', 0)
            ->whereNotNull('arrival_datetime')
            ->groupBy(DB::raw('YEAR(arrival_datetime)'))
            ->orderBy('year', 'ASC')
            ->whereRaw('YEAR(arrival_datetime) >= ?', [date('Y') - 5]) // Last 5 years
            ->get();

        // Fetch year-wise visits grouped by year and inspection types for pie chart
        $pieChartData = DB::table('visits')
            ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->join('inspection_types', 'visit_site_mappings.inspection_category_id', '=', 'inspection_types.id')
            ->selectRaw("
                YEAR(visits.arrival_datetime) as year,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END), 0) as schedule_1,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END), 0) as schedule_2,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END), 0) as schedule_3,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END), 0) as schedule_4
            ")
            ->where(function($query) {
                $query->whereNull('visits.deleted_at')
                    ->where('visits.is_draft', 0)
                    ->where('visits.is_reverted', 0);
            })
            ->whereNull('visit_site_mappings.deleted_at')
            ->whereNotNull('visits.arrival_datetime')
            ->groupBy(DB::raw("YEAR(visits.arrival_datetime)"))
            ->whereRaw('YEAR(visits.arrival_datetime) >= ?', [date('Y') - 5]) // Last 5 years
            ->orderBy('year', 'ASC')
            ->get() ?? collect();

        return view('yearwise-pie-chart', compact('pieChartData', 'years', 'currentYear'));
    }

//     public function yearSequentialPieChart(Request $request)
//     {
//      // Fetch available years (last 5 years)
//      $years = DB::table('visits')
//      ->selectRaw('YEAR(arrival_datetime) as year')
//      ->groupBy(DB::raw('YEAR(arrival_datetime)'))
//      ->orderBy('year', 'ASC')
//      ->whereRaw('YEAR(arrival_datetime) >= ?', [date('Y') - 5])  // Last 5 years
//      ->get();

//  // If a specific year is selected, fetch data for that year, otherwise fetch data for all 5 years
//  $selectedYear = $request->year ?? date('Y'); // Default to current year

//  // Fetch the sequential inspection data for the last 5 years
//  $sequentialInspectionData = DB::table('visits')
//      ->join('inspection_category_types', 'inspection_category_types.id', '=', 'visits.inspection_category_type_id')
//      ->selectRaw('
//          YEAR(visits.arrival_datetime) as year, 
//          COUNT(visits.id) as total
//      ')
//      ->where('inspection_category_types.id', 1) // Filter for Sequential Inspection
//      ->whereRaw('YEAR(visits.arrival_datetime) >= ?', [date('Y') - 5]) // Last 5 years
//      ->groupBy(DB::raw('YEAR(visits.arrival_datetime)'))
//      ->orderBy('year', 'ASC')
//      ->get();

//  // If no data found, use default 0 values
//  if ($sequentialInspectionData->isEmpty()) {
//      $sequentialInspectionData = collect([['year' => $selectedYear, 'total' => 0]]);
//  }

    
//         return view('sequential-pie-chart', [
//             'years' => $years,
//             'selectedYear' => $selectedYear,
//             'sequentialInspectionData' => $sequentialInspectionData,
//         ]);
//     }



    public function yearSequentialPieChart(Request $request)
    {
        // Fetch available years (last 5 years, only active records)
        $years = DB::table('visits')
            ->selectRaw('YEAR(arrival_datetime) as year')
            ->whereNull('deleted_at')
            ->where('is_draft', 0)
            ->where('is_reverted', 0)
            ->whereNotNull('arrival_datetime')
            ->groupBy(DB::raw('YEAR(arrival_datetime)'))
            ->orderBy('year', 'ASC')
            ->whereRaw('YEAR(arrival_datetime) >= ?', [date('Y') - 5]) // Last 5 years
            ->get();

        // If a specific year is selected, fetch data for that year, otherwise fetch data for all 5 years
        $selectedYear = $request->year ?? date('Y'); // Default to current year

        // Fetch the sequential inspection data for the last 5 years
        $sequentialInspectionData = DB::table('visits')
            ->join('inspection_category_types', 'inspection_category_types.id', '=', 'visits.inspection_category_type_id')
            ->selectRaw('
                YEAR(visits.arrival_datetime) as year, 
                COUNT(visits.id) as total
            ')
            ->where('inspection_category_types.id', 1) // Filter for Sequential Inspection
            ->where(function($query) {
                $query->whereNull('visits.deleted_at')
                    ->where('visits.is_draft', 0)
                    ->where('visits.is_reverted', 0);
            })
            ->whereNotNull('visits.arrival_datetime')
            ->whereRaw('YEAR(visits.arrival_datetime) >= ?', [date('Y') - 5]) // Last 5 years
            ->groupBy(DB::raw('YEAR(visits.arrival_datetime)'))
            ->orderBy('year', 'ASC')
            ->get();

        // If no data found, use default 0 values
        if ($sequentialInspectionData->isEmpty()) {
            $sequentialInspectionData = collect([['year' => $selectedYear, 'total' => 0]]);
        }

        return view('sequential-pie-chart', [
            'years' => $years,
            'selectedYear' => $selectedYear,
            'sequentialInspectionData' => $sequentialInspectionData,
        ]);
    }
    
    // public function showMonthlyReport($year)
    // {
    //     $months = [
    //         1 => 'Jan',
    //         2 => 'Feb',
    //         3 => 'Mar',
    //         4 => 'Apr',
    //         5 => 'May',
    //         6 => 'Jun',
    //         7 => 'Jul',
    //         8 => 'Aug',
    //         9 => 'Sep',
    //         10 => 'Oct',
    //         11 => 'Nov',
    //         12 => 'Dec'
    //     ];

    //     $monthlyVisits = DB::table('visits')
    //     ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
    //         ->selectRaw("
    //             MONTH(arrival_datetime) as month,
    //             MAX(visits.id) as last_id, 
    //             MONTH(MIN(arrival_datetime)) as first_arrival_month, -- Extract month number
    //             SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END) as schedule_1,
    //             SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END) as schedule_2,
    //             SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END) as schedule_3,
    //             SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END) as ocpf,
    //             COUNT(*) as total
    //         ")
    //         ->whereYear('arrival_datetime', $year)
    //         ->groupBy(DB::raw("MONTH(arrival_datetime)"))
    //         ->get()
    //         ->keyBy('month');

    //     // Prepare report for all months
    //     $monthlyReport = [];
    //     foreach ($months as $key => $month) {
    //         $monthlyReport[] = [
    //             'month_name'    => $month,
    //             'last_id'       => $monthlyVisits[$key]->last_id ?? null,
    //             'arrival_month' => $monthlyVisits[$key]->first_arrival_month ?? null,  // Month number
    //             'schedule_1'    => $monthlyVisits[$key]->schedule_1 ?? 0,
    //             'schedule_2'    => $monthlyVisits[$key]->schedule_2 ?? 0,
    //             'schedule_3'    => $monthlyVisits[$key]->schedule_3 ?? 0,
    //             'ocpf'          => $monthlyVisits[$key]->ocpf ?? 0,
    //             'total'         => $monthlyVisits[$key]->total ?? 0,
    //         ];
    //     }

    //     return view('month-wisereport', compact('monthlyReport', 'year'));
    // }



    public function showMonthlyReport($year)
    {
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        $monthlyVisits = DB::table('visits')
            ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->selectRaw("
                MONTH(arrival_datetime) as month,
                MAX(visits.id) as last_id, 
                MONTH(MIN(arrival_datetime)) as first_arrival_month,
                SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END) as schedule_1,
                SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END) as schedule_2,
                SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END) as schedule_3,
                SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END) as ocpf,
                COUNT(*) as total
            ")
            ->where(function($query) {
                $query->whereNull('visits.deleted_at')
                    ->where('visits.is_draft', 0)
                    ->where('visits.is_reverted', 0);
            })
            ->whereNull('visit_site_mappings.deleted_at')
            ->whereYear('arrival_datetime', $year)
            ->groupBy(DB::raw("MONTH(arrival_datetime)"))
            ->get()
            ->keyBy('month');

        // Prepare report for all months
        $monthlyReport = [];
        foreach ($months as $key => $month) {
            $monthlyReport[] = [
                'month_name'    => $month,
                'last_id'       => $monthlyVisits[$key]->last_id ?? null,
                'arrival_month' => $monthlyVisits[$key]->first_arrival_month ?? null,
                'schedule_1'    => $monthlyVisits[$key]->schedule_1 ?? 0,
                'schedule_2'    => $monthlyVisits[$key]->schedule_2 ?? 0,
                'schedule_3'    => $monthlyVisits[$key]->schedule_3 ?? 0,
                'ocpf'          => $monthlyVisits[$key]->ocpf ?? 0,
                'total'         => $monthlyVisits[$key]->total ?? 0,
            ];
        }


        // Calculate totals for each inspection category
        $categoryTotals = [
            'schedule_1' => array_sum(array_column($monthlyReport, 'schedule_1')),
            'schedule_2' => array_sum(array_column($monthlyReport, 'schedule_2')),
            'schedule_3' => array_sum(array_column($monthlyReport, 'schedule_3')),
            'ocpf' => array_sum(array_column($monthlyReport, 'ocpf')),
            'total' => array_sum(array_column($monthlyReport, 'total'))
        ];


        return view('month-wisereport', compact('monthlyReport', 'year', 'categoryTotals'));
    }

    // public function showByCountry($country)
    // {
    //     $inspections = Inspection::with(['inspector' => function ($query) {
    //         $query->withTrashed();
    //     }, 'category' => function ($query) {
    //         $query->withTrashed();
    //     }])
    //         ->join('inspectors', 'inspections.inspector_id', '=', 'inspectors.id')
    //         ->join('nationalities', 'inspectors.nationality_id', '=', 'nationalities.id')
    //         ->where('nationalities.country_name', $country)
    //         ->select('inspections.*')
    //         ->get();

    //     return view('inspections_by_country', compact('inspections', 'country'));
    // }


    public function showByCountry($country)
    {
        $inspections = Inspection::with([
                'inspector' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->where('is_draft', 0)
                        ->where('is_reverted', 0);
                }, 
                'category' => function ($query) {
                    $query->whereNull('deleted_at');
                }
            ])
            ->join('inspectors', 'inspections.inspector_id', '=', 'inspectors.id')
            ->join('nationalities', 'inspectors.nationality_id', '=', 'nationalities.id')
            ->where('nationalities.country_name', $country)
            ->whereNull('inspections.deleted_at')
            ->where(function($query) {
                $query->whereNull('inspectors.deleted_at')
                    ->where('inspectors.is_draft', 0)
                    ->where('inspectors.is_reverted', 0);
            })
            ->select('inspections.*')
            ->get();

        return view('inspections_by_country', compact('inspections', 'country'));
    }



    public function listInspectors(Request $request, $id = null, $inst = null, $year = null, $month = null, $stateid = null)
    {
        Log::info('Request Inputs:', ['data' => $request->all()]);
        $escortOfficerIds = $request->input('escortOfficer');
        $dateOfJoiningFrom = $request->input('dateOfJoiningFrom');
        $dateOfJoiningTo = $request->input('dateOfJoiningTo');
        $stateId = $request->input('state');
        $countryId = $request->input('country');
        $rankId = $request->input('rank');
        $designationId = $request->input('designation');
        $statusId = $request->input('status');
        $issueId = $request->input('issue');
        $siteCodeId = $request->input('siteCode');
        $typeOfInspection = $request->input('typeOfInspection');
        $inspectionCategoryTypeId = $request->input('inspectionCategoryType');
        $visitCategoryId = $request->input('visitCategory');
        // $inspectionTypeSelection = $request->input('inspectionTypeSelection');
        $inspectionTypeSelection = $request->input('inspectionTypeSelection', []);
        $dateOfDeparture = $request->input('dateOfDeparture');
        $filter_type = $request->get('filter_type', '1'); // Default to 1 if not provided

        $escortOfficers = EscortOfficer::withTrashed()->pluck('officer_name', 'id');
        $entryExitPoints = EntryExitPoint::withTrashed()->pluck('point_name', 'id');
        $opcwDocuments = OpcwFax::withTrashed()->pluck('fax_number', 'id');
        $allStates = State::withTrashed()->get();
        $allCountry = Nationality::withTrashed()->get();
        $siteCodes = SiteCode::withTrashed()->get();
        $inspectionCategories = InspectionCategory::withTrashed()->get();
        $allInspectionCategoryType = InspectionCategoryType::withTrashed()->get();
        $allVisitCategory = VisitCategory::withTrashed()->get();
        $typesOfInspection = InspectionType::withTrashed()->get();
        $allInspectors = Inspector::withTrashed()->get();
        $allRank = Rank::withTrashed()->get();
        $allDesignation = Designation::withTrashed()->get();
        $allStatus = Status::withTrashed()->get();

        $inspection_properties = InspectionProperties::withTrashed()->get();




   

        $escortOfficerPoEIds = $request->input('escortOfficerPoE', []);
        $escortOfficerIds = $request->input('escortOfficer', []);
        $rankIds = $request->input('rank', []);
        $statusIds = $request->input('status', []);
        $issueIds = $request->input('issue', []);
        $countryIds = $request->input('country', []);
        $stateIds = $request->input('state', []);
        $siteCodeIds = $request->input('siteCode', []);
        $inspectionTypeSelectionIds = $request->input('inspectionTypeSelection', []);
        $inspectionCategoryTypeIds = $request->input('inspectionCategoryType', []);
        $typeOfInspectionIds = $request->input('typeOfInspection', []);
        $visitCategoryIds = $request->input('visitCategory', []);
        $designationIds = $request->input('designation', []);
        $dateOfJoiningFrom = $request->input('dateOfJoiningFrom');
        $dateOfJoiningTo = $request->input('dateOfJoiningTo');
        $dateOfArrivalFrom = $request->input('dateOfArrivalFrom');
        $dateOfArrivalTo = $request->input('dateOfArrivalTo');
        $dateOfDepartureFrom = $request->input('dateOfDepartureFrom');
        $dateOfDepartureTo = $request->input('dateOfDepartureTo');



        $opcwCommunicationFrom = $request->input('opcwCommunicationFrom');
        $opcwCommunicationTo = $request->input('opcwCommunicationTo');


        $opcwDeletionFrom = $request->input('opcwDeletionFrom');
        $opcwDeletionTo = $request->input('opcwDeletionTo');


		Log::info('Request Inputs:', ['data' => $request->all()]);


   


        /*  _______________________________ Escort Officer Dropdown _____________________________________ */
        $escortOfficersPoEDropdown  = $this->dynamicDropdown('EscortOfficer', 'id', 'officer_name',  $escortOfficerPoEIds, '',  'officer_name ASC', false, '');
        /*  _______________________________ Escort Officer Dropdown _____________________________________ */
        $escortOfficersDropdown     = $this->dynamicDropdown('EscortOfficer', 'id', 'officer_name',  $escortOfficerIds, '',  'officer_name ASC', false, '');
        /*  _______________________________ Rank Dropdown _____________________________________ */
        $rankDropdown               = $this->dynamicDropdown('Rank', 'id', 'rank_name',  $rankIds, '',  'rank_name ASC', false, '');
        /*  _______________________________ Status Dropdown _____________________________________ */
        $statusDropdown             = $this->dynamicDropdown('Status', 'id', 'status_name',  $statusIds, '',  'status_name ASC', false, '');
        /*  _______________________________ Status Dropdown _____________________________________ */
        $issueDropdown             = $this->dynamicDropdown('InspectionIssue', 'id', 'name',  $issueIds, '',  'name ASC', false, '');
        /*  _______________________________ Nationality Dropdown _____________________________________ */
        $nationalityDropdown        = $this->dynamicDropdown('Nationality', 'id', 'country_name',  $countryIds, '',  'country_name ASC', false, '');
        /*  _______________________________ State Dropdown _____________________________________ */
        $stateDropdown              = $this->dynamicDropdown('State', 'id', 'state_name',  $stateIds, '',  'state_name ASC', false, '');
        /*  _______________________________ SiteCode Dropdown _____________________________________ */
        $siteCodeDropdown           = $this->dynamicDropdown('SiteCode', 'id', 'site_code',  $siteCodeIds, '',  'site_code ASC', false, '');
        /*  _______________________________ Inspection Type Dropdown _____________________________________ */
        $inspectionTypeDropdown     = $this->dynamicDropdown('InspectionProperties', 'id', 'name',  $inspectionTypeSelectionIds, '',  'name ASC', false, '');
        /*  _______________________________ Sub Category Type Dropdown _____________________________________ */
        $subCategoryTypeDropdown    = $this->dynamicDropdown('InspectionCategoryType', 'id', 'type_name',  $inspectionCategoryTypeIds, '',  'type_name ASC', false, '');
        /*  _______________________________ Inspection Category Dropdown _____________________________________ */
        $inspectionCategoryDropdown = $this->dynamicDropdown('InspectionType', 'id', 'type_name',  $typeOfInspectionIds, '',  'type_name ASC', false, '');
        /*  _______________________________ Visit Category Dropdown _____________________________________ */
        $visitCategoryDropdown      = $this->dynamicDropdown('VisitCategory', 'id', 'category_name',  $visitCategoryIds, '',  'category_name ASC', false, '');
        /*  _______________________________ Designation Dropdown _____________________________________ */
        $designationDropdown        = $this->dynamicDropdown('Designation', 'id', 'designation_name',  $designationIds, '',  'designation_name ASC', false, '');




        if($stateid){
            $stateId = $stateid;
        }

        if ($id) {
            $inspectionCategoryTypeId = $id;
        }

        if ($inst) {

            $typeOfInspection = $inst;
        }

        $monthName = 0;
        if ($month) {
            $months = [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December'
            ];
            $monthName = $months[$month] ?? ''; 
        }

        if (!$year || $year == 0) {
            $year = null;
        }
       
        if($filter_type == 0){
            // $inspectorsQuery = Inspector::withTrashed();

            $inspectorsQuery = Inspector::withTrashed()
                ->where(function($query) {
                    $query->where('is_draft', 0)
                        ->where('is_reverted', 0);
                });

            if ($year) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($year) {
                    $query->withTrashed()  
                        ->whereYear('arrival_datetime', '=', $year);
                });
            }

            if ($year && $month && $inst) {
                $typeOfInspection = $inst;
                $inspectorsQuery->whereHas('visits', function ($query) use ($year, $month, $typeOfInspection) {
                    $query->withTrashed()
                        ->whereYear('arrival_datetime', $year)
                        ->whereMonth('arrival_datetime', $month);
                    if (is_array($typeOfInspection)) {
                        $query->whereIn('visit_site_mappings.inspection_category_id', $typeOfInspection);
                    } else {
                        $query->where('visit_site_mappings.inspection_category_id', $typeOfInspection);
                    }
                });
            }

            if ($year && $month) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($year, $month) {
                    $query->withTrashed()
                        ->whereYear('arrival_datetime', $year)
                        ->whereMonth('arrival_datetime', $month);
                });
            }
            if ($dateOfJoiningFrom && $dateOfJoiningTo) {
                $inspectorsQuery->whereHas('inspections', function ($query) use ($dateOfJoiningFrom, $dateOfJoiningTo) {
                    $query->withTrashed()  
                        ->whereBetween('date_of_joining', [$dateOfJoiningFrom, $dateOfJoiningTo]);
                });
            } elseif ($dateOfJoiningFrom) {
                $inspectorsQuery->whereHas('inspections', function ($query) use ($dateOfJoiningFrom) {
                    $query->withTrashed()  
                        ->whereDate('date_of_joining', '>=', $dateOfJoiningFrom);
                });
            } elseif ($dateOfJoiningTo) {
                $inspectorsQuery->whereHas('inspections', function ($query) use ($dateOfJoiningTo) {
                    $query->withTrashed()  
                        ->whereDate('date_of_joining', '<=', $dateOfJoiningTo);
                });
            }

            if ($dateOfArrivalFrom && $dateOfArrivalTo) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($dateOfArrivalFrom, $dateOfArrivalTo) {
                    $query->withTrashed()  // Include trashed visits
                        ->whereBetween('arrival_datetime', [$dateOfArrivalFrom, $dateOfArrivalTo]);
                });
            } elseif ($dateOfArrivalFrom) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($dateOfArrivalFrom) {
                    $query->withTrashed()  // Include trashed visits
                        ->whereDate('arrival_datetime', '>=', $dateOfArrivalFrom);
                });
            } elseif ($dateOfArrivalTo) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($dateOfArrivalTo) {
                    $query->withTrashed()  // Include trashed inspections
                        ->whereDate('arrival_datetime', '<=', $dateOfArrivalTo);
                });
            }

            if ($dateOfDepartureFrom && $dateOfDepartureTo) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($dateOfDepartureFrom, $dateOfDepartureTo) {
                    $query->withTrashed()  // Include trashed visits
                        ->whereBetween('departure_datetime', [$dateOfDepartureFrom, $dateOfDepartureTo]);
                });
            } elseif ($dateOfDepartureFrom) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($dateOfDepartureFrom) {
                    $query->withTrashed()  // Include trashed visits
                        ->whereDate('departure_datetime', '>=', $dateOfDepartureFrom);
                });
            } elseif ($dateOfDepartureTo) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($dateOfDepartureTo) {
                    $query->withTrashed()  // Include trashed inspections
                        ->whereDate('departure_datetime', '<=', $dateOfDepartureTo);
                });
            }

            if ($escortOfficerIds) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($escortOfficerIds) {
                    $query->whereHas('escortOfficers', function ($subQuery) use ($escortOfficerIds) {
                        $subQuery->withTrashed()->whereIn('escort_officers.id', $escortOfficerIds);
                    });
                });
            }

            if ($stateId) {
                if (is_array($stateId)) {
                    $inspectorsQuery->whereHas('visits', function ($query) use ($stateId) {
                        $query->whereHas('siteMappings', function ($subQuery) use ($stateId) {
                            $subQuery->whereIn('state_id', $stateId);
                        });
                    });
                } else {
                    $inspectorsQuery->whereHas('visits', function ($query) use ($stateId) {
                        $query->whereHas('siteMappings', function ($subQuery) use ($stateId) {
                            $subQuery->where('state_id', '=', $stateId);
                        });
                    });
                }
            }

            if ($inspectionTypeSelection && is_array($inspectionTypeSelection)) {
                // If multiple inspection_property_ids are selected
                $inspectorsQuery->whereHas('visits', function ($query) use ($inspectionTypeSelection) {
                    $query->withTrashed()
                        ->whereIn('inspection_property_id', $inspectionTypeSelection); // Changed to inspection_property_id
                });
            } elseif ($inspectionTypeSelection) {
                // If only one inspection_property_id is selected
                $inspectorsQuery->whereHas('visits', function ($query) use ($inspectionTypeSelection) {
                    $query->withTrashed()
                        ->where('inspection_property_id', '=', $inspectionTypeSelection); // Changed to inspection_property_id
                });
            }
            

            if ($inspectionCategoryTypeId) {
                if (is_array($inspectionCategoryTypeId)) {
                    $inspectorsQuery->whereHas('visits', function ($query) use ($inspectionCategoryTypeId) {
                        $query->withTrashed()->whereIn('inspection_category_type_id', $inspectionCategoryTypeId);
                    });
                } else {
                    $inspectorsQuery->whereHas('visits', function ($query) use ($inspectionCategoryTypeId) {
                        $query->withTrashed()->where('inspection_category_type_id', '=', $inspectionCategoryTypeId);
                    });
                }
            }

            if ($typeOfInspection) {

                if (is_array($typeOfInspection)) {
                    $inspectorsQuery->whereHas('visits', function ($query) use ($typeOfInspection) {
                        $query->withTrashed()->whereHas('siteMappings', function ($subQuery) use ($typeOfInspection) {
                            $subQuery->whereIn('visit_site_mappings.inspection_category_id', $typeOfInspection);
                    
                        });
                    });
                } else {
                    $inspectorsQuery->whereHas('visits', function ($query) use ($typeOfInspection) {
                        $query->withTrashed()->whereHas('siteMappings', function ($subQuery) use ($typeOfInspection) {
                            $subQuery->where('visit_site_mappings.inspection_category_id', '=', $typeOfInspection);
                    
                        });
                    
                    });
                }
            }

            if ($visitCategoryId) {
                if (is_array($visitCategoryId)) {
                    $inspectorsQuery->whereHas('visits', function ($query) use ($visitCategoryId) {
                        $query->withTrashed()->whereIn('category_id', $visitCategoryId);
                    });
                } else {
                    $inspectorsQuery->whereHas('visits', function ($query) use ($visitCategoryId) {
                        $query->withTrashed()->where('category_id', '=', $visitCategoryId);
                    });
                }
            }

            if ($countryId) {
                if (is_array($countryId)) {
                    $inspectorsQuery->whereHas('nationality', function ($query) use ($countryId) {
                        $query->withTrashed()->whereIn('id', $countryId);
                    });
                } else {
                    $inspectorsQuery->whereHas('nationality', function ($query) use ($countryId) {
                        $query->withTrashed()->where('id', '=', $countryId);
                    });
                }
            }

            if ($rankId) {
                if (is_array($rankId)) {
                    $inspectorsQuery->whereHas('rank', function ($query) use ($rankId) {
                        $query->withTrashed()->whereIn('id', $rankId);
                    });
                } else {
                    $inspectorsQuery->whereHas('rank', function ($query) use ($rankId) {
                        $query->withTrashed()->where('id', '=', $rankId);
                    });
                }
            }

            if ($designationId) {
                if (is_array($designationId)) {
                    $inspectorsQuery->whereHas('designation', function ($query) use ($designationId) {
                        $query->withTrashed()->whereIn('id', $designationId);
                    });
                } else {
                    $inspectorsQuery->whereHas('designation', function ($query) use ($designationId) {
                        $query->withTrashed()->where('id', '=', $designationId);
                    });
                }
            }

            if ($siteCodeId && is_array($siteCodeId)) {
                $visitIds = VisitSiteMapping::whereIn('site_code_id', $siteCodeId)
                    ->pluck('visit_id'); 
                $inspectorsQuery->whereHas('visits', function ($query) use ($visitIds) {
                    $query->whereIn('id', $visitIds);  
                });
            }

            if ($statusId) {
                if (is_array($statusId)) {
                    $inspectorsQuery->whereHas('inspections', function ($query) use ($statusId) {
                        $query->withTrashed()->whereIn('status_id', $statusId);
                    });
                } else {
                    $inspectorsQuery->whereHas('inspections', function ($query) use ($statusId) {
                        $query->withTrashed()->where('status_id', '=', $statusId);
                    });
                }
            }
           

            if ($dateOfDeparture) {
                $inspectorsQuery->whereHas('visits', function ($query) use ($dateOfDeparture) {
                    $query->withTrashed()->whereDate('departure_datetime', '=', $dateOfDeparture); 
                });
            }

            $inspectors = $inspectorsQuery->with([
                'inspections',
                'visits' => function ($query) {
                    $query->withTrashed()
                    ->where('is_draft', 0)
                    ->where('is_reverted', 0)
                    ->with([
                        'inspector',
                        'typeOfInspection',
                        'teamLead',
                        'siteMappings.inspectionType',
                        'siteMappings', 
                        'category',
                        'inspectionCategory',
                        'inspectionCategoryType'
                    ]);
                }
            ])->get();

            $inspectorsData = $inspectors->map(function ($inspector) use ($allStates) {
                $countCti = DB::table(table: 'visits')
                    ->where('inspector_id', $inspector->id)
                    ->select(DB::raw('count(*) as count_cti'))
                    ->pluck('count_cti')
                    ->first();

                return [
                    'id' => $inspector->id,
                    'name' => $inspector->name ?? 'N/A',
                    'count_cti' => $countCti ?? 0,
                    'gender' => $inspector->gender->gender_name ?? 'N/A',
                    'dob' => $inspector->dob ? Carbon::parse($inspector->dob)->format('d-m-Y') : 'N/A',
                    'nationality' => $inspector->nationality ?? 'N/A',
                    'passport_number' => $inspector->passport_number ?? 'N/A',
                    'unlp_number' => $inspector->unlp_number ?? 'N/A',
                    'rank' => $inspector->rank->rank_name ?? 'N/A',
                    'ibStatus' => $inspector->ibStatus->status_name ?? 'N/A',
                    'rawStatus' => $inspector->rawStatus->status_name ?? 'N/A',
                    'meaStatus' => $inspector->meaStatus->status_name ?? 'N/A',
                    'designation' => $inspector->designation->designation_name ?? 'N/A',
                    'qualifications' => $inspector->qualifications ?? 'N/A',
                    'professional_experience' => $inspector->professional_experience ?? 'N/A',
                    'remarks' => $inspector->remarks ?? 'N/A',
                    'inspections' => $inspector->inspections,
                    'visits' => $inspector->visits->map(function ($visit) use ($allStates) {
                        $state = $allStates->firstWhere('id', $visit->state_id);
                        $visit->state_name = $state ? $state->state_name : 'N/A';
                        return $visit;
                    }),
                ];
            });

            if ($inspectorsData->isEmpty()) {
                $inspectorsData = [];
            }

            return view('list_inspectors', compact(
                'monthName',
                'inspectorsData',
                'escortOfficers',
                'allStates',
                'allCountry',
                'siteCodes',
                'inspectionCategories',
                'typesOfInspection',
                'typeOfInspection',
                'allInspectors',
                'allRank',
                'allDesignation',
                'allStatus',
                'allVisitCategory',
                'allInspectionCategoryType',
                'dateOfJoiningFrom',
                'dateOfJoiningTo',
                'dateOfArrivalFrom',
                'dateOfArrivalTo',
                'dateOfDepartureFrom',
                'dateOfDepartureTo',
                'inspectionTypeSelection',
                'year',
                'filter_type',
                'inspection_properties',
                'escortOfficersPoEDropdown',
                'escortOfficersDropdown',
                'rankDropdown',
                'statusDropdown',
                'issueDropdown',
                'nationalityDropdown',
                'stateDropdown',
                'siteCodeDropdown',
                'inspectionTypeDropdown',
                'subCategoryTypeDropdown',
                'inspectionCategoryDropdown',
                'visitCategoryDropdown',
                'designationDropdown',
                'entryExitPoints',
                'opcwDocuments'
            ));
        }
        elseif($filter_type == 1){

            //\DB::enableQueryLog(); 

            $results = DB::table('visit_site_mappings as vsm')
                ->join('visits as v', 'vsm.visit_id', '=', 'v.id')
                ->where('v.is_draft', 0)
                ->where('v.is_reverted', 0)
                ->join('inspection_types as it', function($join) {
                    $join->on('vsm.inspection_category_id', '=', 'it.id')
                        ->whereNull('it.deleted_at');
                })
                ->join('states as s', 'vsm.state_id', '=', 's.id')
                ->join('site_codes as sc', 'vsm.site_code_id', '=', 'sc.id')
                ->leftJoin('visit_categories as vc', 'v.category_id', '=', 'vc.id')
                ->leftJoin('inspectors as tl', 'v.team_lead_id', '=', 'tl.id')
                ->leftJoin('opcw_faxes as of', 'v.opcw_document_id', '=', 'of.id')
                ->leftJoin('inspection_phases as iph', 'vsm.inspection_phase_id', '=', 'iph.id')
                ->join('inspection_properties as ip', function($join) {
                    $join->on('v.inspection_property_id', '=', 'ip.id')
                        ->whereNull('ip.deleted_at');
                })
                ->leftJoin('inspection_category_types as ict', function($join) {
                    $join->on('v.inspection_category_type_id', '=', 'ict.id')
                        ->whereNull('ict.deleted_at');
                })
                ->leftJoin('inspection_issues as ii', function($join) {
                    $join->on('vsm.inspection_issue_id', '=', 'ii.id')
                        ->whereNull('ii.deleted_at');
                })
                ->select([
                    'ip.name as ip_name',
                    'it.type_name as it_name',
                    'ict.type_name as ict_name',
                    DB::raw("CONCAT(
                        IFNULL(sc.site_code, 'N/A'), ' ',
                        vsm.site_of_inspection, ' ',
                        IFNULL(s.state_name, 'N/A')
                    ) as sc_name"),
                    'v.arrival_datetime',
                    'v.departure_datetime',
                    'v.acentric_report',
                    DB::raw("IF(v.category_id IS NOT NULL, vc.category_name, '') as vc_name"),
                    'v.point_of_entry',
                    'v.point_of_exit',
                    'tl.name as tl_name',
                    'v.clearance_certificate',
                    'v.visit_report',
                    'v.list_of_inspectors',
                    'v.list_of_escort_officers',
                    'v.escort_officers_poe',
                    'of.fax_number',  
                    'v.remarks',
                    'v.deleted_at as v_deleted_at',
                    'of.fax_date as receipt_date', 
                    'ii.name as inspection_issue_name',
                    DB::raw("IFNULL(iph.phase_type_name, 'N/A') as inspection_phase_name")
                ]);

                if (!empty($rankIds) && is_array($rankIds)) {
                    $rankInspectorsIdsList = $this->getInspectorsIdsList('ranks', 'inspectors', 'rank_id', 'id', 'inspectors_ids', true);
                    $this->applyFilter($results, $rankIds, $rankInspectorsIdsList, 'v.team_lead_id');
                }

                if (!empty($statusIds) && is_array($statusIds)) {
                    $statusInspectorsIdsList = $this->getConcatenatedList('inspections', 'status_id', 'inspector_id', 'inspector_ids', false);
                    $this->applyFilter($results, $statusIds, $statusInspectorsIdsList, 'v.team_lead_id');
                }

                if (!empty($countryIds) && is_array($countryIds)) {
                    $nationalityInspectorsIdsList = $this->getConcatenatedList('inspectors', 'nationality_id', 'id', 'inspector_ids', false);
                    $this->applyFilter($results,$countryIds,$nationalityInspectorsIdsList,'v.team_lead_id');
                }

                
                if (!empty($designationIds) && is_array($designationIds)) {
                    $designationInspectorsIdsList = $this->getInspectorsIdsList('designations', 'inspectors', 'designation_id', 'id', 'inspectors_ids', true);
                    $this->applyFilter($results, $designationIds, $designationInspectorsIdsList, 'v.team_lead_id'); 
                }

                $results = $this->addFilterCondition($results, $stateIds, 's.id');
                $results = $this->addFilterCondition($results, $issueIds, 'ii.id');

                $results = $this->addFilterCondition($results, $siteCodeIds, 'vsm.site_code_id');
                $results = $this->addFilterCondition($results, $inspectionTypeSelectionIds, 'v.inspection_property_id');
                $results = $this->addFilterCondition($results, $inspectionCategoryTypeIds, 'v.inspection_category_type_id');
                $results = $this->addFilterCondition($results, $typeOfInspectionIds, 'vsm.inspection_category_id');
                $results = $this->addFilterCondition($results, $visitCategoryIds, 'v.category_id');

                $results = $this->addDateRangeFilter($results, 'v.arrival_datetime', $dateOfArrivalFrom, $dateOfArrivalTo);
                $results = $this->addDateRangeFilter($results, 'v.departure_datetime', $dateOfDepartureFrom, $dateOfDepartureTo);

                $results = $this->addJsonFilterConditionForSet($results, 'escortOfficer', 'v.list_of_escort_officers');
                $results = $this->addJsonFilterConditionForSet($results, 'escortOfficerPoE', 'v.escort_officers_poe');

                $results = $results->get();

                //dd(\DB::getQueryLog()); 

                $entry_exit_points  = $this->getList('entry_exit_points', 'id', 'point_name', true);
                $escort_officers    = $this->getList('escort_officers', 'id', 'officer_name', true);
                $inspectors    = $this->getList('inspectors', 'id', 'name', true);
                $inspection_issues    = $this->getList('inspection_issues', 'id', 'name', true);

            return view('list_inspectors', 
                compact(
                    'results',
                    'entry_exit_points',
                    'escort_officers',
                    'escortOfficersPoEDropdown',
                    'escortOfficersDropdown',
                    'rankDropdown',
                    'statusDropdown',
                    'issueDropdown',
                    'nationalityDropdown',
                    'stateDropdown',
                    'siteCodeDropdown',
                    'inspectionTypeDropdown',
                    'subCategoryTypeDropdown',
                    'inspectionCategoryDropdown',
                    'visitCategoryDropdown',
                    'designationDropdown',
                    'dateOfJoiningFrom',
                    'dateOfJoiningTo',
                    'dateOfArrivalFrom',
                    'dateOfArrivalTo',
                    'dateOfDepartureFrom',
                    'dateOfDepartureTo',
                    'monthName',
                    'year',                
                    'filter_type',
                    'inspectors',
                    'inspection_issues'
                )
            );

        }
        elseif($filter_type == 2){
            // $otherStaffQuery = OtherStaff::withTrashed();

            $otherStaffQuery = OtherStaff::withTrashed()
                ->where('is_draft', 0)
                ->where('is_reverted', 0);

           // Filter by OPCW Communication Dates
            if ($opcwCommunicationFrom || $opcwCommunicationTo) {
                if ($opcwCommunicationFrom && $opcwCommunicationTo) {
                    $otherStaffQuery->whereBetween('opcw_communication_date', [$opcwCommunicationFrom, $opcwCommunicationTo]);
                } elseif ($opcwCommunicationFrom) {
                    $otherStaffQuery->whereDate('opcw_communication_date', '>=', $opcwCommunicationFrom);
                } elseif ($opcwCommunicationTo) {
                    $otherStaffQuery->whereDate('opcw_communication_date', '<=', $opcwCommunicationTo);
                }
            }

            // Filter by OPCW Deletion Dates
            if ($opcwDeletionFrom || $opcwDeletionTo) {
                if ($opcwDeletionFrom && $opcwDeletionTo) {
                    $otherStaffQuery->whereBetween('deletion_date', [$opcwDeletionFrom, $opcwDeletionTo]);
                } elseif ($opcwDeletionFrom) {
                    $otherStaffQuery->whereDate('deletion_date', '>=', $opcwDeletionFrom);
                } elseif ($opcwDeletionTo) {
                    $otherStaffQuery->whereDate('deletion_date', '<=', $opcwDeletionTo);
                }
            }


            // Filter by Nationality
            if ($countryId) {
                if (is_array($countryId)) {
                    $otherStaffQuery->whereIn('nationality_id', $countryId); // Directly filter by nationality_id
                } else {
                    $otherStaffQuery->where('nationality_id', '=', $countryId); // Directly filter by nationality_id
                }
            }

            // Filter by Rank
            if ($rankId) {
                if (is_array($rankId)) {
                    $otherStaffQuery->whereIn('rank_id', $rankId); // Directly filter by rank_id
                } else {
                    $otherStaffQuery->where('rank_id', '=', $rankId); // Directly filter by rank_id
                }
            }

            // Filter by Designation
            if ($designationId) {
                if (is_array($designationId)) {
                    $otherStaffQuery->whereIn('designation_id', $designationId); // Directly filter by designation_id
                } else {
                    $otherStaffQuery->where('designation_id', '=', $designationId); // Directly filter by designation_id
                }
            }

           

           
           

         

            $otherStaff = $otherStaffQuery->get();

            $otherStaffData = $otherStaff->map(function ($inspector) use ($allStates) {

                return [
                    'id' => $inspector->id,
                    'name' => $inspector->name ?? 'N/A',
                 
                    'gender' => $inspector->gender->gender_name ?? 'N/A',
                    'dob' => $inspector->dob ? Carbon::parse($inspector->dob)->format('d-m-Y') : 'N/A',
                    'opcw_communication_date' => $inspector->opcw_communication_date ? Carbon::parse($inspector->opcw_communication_date)->format('d-m-Y') : 'N/A',
                    'deletion_date' => $inspector->deletion_date ? Carbon::parse($inspector->deletion_date)->format('d-m-Y') : 'N/A',
                    'nationality' => $inspector->nationality ?? 'N/A',
                    'passport_number' => $inspector->passport_number ?? 'N/A',
                    'place_of_birth' => $inspector->place_of_birth ?? 'N/A',
                    'unlp_number' => $inspector->unlp_number ?? 'N/A',
                    'scope_of_access' => $inspector->scope_of_access ?? 'N/A',
                    'security_status' => $inspector->security_status ?? 'N/A',
                    'rank' => $inspector->rank->rank_name ?? 'N/A',
                    'designation' => $inspector->designation->designation_name ?? 'N/A',
                    'qualifications' => $inspector->qualifications ?? 'N/A',
                    'professional_experience' => $inspector->professional_experience ?? 'N/A',
                    'filter_type' => 2,
                    'remarks' => $inspector->remarks ?? 'N/A',
                  
                  
                ];
            });

            if ($otherStaffData->isEmpty()) {
                $otherStaffData = [];
            }

            return view('list_inspectors', compact(
                'monthName',
                'otherStaffData',
                'allStates',
                'allCountry',
                'allInspectors',
                'allRank',
                'allDesignation',
                'allStatus',
                'year',
                'filter_type',
                'rankDropdown',
                'statusDropdown',
                'nationalityDropdown',
                'stateDropdown',
                'designationDropdown',
                'opcwCommunicationFrom',
                'opcwCommunicationTo',
                'opcwDeletionFrom',
                'opcwDeletionTo'
            ));
        }
    }

    private function addFilterCondition($query, $values, $columnName)
    {
        if (!empty($values) && is_array($values) && !empty($values[0])) {
            $query->whereIn($columnName, array_map('trim', $values)); // Clean input and apply `whereIn`
        }
        return $query;
    }

    private function addDateRangeFilter($query, $ColName, $validFrom = null, $validTo = null)
    {
        if (!empty($validFrom)) {
            $query->where($ColName, '>=', $validFrom);
            if (empty($validTo)) {
                $validFrom = date('Y-m-d', strtotime($validFrom));
                $query->where($ColName, '<=', now()->toDateString());
            }
        }

        if (!empty($validTo)) {
            $validTo = date('Y-m-d', strtotime($validTo . ' +1 day'));
            $query->where($ColName, '<=', $validTo);
        }

        return $query;
    }

    private function addJsonFilterConditionForSet($query, $fieldName, $columnName)
    {
        $columnName = "REPLACE( TRIM(BOTH '".'"'."' FROM REPLACE(REPLACE(JSON_UNQUOTE(JSON_EXTRACT($columnName, '$')), '".'","'."', ','), '[".'"'."', '')) , '".'"'."]', '')";
        $values = request($fieldName);
        if (!empty($values) && is_array($values) && !empty($values[0])) {

            $conditions = [];
            foreach (array_map('trim', $values) as $value) {
                $conditions[] = "FIND_IN_SET(?, $columnName)";
            }
            $query->whereRaw(implode(' OR ', $conditions), $values);
        }
        return $query;
    }

    private function getList($table, $index, $name, $deleted_at = false, $groupBy = null)
    {
        $query = DB::table($table);
        if ($deleted_at) {
            $query->whereNull('deleted_at');
        }
        if (!empty($groupBy)) {
            $query->groupBy($groupBy);
        }
        $results = $query->orderBy($index, 'ASC')
            ->pluck($name, $index)
            ->toArray();
        return $results ?: [];
    }

    private function dynamicDropdown($table, $value_column, $label_column, $selected_values = [], $condition = '', $order_by = '', $include_trashed = false, $extra_options = ''){
        $query = app("App\\Models\\" . ucfirst($table))::select($value_column, $label_column);
        if ($include_trashed) {
            $query->withTrashed();
        }
        if ($condition) {
            $query->whereRaw($condition);
        }
        if ($order_by) {
            $query->orderByRaw($order_by);
        }
        $results = $query->get();
        $dropdown = "<option value=''>Choose</option>";
        foreach ($results as $row) {
            $selected = in_array($row->$value_column, $selected_values) ? ' selected' : '';
            $dropdown .= "<option value='" . htmlspecialchars($row->$value_column, ENT_QUOTES) . "'$selected>" . htmlspecialchars($row->$label_column, ENT_QUOTES) . "</option>";
        }
        return $dropdown;
    }

    private function getConcatenatedList($table, $index, $colName, $alias, $deleted_at = false)
    {
        $query = DB::table($table);

        if ($deleted_at) {
            $query->whereNull($table . '.deleted_at');
        }

        $query->select(
                $index,
                DB::raw("GROUP_CONCAT($colName) as $alias")
            )
            ->groupBy($index)
            ->orderBy($index, 'ASC');

        $results = $query->pluck($alias, $index)
                        ->toArray();

        return $results ?: [];
    }

    private function applyFilter(&$results, $filterValues, $listData, $columnName)
    {
        if (!empty($filterValues) && is_array($filterValues)) {
            $filteredIds = [];
            foreach ($filterValues as $value) {
                if (isset($listData[$value])) {
                    $filteredIds = array_merge($filteredIds,explode(",",$listData[$value]));
                }
            }
            $filteredIds = array_unique($filteredIds);
            if (!empty($filteredIds) && is_array($filterValues)) {
                $results = $this->addFilterCondition($results, $filteredIds, $columnName);
            }else{
                $results->whereRaw('0 = 1');
            }
        }
    }


    private function getInspectorsIdsList($table, $joinTable, $joinColumn, $index, $name, $deleted_at = false)
    {
        $query = DB::table($table)
                ->leftJoin($joinTable, $joinTable . '.' . $joinColumn, '=', $table . '.id')
                ->groupBy($table . '.id');

        if ($deleted_at) {
            $query->whereNull($table . '.deleted_at');
        }
        $query->select($table . '.id', DB::raw('GROUP_CONCAT(' . $joinTable . '.id) AS inspectors_ids'));
        $results = $query->orderBy($index, 'ASC')
                        ->pluck($name, $index)
                        ->toArray();
        return $results ?: [];
    }

    public function inspectionReport()
    {
        return view('inspection-report');
    }

}
