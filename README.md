#ส่วนที่ต้องปรับเพิ่มและแก้ไขให้รองรับ การเลือกลำดับสาขา
1.เพิ่มตาราง register_details สำหรับการเก็บใบสมัครหลาย ๆ รายการ โดยอ้างอิงจากตารางหลักการรับสมัครคือ
ตาราง register
#->ในตารางเพิ่ม ฟิวด์ใหม่อีก 3 ฟิวด์ คือ
#1.reg_priority สำหรับเก็บลำดับที่สาขาที่เลขสมัคร เช่น ลำดับที่ 1 - 2 - 3 มีได้สูงสุด 3 อันดับ
#2.reg_fee สำหรับเก็บค่าสมัคร 400-300-200
#3.paymentDisplay400 สำหรับดึงไปแสดงในใบสำหรับจ่ายเงินค่าสมัคร จะผูกเข้ากับ Procedures getpayment
#4.reg_id รหัสการสมัครเรียน

#getpayment
CREATE DEFINER=`root`@`localhost` PROCEDURE `getpayment`(
IN p_reg_year VARCHAR(4),
IN p_reg_id INT,
IN p_reg_student VARCHAR(20),
IN p_reg_project INT
)
BEGIN
SELECT
p.id AS project_id,
p.name_full AS project_name,
p.name_etc AS project_name_etc,
p.name_round AS project_round,
p.name_eng AS project_eng,
p.name_short AS project_short,
m.major_id AS major_id,
m.major_faculty_id AS major_faculty_id,
m.major_faculty_name AS major_faculty_name,
m.major_level_id AS major_level_id,
m.major_level_name AS major_level_name,
m.major_program_id AS major_program_id,
m.major_program_code AS major_program_code,
m.major_program_name AS major_program_name,
m.major_course AS major_course,
m.major_course_short AS major_course_short,
r.reg_id,
r.reg_code,
r.reg_student,
CONCAT('|099400040150770',CHAR(13),CHAR(10),r.reg_code,CHAR(13),CHAR(10),r.reg_student,CHAR(13),CHAR(10),20000) AS payment_display200,
CONCAT('|099400040150770',CHAR(13),CHAR(10),r.reg_code,CHAR(13),CHAR(10),r.reg_student,CHAR(13),CHAR(10),30000) AS payment_display300,
CONCAT('|099400040150770',CHAR(13),CHAR(10),r.reg_code,CHAR(13),CHAR(10),r.reg_student,CHAR(13),CHAR(10),40000) AS payment_display400
FROM register r
LEFT JOIN project p ON p.id = r.reg_project
LEFT JOIN major m ON m.major_program_code = r.reg_course
WHERE r.reg_year = p_reg_year
AND r.reg_id = p_reg_id
AND r.reg_student = p_reg_student
AND r.reg_project = p_reg_project;
END

#2.Models RegisterDetail

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterDetail extends Model
{
    use HasFactory;
        protected $table = 'register_details';
}

