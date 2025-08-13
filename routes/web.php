<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PageController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NewStudentController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'DONE 1'; //Return anything
});

Route::get('/', function () {

    $view_page = DB::table('view_page')
        ->where('vp_name', 'index')
        ->first();

    $view_page_count = $view_page->vp_view + 1;

    DB::table('view_page')->where('vp_name', 'index')
        ->update([
            'vp_view' => $view_page_count,
            'vp_last' => date('Y-m-d H:i:s'),
        ]);


    // $response = Http::get('https://www.rmu.ac.th');
    // $html = $response->body();

    // $dom = new \DOMDocument();
    // libxml_use_internal_errors(true);
    // $dom->loadHTML($html);
    // libxml_clear_errors();

    // $images = [];
    // foreach ($dom->getElementsByTagName('img') as $img) {
    //     $src = $img->getAttribute('src');
    //     if (strpos($src, '/uploads/sliders/') !== false) {
    //         $images[] = $src;
    //     }
    // }

    $images = DB::table('im_slider')
        ->where('status', '2')
        ->limit(15)
        ->orderBy('id_im_slider', 'desc')
        ->get();

    $data = [
        'page_name' => 'index',
        'images' => $images,
    ];

    return view('page_student.index')->with($data);
})->name('student_index');

Route::get('/register', [PageController::class, 'page_register']);

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'DONE 2'; //Return anything
});

Route::get('/privacy', function () {
    $data = [
        'page_name' => 'page',
    ];
    return view('page.privacy')->with($data);
});

Route::get('news/{id}', [PostController::class, 'preview']);
Route::get('news', [PostController::class, 'list_index']);
// หน้าเกี่ยวกับมหาลัย
Route::get('/about/{name}', [PageController::class, 'about_university']);
Route::get('/howto/{name}', [PageController::class, 'howto']);
Route::get('/course/{id}', [PageController::class, 'about_course']);

// หน้าข้อมูลแผนการรับนักศึกษา
Route::get('/plan-student/{condition_page}/{project_name}', [PlanController::class, 'page_plan_index']);
Route::get('/plan-student/{condition_page}/{project_name}/{faculty_name}', [PlanController::class, 'page_plan_statistic']);


// หน้าเลือกโครงการที่จะสมัคร
Route::get('/register', [PageController::class, 'page_register']);
// หน้ากรอก รหัส ปจต ปชช (บันทึก Accout ใหม่ เช็ึค Account เดิม)
Route::get('/register/{project_alias}', [PageController::class, 'page_register_form']);
// หน้า คลิกปุ่ม กรอกใบสมัคร
Route::POST('/register/verify-identity', [PageController::class, 'page_register_form_check'])->name('register.form.check.user');
// หน้า กรอกฟอร์ม ข้อมูลส่วนตัว
Route::get('/register/{project_alias}/forms/{person_ident}', [PageController::class, 'page_register_form_step1'])->name('register.form.step1');
// Update ข้อมูลส่วนตัว
Route::post('/register/save/personal-information', [PageController::class, 'page_register_form_step2'])->name('register.form.step2');
// หน้า เลือก สาขาวิชา
Route::get('/register/{project_alias}/forms/{person_ident}/select-course', [PageController::class, 'page_register_form_step3']);
// Update การเลือกสาขาวิชา
Route::post('/register/save/select-course', [PageController::class, 'page_register_form_step4'])->name('register.form.step4');
// แสดงผลการกรอกข้อมูล
Route::get('/register/{project_alias}/preview/{person_ident}', [PageController::class, 'page_register_form_step5']);
Route::post('/register/submit=success/preview', [PageController::class, 'page_register_form_preview'])->name('register.form.preview');
// ยกเลิกการสมัคร
Route::post('/register/forms/cancel', [PageController::class, 'page_register_form_cancel'])->name('register.form.cancel');


// หน้าพิมพ์ค่าสมัคร
Route::get('/invoice/application', [InvoiceController::class, 'invoice_application']);
Route::post('/invoice/application/pdf', [InvoiceController::class, 'invoice_application_pdf'])->name('register.invoice.application.pdf');
Route::get('/invoice/application/pdf_v2/{reg_id}/{reg_year}/{reg_project}/{reg_student}', [InvoiceController::class, 'invoice_application_pdf']);
Route::get('/invoice/application/pdf_v3/{reg_id}/{reg_year}/{reg_project}/{reg_student}', [InvoiceController::class, 'invoice_application_pdf_v3']);


