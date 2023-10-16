<?php

use App\Http\Controllers\Academics\AcademicPeriodsController;
use App\Http\Controllers\Academics\AssessmentsTypesController;
use App\Http\Controllers\Academics\ClassAssessmentsController;
use App\Http\Controllers\Academics\ClassesController;
use App\Http\Controllers\Academics\CourseLevelsController;
use App\Http\Controllers\Academics\CoursesController;
use App\Http\Controllers\Academics\DepartmentsController;
use App\Http\Controllers\Academics\IntakeController;
use App\Http\Controllers\Academics\PrerequisiteController;
use App\Http\Controllers\Academics\ProgramCoursesController;
use App\Http\Controllers\Academics\ProgramsController;
use App\Http\Controllers\Academics\QualicationsController;
use App\Http\Controllers\MyAccountController;
use App\Http\Controllers\student\ApplicationsController;
use App\Http\Controllers\Student\RegistrationController;
use App\Http\Controllers\SuperAdmin\SettingsController;
use App\Http\Controllers\SupportTeam\AcademicFeesController;
use App\Http\Controllers\SupportTeam\FeesController;
use App\Http\Controllers\SupportTeam\PeriodTypeController;
use App\Http\Controllers\SupportTeam\StudentProfileController;
use App\Http\Controllers\SupportTeam\StudentRecordController;
use App\Http\Controllers\SupportTeam\StudyModeController;
use App\Http\Controllers\SupportTeam\UsersController;
use App\Http\Middleware\Custom\SuperAdmin;
use Illuminate\Support\Facades\Route;

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
 Auth::routes();
