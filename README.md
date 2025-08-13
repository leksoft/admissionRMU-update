<h1>การปรับปรุงระบบรับสมัครนักศึกษา - รองรับการเลือกลำดับสาขา</h1>
<h2>📋 ภาพรวมการเปลี่ยนแปลง</h2>
<p>ระบบได้รับการปรับปรุงเพื่อรองรับการเลือกสาขาวิชาแบบหลายลำดับ (อันดับ 1-3) โดยเฉพาะสำหรับโครงการ Portfolio</p>
<h2>🗄️ 1. การปรับปรุงฐานข้อมูล</h2>
<h3>เพิ่มตาราง <code>register_details</code></h3>
<pre><code class="language-sql">-- สำหรับเก็บใบสมัครหลายรายการ โดยอ้างอิงจากตารางหลัก register
CREATE TABLE register_details (
    reg_id INT,              -- รหัสการสมัครเรียน (FK)
    reg_priority INT,        -- ลำดับที่สาขา (1-3)
    reg_course VARCHAR,      -- รหัสสาขาวิชา
    reg_fee INT,            -- ค่าสมัคร (400-300-200)
    paymentDisplay400 TEXT   -- ข้อมูลสำหรับใบจ่ายเงิน
);
</code></pre>
<h3>ปรับปรุง Stored Procedure <code>getpayment</code></h3>
<pre><code class="language-sql">-- เพิ่มฟิลด์ payment_display สำหรับแสดงในใบจ่ายเงิน
-- รองรับการแสดงค่าสมัคร 200, 300, 400 บาท
</code></pre>
<pre><code>
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

</code></pre>
<h2>🏗️ 2. Models</h2>
<h3>RegisterDetail Model</h3>
<pre><code class="language-php">&#x3C;?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisterDetail extends Model
{
    protected $table = 'register_details';
    // รองรับการจัดการรายละเอียดการสมัครแต่ละลำดับ
}
</code></pre>
<h2>🎮 3.แก้ส่วนของ controller function page_register_form_step4</h2>
<h3>Http/Controllers/PageController.php - ฟังก์ชันหลัก</h3>
<h4><code>page_register_form_step4()</code> - บันทึกการเลือกสาขา</h4>
<ul>
<li><strong>โครงการ Portfolio (reg_project = 1)</strong>: ใช้ 2 ตาราง (<code>register</code> + <code>register_details</code>)</li>
<li><strong>โครงการอื่นๆ</strong>: ใช้ตาราง <code>register</code> ตารางเดียวเหมือนเดิม</li>
<li>รองรับการเลือกสาขา 1-3 อันดับ</li>
<li>คำนวณค่าสมัครอัตโนมัติ (200 + 100 + 100 บาท)</li>
</ul>
<pre>
    <code>
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



    </code>
</pre>
<h4><code>page_register_form_step5()</code> - แสดงผลการสมัคร</h4>
<ul>
<li>ดึงข้อมูลสาขาที่เลือกทั้งหมดตามลำดับอันดับ</li>
<li>แสดงรายละเอียดแต่ละสาขาพร้อมข้อมูลคณะและหลักสูตร</li>
</ul>
<pre>
    <code>
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


    </code>
</pre>
<h4>API Functions - เพิ่มฟังชันสำหรับ ดึงข้อมูล ระดับการศึกษา หลักสูตร สาขาวิชา </h4>
<pre><code class="language-php">// ดึงข้อมูลระดับการศึกษา
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


</code></pre>
<h2>🎨 4. Views - หน้าแสดงผล -> แก้ไขส่วนของ views ในไฟล์ views/page_student/register_form_preview.blade.php
</h2>
<h3><code>register_form_preview.blade.php</code> (บรรทัด 159-259)</h3>
<pre>
    <code>
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

    </code>
</pre>
<h4>#5แก้ส่วนของใน payment ให้สามารถแสดงรายการที่สมัครแต่ละอันดับที่เลือก และราคาค่าสมัคร   400-300-200</h4>
<h5>ใน controller InvoiceController function invoice_application_pdf และ invoice_application_pdf_v3 และ invoice_confirm_pdf </h5>
<h5><b>หลักๆ จะอยู่ใน invoice_confirm_pdf </b></h5>
<pre><code class="language-blade">@forelse ($choices as $choice)
    &#x3C;div class="card shadow-sm mb-4 border-0">
        &#x3C;div class="card-header bg-gradient-teal text-white fw-bold">
            สาขาที่สมัครเรียน อันดับ {{ $choice->rank }}
        &#x3C;/div>
        &#x3C;div class="card-body">
            &#x3C;!-- รายละเอียดสาขา คณะ หลักสูตร -->
        &#x3C;/div>
    &#x3C;/div>
