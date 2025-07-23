<?php

use App\Http\Controllers\OtherStaffController;
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
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\LockController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProtectionController;
use App\Models\Inspector;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

Route::get('/captcha', [CaptchaController::class, 'generateCaptcha'])->name('captcha');



// Route::group(["middleware" => ["guest"]], function () {
//     Route::get('/', [AuthController::class, 'loadLogin'])->name('loadLogin');
//     Route::post('/login', [AuthController::class, 'userLogin'])->middleware('throttle:5,1')->name('userLogin');

//     Route::get('/register', [AuthController::class, 'loadRegister'])->name('loadRegister');
//     Route::post('/register', [AuthController::class, 'userRegister'])->name('userRegister');
// });


Route::group(["middleware" => ["guest"]], function () {
    Route::get('/', [AuthController::class, 'loadLogin'])->name('loadLogin');
    Route::get('/login', [AuthController::class, 'loadLogin'])->name('login'); // 👈 Add this

    Route::post('/login', [AuthController::class, 'userLogin'])->middleware('throttle:5,1')->name('userLogin');

    Route::get('/register', [AuthController::class, 'loadRegister'])->name('loadRegister');
    Route::post('/register', [AuthController::class, 'userRegister'])->name('userRegister');
});


    Route::group(['middleware' => ['isAuthenticated']], function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //Manage Inspector Route
    Route::get('/view-inspector', [InspectorController::class, 'manageInspector'])->name('manageInspector');
    Route::get('/view-inspector/{id}', [InspectorController::class, 'show'])->name('inspector.show');

    Route::post('/create-inspector', [InspectorController::class, 'createInspector'])->name('createInspector');
    Route::get('/add-inspector', [InspectorController::class, 'addInspector'])->name('addInspector');
    Route::get('/edit-inspector/{id}', [InspectorController::class, 'editInspector'])->name('editInspector');
    Route::post('/update-inspector/{id}', [InspectorController::class, 'updateInspector'])->name('updateInspector');
    Route::delete('/delete-inspector', [InspectorController::class, 'deleteInspector'])->name('deleteInspector');
    Route::patch('/inspector/{id}/update-status', [InspectorController::class, 'updateInspectorStatus']);

    Route::patch('/inspector/{id}/approve', [InspectorController::class, 'approve'])->name('inspector.approve');
    Route::patch('/inspectors/{id}/revert', [InspectorController::class, 'revert'])
    ->name('inspector.revert');
    Route::post('/inspector/{id}/send-to-draft', [InspectorController::class, 'sendToDraft'])->name('inspector.sendToDraft');
    Route::post('/inspectors/bulk-revert', [App\Http\Controllers\InspectorController::class, 'bulkRevert'])->name('inspectors.bulkRevert');
    Route::post('/inspectors/bulk-approve', [App\Http\Controllers\InspectorController::class, 'bulkApprove'])->name('inspectors.bulkApprove');
    Route::post('/inspectors/bulk-reject', [App\Http\Controllers\InspectorController::class, 'bulkReject'])->name('inspectors.bulkReject');




    //Manage Inspection Route
    Route::get('/view-inspection', [InspectionController::class, 'manageInspection'])->name('manageInspection');
    Route::get('/view-inspection/{id}', [InspectionController::class, 'show'])->name('inspection.show');

    Route::post('/create-inspection', [InspectionController::class, 'createInspection'])->name('createInspection');
    Route::get('/add-inspection', [InspectionController::class, 'addInspection'])->name('addInspection');
    Route::get('/edit-inspection/{id}', [InspectionController::class, 'editInspection'])->name('editInspection');
    Route::post('/update-inspection/{id}', [InspectionController::class, 'updateInspection'])->name('updateInspection');
    Route::delete('/delete-inspection', [InspectionController::class, 'deleteInspection'])->name('deleteInspection');
    Route::patch('/inspection/{id}/update-status', [InspectionController::class, 'updateInspectionStatus']);

    //Manage Visit Route
    Route::get('/view-inspection', [VisitController::class, 'manageVisit'])->name('manageVisit');
    Route::get('/view-inspection/{id}', [VisitController::class, 'show'])->name('visit.show');
    Route::post('/create-inspection', [VisitController::class, 'createVisit'])->name('createVisit');
    Route::get('/add-inspection', [VisitController::class, 'addVisit'])->name('addVisit');
    Route::get('/edit-inspection/{id}', [VisitController::class, 'editVisit'])->name('editVisit');
    Route::post('/update-inspection/{id}', [VisitController::class, 'updateVisit'])->name('updateVisit');
    Route::delete('/delete-inspection', [VisitController::class, 'deleteVisit'])->name('deleteVisit');
    Route::patch('/inspection/{id}/update-status', [VisitController::class, 'updateVisitStatus']);
    Route::get('/inspection-document/{id?}', [VisitController::class, 'getFaxDetails'])->name('getFaxDetails');

    Route::patch('/inspection/{id}/approve', [VisitController::class, 'approve'])->name('visit.approve');
    Route::patch('/inspections/{id}/revert', [VisitController::class, 'revert'])->name('visit.revert');
    Route::post('/inspection/{id}/send-to-draft', [VisitController::class, 'sendToDraft'])->name('visit.sendToDraft');





     //Manage Other Staff
    Route::get('/view-staff', [OtherStaffController::class, 'manageOtherStaff'])->name('manageOtherStaff');    
    Route::post('/create-other-staff', [OtherStaffController::class, 'createOtherStaff'])->name('createOtherStaff');
    Route::get('/add-other-staff', [OtherStaffController::class, 'addOtherStaff'])->name('addOtherStaff');
    Route::get('/view-other-staff/{id}', [OtherStaffController::class, 'show'])->name('otherstaff.show');
    Route::get('/edit-other-staff/{id}', [OtherStaffController::class, 'editOtherStaff'])->name('editOtherStaff');
    Route::post('/update-other-staff/{id}', [OtherStaffController::class, 'updateOtherStaff'])->name('updateOtherStaff');
    Route::patch('/other-staff/{id}/update-status', [OtherStaffController::class, 'updateOtherStaffStatus']);

     Route::patch('/other-staff/{id}/approve', [OtherStaffController::class, 'approve'])->name('otherStaff.approve');
    Route::patch('/other-staff/{id}/revert', [OtherStaffController::class, 'revert'])->name('otherStaff.revert');
    Route::post('/other-staff/{id}/send-to-draft', [OtherStaffController::class, 'sendToDraft'])->name('otherStaff.sendToDraft');



    


    //Manage Opcw Route
    Route::get('/view-opcw', [OpcwController::class, 'manageOpcw'])->name('manageOpcw');
    Route::post('/create-opcw', [OpcwController::class, 'createOpcw'])->name('createOpcw');
    Route::get('/add-opcw', [OpcwController::class, 'addOpcw'])->name('addOpcw');
    Route::get('/edit-opcw/{id}', [OpcwController::class, 'editOpcw'])->name('editOpcw');
    Route::post('/update-opcw/{id}', [OpcwController::class, 'updateOpcw'])->name('updateOpcw');
    Route::delete('/delete-opcw', [OpcwController::class, 'deleteOpcw'])->name('deleteOpcw');
    Route::patch('/fax/{id}/update-status', [OpcwController::class, 'updateOpcwStatus']);

    //Manage Report Route
    Route::get('/view-report', [ReportController::class, 'manageReport'])->name('manageReport');
    Route::get('/year-wiseReport', [ReportController::class, 'yearwiseReport'])->name('yearwiseReport');
    Route::get('/state-wise-report', [ReportController::class, 'stateWiseReport'])->name('stateWiseReport');
    Route::post('/state-wise-report', [ReportController::class, 'stateWiseReport'])->name('stateWiseReport.post');
    Route::get('/plantsite-wise-report', [ReportController::class, 'plantsitewiseReport'])->name('plantsitewiseReport');
    Route::post('/plantsite-wise-report', [ReportController::class, 'plantsitewiseReport'])->name('plantsitewiseReport.post');
    Route::get('/monthly-report/{year?}', [ReportController::class, 'showMonthlyReport'])->name('monthly.report');
    Route::get('/inspections-report/country/{country}', [ReportController::class, 'showByCountry'])->name('inspections.byCountry');
    Route::get('/list-inspectors/{id?}/{inst?}/{year?}/{month?}/{stateid?}', [ReportController::class, 'listInspectors'])->name('listInspectors.month');
    Route::get('/list-inspectors', [ReportController::class, 'listInspectors'])->name('listInspectorsgen.get');
    Route::get('/list-inspectors/{id?}', [ReportController::class, 'listInspectors'])->name('listInspectorsdash.get');
    Route::post('/list-inspectors', [ReportController::class, 'listInspectors'])->name('listInspectors.post');
    Route::get('/year-graph', [ReportController::class, 'yearwiseBarGraph'])->name('yearwiseBarGraph');
    Route::get('/year-pie', [ReportController::class, 'yearwisePieChart'])->name('yearwisePieChart');
    Route::get('/sequential-pie', [ReportController::class, 'yearSequentialPieChart'])->name('yearSequentialPieChart');
    Route::get('/national-wise-inspection-report', [ReportController::class, 'nationalWiseInspectionReport'])->name('nationalWiseInspectionReport');
    Route::post('/national-wise-inspection-report', [ReportController::class, 'nationalWiseInspectionReport'])->name('nationalWiseInspectionReport.post');
    Route::get('/inspection-report', [App\Http\Controllers\ReportController::class, 'inspectionReport'])->name('updateInspectionReport');



 // Change password routes
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('showChangePassword');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('changePassword');


});