Route::group(['middleware' => 'auth'], function () {

//    Route::get('/', 'HomeController@dashboard')->name('home');
//    Route::get('/home', 'HomeController@dashboard')->name('home');
//    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');

    Route::get('/', function () {
       // return view('welcome');
        //return view('home');
        return view('pages.support_team.dashboard');
    });

    //Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class,'dashboard'])->name('dashboard');

    Route::group(['middleware' => SuperAdmin::class,'prefix' => 'academics'], function() {
        Route::get('/', [AcademicPeriodsController::class,'index'])->name('calendar');
        Route::post('/create', [AcademicPeriodsController::class,'store'])->name('create');
        Route::get('/edit/{id}', [AcademicPeriodsController::class,'edit'])->name('update');
        Route::put('/update/{id}', [AcademicPeriodsController::class,'update'])->name('acupdate');
        //Route::delete('/delete/{id}', [AcademicPeriodsController::class,'destroy'])->name('delete.ac');
        Route::resource('academics', AcademicPeriodsController::class);

        Route::get('/show/{id}', [AcademicPeriodsController::class,'show'])->name('academic.show');
//        Academic feesdestroy.academic.fees
        Route::post('ac-fees',[AcademicPeriodsController::class,'addAcfees'])->name('add.fees');
        Route::delete('/delete/{id}',[AcademicFeesController::class,'destroy'])->name('destroy.academic.fees');
        //Program courses
        Route::post('/add', [ProgramCoursesController::class,'store'])->name('store.courses');
        Route::delete('/delete/{programID}/{levelID}/{courseID}', [ProgramCoursesController::class,'destroy'])->name('destroy.programsCourse');
        //Prerequisite
        Route::group(['prefix' => '/prerequisites'], function (){
            Route::post('/add',[PrerequisiteController::class,'store'])->name('store.prerequisite');
            Route::get('/',[PrerequisiteController::class,'index'])->name('index.prerequisite');
            Route::get('/edit/{id}',[PrerequisiteController::class,'edit'])->name('edit.prerequisite');
            Route::delete('/delete/{id}',[PrerequisiteController::class,'destroy'])->name('delete.prerequisite');
            Route::put('/update/{id}',[PrerequisiteController::class,'update'])->name('update.prerequisite');
        });
        Route::group(['prefix' => 'classes'], function (){
            Route::post('/add',[ClassesController::class,'store'])->name('store.classes');
            Route::delete('/delete/{id}',[ClassesController::class,'destroy'])->name('classes.delete');
            Route::get('/show/{period}/{program}',[ClassesController::class,'customShow'])->name('classes.customShow');
        });
    });

    Route::group(['prefix' => 'my_account'], function() {
        Route::get('/', [MyAccountController::class,'index'])->name('my_account');
//        Route::put('/', 'MyAccountController@update_profile')->name('my_account.update');
        Route::put('/change_password',[MyAccountController::class,'change_pass'])->name('my_account.change_pass');
    });
    //admit student
    Route::group(['prefix' => 'admit'], function (){
        Route::get('/get-states/{id}',[StudentRecordController::class,'getStates'])->name('get_states');
        Route::get('/get-towns/{id}',[StudentRecordController::class,'getTowns'])->name('get_towns');
        Route::get('/get-programs/{id}',[StudentRecordController::class,'getPrograms'])->name('get_programs');
        Route::get('/get-levels/{id}',[StudentRecordController::class,'getLevels'])->name('get_levels');
    });
    Route::group(['prefix' => 'students'], function (){
        Route::get('/search',[StudentRecordController::class,'getStudents'])->name('students.list');
        Route::post('/search',[StudentRecordController::class,'getStudentsSearch'])->name('students.lists');
        Route::get('/profile/{id}',[StudentProfileController::class,'show'])->name('student.profile');
    });

    Route::group(['prefix' => 'assess'], function (){
        Route::get('/classes/{id}',[ClassAssessmentsController::class,'getClasses'])->name('class-names');
    });
    Route::group(['prefix' => 'academic'], function (){
        Route::get('/',[RegistrationController::class,'programs'])->name('my-program');
    });
    Route::group(['prefix' => 'apply'], function (){
        //get
        Route::get('/change-program',[ApplicationsController::class,'ChangePrograms'])->name('changePrograms');
        Route::get('/exemptions',[ApplicationsController::class,'Exemption'])->name('exemptions');
        Route::get('/withdrawal-deferment',[ApplicationsController::class,'WithDef'])->name('Withdrawal_Deferment');
        Route::get('/add-drop',[ApplicationsController::class,'ADCourses'])->name('Add_Drop_courses');
        Route::get('/study-mode',[ApplicationsController::class,'ChangeStudyMode'])->name('change_mode');
        //post change_program_apply
        Route::post('/change-program',[ApplicationsController::class,'ChangeProgramsApply'])->name('change_program_apply');
    });
    Route::group(['prefix' => 'student'], function (){
        Route::get('/finances',[StudentProfileController::class,'MyFinances'])->name('student_finance');
        Route::get('/results',[StudentProfileController::class,'MyResults'])->name('student-exam_results');
        Route::get('/ca-results',[StudentProfileController::class,'MyCAResults'])->name('student_ca_results');
        Route::get('/exam-registration',[StudentProfileController::class,'ExamRegistration'])->name('student-exam_registration');
        Route::get('/profile/{user_id}',[StudentProfileController::class,'profile'])->name('profile');
    });

    Route::group(['prefix' => 'classAssessments'], function (){
        Route::post('/updateExams/{id}',[ClassAssessmentsController::class,'UpdateTotalResultsExams'])->name('classExamUpdateTotal');
        Route::post('/process',[ClassAssessmentsController::class,'ProcessUploadedResults'])->name('import.process');
        Route::get('/results-programs/{id}',[ClassAssessmentsController::class,'ProgramForResults'])->name('program-names');
        Route::get('/student-list/{class}/{assessid}',[ClassAssessmentsController::class,'StudentListResults'])->name('myClassStudentList');
        Route::get('/class-list/{id}',[ClassAssessmentsController::class,'getClassesToPublish'])->name('myClassList');
        Route::post('/post-results',[ClassAssessmentsController::class,'PostStudentResults'])->name('postedResults.process');
        Route::get('/publish-program-list/{id}',[ClassAssessmentsController::class,'GetProgramsToPublish'])->name('getPublishPrograms');
        Route::get('/program-results/{aid}/{pid}',[ClassAssessmentsController::class,'GetProgramResults'])->name('getPramResults');
        Route::post('/publish-program-results',[ClassAssessmentsController::class,'PublishProgramResults'])->name('publishProgramResults');
        Route::post('/update-results-publish/{id}',[ClassAssessmentsController::class,'UpdateResultsPublish'])->name('resultsPublish');
        Route::post('/get-results-update',[ClassAssessmentsController::class,'getAssessToUpdate'])->name('update.assessments');
        Route::get('/program-results-levels',[ClassAssessmentsController::class,'GetProgramResultsLevel'])->name('getPramResultsLevel');
        Route::post('/board-exam-update',[ClassAssessmentsController::class,'BoardofExaminersUpdateResults'])->name('BoardofExaminersUpdateResults');
//not working
        Route::post('/load-more',[ClassAssessmentsController::class,'LoadMoreResults'])->name('load.more.results.board');

        Route::group(['prefix' => 'reports'], function (){
            Route::get('/{id}',[\App\Http\Controllers\Academics\AssessmentReportsController::class,'index'])->name('reports.index');
        });
    });

        Route::resource('apply', ApplicationsController::class);
        Route::resource('classAssessments',ClassAssessmentsController::class);
        Route::resource('assessments',AssessmentsTypesController::class);
        Route::resource('students', StudentRecordController::class);
        Route::resource('student', RegistrationController::class);
        Route::resource('classes', ClassesController::class);
        Route::resource('users', UsersController::class);
        Route::resource('programs', ProgramsController::class);
        Route::resource('courses', CoursesController::class);
        Route::resource('studymodes', StudyModeController::class);
        Route::resource('periodtypes', PeriodTypeController::class);
        Route::resource('departments', DepartmentsController::class);
        Route::resource('qualifications', QualicationsController::class);
        Route::resource('levels', CourseLevelsController::class);
        Route::resource('intakes', IntakeController::class);
        Route::resource('fees', FeesController::class);


    Route::group(['namespace' => 'SuperAdmin','middleware' => 'super_admin', 'prefix' => 'super_admin'], function(){

        //Route::get('/settings', 'SettingsController@index')->name('settings');
        //Route::put('/settings', 'SettingController@update')->name('settings.update');

    });
    Route::put('/settings', [SettingsController::class,'update'])->name('settings.update');
    Route::get('/settings', [SettingsController::class,'index'])->name('settings');



});
Route::get('/try', [AcademicPeriodsController::class,'testddump']);