// หน้าพิพม์ค่ายืนยันสิทธิ์
Route::get('/invoice/confirm', [InvoiceController::class, 'invoice_confirm']);
Route::post('/invoice/confirm/pdf', [InvoiceController::class, 'invoice_confirm_pdf'])->name('register.invoice.confirm.pdf');
Route::get('/invoice/confirm/pdf_v2/{reg_id}/{reg_year}/{reg_project}/{reg_student}', [InvoiceController::class, 'invoice_confirm_pdf']);

Route::get('/invoice/application-confirm/pdf/{reg_id}/{reg_year}/{reg_project}/{reg_student}', [InvoiceController::class, 'invoice_all_pdf'])->name('register.invoice.all');

// ตรวจสอบสถานะการสมัครสอบ
Route::get('/status/student-admissions', [StatusController::class, 'status_page']);
//Route::POST('/status/student-admissions/results', [StatusController::class, 'page_register_form_check'])->name('status.form.check');

// แจ้งชำระค่าสมัคร
Route::get('/payment/application', [PaymentController::class, 'payment_application_page']);
Route::get('/payment/application/{project}', [PaymentController::class, 'payment_application_page']);
// แจ้งชำระค่ายืนยันสิทธิ์
Route::get('/payment/confirm', [PaymentController::class, 'payment_confirm_page']);


Route::post('/identification-card/results', [StatusController::class, 'identification_pdf'])->name('register.ident.card.pdf');
Route::get('/card', [StatusController::class, 'card_page']);

Route::get('/create-post', [StatusController::class, 'create']);
Route::post('/submit-post', [StatusController::class, 'store']);
Route::get('/refresh-captcha', [StatusController::class, 'refreshCaptcha']);

// ระบบรายงานตัว นศ ใหม่
Route::get('/newstudent', [NewStudentController::class, 'index']);

// หน้าอัปโหลด Port
Route::get('/myPortfolio', [PortfolioController::class, 'index']);
Route::post('/myPortfolio/check', [PortfolioController::class, 'checkAuth']);
Route::get('/myPortfolio/{reg_code}/{person_ident}', [PortfolioController::class, 'listRegister']);

Route::get('/viewPortfolio/{person_ident}/{reg_code}', [PortfolioController::class, 'viewPortfolio']);

Route::post('/myPortfolio/upload', [PortfolioController::class, 'upload']);

Route::post('/myPortfolio/PortfolioUpdateType', [PortfolioController::class, 'PortfolioUpdateType']);

// ตรวจสอบสถานที่สอบ
Route::get('/examination', [StatusController::class, 'examinationPage']);

Route::post('/examination/check', [StatusController::class, 'examinationList']);
Route::post('/examination/check/view', [StatusController::class, 'examinationView']);
//แจ้งปัญหา
Route::get('/problem/payment', [PaymentController::class, 'problemPayment']);
Route::post('/problem/payment/upload', [PaymentController::class, 'problem_upload'])->name('problem.upload');

Route::get("/contact-course", function () {
    return view("page_student.contactCourse", ['page_name' => 'contactCourse']);
});



// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
Route::get('s_enroll/{id}',  [App\Http\Controllers\ViewFileController::class, 'securePdf']);

