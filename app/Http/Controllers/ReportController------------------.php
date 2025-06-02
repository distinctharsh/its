<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\EscortOfficer;
use App\Models\Inspection;
use App\Models\InspectionCategory;
use App\Models\InspectionCategoryType;
use App\Models\InspectionType;
use App\Models\Inspector;
use App\Models\Nationality;
use App\Models\Rank;
use App\Models\SiteCode;
use App\Models\State;
use App\Models\Status;
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

    public function yearwiseReport()
    {
        // Fetch inspection types and categories
        $inspectionTypes = InspectionType::whereNull('deleted_at')->get();
        $categories = InspectionCategory::withTrashed()->get();
    
        // Fetch yearly data grouped by year and type of inspection using JOIN
        $visits = DB::table('visits')
        ->join('inspection_types', 'visits.type_of_inspection_id', '=', 'inspection_types.id')
        ->selectRaw("
            YEAR(visits.arrival_datetime) as year,
            SUM(CASE WHEN visits.type_of_inspection_id = 1 THEN 1 ELSE 0 END) as schedule_1,
            SUM(CASE WHEN visits.type_of_inspection_id = 2 THEN 1 ELSE 0 END) as schedule_2,
            SUM(CASE WHEN visits.type_of_inspection_id = 3 THEN 1 ELSE 0 END) as schedule_3,
            SUM(CASE WHEN visits.type_of_inspection_id = 4 THEN 1 ELSE 0 END) as ocpf,
            COUNT(visits.id) as total
        ")
        ->whereNotNull('visits.arrival_datetime')
        ->groupBy(DB::raw("YEAR(visits.arrival_datetime)"))
        ->orderBy('year', 'ASC')
        ->get() ?? collect();
    
        return view('year-wisereport', compact('inspectionTypes', 'visits', 'categories'));
    }
    
  
    public function showMonthlyReport($year)
    {
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];
    
        $monthlyVisits = DB::table('visits')
            ->selectRaw("
                MONTH(arrival_datetime) as month,
                MAX(id) as last_id, 
                MONTH(MIN(arrival_datetime)) as first_arrival_month, -- Extract month number
                SUM(CASE WHEN type_of_inspection_id = 1 THEN 1 ELSE 0 END) as schedule_1,
                SUM(CASE WHEN type_of_inspection_id = 2 THEN 1 ELSE 0 END) as schedule_2,
                SUM(CASE WHEN type_of_inspection_id = 3 THEN 1 ELSE 0 END) as schedule_3,
                SUM(CASE WHEN type_of_inspection_id = 4 THEN 1 ELSE 0 END) as ocpf,
                COUNT(id) as total
            ")
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
                'arrival_month' => $monthlyVisits[$key]->first_arrival_month ?? null,  // Month number
                'schedule_1'    => $monthlyVisits[$key]->schedule_1 ?? 0,
                'schedule_2'    => $monthlyVisits[$key]->schedule_2 ?? 0,
                'schedule_3'    => $monthlyVisits[$key]->schedule_3 ?? 0,
                'ocpf'          => $monthlyVisits[$key]->ocpf ?? 0,
                'total'         => $monthlyVisits[$key]->total ?? 0,
            ];
        }
    
        return view('month-wisereport', compact('monthlyReport', 'year'));
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



    public function listInspectors(Request $request, $id = null, $inst = null, $year = null ,$month = null)
    {
         
        Log::info('Request Inputs:', ['data' => $request->all()]);
    
        // Capture filter parameters from the request
        $escortOfficerIds = $request->input('escortOfficer');
        $dateOfJoiningFrom = $request->input('dateOfJoiningFrom');
        $dateOfJoiningTo = $request->input('dateOfJoiningTo');

        $stateId = $request->input('state');
        $countryId = $request->input('country');
        $rankId = $request->input('rank');
        $designationId = $request->input('designation');
        $statusId = $request->input('status');
        $siteCodeId = $request->input('siteCode');
        $typeOfInspection = $request->input('typeOfInspection');
        $inspectionCategoryTypeId = $request->input('inspectionCategoryType');
        $visitCategoryId = $request->input('visitCategory');
        $inspectionTypeSelection = $request->input('inspectionTypeSelection');
        $dateOfArrival = $request->input('dateOfArrival');
        $dateOfDeparture = $request->input('dateOfDeparture');
    
        // Fetch data for dropdowns and filters
        $escortOfficers = EscortOfficer::withTrashed()->pluck('officer_name', 'id'); 
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


          

    
                if ($id) {
                  $inspectionCategoryTypeId = $id;
                                    
                }
                
                if($inst){
                  
                 $typeOfInspection =$inst;
                
                     }
                     $monthName=0;

                     if($month)
                     {
                        

                        $months = [
                            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
                            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
                            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                        ];
                        
                        $monthName = $months[$month] ?? '';  // Get the month name based on the number
                     }

               
        // Start with the Inspector query
        // $inspectorsQuery = Inspector::query();
        
        // Start with the Inspector query and include trashed records
        $inspectorsQuery = Inspector::withTrashed();


        if ($year) {
            // Filter by the year part of the timestamp
            $inspectorsQuery->whereHas('visits', function ($query) use ($year) {
                $query->withTrashed()  // Include trashed visits
                      ->whereYear('arrival_datetime', '=', $year);  // Filter based on the year
            });
        }

        if ($year && $month && $inst) {

            $typeOfInspection = $inst;
        
            $inspectorsQuery->whereHas('visits', function ($query) use ($year, $month, $typeOfInspection) {
                $query->withTrashed()
                      ->whereYear('arrival_datetime', $year)
                      ->whereMonth('arrival_datetime', $month);
        
                if (is_array($typeOfInspection)) {
                    $query->whereIn('type_of_inspection_id', $typeOfInspection);
                } else {
                    $query->where('type_of_inspection_id', $typeOfInspection);
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
        
        
        if ($dateOfArrival ) {  // If a specific date of arrival is set, filter by exact date
            
            $inspectorsQuery->whereHas('visits', function ($query) use ($dateOfArrival) {
                $query->withTrashed()
                      ->whereDate('arrival_datetime', '=', $dateOfArrival);  // Exact date filtering
            });
        }


        
    
        // Apply filters based on user input
    
        // Filter by Date of Birth
        if ($dateOfJoiningFrom && $dateOfJoiningTo) {
            // Filter by date_of_joining range, including trashed inspections
            $inspectorsQuery->whereHas('inspections', function ($query) use ($dateOfJoiningFrom, $dateOfJoiningTo) {
                $query->withTrashed()  // Include trashed inspections
                      ->whereBetween('date_of_joining', [$dateOfJoiningFrom, $dateOfJoiningTo]);
            });
        } elseif ($dateOfJoiningFrom) {
            // Filter by date_of_joining from a specific date, including trashed inspections
            $inspectorsQuery->whereHas('inspections', function ($query) use ($dateOfJoiningFrom) {
                $query->withTrashed()  // Include trashed inspections
                      ->whereDate('date_of_joining', '>=', $dateOfJoiningFrom);
            });
        } elseif ($dateOfJoiningTo) {
            // Filter by date_of_joining up to a specific date, including trashed inspections
            $inspectorsQuery->whereHas('inspections', function ($query) use ($dateOfJoiningTo) {
                $query->withTrashed()  // Include trashed inspections
                      ->whereDate('date_of_joining', '<=', $dateOfJoiningTo);
            });
        }
        
    
        // Filter by Escort Officer IDs
        if ($escortOfficerIds) {
            $inspectorsQuery->whereHas('visits', function ($query) use ($escortOfficerIds) {
                $query->whereHas('escortOfficers', function ($subQuery) use ($escortOfficerIds) {
                    $subQuery->withTrashed()->whereIn('escort_officers.id', $escortOfficerIds);
                });
            });
        }
    
        // Filter by State ID
        if ($stateId) {
            if (is_array($stateId)) {
                // If it's an array (multiple states selected), use whereIn
                $inspectorsQuery->whereHas('visits', function ($query) use ($stateId) {
                    $query->whereHas('siteMappings', function ($subQuery) use ($stateId) {
                        $subQuery->whereIn('state_id', $stateId);
                    });
                });
            } else {
                // If it's a single state, use where
                $inspectorsQuery->whereHas('visits', function ($query) use ($stateId) {
                    $query->whereHas('siteMappings', function ($subQuery) use ($stateId) {
                        $subQuery->where('state_id', '=', $stateId);
                    });
                });
            }
        }

      
        // Filter by Inspection Type Selection (Routine or Challenge), including trashed visits
        if ($inspectionTypeSelection && is_array($inspectionTypeSelection)) {
            $inspectorsQuery->whereHas('visits', function ($query) use ($inspectionTypeSelection) {
                $query->withTrashed() // Include trashed visits
                    ->whereIn('inspection_type_selection', $inspectionTypeSelection);  // Use whereIn for multiple values
            });
        } elseif ($inspectionTypeSelection) {
            // If it's just a single selection, include trashed visits as well
            $inspectorsQuery->whereHas('visits', function ($query) use ($inspectionTypeSelection) {
                $query->withTrashed() // Include trashed visits
                    ->where('inspection_type_selection', '=', $inspectionTypeSelection);
            });
        }



        // Filter by Inspection Sub Category Type Selection (Single or Sequential)
     
            if ($inspectionCategoryTypeId) {
                if (is_array($inspectionCategoryTypeId)) {
                    // If it's an array (multiple sub categories selected), use whereIn
                    $inspectorsQuery->whereHas('visits', function ($query) use ($inspectionCategoryTypeId) {
                        $query->withTrashed()->whereIn('inspection_category_type_id', $inspectionCategoryTypeId);
                    });
                } else {
                    // If it's a single sub category, use where
                    $inspectorsQuery->whereHas('visits', function ($query) use ($inspectionCategoryTypeId) {
                        $query->withTrashed()->where('inspection_category_type_id', '=', $inspectionCategoryTypeId);
                    });
                }
            }
        
        

        // Filter by Inspection Sub Category Type Selection (Single or Sequential)
        if ($typeOfInspection) {

            if (is_array($typeOfInspection)) {
                // If it's an array (multiple types selected), use whereIn
                $inspectorsQuery->whereHas('visits', function ($query) use ($typeOfInspection) {
                    $query->withTrashed()->whereIn('type_of_inspection_id', $typeOfInspection);
                });
            } else {
                // If it's a single type, use where
                $inspectorsQuery->whereHas('visits', function ($query) use ($typeOfInspection) {
                    $query->withTrashed()->where('type_of_inspection_id', '=', $typeOfInspection);
                });
            }
        }

        // Filter by Inspection Sub Category Type Selection (Single or Sequential)
        if ($visitCategoryId) {
            if (is_array($visitCategoryId)) {
                // If it's an array (multiple visit categories selected), use whereIn
                $inspectorsQuery->whereHas('visits', function ($query) use ($visitCategoryId) {
                    $query->withTrashed()->whereIn('category_id', $visitCategoryId);
                });
            } else {
                // If it's a single visit category, use where
                $inspectorsQuery->whereHas('visits', function ($query) use ($visitCategoryId) {
                    $query->withTrashed()->where('category_id', '=', $visitCategoryId);
                });
            }
        }

    
        // Filter by Nationality ID (Nationality), including trashed records
        if ($countryId) {
            if (is_array($countryId)) {
                // If it's an array (multiple countries selected), use whereIn
                $inspectorsQuery->whereHas('nationality', function ($query) use ($countryId) {
                    $query->withTrashed()->whereIn('id', $countryId);
                });
            } else {
                // If it's a single country, use where
                $inspectorsQuery->whereHas('nationality', function ($query) use ($countryId) {
                    $query->withTrashed()->where('id', '=', $countryId);
                });
            }
        }


       // Filter by Rank ID (Rank), including trashed records
        if ($rankId) {
            if (is_array($rankId)) {
                // If it's an array (multiple designations selected), use whereIn
                $inspectorsQuery->whereHas('rank', function ($query) use ($rankId) {
                    $query->withTrashed()->whereIn('id', $rankId);
                });
            } else {
                // If it's a single designation, use where
                $inspectorsQuery->whereHas('rank', function ($query) use ($rankId) {
                    $query->withTrashed()->where('id', '=', $rankId);
                });
            }
        }

        if ($designationId) {
            if (is_array($designationId)) {
                // If it's an array (multiple designations selected), use whereIn
                $inspectorsQuery->whereHas('designation', function ($query) use ($designationId) {
                    $query->withTrashed()->whereIn('id', $designationId);
                });
            } else {
                // If it's a single designation, use where
                $inspectorsQuery->whereHas('designation', function ($query) use ($designationId) {
                    $query->withTrashed()->where('id', '=', $designationId);
                });
            }
        }

    
   
    
       
      
       // Filter by Site Code ID, ensuring only active records are used
        if ($siteCodeId && is_array($siteCodeId)) {
            // Step 1: Get the visit_ids matching any of the selected site_code_ids from VisitSiteMapping
            $visitIds = VisitSiteMapping::whereIn('site_code_id', $siteCodeId)
                ->pluck('visit_id'); // This fetches only active (non-deleted) records

            // Step 2: Filter visits by the visit_ids obtained in Step 1
            $inspectorsQuery->whereHas('visits', function ($query) use ($visitIds) {
                $query->whereIn('id', $visitIds);  // Only match visits with active visit_ids
            });
        }





        // Filter by Status ID, including trashed records
        if ($statusId) {
            if (is_array($statusId)) {
                // If it's an array (multiple statuses selected), use whereIn
                $inspectorsQuery->whereHas('inspections', function ($query) use ($statusId) {
                    $query->withTrashed()->whereIn('status_id', $statusId);
                });
            } else {
                // If it's a single status, use where
                $inspectorsQuery->whereHas('inspections', function ($query) use ($statusId) {
                    $query->withTrashed()->where('status_id', '=', $statusId);
                });
            }
        }

        // Filter by Date of Departure, including trashed visits
        if ($dateOfDeparture) {
            $inspectorsQuery->whereHas('visits', function ($query) use ($dateOfDeparture) {
                $query->withTrashed()->whereDate('departure_datetime', '=', $dateOfDeparture);  // Filter based on the departure_date
            });
        }

    
        // Get filtered inspectors with their relationships (eager loading 'inspections' and 'visits')
        // $inspectors = $inspectorsQuery->with(['inspections', 'visits'])->get();

        $inspectors = $inspectorsQuery->with(['inspections', 
            'visits' => function ($query) {
                $query->withTrashed()->with([
                    'inspector', 
                    'typeOfInspection', 
                    'teamLead', 
                    'inspectionType', 
                    'siteMappings', // Eager load siteMappings
                    'category', 
                    'inspectionCategory', 
                    'inspectionCategoryType'
                ]);
            }
        ])->get();
    
            // Prepare inspectors data for the view
            $inspectorsData = $inspectors->map(function ($inspector) use ($allStates) {
            // Get the count of inspection_category_type_id for the current inspector
            $countCti = DB::table('visits')
            ->where('inspector_id', $inspector->id) 
            ->select(DB::raw('count(*) as count_cti'))
            ->pluck('count_cti')
            ->first(); 


    // Return the inspector data with the count_cti included
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
        'qualifications' => $inspector->qualifications ?? 'N/A',
        'professional_experience' => $inspector->professional_experience ?? 'N/A',
        'remarks' => $inspector->remarks ?? 'N/A',
        'inspections' => $inspector->inspections,
        'visits' => $inspector->visits->map(function ($visit) use ($allStates) {
            // Map state data for visits
            $state = $allStates->firstWhere('id', $visit->state_id);
            $visit->state_name = $state ? $state->state_name : 'N/A';
            return $visit;
        }),
    ];
});

// If no inspectors are found, return empty array
if ($inspectorsData->isEmpty()) {
    $inspectorsData = [];
}

// Return the view with the filtered data
return view('list_inspectors', compact('monthName','inspectorsData','escortOfficers', 'allStates', 'allCountry', 'siteCodes', 'inspectionCategories', 'typesOfInspection','stateId', 'typeOfInspection', 'countryId', 'inspectionCategoryTypeId',
    'dateOfArrival', 'dateOfDeparture', 'allInspectors', 'escortOfficerIds', 'allRank', 'allDesignation',
    'rankId', 'allStatus', 'statusId', 'allVisitCategory', 'allInspectionCategoryType', 'designationId',
    'visitCategoryId', 'siteCodeId', 'dateOfJoiningFrom', 'dateOfJoiningTo', 'inspectionTypeSelection','year'
));

    }
    









//     public function listInspectors(Request $request)
//     {
//         Log::info('Request Inputs:', ['data' => $request->all()]);

//         // Capture filter parameters from the request
//         $escortOfficerIds = $request->input('escortOfficer');
//         $dateOfBirth = $request->input('dateOfBirth');
//         $inspectionCategoryId = $request->input('inspectionCategory');
//         $stateId = $request->input('state');
//         $countryId = $request->input('country');
//         $siteCodeId = $request->input('siteCode');
//         $typeOfInspection = $request->input('typeOfInspection');
//         $dateOfArrival = $request->input('dateOfArrival');
//         $dateOfDeparture = $request->input('dateOfDeparture');

//         // Start with the Visit query
//         $visitsQuery = Visit::query();

//         // Apply filters to visits if they are provided
//         if ($escortOfficerIds) {
//             Log::info('Filtering by escort officers:', ['escortOfficerIds' => $escortOfficerIds]);

//             // Filter visits by escort officer IDs using the pivot table (visit_escort_officer)
//             $visitsQuery->whereHas('escortOfficers', function ($query) use ($escortOfficerIds) {
//                 $query->whereIn('escort_officers.id', $escortOfficerIds);
//             });
//         }

//         // If a `typeOfInspection` filter is provided, filter visits by `type_of_inspection_id`
//         if ($typeOfInspection) {
//             Log::info('Filtering by type of inspection:', ['typeOfInspection' => $typeOfInspection]);

//             // Filter visits based on type_of_inspection_id
//             $visitsQuery->where('type_of_inspection_id', '=', $typeOfInspection);
//         }

//         if ($dateOfArrival) {
//             $visitsQuery->whereDate('arrival_datetime', '=', $dateOfArrival);
//         }

//         if ($dateOfDeparture) {
//             $visitsQuery->whereDate('departure_datetime', '=', $dateOfDeparture);
//         }

//         if ($inspectionCategoryId) {
//             $visitsQuery->where('inspection_category_id', '=', $inspectionCategoryId);
//         }

//         // Fetch visits data after applying filters
//         $visits = $visitsQuery->get();
//         Log::info('Fetched Visits:', ['visits' => $visits]);

//         // Fetch other necessary data for the filters
//         $escortOfficers = EscortOfficer::all()->pluck('officer_name', 'id');
//         $allStates = State::withTrashed()->get();
//         $allCountry = Nationality::withTrashed()->get();
//         $siteCodes = SiteCode::whereNull('deleted_at')->get();
//         $inspectionCategories = InspectionCategory::all();
//         $typesOfInspection = InspectionType::all();

        

//         // Start with the Inspector query
//         $inspectorsQuery = Inspector::query();

//         // Apply dateOfBirth filter to the Inspector model (dob is in the inspectors table)
//         if ($dateOfBirth) {
//             $inspectorsQuery->whereDate('dob', '=', $dateOfBirth);
//         }

//         // 1. If a state filter is provided, use VisitSiteMapping to filter visits by state_id
//         if ($stateId) {
//             Log::info('Filtering by state:', ['stateId' => $stateId]);

//             // Fetch visits whose state_id matches the provided stateId from VisitSiteMapping
//             $visitIdsWithState = VisitSiteMapping::where('state_id', $stateId)
//                 ->pluck('visit_id');

//             // Filter the visits to only include those with the selected state_id
//             $visitsQuery->whereIn('id', $visitIdsWithState);
//         }

//     // 2. Filter inspectors by the visits that have been filtered by state
//     $inspectorsQuery->whereHas('visits', function ($query) use ($visitsQuery) {
//         // Here we check for visits that match the filtered visits (from VisitSiteMapping)
//         $query->whereIn('id', $visitsQuery->pluck('id'));
//     });

//     if ($countryId) {
//         Log::info('Filtering by country:', ['countryId' => $countryId]);

//         // Filter inspectors by nationality_id matching the provided countryId
//         $inspectorsQuery->where('nationality_id', '=', $countryId);
//     }



//     // Eager load relationships
//     $inspectors = $inspectorsQuery->with([
//         'inspections' => function ($query) {
//             $query->withTrashed()->with(['category', 'status']);
//         },
//         'visits' => function ($query) {
//             $query->withTrashed()->with([
//                 'inspector',
//                 'typeOfInspection',
//                 'teamLead',
//                 'inspectionType',
//                 'siteMappings',
//                 'category',
//                 'inspectionCategory',
//                 'inspectionCategoryType'
//             ]);
//         },
//         'gender',
//         'rank',
//         'nationality'
//     ])->get();


//     $inspectors = Inspector::with(['inspections', 'visits'])->get(); // No 'whereHas' condition


//     // Prepare inspectors data for the view
//     $inspectorsData = $inspectors->map(function ($inspector) use ($allStates) {
//         return [
//             'id' => $inspector->id,
//             'name' => $inspector->name ?? 'N/A',
//             'gender' => $inspector->gender->gender_name ?? 'N/A',
//             'dob' => $inspector->dob ?? 'N/A',
//             'nationality' => $inspector->nationality ?? 'N/A',
//             'passport_number' => $inspector->passport_number ?? 'N/A',
//             'unlp_number' => $inspector->unlp_number ?? 'N/A',
//             'rank' => $inspector->rank->rank_name ?? 'N/A',
//             'qualifications' => $inspector->qualifications ?? 'N/A',
//             'professional_experience' => $inspector->professional_experience ?? 'N/A',
//             'remarks' => $inspector->remarks ?? 'N/A',
//             'inspections' => $inspector->inspections,
//             'visits' => $inspector->visits->map(function ($visit) use ($allStates) {
//                 $state = $allStates->firstWhere('id', $visit->state_id);
//                 $visit->state_name = $state ? $state->state_name : 'N/A';
//                 return $visit;
//             }),
//         ];
//     });



//     // If no inspectors are found, make sure we return an empty array or structure
// if ($inspectorsData->isEmpty()) {
//     $inspectorsData = [];
// }


//     $allInspectors = Inspector::whereNull('deleted_at')->get();

//     // Return the view with the filtered data
//     return view('list_inspectors', compact(
//         'visits',
//         'escortOfficers',
//         'allStates',
//         'allCountry',
//         'siteCodes',
//         'inspectionCategories',
//         'typesOfInspection',
//         'escortOfficerIds', // Pass the selected filters back
//         'dateOfBirth',
//         'inspectionCategoryId',
//         'stateId',
//         'countryId',
//         'siteCodeId',
//         'typeOfInspection',
//         'dateOfArrival',
//         'dateOfDeparture',
//         'allInspectors',
//         'inspectorsData'
//     ));
// }




}