#3.แก้ส่วนของ controller function page_register_form_step4 
#ไฟล์ Http/Controllers/PageController.php

    /**
     * บันทึกข้อมูลการเลือกสาขา (อันดับ 1-3)
    **/

    public function page_register_form_step4(Request $request)
    {
        // ตรวจสอบค่า reg_project จาก request
        $reg_project = $request->reg_project;

        // ======================================================================
        //  กรณีที่ 1: โครงการ Portfolio (reg_project = 1) - ใช้ 2 ตาราง
        // ======================================================================
        if ($reg_project == 1) {

            // ===== 1. ตรวจสอบข้อมูลพื้นฐาน =====
            $user_check = DB::table('person')->where('person_code', $request->reg_student)->first();
            $plan_list = DB::table('plan')
                ->leftJoin('project', 'project.id', '=', 'plan.project')
                ->where('plan.year', $request->reg_year)
                ->where('plan.plan_status', 1)
                ->where('project.id', $request->reg_project)
                ->first();

            if (is_null($user_check) || is_null($plan_list)) {
                return redirect()->back()->with('AlertError', 'ไม่พบข้อมูลผู้สมัครหรือแผนการรับสมัคร กรุณาลองใหม่อีกครั้ง');
            }
            if (empty($request->reg_major1)) {
                return redirect()->back()->with('AlertError', 'กรุณาเลือกสาขาวิชาลำดับที่ 1 (บังคับเลือก)');
            }

            DB::beginTransaction();
            try {
                // ===== 2. ลบข้อมูลเก่า (ถ้ามี) เพื่อรองรับการแก้ไข =====
                // ค้นหาใบสมัครเก่าของผู้ใช้ในโครงการนี้
                $old_registration = DB::table('register')
                    ->where('reg_project', $request->reg_project)
                    ->where('reg_year', $request->reg_year)
                    ->where('reg_student', $user_check->person_code)
                    ->first();

                if ($old_registration) {
                    // ถ้ามีใบสมัครเก่า ให้ลบรายละเอียดใน register_details ก่อน (Cascade on delete อาจจัดการให้แล้ว แต่ทำเพื่อความแน่นอน)
                    DB::table('register_details')->where('reg_id', $old_registration->reg_id)->delete();
                    // จากนั้นจึงลบใบสมัครหลักใน register
                    DB::table('register')->where('reg_id', $old_registration->reg_id)->delete();
                }

                // ===== 3. สร้างใบสมัครหลัก (Master Record) ในตาราง `register` =====

                // --- สร้าง reg_code ---
                $last_register = DB::table('register')->orderBy('reg_id', 'DESC')->lockForUpdate()->first();
                $next_reg_code = $last_register ? (int)$last_register->reg_code + 1 : (int)(Str::substr($request->reg_year, 2, 2) . str_pad($request->reg_project, 2, '0', STR_PAD_RIGHT) . '1' . '00001');

                // --- คำนวณค่าสมัครรวมและรวบรวมสาขาที่จะบันทึก ---
                $total_fee = 0;
                $majors_to_insert = [];
                if (!empty($request->reg_major1)) { $total_fee += 200; $majors_to_insert[] = ['priority' => 1, 'course' => $request->reg_major1]; }
                if (!empty($request->reg_major2)) { $total_fee += 100; $majors_to_insert[] = ['priority' => 2, 'course' => $request->reg_major2]; }
                if (!empty($request->reg_major3)) { $total_fee += 100; $majors_to_insert[] = ['priority' => 3, 'course' => $request->reg_major3]; }

                // --- บันทึกข้อมูลใบสมัครหลัก และดึง ID ที่เพิ่งสร้างออกมา ---
                $master_reg_id = DB::table('register')->insertGetId([
                    'reg_student'         => $request->reg_student,
                    'reg_project'         => $request->reg_project,
                    'reg_year'            => $request->reg_year,
                    'reg_fee'             => $total_fee, // เก็บค่าธรรมเนียมรวมไว้ที่ใบสมัครหลัก
                    'reg_status_register' => 1,
                    'reg_status_pay'      => 0,
                    'reg_status_confirm'  => 0,
                    'reg_code'            => $next_reg_code,
                    'reg_datetime'        => Carbon::now(),
                    'reg_date'            => Carbon::now()->toDateString(),
                    // ไม่ต้องใส่ reg_course และ reg_priority ที่นี่
                ]);

                // ===== 4. บันทึกรายละเอียดอันดับ (Details) ในตาราง `register_details` =====
                $details_data = [];
                foreach ($majors_to_insert as $major) {
                    $details_data[] = [
                        'reg_id'       => $master_reg_id, // Foreign Key ชี้กลับไปที่ใบสมัครหลัก
                        'reg_course'   => $major['course'],
                        'reg_priority' => $major['priority'],
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now(),
                    ];
                }

                if (!empty($details_data)) {
                    DB::table('register_details')->insert($details_data);
                }

                DB::commit();

                return redirect('register/' . $plan_list->project_alias . '/preview/' . $user_check->person_ident)
                    ->with('AlertSuccess', 'บันทึกข้อมูลการสมัครเรียนแล้ว จำนวน ' . count($majors_to_insert) . ' สาขาวิชา ค่าสมัครรวม ' . number_format($total_fee) . ' บาท กรุณาตรวจสอบความถูกต้องของข้อมูล');

            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Register Error (Project 1 - Multi-table): ' . $e->getMessage() . ' on line ' . $e->getLine());
                return redirect()->back()->with('AlertError', 'เกิดข้อผิดพลาดร้ายแรงในการบันทึกข้อมูล กรุณาติดต่อผู้ดูแลระบบ');
            }

        } else {
            // ======================================================================
            //  กรณีที่ 2: โครงการอื่นๆ (ยังใช้ตาราง register ตารางเดียวเหมือนเดิม)
            // ======================================================================
            $user_check = DB::table('person')->where('person_code', $request->reg_student)->first();
            $plan_list = DB::table('plan')
                ->leftJoin('project', 'project.id', '=', 'plan.project')
                ->where('plan.year', $request->reg_year)
                ->where('plan.plan_status', 1)
                ->where('project.id', $request->reg_project)
                ->first();

            if (is_null($user_check) || is_null($plan_list)) {
                return redirect()->back()->with('AlertError', 'ไม่พบการจัดทำประวัติ/ข้อมูลผู้สมัคร หรือ แผนการรับนักศึกษา กรุณาลองใหม่อีกครั้ง');
            }

            $check_register = DB::table('register')
                ->where('reg_project', $request->reg_project)
                ->where('reg_year', $request->reg_year)
                ->where('reg_course', $request->reg_major1)
                ->where('reg_student', $user_check->person_code)
                ->orderBy('reg_datetime', 'DESC')
                ->first();

            if (is_null($check_register)) {
                // ยังไม่เคยสมัครในสาขานี้ สามารถสมัครได้
                $last_register = DB::table('register')->orderBy('reg_id', 'DESC')->first();
                $reg_code_final = $last_register ? $last_register->reg_code + 1 : (int)(Str::substr($request->reg_year, 2, 2) . str_pad($request->reg_project, 2, '0', STR_PAD_RIGHT) . '1' . '00001');

                $data = [
                    'reg_student' => $request->reg_student,
                    'reg_project' => $request->reg_project,
                    'reg_year' => $request->reg_year,
                    'reg_course' => $request->reg_major1,
                    'reg_priority' => 1, // โครงการอื่นมี priority เป็น 1 เสมอ
                    'reg_status_register' => 1,
                    'reg_status_pay' => 1,
                    'reg_status_confirm' => 1,
                    'reg_code' => $reg_code_final,
                    'reg_datetime' => Carbon::now(),
                    'reg_date' => Carbon::now()->toDateString(),
                ];
                DB::table('register')->insert($data);
                return redirect('register/' . $plan_list->project_alias . '/preview/' . $user_check->person_ident)
                    ->with('AlertSuccess', 'บันทึกข้อมูล และความประสงค์ที่จะเข้าศึกษาต่อในหลักสูตร/สาขาวิชา ที่ผู้สมัครเลือกแล้ว กรุณาตรวจสอบความถูกต้องของข้อมูล');
            } else {
                // เคยสมัครในสาขานี้แล้ว
                if ($request->reg_project == 9) {
                    // เงื่อนไขพิเศษสำหรับโครงการ 9
                    $mytime = Carbon::now();
                    $start_date = $check_register->reg_datetime;
                    $end_date = Carbon::parse($start_date)->addDay();

                    if ($mytime->greaterThan($end_date)) {
                        // สามารถสมัครใหม่ได้
                        $last_register = DB::table('register')->orderBy('reg_id', 'DESC')->first();
                        $reg_code_final = $last_register->reg_code + 1;

                        $data = [
                            'reg_student' => $request->reg_student,
                            'reg_project' => $request->reg_project,
                            'reg_year' => $request->reg_year,
                            'reg_course' => $request->reg_major1,
                            'reg_priority' => 1,
                            'reg_status_register' => 1,
                            'reg_status_pay' => 1,
                            'reg_status_confirm' => 1,
                            'reg_code' => $reg_code_final,
                            'reg_datetime' => Carbon::now(),
                            'reg_date' => Carbon::now()->toDateString(),
                        ];
                        DB::table('register')->insert($data);
                        return redirect('register/' . $plan_list->project_alias . '/preview/' . $user_check->person_ident)
                            ->with('AlertSuccess', 'บันทึกข้อมูล และความประสงค์ที่จะเข้าศึกษาต่อในหลักสูตร/สาขาวิชา ที่ผู้สมัครเลือกแล้ว กรุณาตรวจสอบความถูกต้องของข้อมูล');
                    } else {
                        return redirect()->back()->with('AlertError', 'ไม่สามารถส่งใบสมัครได้ กรุณาเลือกหลักสูตรหรือสาขาวิชาอื่นที่ไม่ซ้ำกับที่ส่งใบสมัครในโครงการนี้ (เด็กดีเพื่อการพัฒนาท้องถิ่น) หรือรอให้ครบ 24 ชั่วโมง');
                    }
                } else {
                    return redirect()->back()->with('AlertError', 'ไม่สามารถส่งใบสมัครได้ กรุณาเลือกหลักสูตรหรือสาขาวิชาอื่นที่ไม่ซ้ำกับที่ส่งใบสมัครในโครงการนี้');
                }
            }
        }
    }


	#page_register_form_step5 สำหรับส่งข้อมูลไปแสดงผลที่ views 
    public function page_register_form_step5($project_alias, $person_ident)
    {

        $year_SQL = DB::table('year')->first();
        // Student Data

        $user_check = DB::table('person')->where('person_ident', $person_ident)->first();

        if (!$user_check) {
            abort(404, 'ไม่พบข้อมูลผู้สมัคร');
        }


        $plan_list = DB::table('plan')
            ->leftJoin('project', 'project.id', '=', 'plan.project')
            ->where('plan.year', $year_SQL->year_name)
            ->where('plan.plan_status', 1)
            ->where('project.project_alias', $project_alias)
            ->first();

        if (is_null($plan_list) or is_null($user_check)) {
            return redirect()->back()->with('AlertError', 'ไม่พบแผนการรับนักศึกษา หรือข้อมูลผู้ทำรายการ กรุณาลองใหม่อีกครั้ง');
        } else {

            $date_now = date('Y-m-d H:i:s');

            if ($date_now < $plan_list->apply_open) {
                return redirect('/register')->with('AlertError', 'ยังไม่ถึงวันและเวลาที่รับสมัคร ' . $plan_list->name_full);
            } elseif ($date_now > $plan_list->apply_close) {
                return redirect('/register')->with('AlertError', 'ปิดรับสมัคร ' . $plan_list->name_full . ' แล้ว');
            } else {

                $register = DB::table('register')
                    ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                    ->where('reg_student', $user_check->person_code)
                    ->where('reg_project', $plan_list->id)
                    ->where('reg_year', $year_SQL->year_name)
                    ->orderBy('reg_datetime', 'DESC')
                    ->first();


                    // ดึงตัวเลือกสาขาที่บันทึกไว้ของผู้สมัครทั้งหมด ตามลำดับอันดับ
                $choices = DB::table('register_details as rd')
                    ->join('major as m', 'm.major_program_code', '=', 'rd.reg_course') // ถ้า reg_course เก็บเป็น id ให้เปลี่ยน join เป็น m.major_program_id
                    ->where('rd.reg_id', $register->reg_id)
                    ->orderBy('rd.reg_priority', 'asc')
                    ->get([
                        'rd.reg_priority as rank',
                        'rd.reg_course as code',
                        'm.major_program_id',
                        'm.major_program_code',
                        'm.major_faculty_name',
                        'm.major_level_name',
                        'm.major_course',
                        'm.major_program_name',
                    ]);


                if (is_null($register)) {
                    return redirect()->back()->with('AlertError', 'ไม่พบข้อมูลผู้สมัคร กรุณาลองใหม่อีกครั้ง');
                } else {

                    $my_age = Carbon::parse($user_check->person_birthday)->diff($register->reg_date)->format('%y ปี %m เดือน %d วัน');

                    return view('page_student.register_form_preview', [
                        'page_name' => 'register',
                        'plan_list' => $plan_list,
                        'users' => $user_check,
                        'register' => $register,
                        'person_ident' =>  $person_ident,
                        'my_age' =>  $my_age,
                        'choices'      => $choices, //สาขาวิชาที่เลือก แต่ละลำดับ

                    ]);
                }
            }

        }
    }