Route::group(['middleware' => 'auth'], function () {

    Route::get('/index', function () {
        return view('page_student.index_logged', [
            'page_name' => 'index_logged'
        ]);
    });

    Route::get('/status/student-admissions/{reg_id}/{reg_code}', [StatusController::class, 'status_page_view']);
    Route::post('/status/student-admissions/results/cancel', [StatusController::class, 'register_cancel'])->name('register.cancel');

    // แนบหลักฐาน
    Route::post('/payment/application/upload', [PaymentController::class, 'payment_upload'])->name('payment.upload');
    Route::post('/payment/confirm/upload', [PaymentController::class, 'payment_upload2'])->name('payment.upload2');

    // บันทึกคำร้องขึ้นทะเบียนนักศึกษาใหม่
    Route::post('/newstudent/add', [NewStudentController::class, 'insert_newstudent'])->name('newstudent.add');

    Route::get('/newstudent/forms1/add', [NewStudentController::class, 'page_forms1']);
    Route::post('/newstudent/forms1/save', [NewStudentController::class, 'save_forms1']);

    Route::get('/newstudent/forms2/add', [NewStudentController::class, 'page_forms2']);
    Route::post('/newstudent/forms2/save', [NewStudentController::class, 'save_forms2']);

    Route::get('/newstudent/forms3/add', [NewStudentController::class, 'page_forms3']);
    Route::post('/newstudent/forms3/save', [NewStudentController::class, 'save_forms3']);

    Route::get('/newstudent/forms4/add', [NewStudentController::class, 'page_forms4']);
    Route::post('/newstudent/forms4/save', [NewStudentController::class, 'save_forms4']);

    Route::get('/newstudent/forms5/add', [NewStudentController::class, 'page_forms5']);
    Route::post('/newstudent/forms5/save', [NewStudentController::class, 'save_forms5']);
    Route::post('/newstudent/forms5/confirm', [NewStudentController::class, 'confirm_forms5']);

    Route::post('/newstudent/forms5Comment/save', [NewStudentController::class, 'comment_forms5']);

    Route::post('/newstudent/img/uploadfile', [NewStudentController::class, "imageCropPost"])->name("student.imageCrop");

    Route::post('/newstudent/forms/save', [NewStudentController::class, 'confirm_success']);

    Route::post('/newstudent/forms/report_1', [NewStudentController::class, 'reportPDF_1']);
    Route::post('/newstudent/forms/report_2', [NewStudentController::class, 'reportPDF_2']);
    Route::post('/newstudent/forms/report_3', [NewStudentController::class, 'reportPDF_3']);
    Route::post('/newstudent/forms/report_4', [NewStudentController::class, 'reportPDF_4']);

    /*  Route::get('/invoice/application/pdf_v2', [InvoiceController::class, 'invoice_application_pdf']); */

    Route::post('/status/register-reStep', [StatusController::class, 'reStepSave'])->name('register.form.reStep');


    Route::get('/newstudent/formSubmit', [NewStudentController::class, 'page_formsSubmit']);
});


//Route::get('/rmu_admission/file_student/{id}', [App\Http\Controllers\ViewFileController::class, 'securePdf']);
//Route::get('/rmu_admission/file_student/{filename}', [App\Http\Controllers\ViewFileController::class, 'index']);
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
/*
Route::get('/tambon', function () {
    $provinces = Tambon::select('province')->distinct()->get();
    $amphoes = Tambon::select('amphoe')->distinct()->get();
    $tambons = Tambon::select('tambon')->distinct()->get();
    return view("tambon/index", compact('provinces', 'amphoes', 'tambons'));
});
*/
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
Route::get('/information', function () {
    return view(
        'login_admin'
    );
});
Route::get('/information/login', [App\Http\Controllers\Auth\LoginAdministratorController::class, 'showLoginForm'])->name('information.login');
Route::post('/information/login', [App\Http\Controllers\Auth\LoginAdministratorController::class, 'login'])->name('information.login.post');
Route::post('/information/logout', [App\Http\Controllers\Auth\LoginAdministratorController::class, 'logout'])->name('information.logout');