Route::middleware(['auth', 'admin'])->group(function () {
    //Manage Nationality Route
    Route::get('/view-nationality', [NationalityController::class, 'manageNationality'])->name('manageNationality');
    Route::post('/create-nationality', [NationalityController::class, 'createNationality'])->name('createNationality');
    Route::get('/add-nationality', [NationalityController::class, 'addNationality'])->name('addNationality');
    Route::get('/edit-nationality/{id}', [NationalityController::class, 'editNationality'])->name('editNationality');
    Route::post('/update-nationality/{id}', [NationalityController::class, 'updateNationality'])->name('updateNationality');
    Route::delete('/delete-nationality', [NationalityController::class, 'deleteNationality'])->name('deleteNationality');
    Route::patch('/nationality/{id}/update-status', [NationalityController::class, 'updateNationalityStatus']);

    //Manage Rank Route
    Route::get('/view-rank', [RankController::class, 'manageRank'])->name('manageRank');
    Route::post('/create-rank', [RankController::class, 'createRank'])->name('createRank');
    Route::get('/add-rank', [RankController::class, 'addRank'])->name('addRank');
    Route::get('/edit-rank/{id}', [RankController::class, 'editRank'])->name('editRank');
    Route::post('/update-rank/{id}', [RankController::class, 'updateRank'])->name('updateRank');
    Route::delete('/delete-rank', [RankController::class, 'deleteRank'])->name('deleteRank');
    Route::patch('/rank/{id}/update-status', [RankController::class, 'updateRankStatus']);

    //Manage Designation Route
    Route::get('/view-designation', [DesignationController::class, 'manageDesignation'])->name('manageDesignation');
    Route::post('/create-designation', [DesignationController::class, 'createDesignation'])->name('createDesignation');
    Route::get('/add-designation', [DesignationController::class, 'addDesignation'])->name('addDesignation');
    Route::get('/edit-designation/{id}', [DesignationController::class, 'editDesignation'])->name('editDesignation');
    Route::post('/update-designation/{id}', [DesignationController::class, 'updateDesignation'])->name('updateDesignation');
    Route::delete('/delete-designation', [DesignationController::class, 'deleteDesignation'])->name('deleteDesignation');
    Route::patch('/designation/{id}/update-status', [DesignationController::class, 'updateDesignationStatus']);


    //Manage Status Route
    Route::get('/view-status', [StatusController::class, 'manageStatus'])->name('manageStatus');
    Route::post('/create-status', [StatusController::class, 'createStatus'])->name('createStatus');
    Route::get('/add-status', [StatusController::class, 'addStatus'])->name('addStatus');
    Route::get('/edit-status/{id}', [StatusController::class, 'editStatus'])->name('editStatus');
    Route::post('/update-status/{id}', [StatusController::class, 'updateStatus'])->name('updateStatus');
    Route::delete('/delete-status', [StatusController::class, 'deleteStatus'])->name('deleteStatus');
    Route::patch('/status/{id}/update-status', [StatusController::class, 'updateStatusStatus']);

    //Manage Inspection Category
    Route::get('/view-inspection-category', [InspectionCategoryController::class, 'manageInspectionCategory'])->name('manageInspectionCategory');
    Route::get('/view-category-inspection', [InspectionCategoryController::class, 'manageCategoryInspection'])->name('manageCategoryInspection');
    Route::post('/create-inspection-category', [InspectionCategoryController::class, 'createInspectionCategory'])->name('createInspectionCategory');
    Route::post('/create-category-inspection', [InspectionCategoryController::class, 'createCategoryInspection'])->name('createCategoryInspection');
    Route::get('/add-inspection-category', [InspectionCategoryController::class, 'addInspectionCategory'])->name('addInspectionCategory');
    Route::get('/add-category-inspection', [InspectionCategoryController::class, 'addCategoryInspection'])->name('addCategoryInspection');
    Route::get('/edit-inspection-category/{id}', [InspectionCategoryController::class, 'editInspectionCategory'])->name('editInspectionCategory');
    Route::get('/edit-category-inspection/{id}', [InspectionCategoryController::class, 'editCategoryInspection'])->name('editCategoryInspection');
    Route::post('/update-inspection-category/{id}', [InspectionCategoryController::class, 'updateInspectionCategory'])->name('updateInspectionCategory');
    Route::post('/update-category-inspection/{id}', [InspectionCategoryController::class, 'updateCategoryInspection'])->name('updateCategoryInspection');
    Route::delete('/delete-inspection-category', [InspectionCategoryController::class, 'deleteInspectionCategory'])->name('deleteInspectionCategory');
    Route::patch('/inspection-category/{id}/update-status', [InspectionCategoryController::class, 'updateInspectionCategoryStatus']);
    Route::patch('/category-inspection/{id}/update-status', [InspectionCategoryController::class, 'updateCategoryInspectionStatus']);

    //Manage Inspection Category Type
    Route::get('/view-inspection-category-type', [InspectionCategoryTypeController::class, 'manageInspectionCategoryType'])->name('manageInspectionCategoryType');
    Route::get('/add-inspection-category-type', [InspectionCategoryTypeController::class, 'addInspectionCategoryType'])->name('addInspectionCategoryType');
    Route::post('/create-inspection-category-type', [InspectionCategoryTypeController::class, 'createInspectionCategoryType'])->name('createInspectionCategoryType');
    Route::get('/edit-inspection-category-type/{id}', [InspectionCategoryTypeController::class, 'editInspectionCategoryType'])->name('editInspectionCategoryType');
    Route::post('/update-inspection-category-type/{id}', [InspectionCategoryTypeController::class, 'updateInspectionCategoryType'])->name('updateInspectionCategoryType');
    Route::delete('/delete-inspection-category-type', [InspectionCategoryTypeController::class, 'deleteInspectionCategoryType'])->name('deleteInspectionCategoryType');
    Route::patch('/inspection-category-type/{id}/update-status', [InspectionCategoryTypeController::class, 'updateInspectionCategoryTypeStatus']);

    //Manage Visit Category
    Route::get('/view-visit-category', [VisitCategoryController::class, 'manageVisitCategory'])->name('manageVisitCategory');
    Route::post('/create-visit-category', [VisitCategoryController::class, 'createVisitCategory'])->name('createVisitCategory');
    Route::get('/add-visit-category', [VisitCategoryController::class, 'addVisitCategory'])->name('addVisitCategory');
    Route::get('/edit-visit-category/{id}', [VisitCategoryController::class, 'editVisitCategory'])->name('editVisitCategory');
    Route::post('/update-visit-category/{id}', [VisitCategoryController::class, 'updateVisitCategory'])->name('updateVisitCategory');
    Route::delete('/delete-visit-category', [VisitCategoryController::class, 'deleteVisitCategory'])->name('deleteVisitCategory');
    Route::patch('/visit-category/{id}/update-status', [VisitCategoryController::class, 'updateVisitCategoryStatus']);
    
    //Manage Escort Officer
    Route::get('/view-escort-officer', [EscortOfficerController::class, 'manageEscortOfficer'])->name('manageEscortOfficer');
    Route::post('/create-escort-officer', [EscortOfficerController::class, 'createEscortOfficer'])->name('createEscortOfficer');
    Route::get('/add-escort-officer', [EscortOfficerController::class, 'addEscortOfficer'])->name('addEscortOfficer');
    Route::patch('/status/{id}/update-officer-status', [EscortOfficerController::class, 'updateEscortOfficerStatus']);
    Route::get('/edit-escort-officer/{id}', [EscortOfficerController::class, 'editEscortOfficer'])->name('editEscortOfficer');
    Route::post('/update-escort-officer/{id}', [EscortOfficerController::class, 'updateEscortOfficer'])->name('updateEscortOfficer');
    Route::delete('/delete-escort-officer', [EscortOfficerController::class, 'deleteEscortOfficer'])->name('deleteEscortOfficer');
    
    //Manage SiteCode Route
    Route::get('/view-site-code', [SiteCodeController::class, 'manageSiteCode'])->name('manageSiteCode');
    Route::post('/create-site-code', [SiteCodeController::class, 'createSiteCode'])->name('createSiteCode');
    Route::get('/add-site-code', [SiteCodeController::class, 'addSiteCode'])->name('addSiteCode');
    Route::get('/edit-site-code/{id}', [SiteCodeController::class, 'editSiteCode'])->name('editSiteCode');
    Route::post('/update-site-code/{id}', [SiteCodeController::class, 'updateSiteCode'])->name('updateSiteCode');
    Route::delete('/delete-site-code', [SiteCodeController::class, 'deleteSiteCode'])->name('deleteSiteCode');
    Route::patch('/site-code/{id}/update-status', [SiteCodeController::class, 'updateSiteCodeStatus']);
    
    //Manage State Route
    Route::get('/view-state', [StateController::class, 'manageState'])->name('manageState');
    Route::post('/create-state', [StateController::class, 'createState'])->name('createState');
    Route::get('/add-state', [StateController::class, 'addState'])->name('addState');
    Route::get('/edit-state/{id}', [StateController::class, 'editState'])->name('editState');
    Route::post('/update-state/{id}', [StateController::class, 'updateState'])->name('updateState');
    Route::delete('/delete-state', [StateController::class, 'deleteState'])->name('deleteState');
    Route::patch('/state/{id}/update-status', [StateController::class, 'updateStateStatus']);
    
    //Display Audit Trails
    Route::get('/view-login-logs', [AuditTrailController::class, 'loginLogs'])->name('loginLogs.get');
    Route::post('/view-login-logs', [AuditTrailController::class, 'loginLogs'])->name('loginLogs.post');
    Route::get('/view-activity-logs', [AuditTrailController::class, 'activityLogs'])->name('activityLogs.get');
    Route::post('/view-activity-logs', [AuditTrailController::class, 'activityLogs'])->name('activityLogs.post');
    
   
    
    //Manage User
    Route::get('/view-users', [UserController::class, 'manageUser'])->name('manageUser');
    Route::post('/create-user', [UserController::class, 'createUser'])->name('createUser');
    Route::get('/add-user', [UserController::class, 'addUser'])->name('addUser');
    Route::get('/edit-user/{id}', [UserController::class, 'editUser'])->name('editUser');
    Route::post('/update-user/{id}', [UserController::class, 'updateUser'])->name('updateUser');
    Route::patch('/user/{id}/update-status', [UserController::class, 'updateUserStatus']);
    
    //Manage Issue
    Route::get('/view-issues', [IssueController::class, 'manageIssue'])->name('manageIssue');
    Route::post('/create-issue', [IssueController::class, 'createIssue'])->name('createIssue');
    Route::get('/add-issue', [IssueController::class, 'addIssue'])->name('addIssue');
    Route::get('/edit-issue/{id}', [IssueController::class, 'editIssue'])->name('editIssue');
    Route::post('/update-issue/{id}', [IssueController::class, 'updateIssue'])->name('updateIssue');
    Route::patch('/issue/{id}/update-status', [IssueController::class, 'updateIssueStatus']);



    //Manage Lock
    Route::get('/lock', [LockController::class, 'manageLock'])->name('manageLock');
    Route::post('/lock', [LockController::class, 'createLock'])->name('createLock');
    Route::put('/locks/{id}', [LockController::class, 'updateLock']);
    Route::delete('/locks/{id}', [LockController::class, 'deleteLock']);


});