#เพิ่มฟังชันสำหรับ ดึงข้อมูล ระดับการศึกษา หลักสูตร สาขาวิชา 
public function getLevels(Request $request)
{
    try {
        $levels = DB::table('plan_course')
            ->join('major', 'major.major_program_code', '=', 'plan_course.plan_course_major')
            ->where('major.major_faculty_name', $request->faculty)
            ->where('major.major_status', 1)
            ->select('major.major_level_name as major_level')
            ->distinct()
            ->orderBy('major.major_level_name')
            ->get();

        return response()->json($levels);
    } catch (\Exception $e) {
        Log::error('Error getting levels: ' . $e->getMessage());
        return response()->json(['error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลระดับ: ' . $e->getMessage()], 500);
    }
}

public function getCourses(Request $request)
{
    try {
        $courses = DB::table('plan_course')
            ->join('major', 'major.major_program_code', '=', 'plan_course.plan_course_major')
            ->where('major.major_faculty_name', $request->faculty)
            ->where('major.major_level_name', $request->level)
            ->where('major.major_status', 1)
            ->select('major.major_course')
            ->distinct()
            ->orderBy('major.major_course')
            ->get();

        return response()->json($courses);
    } catch (\Exception $e) {
        Log::error('Error getting courses: ' . $e->getMessage());
        return response()->json(['error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลหลักสูตร: ' . $e->getMessage()], 500);
    }
}

