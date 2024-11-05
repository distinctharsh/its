<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\EscortOfficerController;
use App\Http\Controllers\InspectionCategoryController;
use App\Http\Controllers\InspectionCategoryTypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectorController;
use App\Http\Controllers\NationalityController;
use App\Http\Controllers\OpcwController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\SiteCodeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\VisitCategoryController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AuditTrailController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/captcha', [CaptchaController::class, 'generateCaptcha'])->name('captcha');



Route::group(["middleware"=> ["guest"]], function () {
    Route::get('/', [AuthController::class,'loadLogin'])->name('loadLogin');
    Route::post('/', [AuthController::class,'userLogin'])->name('userLogin');
    Route::get('/register', [AuthController::class,'loadRegister'])->name('loadRegister');
    Route::post('/register', [AuthController::class,'userRegister'])->name('userRegister');
});


Route::group(['middleware'=> ['isAuthenticated']], function () {
    Route::get('/dashboard', [AuthController::class,'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');


    //Manage Roles Route
    // Route::get('/manage-role', [RoleController::class,'manageRole'])->name('manageRole');
    // Route::post('/create-role', [RoleController::class,'createRole'])->name('createRole');
    // Route::delete('/delete-role', [RoleController::class,'deleteRole'])->name('deleteRole');
    
    //Manage Permissions Route
    // Route::get('/manage-permission', [PermissionController::class,'managePermission'])->name('managePermission');
    // Route::post('/create-permission', [PermissionController::class,'createPermission'])->name('createPermission');
    // Route::delete('/delete-permission', [PermissionController::class,'deletePermission'])->name('deletePermission');
    
    
    //Assign Permission to Role routes
    // Route::get('/assign-permission-role', [PermissionController::class,'assignPermissionRole'])->name('assignPermissionRole');
    // Route::post('/create-permission-role', [PermissionController::class,'createPermissionRole'])->name('createPermissionRole');


    //Manage Inspector Route
    Route::get('/view-inspector', [InspectorController::class,'manageInspector'])->name('manageInspector');
    Route::get('/view-inspector/{id}', [InspectorController::class, 'show'])->name('inspector.show');
    
    Route::post('/create-inspector', [InspectorController::class,'createInspector'])->name('createInspector');
    Route::get('/add-inspector', [InspectorController::class, 'addInspector'])->name('addInspector');
    Route::get('/edit-inspector/{id}', [InspectorController::class,'editInspector'])->name('editInspector');
    Route::post('/update-inspector/{id}', [InspectorController::class, 'updateInspector'])->name('updateInspector');
    Route::delete('/delete-inspector', [InspectorController::class,'deleteInspector'])->name('deleteInspector');
    Route::patch('/inspector/{id}/update-status', [InspectorController::class, 'updateInspectorStatus']);
    
    
    //Manage Inspection Route
    Route::get('/view-inspection', [InspectionController::class,'manageInspection'])->name('manageInspection');
    Route::get('/view-inspection/{id}', [InspectionController::class, 'show'])->name('inspection.show');
    
    Route::post('/create-inspection', [InspectionController::class,'createInspection'])->name('createInspection');
    Route::get('/add-inspection', [InspectionController::class, 'addInspection'])->name('addInspection');
    Route::get('/edit-inspection/{id}', [InspectionController::class,'editInspection'])->name('editInspection');
    Route::post('/update-inspection/{id}', [InspectionController::class, 'updateInspection'])->name('updateInspection');
    Route::delete('/delete-inspection', [InspectionController::class,'deleteInspection'])->name('deleteInspection');
    Route::patch('/inspection/{id}/update-status', [InspectionController::class, 'updateInspectionStatus']);
    
    //Manage Visit Route
    Route::get('/view-visit', [VisitController::class,'manageVisit'])->name('manageVisit');
    Route::get('/view-visit/{id}', [VisitController::class, 'show'])->name('visit.show');
    Route::post('/create-visit', [VisitController::class, 'createVisit'])->name('createVisit');
    Route::get('/add-visit', [VisitController::class, 'addVisit'])->name('addVisit');
    Route::get('/edit-visit/{id}', [VisitController::class,'editVisit'])->name('editVisit');
    Route::post('/update-visit/{id}', [VisitController::class, 'updateVisit'])->name('updateVisit');
    Route::delete('/delete-visit', [VisitController::class,'deleteVisit'])->name('deleteVisit');
    Route::patch('/visit/{id}/update-status', [VisitController::class, 'updateVisitStatus']);
    
    //Manage Opcw Route
    Route::get('/view-opcw', [OpcwController::class,'manageOpcw'])->name('manageOpcw');
    Route::post('/create-opcw', [OpcwController::class,'createOpcw'])->name('createOpcw');
    Route::get('/add-opcw', [OpcwController::class, 'addOpcw'])->name('addOpcw');
    Route::get('/edit-opcw/{id}', [OpcwController::class,'editOpcw'])->name('editOpcw');
    Route::post('/update-opcw/{id}', [OpcwController::class, 'updateOpcw'])->name('updateOpcw');
    Route::delete('/delete-opcw', [OpcwController::class,'deleteOpcw'])->name('deleteOpcw');
    Route::patch('/fax/{id}/update-status', [OpcwController::class, 'updateOpcwStatus']);
    
    //Manage Report Route
    Route::get('/view-report', [ReportController::class,'manageReport'])->name('manageReport');
    Route::get('/inspections-report/country/{country}', [ReportController::class, 'showByCountry'])->name('inspections.byCountry');
    Route::get('/list-inspectors', [ReportController::class, 'listInspectors'])->name('listInspectors');
    
    
    
    
    //Manage Nationality Route
    Route::get('/view-nationality', [NationalityController::class,'manageNationality'])->name('manageNationality');
    Route::post('/create-nationality', [NationalityController::class,'createNationality'])->name('createNationality');
    Route::get('/add-nationality', [NationalityController::class, 'addNationality'])->name('addNationality');
    Route::get('/edit-nationality/{id}', [NationalityController::class,'editNationality'])->name('editNationality');
    Route::post('/update-nationality/{id}', [NationalityController::class, 'updateNationality'])->name('updateNationality');
    Route::delete('/delete-nationality', [NationalityController::class,'deleteNationality'])->name('deleteNationality');
    Route::patch('/nationality/{id}/update-status', [NationalityController::class, 'updateNationalityStatus']);
    
    
    
    //Manage Rank Route
    Route::get('/view-rank', [RankController::class,'manageRank'])->name('manageRank');
    Route::post('/create-rank', [RankController::class,'createRank'])->name('createRank');
    Route::get('/add-rank', [RankController::class, 'addRank'])->name('addRank');
    Route::get('/edit-rank/{id}', [RankController::class,'editRank'])->name('editRank');
    Route::post('/update-rank/{id}', [RankController::class, 'updateRank'])->name('updateRank');
    Route::delete('/delete-rank', [RankController::class,'deleteRank'])->name('deleteRank');
    Route::patch('/rank/{id}/update-status', [RankController::class, 'updateRankStatus']);
    
    
    //Manage Status Route
    Route::get('/view-status', [StatusController::class,'manageStatus'])->name('manageStatus');
    Route::post('/create-status', [StatusController::class,'createStatus'])->name('createStatus');
    Route::get('/add-status', [StatusController::class, 'addStatus'])->name('addStatus');
    Route::get('/edit-status/{id}', [StatusController::class,'editStatus'])->name('editStatus');
    Route::post('/update-status/{id}', [StatusController::class, 'updateStatus'])->name('updateStatus');
    Route::delete('/delete-status', [StatusController::class,'deleteStatus'])->name('deleteStatus');
    Route::patch('/status/{id}/update-status', [StatusController::class, 'updateStatusStatus']);
    
    //Manage Inspection Category
    Route::get('/view-inspection-category', [InspectionCategoryController::class,'manageInspectionCategory'])->name('manageInspectionCategory');
    Route::post('/create-inspection-category', [InspectionCategoryController::class,'createInspectionCategory'])->name('createInspectionCategory');
    Route::get('/add-inspection-category', [InspectionCategoryController::class, 'addInspectionCategory'])->name('addInspectionCategory');
    Route::get('/edit-inspection-category/{id}', [InspectionCategoryController::class,'editInspectionCategory'])->name('editInspectionCategory');
    Route::post('/update-inspection-category/{id}', [InspectionCategoryController::class, 'updateInspectionCategory'])->name('updateInspectionCategory');
    Route::delete('/delete-inspection-category', [InspectionCategoryController::class,'deleteInspectionCategory'])->name('deleteInspectionCategory');
    Route::patch('/inspection-category/{id}/update-status', [InspectionCategoryController::class, 'updateInspectionCategoryStatus']);
    
    //Manage Inspection Category Type
    Route::get('/view-inspection-category-type', [InspectionCategoryTypeController::class, 'manageInspectionCategoryType'])->name('manageInspectionCategoryType');
    Route::get('/add-inspection-category-type', [InspectionCategoryTypeController::class, 'addInspectionCategoryType'])->name('addInspectionCategoryType');
    Route::post('/create-inspection-category-type', [InspectionCategoryTypeController::class,'createInspectionCategoryType'])->name('createInspectionCategoryType');
    Route::get('/edit-inspection-category-type/{id}', [InspectionCategoryTypeController::class,'editInspectionCategoryType'])->name('editInspectionCategoryType');
    Route::post('/update-inspection-category-type/{id}', [InspectionCategoryTypeController::class, 'updateInspectionCategoryType'])->name('updateInspectionCategoryType');
    Route::delete('/delete-inspection-category-type', [InspectionCategoryTypeController::class,'deleteInspectionCategoryType'])->name('deleteInspectionCategoryType');
    Route::patch('/inspection-category-type/{id}/update-status', [InspectionCategoryTypeController::class, 'updateInspectionCategoryTypeStatus']);
    
    //Manage Visit Category
    Route::get('/view-visit-category', [VisitCategoryController::class,'manageVisitCategory'])->name('manageVisitCategory');
    Route::post('/create-visit-category', [VisitCategoryController::class,'createVisitCategory'])->name('createVisitCategory');
    Route::get('/add-visit-category', [VisitCategoryController::class, 'addVisitCategory'])->name('addVisitCategory');
    Route::get('/edit-visit-category/{id}', [VisitCategoryController::class,'editVisitCategory'])->name('editVisitCategory');
    Route::post('/update-visit-category/{id}', [VisitCategoryController::class, 'updateVisitCategory'])->name('updateVisitCategory');
    Route::delete('/delete-visit-category', [VisitCategoryController::class,'deleteVisitCategory'])->name('deleteVisitCategory');
    Route::patch('/visit-category/{id}/update-status', [VisitCategoryController::class, 'updateVisitCategoryStatus']);
    
    //Manage Escort Officer
    Route::get('/view-escort-officer', [EscortOfficerController::class,'manageEscortOfficer'])->name('manageEscortOfficer');
    Route::post('/create-escort-officer', [EscortOfficerController::class,'createEscortOfficer'])->name('createEscortOfficer');
    Route::get('/add-escort-officer', [EscortOfficerController::class, 'addEscortOfficer'])->name('addEscortOfficer');
    Route::patch('/status/{id}/update-officer-status', [EscortOfficerController::class, 'updateEscortOfficerStatus']);

    Route::get('/edit-escort-officer/{id}', [EscortOfficerController::class,'editEscortOfficer'])->name('editEscortOfficer');
    Route::post('/update-escort-officer/{id}', [EscortOfficerController::class, 'updateEscortOfficer'])->name('updateEscortOfficer');
    Route::delete('/delete-escort-officer', [EscortOfficerController::class,'deleteEscortOfficer'])->name('deleteEscortOfficer');


     
    //Manage SiteCode Route
    Route::get('/view-site-code', [SiteCodeController::class,'manageSiteCode'])->name('manageSiteCode');
    Route::post('/create-site-code', [SiteCodeController::class,'createSiteCode'])->name('createSiteCode');
    Route::get('/add-site-code', [SiteCodeController::class, 'addSiteCode'])->name('addSiteCode');
    Route::get('/edit-site-code/{id}', [SiteCodeController::class,'editSiteCode'])->name('editSiteCode');
    Route::post('/update-site-code/{id}', [SiteCodeController::class, 'updateSiteCode'])->name('updateSiteCode');
    Route::delete('/delete-site-code', [SiteCodeController::class,'deleteSiteCode'])->name('deleteSiteCode');
    Route::patch('/site-code/{id}/update-status', [SiteCodeController::class, 'updateSiteCodeStatus']);

    //Manage State Route
    Route::get('/view-state', [StateController::class,'manageState'])->name('manageState');
    Route::post('/create-state', [StateController::class,'createState'])->name('createState');
    Route::get('/add-state', [StateController::class, 'addState'])->name('addState');
    Route::get('/edit-state/{id}', [StateController::class,'editState'])->name('editState');
    Route::post('/update-state/{id}', [StateController::class, 'updateState'])->name('updateState');
    Route::delete('/delete-state', [StateController::class,'deleteState'])->name('deleteState');
    Route::patch('/state/{id}/update-status', [StateController::class, 'updateStateStatus']);

    //Display Audit Trails
    Route::get('/view-login-logs', [AuditTrailController::class,'loginLogs'])->name('loginLogs');
    Route::get('/view-activity-logs', [AuditTrailController::class,'activityLogs'])->name('activityLogs');

    
});