Route::post('/toggle-protection', [ProtectionController::class, 'toggle'])
    ->name('toggle.protection')
    ->middleware('auth');


Route::get('/check-app-key', function () {
    return response()->json(['app_key' => config('app.key')]);
});



Route::get('/test-decrypt', function () {
    $inspectors = Inspector::all();

    foreach ($inspectors as $inspector) {
        try {
            // First decryption (removes double encryption)
            $correctName = Crypt::decryptString($inspector->name);
            $correctPassport = Crypt::decryptString($inspector->passport_number);
            $correctUnlp = $inspector->unlp_number ? Crypt::decryptString($inspector->unlp_number) : null;
    
            // Update table with corrected values
            DB::table('inspectors')->where('id', $inspector->id)->update([
                'name' => $correctName,
                'passport_number' => $correctPassport,
                'unlp_number' => $correctUnlp,
            ]);
    
            echo "Fixed Inspector ID: {$inspector->id}\n";
        } catch (\Exception $e) {
            echo "Error fixing Inspector ID: {$inspector->id}\n";
        }
    }
    
});



Route::get('/test-encrypt', function () {
    $inspectors = Inspector::all();

    foreach ($inspectors as $inspector) {
        // Ensure data is encrypted only once
        if (!$this->isEncrypted($inspector->getRawOriginal('name'))) {
            $inspector->update([
                'name' => $inspector->name, // Triggers setNameAttribute() to encrypt
                'passport_number' => $inspector->passport_number, // Triggers setPassportNumberAttribute() to encrypt
                'unlp_number' => $inspector->unlp_number // Triggers setUnlpNumberAttribute() to encrypt
            ]);
        }
    }

    $this->info('Inspector data encrypted successfully.');
    
});




Route::get('/download-pdf', [PDFController::class, 'generatePDF'])->name('download.pdf');

Route::get('/optimize-clear', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('clear-compiled');
    \Artisan::call('optimize');

    return 'All caches cleared and optimized successfully!';
})->name('optimize.clear');

Route::get('/clear-config', function () {
    Artisan::call('config:clear');
    return "Configuration cleared!";
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return "Cache cleared!";
});

Route::get('/clear-route', function () {
    Artisan::call('route:clear');
    return "Route cleared!";
});

Route::get('/clear-view', function () {
    Artisan::call('view:clear');
    return "View cleared!";
});






Route::get('/check-db', function () {
    try {
        DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();
        return "✅ Connected successfully to DB: {$dbName}";
    } catch (\Exception $e) {
        return "❌ Could not connect to the database. Error: " . $e->getMessage();
    }
});





Route::get('/debug-headers', function () {
    return response('Header Check')
        ->header('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)')
        ->withHeaders(headers_list());
});