public function getMajors(Request $request)
{
    try {
        $majors = DB::table('plan_course')
            ->join('major', 'major.major_program_code', '=', 'plan_course.plan_course_major')
            ->where('major.major_faculty_name', $request->faculty)
            ->where('major.major_level_name', $request->level)
            ->where('major.major_course', $request->course)
            ->where('major.major_status', 1)
            ->select('major.major_program_code as major_id', 'major.major_program_name as major_name')
            ->distinct()
            ->orderBy('major.major_program_name')
            ->get();

        return response()->json($majors);
    } catch (\Exception $e) {
        Log::error('Error getting majors: ' . $e->getMessage());
        return response()->json(['error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลสาขาวิชา: ' . $e->getMessage()], 500);
    }
}



#4.แก้ไขส่วนของ views ในไฟล์  views/page_student/register_form_preview.blade.php

#บรรทัดที่ 159-259

                <div class="container my-4">
                    <!-- ส่วนหัว -->
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div>
                            <h4 class="mb-1 text-primary fw-bold">มหาวิทยาลัยราชภัฏมหาสารคาม</h4>
                            <small class="text-muted">
                                80 ถ.นครสวรรค์ ต.ตลาด อ.เมืองมหาสารคาม จ.มหาสารคาม (44000)
                            </small>
                        </div>
                        <img src="{{ asset('rmu_admission/img/favicon_rmu-02.png') }}" alt="RMU Logo" class="img-fluid"
                            style="max-width:90px;">
                    </div>

                    {{-- แสดงสาขาที่สมัคร --}}
                    @forelse ($choices as $choice)
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-gradient-teal text-white fw-bold rounded-top">
                                สาขาที่สมัครเรียน อันดับ {{ $choice->rank }}
                            </div>
                            <div class="card-body bg-white border-start border-4 border-primary">
                                <p class="mb-2">
                                    <i class="bi bi-plus-circle-dotted text-success me-2"></i>
                                    <strong>รหัส:</strong> {{ $choice->major_program_id }} -
                                    {{ $choice->major_program_code }}
                                </p>
                                <p class="mb-2">
                                    <i class="bi bi-caret-right-fill text-info me-2"></i>
                                    {{ $choice->major_faculty_name }}
                                </p>
                                <p class="mb-2">
                                    <i class="bi bi-caret-right-fill text-warning me-2"></i>
                                    ระดับ {{ $choice->major_level_name }}
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-caret-right-fill text-danger me-2"></i>
                                    หลักสูตร{{ $choice->major_course }} สาขาวิชา {{ $choice->major_program_name }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            ไม่พบรายการสาขาที่ผู้สมัครเลือกไว้
                        </div>
                    @endforelse
                </div>

                {{-- CSS เพิ่มสีพิเศษ --}}
                <style>
                    .bg-gradient-teal {
                        background: linear-gradient(135deg, #00695c, #009688);
                    }

                    .card-body {
                        transition: background-color 0.3s ease;
                    }

                    .card-body:hover {
                        background-color: #f8f9fa;
                    }
                </style>

#5แก้ส่วนของใน payment ให้สามารถแสดงรายการที่สมัครแต่ละอันดับที่เลือก และราคาค่าสมัคร   400-300-200
ใน controller InvoiceController function invoice_application_pdf และ invoice_application_pdf_v3 และ invoice_confirm_pdf 

หลักๆ จะอยู่ใน invoice_confirm_pdf 

#6แก้ส่วนของการแสดงรายชื่อ ผู้สมัคร ให้ครอบครุม 

#-------เพิ่มเติม----------------#
1.เพิ่มช่องให้กรอกความสามารถพิเศษ 
2.การรองรับการเพิ่มโครงการใหม่ 
3.เรียงสาขาที่เปิดรับตามตัวอักษร

#ส่วนของ route ที่เกี่ยวข้อง 
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
Route::get('/api/get-levels', [PageController::class, 'getLevels'])->name('api.get.levels');
Route::get('/api/get-courses', [PageController::class, 'getCourses'])->name('api.get.courses');
Route::get('/api/get-majors', [PageController::class, 'getMajors'])->name('api.get.majors');

// หน้าพิมพ์ค่าสมัคร
Route::get('/invoice/application', [InvoiceController::class, 'invoice_application']);
Route::post('/invoice/application/pdf', [InvoiceController::class, 'invoice_application_pdf'])->name('register.invoice.application.pdf');
Route::get('/invoice/application/pdf_v2/{reg_id}/{reg_year}/{reg_project}/{reg_student}', [InvoiceController::class, 'invoice_application_pdf']);
Route::get('/invoice/application/pdf_v3/{reg_id}/{reg_year}/{reg_project}/{reg_student}', [InvoiceController::class, 'invoice_application_pdf_v3']);

#หน้าแก้ไขใบจ่ายเงิน 
views/report/invoice_application_v3.blade.php

#เพิ่มไฟล์ในหน้า views ใหม่ ชื่อ invoice_application_v3_reg_project_1.blade.php สำหรับการแสดงผลรายการลำดับสาขาที่เลือก