Route::group(['middleware' => 'administrators'], function () {

    Route::get('/information/home', [App\Http\Controllers\Administrator\HomeController::class, 'home'])->name('information.home');
    Route::get('/information/student/{person_code}', [App\Http\Controllers\Administrator\StudentController::class, 'data_student']);
});
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
Route::group(['middleware' => 'isPersonCourse'], function () {

    Route::get('/auth4/home', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'home'])->name('auth4.home');

    Route::get('/auth4/register/{page_condition}', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'listRegister']);

    Route::get('/auth4/course/exam/{page_condition}', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'examResultPage'])->name('auth4.exam.result');

    Route::post('/auth4/course/exam/result/update/all', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'UpdateResultStatus'])->name('course.UpdateResultStatus');
    Route::post('/auth4/course/exam/result/update/order', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'UpdateExamNo'])->name('course.UpdateExamNo');
    Route::post('/auth4/course/exam/result/pdf', [App\Http\Controllers\Administrator\ReportController::class, 'print_exam_result_course'])->name('print.exam.result.course');
    Route::post('/auth4/course/exam/resultAll/pdf', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'print_exam_result_course_all'])->name('print.exam.result.course.all');

    Route::post('/auth4/course/exam/result/update/allV2', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'UpdateResultStatusV2'])->name('course.UpdateResultStatusV2');


    Route::get('/auth4/course/exam-2/{page_condition}', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'exam_result2'])->name('auth4.exam_result2');
    Route::post('/auth4/course/exam-2/result/update/allV2', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'UpdateResultStatusV2'])->name('course.UpdateResultStatusV2');

    Route::get('/auth4/room', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'index']);
    Route::get('/auth4/room/{project}', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'addRoom']);
    Route::post('/auth4/room/examination/save', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'examinationSave'])->name('auth4.examinationSave');
    Route::get('/auth4/room/notExamination/{year}/{project}/{major}', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'examinationSaveNotExam']);

    Route::post('/auth4/room/examinationSit/save', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'examinationSitSave'])->name('auth4.examinationSitSave');
    Route::post('/auth4/room/examinationSit/Delete', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'examinationSitDelete'])->name('auth4.examinationSitDelete');

    Route::post('/auth4/room/printFormSignaturePDF', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'printFormSignaturePDF'])->name('auth4.printFormSignaturePDF');

    Route::get('/auth4/view/{examination_sit_date}/room', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'viewUseRoom']);
    Route::post('/auth4/view/listRoom', [App\Http\Controllers\Administrator\Auth4\RoomController::class, 'viewUseRoomList'])->name('auth4.viewUseRoomList');

    Route::get('/auth4/student/{id}', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'view_student']);

    Route::get('/auth4/newstudent/{year}', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'newstudent_all']);

    Route::get('/auth4/newstudent-view/{id}', [App\Http\Controllers\Administrator\Auth4\HomeController::class, 'newstudent_view']);
});
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
Route::group(['middleware' => 'isPersonExecutive'], function () {
    Route::get('/auth3/home', [App\Http\Controllers\Administrator\Auth3\HomeController::class, 'home'])->name('auth3.home');
    Route::get('/auth3/register/{page_condition}', [App\Http\Controllers\Administrator\Auth3\HomeController::class, 'listRegister']);
});
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
Route::group(['middleware' => 'isAdmin'], function () {});
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// :
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
Route::group(['middleware' => 'isSuperAdmin'], function () {

    Route::get('/admin/home', [App\Http\Controllers\Administrator\HomeController::class, 'home'])->name('admin.home');
    // Route::get('/admin/home', [App\Http\Controllers\Administrator\HomeController::class, 'home'])->name('information.home');
    // สถานะการสมัครเรียน
    Route::get('/admin/register/result/{page_condition}', [App\Http\Controllers\Administrator\HomeController::class, 'register_result']);
    Route::get('/admin/register/db/{page_condition}', [App\Http\Controllers\Administrator\HomeController::class, 'register_result_db']);

    Route::get('/admin/db/view/register', [App\Http\Controllers\Administrator\HomeController::class, 'db_register_page']);
    Route::post('/admin/db/view/register', [App\Http\Controllers\Administrator\HomeController::class, 'db_register_view']);

    // หน้าเลือกโครงการ
    Route::get('/admin/schedule/page', [App\Http\Controllers\Administrator\HomeController::class, 'schedule_page']);
    // ข้อมูลโครงการ
    Route::get('/admin/schedule/view', [App\Http\Controllers\Administrator\HomeController::class, 'schedule_view']);
    // แบบฟอร์ม กำหนดการโครงการและประกาศการรับสมัคร
    Route::post('/admin/schedule/forms', [App\Http\Controllers\Administrator\HomeController::class, 'schedule_forms'])->name('admin.schedule.forms');
    // อัปเดทแบบฟอร์ม
    Route::post('/admin/schedule/update', [App\Http\Controllers\Administrator\HomeController::class, 'schedule_update'])->name('admin.schedule.update');

    Route::post('/admin/schedule/copy', [App\Http\Controllers\Administrator\HomeController::class, 'schedule_copy'])->name('admin.schedule.copy');

    Route::post('/academic/img/uploadfile', [App\Http\Controllers\Administrator\HomeController::class, 'schedule_forms_signal'])->name("personnel.imageCrop");
    // อัปโหลดไฟล์ประกาศ
    Route::post('/admin/uploadfile/announce', [App\Http\Controllers\Administrator\UploadController::class, 'upload_file_announce'])->name("admin.uploadfile.announce");
    // ลบไฟล์ประกาศ
    Route::get('/admin/delete-file/announce/{id}/{file}/{year}/{project}', [App\Http\Controllers\Administrator\UploadController::class, 'fileDelete_announce'])->name('fileDelete');
    Route::post('/admin/schedule/update/payment-amount/confirm', [App\Http\Controllers\Administrator\HomeController::class, 'update_payment_amount'])->name("admin.schedule.update.confirm_amount");
    // Show Hide
    Route::get('/admin/schedule/plan-display/{year}/{project}/{status}', [App\Http\Controllers\Administrator\HomeController::class, 'update_plan_status']);


    Route::post('/admin/report/plan/major/pdf', [App\Http\Controllers\Administrator\ReportController::class, 'report_plan_major'])->name('report.plan.major.pdf');

    // PDF
    // รับสมัคร
    Route::post('/admin/report/announce/applications/pdf', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_applications'])->name('report.announce.applications.pdf');
    Route::post('/admin/report/announce/applications/word', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_applications_word'])->name('report.announce.applications.word');

    // ผู้มีสิทธิ์สอบ
    Route::post('/admin/report/announce/examiner/pdf', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_examiner'])->name('report.announce.examiner.pdf');
    Route::post('/admin/report/announce/examiner/attachmentPDF', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_examiner_attachment'])->name('report.announce.examiner.attachment.pdf');
    Route::post('/admin/report/announce/examiner/word', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_examiner_word'])->name('report.announce.examiner.word');

    // ผ่านการคัดเลือก
    Route::post('/admin/report/announce/pass/pdf', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_pass'])->name('report.announce.pass.pdf');
    Route::post('/admin/report/announce/pass/word', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_pass_word'])->name('report.announce.pass.word');
    Route::get('/admin/preview/exam-pass', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_pass_html']);

    Route::post('/admin/report/announce/pass-second/pdf', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_second_pass_pdf'])->name('report.announce.pass_second.pdf');
    Route::post('/admin/report/announce/pass-second/word', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_second_pass_word'])->name('report.announce.pass_second.word');

    Route::post('/admin/report/announce/study-confirm/word', [App\Http\Controllers\Administrator\ReportController::class, 'report_studyConfirmPay'])->name('report.announce.studyConfirmPay.word');

    /*
    Route::post('/admin/report/announce/report3/pdf', [App\Http\Controllers\Administrator\ReportController::class, 'report3_pdf'])->name('report.announce.report3.pdf');
    Route::post('/admin/report/announce/report3/word', [App\Http\Controllers\Administrator\ReportController::class, 'report3_word'])->name('report.announce.report3.word');
    Route::post('/admin/report/announce/report3/excel', [App\Http\Controllers\Administrator\ReportController::class, 'report3_excel'])->name('report.announce.report3.excel'); */



    // ผ่านการคัดเลือก
    Route::post('/admin/report/announce/confirm/pdf', [App\Http\Controllers\Administrator\ReportController::class, 'report_announce_confirm'])->name('report.announce.confirm.pdf');

    // จัดทำแผนการรับนักศึกษา ประจำปี
    Route::get('/admin/plan/index', [App\Http\Controllers\Administrator\PlanController::class, 'page_index']);
    Route::post('/admin/plan/view', [App\Http\Controllers\Administrator\PlanController::class, 'insert'])->name('admin.plan.insert');
    Route::post('/admin/plan/update', [App\Http\Controllers\Administrator\PlanController::class, 'update'])->name('admin.plan.update');
    Route::post('/admin/plan/project/num/view', [App\Http\Controllers\Administrator\PlanController::class, 'edit_project'])->name('admin.plan.edit.project');
    Route::post('/admin/plan/project/num/update', [App\Http\Controllers\Administrator\PlanController::class, 'update_project'])->name('admin.plan.update.project');


    // ตรวจสอบการชำระเงิน ค่าสมัคร
    Route::get('/admin/payment/applicant/{page_condition}', [App\Http\Controllers\Administrator\PaymentController::class, 'page_applicant']);
    Route::post('/admin/payment/applicant/update/status', [App\Http\Controllers\Administrator\PaymentController::class, 'page_applicant_update_status'])->name('admin.update.reg_status_pay');
    Route::POST('/admin/payment/text/allUpdate', [App\Http\Controllers\Administrator\PaymentController::class, 'page_applicant_update_status_mutiple']);
    Route::get('/admin/payment-status/applicant/', [App\Http\Controllers\Administrator\PaymentController::class, 'tools_page_applicant']);

    Route::get('/admin/payment/applicant/update/status/{reg_status_confirm}/{reg_id}', [App\Http\Controllers\Administrator\PaymentController::class, 'page_applicant_update_status_one']);
    Route::get('/admin/payment/confirm/update/status/{reg_status_pay}/{reg_id}', [App\Http\Controllers\Administrator\PaymentController::class, 'page_confirm_update_status_one']);

    // ผลการชำระค่ายืนยันสิทธิ์
    Route::get('/admin/payment/confirm/{page_condition}', [App\Http\Controllers\Administrator\PaymentController::class, 'index_confirm']);
    Route::post('/admin/payment/confirm/update/status', [App\Http\Controllers\Administrator\PaymentController::class, 'update_status_confirm'])->name('admin.update.reg_status_confirm');
    Route::POST('/admin/payment/text-confirm/allUpdate', [App\Http\Controllers\Administrator\PaymentController::class, 'page_confirm_update_status_mutiple']);
    Route::get('/admin/payment-status/confirm/', [App\Http\Controllers\Administrator\PaymentController::class, 'tools_page_confirm']);

    Route::get('/admin/payment-status/all/', [App\Http\Controllers\Administrator\PaymentController::class, 'tools_page_all']);
    Route::POST('/admin/payment/text-all/allUpdate', [App\Http\Controllers\Administrator\PaymentController::class, 'page_all_update_status_mutiple']);
    // ผลการสอบ
    Route::get('/admin/exam/result/{page_condition}', [App\Http\Controllers\Administrator\ExamResultController::class, 'page_index']);
    Route::post('/admin/exam/result/update/all', [App\Http\Controllers\Administrator\ExamResultController::class, 'UpdateResultStatus'])->name('admin.UpdateResultStatus');



    // Update ปีการศึกษาของระบบ
    Route::post('/information/admin/management-year/save', [App\Http\Controllers\Administrator\HomeController::class, 'management_year_update'])->name('admin.year.update');
    // ข้อมูลการรายงานตัว
    Route::get('/admin/newstudent/{page_condition}', [App\Http\Controllers\Administrator\StudentController::class, 'newstudent_index']);
    Route::get('/admin/newstudent/{year}/{student_person}/{student_id}', [App\Http\Controllers\Administrator\StudentController::class, 'newstudent_data']);

    Route::get('/admin/newstudent/cancelSubmit/{student_id}', [App\Http\Controllers\Administrator\StudentController::class, 'newstudent_cancelSubmit']);
    Route::get('/admin/newstudent/cancelStudent/{student_id}', [App\Http\Controllers\Administrator\StudentController::class, 'newstudent_cancelStudent']);

    Route::get('/admin/newstudent/cancelFileStudent/{student_id}', [App\Http\Controllers\Administrator\StudentController::class, 'newstudent_cancelFileStudent']);

    // ตรวจสอบวุฒิ
    Route::get('/admin/educational/{page_condition}', [App\Http\Controllers\Administrator\StudentController::class, 'educational_index']);
    Route::post('/admin/educational/print', [App\Http\Controllers\Administrator\StudentController::class, 'educational_export']);
    Route::post('/admin/educational/print_att', [App\Http\Controllers\Administrator\StudentController::class, 'educational_export_att']);


    Route::get('/admin/newstudent/forms/report_1/{year}/{person_code}', [App\Http\Controllers\Administrator\StudentController::class, 'reportPDF_1']);
    Route::get('/admin/newstudent/forms/report_2/{year}/{person_code}', [App\Http\Controllers\Administrator\StudentController::class, 'reportPDF_2']);

    Route::get('/admin/forms/generate-student-id', [App\Http\Controllers\Administrator\StudentController::class, 'generate_student_id']);
    Route::POST('/admin/forms/generate-student-id/update', [App\Http\Controllers\Administrator\StudentController::class, 'generate_student_id_save'])->name('admin.generate.student');



    // ข้อมูลผู้สมัคร
    Route::get('/admin/search/applicant', [App\Http\Controllers\Administrator\DataStudentController::class, 'studentSearch']);
    Route::get('/admin/search/applicant/view', [App\Http\Controllers\Administrator\DataStudentController::class, 'studentSearchView'])->name('admin.studentSearchView');

    Route::get('/admin/tools/snackbar', [App\Http\Controllers\Administrator\UiController::class, 'home']);
    Route::post('/admin/tools/snackbar/save', [App\Http\Controllers\Administrator\UiController::class, 'snackbarSave'])->name('tools.snackbar.save');

    Route::get('/admin/tools/menuStudent', [App\Http\Controllers\Administrator\UiController::class, 'menuStudent']);
    Route::get('/admin/tools/menuStudent/{ui_name}/{ui_status}', [App\Http\Controllers\Administrator\UiController::class, 'menuStudentUpdate']);

    Route::get('/admin/register/portfolio/{page_condition}', [App\Http\Controllers\Administrator\HomeController::class, 'register_type_portfolio']);

    // การแจ้งปัญหาการชำระเงิน
    Route::get('/admin/problem/payment', [App\Http\Controllers\Administrator\HomeController::class, 'problemPayment']);
    Route::post('/admin/problem/payment/reply', [App\Http\Controllers\Administrator\MailController::class, 'index'])->name('problemPayment.Reply');


    Route::get('/admin/sys/exam-result', [App\Http\Controllers\Administrator\ManageSystemController::class, 'open_exam_result']);
    Route::post('/admin/sys/exam-result/update1', [App\Http\Controllers\Administrator\ManageSystemController::class, 'open_exam_result_update1'])->name('open_exam_result_update1');
    Route::post('/admin/sys/exam-result/update2', [App\Http\Controllers\Administrator\ManageSystemController::class, 'open_exam_result_update2'])->name('open_exam_result_update2');

    Route::get('/admin/sys/exam-result-2nd', [App\Http\Controllers\Administrator\ManageSystemController::class, 'open_exam_result_2']);
    Route::post('/admin/sys/exam-result/update1-2', [App\Http\Controllers\Administrator\ManageSystemController::class, 'open_exam_result_2_update1'])->name('open_exam_result_2_update1');
    Route::post('/admin/sys/exam-result/update2-2', [App\Http\Controllers\Administrator\ManageSystemController::class, 'open_exam_result_2_update2'])->name('open_exam_result_2_update2');


    Route::get('/admin/sys/register-2nd', [App\Http\Controllers\Administrator\ManageSystemController::class, 'open_register_2nd']);
    Route::post('/admin/sys/register-2nd/update1', [App\Http\Controllers\Administrator\ManageSystemController::class, 'open_register_2nd_update1'])->name('open_register_2nd_update1');
    Route::post('/admin/sys/register-2nd/update2', [App\Http\Controllers\Administrator\ManageSystemController::class, 'open_register_2nd_update2'])->name('open_register_2nd_update2');

    Route::post('/admin/sys/addTranscriptUpload', [App\Http\Controllers\Administrator\ManageSystemController::class, 'addTranscriptUpload'])->name('admin.addTranscriptUpload');


    // ปรับสถานะใหม่ให้ นศ กรณี ไม่ผ่านการคัดเลิกและสำรอง

    Route::get('/admin/register/recode', [App\Http\Controllers\Administrator\RegisterRecodeController::class, 'auth1_index']);

    Route::post('/admin/register/recodeSave', [App\Http\Controllers\Administrator\RegisterRecodeController::class, 'reStepSave'])->name('submitRegisterRecode');
    Route::post('/admin/register/recodeSaveExam', [App\Http\Controllers\Administrator\RegisterRecodeController::class, 'reStepSaveExam'])->name('submitRegisterRecodeExam');

    Route::POST('/admin/register/recodeSave/Selected', [App\Http\Controllers\Administrator\RegisterRecodeController::class, 'register_selected']);


    Route::get('posts/list', [PostController::class, 'list']);
    Route::get('posts/preview/{id}', [PostController::class, 'preview']);
    Route::get('posts/create', [PostController::class, 'create']);
    Route::post('posts/store', [PostController::class, 'store'])->name('posts.store');
    Route::post('posts/update', [PostController::class, 'update'])->name('posts.update');
    Route::get('posts/publish/{id}/{status}', [PostController::class, 'updateStatus']);
    Route::get('posts/delete/{id}', [PostController::class, 'delete']);
    Route::get('posts/edit/{id}', [PostController::class, 'edit']);

    //Tynymce Image Uploade
    Route::post('/upload', [\App\Http\Controllers\TynimceController::class, 'upload']);

    Route::get('/admin/recode', [App\Http\Controllers\Administrator\RecodeController::class, 'index']);
    Route::get('/admin/recode/select', [App\Http\Controllers\Administrator\RecodeController::class, 'index']);
    Route::post('/admin/recode/select-save', [App\Http\Controllers\Administrator\RecodeController::class, 'register_new']);
    Route::post('/admin/recode/select-delete', [App\Http\Controllers\Administrator\RecodeController::class, 'destroy']);

    //ระบบการส่งอีเมล
    Route::get('/admin/send-email', [App\Http\Controllers\Administrator\SendEmailController::class, 'send_email_page']);
    Route::get('/admin/send-email/view', [App\Http\Controllers\Administrator\SendEmailController::class, 'send_email_view']);
    Route::any('/admin/send-email/view', [App\Http\Controllers\Administrator\SendEmailController::class, 'send_email_view']);
    Route::post('/admin/send-email/sending', [App\Http\Controllers\Administrator\SendEmailController::class, 'send_email_sending']);


    Route::get('/admin/db/view/register', [App\Http\Controllers\Administrator\HomeController::class, 'db_register_page']);
    Route::post('/admin/db/view/register', [App\Http\Controllers\Administrator\HomeController::class, 'db_register_view']);

    Route::get('/admin/db/newstudent/{year}/{input_faculty?}/{input_major?}', [App\Http\Controllers\Administrator\HomeController::class, 'db_newstudent_page']);
    Route::get('/admin/db/newstudent-view/{year}/{id}', [App\Http\Controllers\Administrator\HomeController::class, 'db_newstudent_view']);
    Route::post('/admin/db/newstudent-report_1', [App\Http\Controllers\Administrator\DataStudentController::class, 'adminViewReportPDF_1']);
    Route::post('/admin/db/newstudent-report_2', [App\Http\Controllers\Administrator\DataStudentController::class, 'adminViewReportPDF_2']);
    Route::post('/admin/db/newstudent-report_3', [App\Http\Controllers\Administrator\DataStudentController::class, 'adminViewReportPDF_3']);
    Route::post('/admin/db/newstudent-report_4', [App\Http\Controllers\Administrator\DataStudentController::class, 'adminViewReportPDF_4']);

    Route::post('/admin/db/newstudent-view/updateStatusFile', [App\Http\Controllers\Administrator\DataStudentController::class, 'updateStatusFile']);

    // สิทธิ์ผู้ใช้งานระบบ
    Route::get('/admin/db/administrator', [App\Http\Controllers\Administrator\HomeController::class, 'db_administrator']);
    Route::get('/admin/db/student', [App\Http\Controllers\Administrator\HomeController::class, 'db_student']);
    Route::get('/admin/db/student/view/{id}', [App\Http\Controllers\Administrator\HomeController::class, 'db_student_view']);

    Route::get('/autocomplete', [App\Http\Controllers\Administrator\HomeController::class, 'autocomplete'])->name('autocomplete');
    // ประวัติการเข้าใช้งานระบบ
    Route::get('/admin/db/history-admin', [App\Http\Controllers\Administrator\ManageSystemController::class, 'admin_login']);
    Route::get('/admin/db/history-student', [App\Http\Controllers\Administrator\ManageSystemController::class, 'student_login']);

    Route::get('/autocompleteName', [App\Http\Controllers\Administrator\HomeController::class, 'autocompleteName'])->name('autocompleteName');

    Route::get('/autocompleteLastname', [App\Http\Controllers\Administrator\HomeController::class, 'autocompleteLastname'])->name('autocompleteLastname');

    Route::get('/autocompletePersoncode', [App\Http\Controllers\Administrator\HomeController::class, 'autocompletePersoncode'])->name('autocompletePersoncode');

    Route::get('/autocompleteRegisterID', [App\Http\Controllers\Administrator\HomeController::class, 'autocompleteRegisterID'])->name('autocompleteRegisterID');

    Route::post('/admin/db/student/updateMajor', [App\Http\Controllers\Administrator\DataStudentController::class, 'updateMajor'])->name('change.student.major');
    Route::post('/admin/db/student/updateProfile', [App\Http\Controllers\Administrator\DataStudentController::class, 'updateProfile'])->name('change.student.profile');


    Route::get('im_slider', [App\Http\Controllers\Administrator\HomeController::class, 'im_slider'])->name('im_slider');
    Route::get('insert_im_slider', [App\Http\Controllers\Administrator\HomeController::class, 'insert_im_slider']);
    Route::post('data_insert_slider', [App\Http\Controllers\Administrator\HomeController::class, 'data_insert_slider'])->name('data_insert_slider');
    Route::put('update_status/{id}', [App\Http\Controllers\Administrator\HomeController::class, 'update_status'])->name('update_status');
    Route::put('delete_slider/{id}', [App\Http\Controllers\Administrator\HomeController::class, 'delete_slider'])->name('delete_slider');
    Route::put('edit_slider/{id}', [App\Http\Controllers\Administrator\HomeController::class, 'edit_slider'])->name('edit_slider');
    Route::post('data_edit_slider', [App\Http\Controllers\Administrator\HomeController::class, 'data_edit_slider'])->name('data_edit_slider');

    Route::get('/admin/set_year', [App\Http\Controllers\Administrator\HomeController::class, 'set_year'])->name('set_year');
    Route::post('/year/update', [App\Http\Controllers\Administrator\HomeController::class, 'update'])->name('year.update');


    Route::get('admin_examination', [App\Http\Controllers\Administrator\HomeController::class, 'admin_examination'])->name('admin_examination');
    Route::get('/examinations/filter', [App\Http\Controllers\Administrator\HomeController::class, 'filter'])->name('examinations.filter');
});