@empty
    &#x3C;div class="alert alert-warning">
        ไม่พบรายการสาขาที่เลือก
    &#x3C;/div>
@endforelse
</code></pre>
<h4>Features</h4>
<ul>
<li>✨ การ์ดแสดงผลแต่ละลำดับแยกชัดเจน</li>
<li>🎨 Gradient สีเขียวสวยงาม</li>
<li>📱 Responsive design</li>
<li>🔍 แสดงข้อมูลครบถ้วน: รหัส, คณะ, ระดับ, หลักสูตร</li>
</ul>
<h2>💰 5. ระบบจ่ายเงิน</h2>
<h3>InvoiceController - ปรับปรุงฟังก์ชัน</h3>
<ul>
<li><code>invoice_application_pdf()</code></li>
<li><code>invoice_application_pdf_v3()</code></li>
<li><code>invoice_confirm_pdf()</code></li>
</ul>
<h4>ไฟล์ Views ใหม่</h4>
<ul>
<li><code>invoice_application_v3_reg_project_1.blade.php</code> - สำหรับแสดงรายการลำดับสาขา</li>
<li>รองรับการแสดงค่าสมัคร 400-300-200 บาท</li>
</ul>
<h2>🛣️ 6. Routes</h2>
<pre><code class="language-php">// หลัก
Route::get('/register', [PageController::class, 'page_register']);
Route::get('/register/{project_alias}', [PageController::class, 'page_register_form']);

// กระบวนการสมัคร
Route::post('/register/verify-identity', [PageController::class, 'page_register_form_check']);
Route::get('/register/{project_alias}/forms/{person_ident}', [PageController::class, 'page_register_form_step1']);
Route::post('/register/save/personal-information', [PageController::class, 'page_register_form_step2']);
Route::get('/register/{project_alias}/forms/{person_ident}/select-course', [PageController::class, 'page_register_form_step3']);
Route::post('/register/save/select-course', [PageController::class, 'page_register_form_step4']);
Route::get('/register/{project_alias}/preview/{person_ident}', [PageController::class, 'page_register_form_step5']);

// API
Route::get('/api/get-levels', [PageController::class, 'getLevels']);
Route::get('/api/get-courses', [PageController::class, 'getCourses']);
Route::get('/api/get-majors', [PageController::class, 'getMajors']);

// ใบจ่ายเงิน
Route::get('/invoice/application', [InvoiceController::class, 'invoice_application']);
Route::post('/invoice/application/pdf', [InvoiceController::class, 'invoice_application_pdf']);
</code></pre>
<h2>🆕 7. ฟีเจอร์เพิ่มเติมที่วางแผน</h2>
<ul>
<li>[ ] <strong>ช่องความสามารถพิเศษ</strong> - เพิ่มฟิลด์กรอกทักษะพิเศษ</li>
<li>[ ] <strong>รองรับโครงการใหม่</strong> - ความยืดหยุ่นในการเพิ่มโครงการ</li>
<li>[ ] <strong>เรียงสาขาตามตัวอักษร</strong> - จัดเรียงรายการสาขาให้เป็นระเบียบ</li>
<li>[ ] <strong>ระบบแสดงรายชื่อผู้สมัคร</strong> - ครอบคลุมข้อมูลทุกลำดับ</li>
</ul>
<h2>🔄 8. กระบวนการทำงาน</h2>
<h3>สำหรับโครงการ Portfolio</h3>
<ol>
<li><strong>เลือกสาขา</strong> → ผู้สมัครเลือกได้ 1-3 อันดับ</li>
<li><strong>คำนวณค่าสมัคร</strong> → อันดับ 1: 200฿, อันดับ 2-3: 100฿</li>
<li><strong>บันทึกข้อมูล</strong> → แยกเก็บใน 2 ตาราง</li>
<li><strong>แสดงผล</strong> → แสดงรายการทุกลำดับที่เลือก</li>
<li><strong>ใบจ่ายเงิน</strong> → แสดงค่าสมัครรวมและรายละเอียด</li>
</ol>
<h3>สำหรับโครงการอื่น</h3>
<ul>
<li>ใช้ระบบเดิม (ตาราง <code>register</code> เพียงตารางเดียว)</li>
<li>เลือกได้เพียง 1 สาขา</li>
</ul>
<hr>
<blockquote>
<p><strong>📝 หมายเหตุ:</strong> การปรับปรุงนี้รักษาความเข้ากันได้กับระบบเดิม และเพิ่มความยืดหยุ่นสำหรับการพัฒนาในอนาคต</p>
</blockquote>
<h4>
    #หน้าแก้ไขใบจ่ายเงิน 
views/report/invoice_application_v3.blade.php
</h4>


