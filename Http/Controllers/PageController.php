<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\SchoolModel;
use App\Models\Tambon;
use App\Models\Course;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Svg\Tag\Rect;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isNull;

class PageController extends Controller
{
    public function about_university($name)
    {
        if ($name == 'map') {
            return view('page.map', ['page_name' => 'about']);
        } elseif ($name == 'history') {
            return view('page.history', ['page_name' => 'about']);
        } elseif ($name == 'mission') {
            return view('page.mission', ['page_name' => 'about']);
        } elseif ($name == 'logo') {
            return view('page.logo', ['page_name' => 'about']);
        }
    }

    public function howto($name)
    {
        return view('page.howto', ['page_name' => 'howto', 'title_name' => $name]);
    }
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function about_course($id)
    {
        $major = DB::table('major')
            ->where('major_id', $id)
            ->orderBy('major_faculty_id', 'ASC')
            ->first();

        return view('page.course', ['page_name' => 'course', 'major' => $major]);
    }

    public function about_course_test()
    {
        return view('page.course_test', ['page_name' => 'course']);
    }
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function page_register()
    {

        $year_SQL = DB::table('year')->first();

        $plan_list = DB::table('plan')
            ->leftJoin('project', 'project.id', '=', 'plan.project')
            ->where('year', $year_SQL->year_name)
            ->where('plan_status', 1)
            ->get();

        return view('page_student.register', [
            'page_name' => 'register',
            'plan_list' => $plan_list,
            'year_SQL' => $year_SQL->year_name,
        ]);
    }
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function page_register_form($project_alias)
    {
        Auth::logout();

        $year_SQL = DB::table('year')->first();

        $plan_list = DB::table('plan')
            ->leftJoin('project', 'project.id', '=', 'plan.project')
            ->where('year', $year_SQL->year_name)
            ->where('plan_status', 1)
            ->where('project.project_alias', 'LIKE', "%$project_alias%")
            ->first();

        if (is_null($plan_list)) {
            return redirect()->back()->with('AddError', 'ไม่พบรายการ การรับสมัครนักศึกษาเข้าศึกษาต่อ กรุณาติดตามประกาศหรือปฏิทินการรับสมัครนักศึกษา');
        } else {

            $date_now = date('Y-m-d H:i:s');

            if ($date_now < $plan_list->apply_open) {
                return redirect()->back()->with('AddError', 'ยังไม่ถึงวันและเวลาที่รับสมัคร ' . $plan_list->name_full);
            } elseif ($date_now > $plan_list->apply_close) {
                return redirect()->back()->with('AddError', 'ปิดรับสมัคร ' . $plan_list->name_full . ' แล้ว');
            } else {

                return view('page_student.register_form', [
                    'page_name' => 'register',
                    'plan_list' => $plan_list,
                    'year_SQL' => $year_SQL->year_name,
                ]);
            }
        }
    }
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function page_register_form_check(Request $request)
    {

        if ($request->id_card != $request->id_card_con) {
            return redirect()->back()->with('AlertError', 'เลขประจำตัว ไม่ตรงกัน กรุณาตรวจสอบความถูกต้อง');
        } else {
            // ดึงข้อมูลส่วนตัว
            $user_check =   DB::table('person')
                ->where('person_code', $request->id_card)
                ->first();
            // ดัึงข้อมูลแผนการรับ นศ. และปีการศึกษา
            $plan_list = DB::table('plan')
                ->leftJoin('project', 'project.id', '=', 'plan.project')
                ->where('plan.year', $request->year)
                ->where('plan.project', $request->project)
                ->where('plan.plan_status', 1)
                ->first();

            if (is_null($plan_list)) {
                return redirect()->back()->with('AlertError', 'ไม่พบแผนการรับนักศึกษา ดังกล่าว');
            } else {

                $person_ident = Str::random(10) . uniqid() . date('YmdHis');
                $person_hash = Hash::make($request->id_card);

                if (is_null($user_check)) {
                    $data = [
                        'person_code' => $request->id_card,
                        'person_ident' => $person_ident,
                        'person_type' => 1,
                        'password' => $person_hash,
                    ];
                    DB::table('person')->insert($data);

                    return view('page_student.register_form_check', [
                        'page_name' => 'register',
                        'year_SQL' => $request->year,

                        'person_code' => $request->id_card,
                        'person_ident' => $person_ident,
                        'member' => 0,

                        'plan_list' => $plan_list,
                    ]);
                } else {

                    DB::table('person')->where('person_code', $request->id_card)
                        ->update([
                            'person_ident' => $person_ident,
                        ]);

                    return view('page_student.register_form_check', [
                        'page_name' => 'register',
                        'year_SQL' => $request->year,

                        'person_code' => $request->id_card,
                        'person_ident' => $person_ident,
                        'member' => 1,

                        'plan_list' => $plan_list,
                    ]);
                }
            }
        }
    }
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function page_register_form_step1($project_alias, $person_ident)
    {
        $year_SQL = DB::table('year')->first();
        /* $token = $request->session()->token(); */
        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        // Student Data
        $user_check =   DB::table('person')
            ->where('person_ident', $person_ident)
            ->first();
        // Plan Data

        $plan_list = DB::table('plan')
            ->leftJoin('project', 'project.id', '=', 'plan.project')
            ->where('plan.year', $year_SQL->year_name)
            ->where('plan.plan_status', 1)
            ->where('project.project_alias', $project_alias)
            ->first();

        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

        if (is_null($plan_list)) {
            return redirect()->back()->with('AlertError', 'ไม่พบแผนการรับนักศึกษา ดังกล่าว');
        } else {
            //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            $provinces = Tambon::select('province')->distinct()->orderBy('province', 'ASC')->get();
            $amphoes = Tambon::select('amphoe')->distinct()->get();
            $tambons = Tambon::select('tambon')->distinct()->get();

            $school_provine = SchoolModel::select('school_provine')->distinct()->orderBy('school_provine', 'ASC')->get();
            $school_name = SchoolModel::select('school_name')->distinct()->get();

            $facultys = Course::select('course_faculty_name')->distinct()->get();

            if (is_null($user_check)) {
                return redirect()->back()->with('AlertError', 'ไม่พบข้อมูลหรือบัญชีผู้สมัคร กรุณาลองใหม่อีกครั้ง');
            } else {
                return view('page_student.register_form_step1', compact('provinces', 'amphoes', 'tambons', 'school_provine', 'school_name', 'facultys'), [
                    'page_name' => 'register',
                    'year_SQL' => $year_SQL->year_name,
                    'person_code' =>  $user_check->person_code,
                    'person_ident' =>  $person_ident,
                    'plan_list' => $plan_list,
                    'users' => $user_check,
                ]);
            }
        }
    }
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function page_register_form_step2(Request $request)
    {
        //ตรวจสอบข้อมูล   

        $request->validate(
            [
                'school_gpa' => 'required|numeric|between:0,4',
                'email' => 'required|email|max:255|unique:person,email,' . $request->person_code . ',person_code'
            ],
            [
                'school_gpa.required' => "กรุณาระบุ ข้อมูลในส่วนนี้",
                'school_gpa.numeric' => "เกรดเฉลี่ยสะสมต้องระบุเป็นตัวเลขเท่านั้น",
                'school_gpa.between' => "เกรดเฉลี่ยสะสมต้องระบุเป็นค่าตัวเลขระหว่าง 0.00 - 4.00 เท่านั้น",
                'email.unique' => "อีเมลซ้ำกับในระบบ กรุณาเปลี่ยนอีเมลใหม่",
            ]
        );

        // Student Data
        $user_check =   DB::table('person')
            ->where('person_code', $request->person_code)
            ->first();

        if ($request->person_prefix == 1) {
            $person_tname = 'นาย';
            $person_etname = 'Mr.';
        } elseif ($request->person_prefix == 2) {
            $person_tname = 'นางสาว';
            $person_etname = 'Miss';
        } elseif ($request->person_prefix == 3) {
            $person_tname = 'นาง';
            $person_etname = 'Mrs.';
        } else {
            $person_tname = '';
            $person_etname = '';
        }

        if (is_null($user_check)) {
            return redirect()->back()->with('AlertError', 'ไม่พบการจัดทำประวัติ/ข้อมูลผู้สมัคร กรุณากรอกข้อมูลส่วนที่ 1 ก่อนข้อมูลส่วนอื่น ๆ');
        } else {

            $person_birthday = ($request->person_year_bd - 543) . '-' . $request->person_month_bd . '-' . $request->person_day_bd;
            DB::table('person')->where('person_code', $request->person_code)
                ->update([
                    'person_code' => $request->person_code,
                    'person_tname' => $person_tname,
                    'person_fname' => $request->person_fname,
                    'person_lname' => $request->person_lname,
                    'person_etname' => $person_etname,
                    'person_efname' => $request->person_efname,
                    'person_elname' => $request->person_elname,
                    'person_birthday' => $person_birthday,
                    'person_disability' => $request->person_disability,
                    'person_nationality' => $request->person_nationality,
                    'person_phone_home' => $request->person_phone_home,
                    'person_phone_mobile' => $request->person_phone_mobile,
                    'person_address1' => $request->person_address1,
                    'person_address2' => $request->person_address2,
                    'person_address3' => $request->person_address3,
                    'person_address4' => $request->person_address4,
                    'person_address5' => $request->person_address5,
                    'person_tambon' => $request->person_tambon,
                    'person_district' => $request->person_district,
                    'person_province' => $request->person_province,
                    'person_zipcode' => $request->person_zipcode,
                    'school_level' => $request->school_level,
                    'school_plan' => $request->school_plan,
                    'school_province' => $request->school_province,
                    'school_name' => $request->school_name,
                    'school_gpa' => $request->school_gpa,

                    'email' => $request->email,

                ]);

            return redirect('register/' . $request->project_alias . '/forms/' . $request->person_ident . '/select-course')->with('AlertSuccess', 'บันทึกข้อมูลส่วนตัวผู้สมัครแล้ว กรุณาเลือกสาขาวิชาหรือหลักสูตรที่มีความประสงค์จะเข้าศึกษาต่อ');
        }
    }
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //:
    //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    public function page_register_form_step3($project_alias, $person_ident)
    {
        $year_SQL = DB::table('year')->first();
        /* $token = $request->session()->token(); */
        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        // Student Data
        $user_check =   DB::table('person')
            ->where('person_ident', $person_ident)
            ->first();
        // Plan Data
        $plan_list = DB::table('plan')
            ->leftJoin('project', 'project.id', '=', 'plan.project')
            ->where('plan.year', $year_SQL->year_name)
            ->where('plan.plan_status', 1)
            ->where('project.project_alias', $project_alias)
            ->first();

        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

        if (is_null($plan_list) or is_null($user_check)) {
            return redirect()->back()->with('AlertError', 'ไม่พบแผนการรับนักศึกษา หรือข้อมูลผู้สมัคร กรุณาลองใหม่อีกครั้ง');
        } else {

            $date_now = date('Y-m-d H:i:s');

            if ($date_now < $plan_list->apply_open) {
                return redirect('/register')->with('AlertError', 'ยังไม่ถึงวันและเวลาที่รับสมัคร ' . $plan_list->name_full);
            } elseif ($date_now > $plan_list->apply_close) {
                return redirect('/register')->with('AlertError', 'ปิดรับสมัคร ' . $plan_list->name_full . ' แล้ว');
            } else {

                $facultys = DB::table('plan_course')
                    ->leftJoin('major', 'major.major_program_code', '=', 'plan_course.plan_course_major')
                    ->select('major.major_faculty_name')->distinct()
                    ->get();


                return view('page_student.register_form_step1', compact('facultys'), [
                    'page_name' => 'register_select_course',
                    'year_SQL' => $year_SQL->year_name,
                    'person_code' =>  $user_check->person_code,
                    'person_ident' =>  $person_ident,
                    'plan_list' => $plan_list,
                    'users' => $user_check,
                ]);
            }
        }
    }
    
    public function page_register_form_step4(Request $request)
    {

        $user_check =   DB::table('person')
            ->where('person_code', $request->reg_student)
            ->first();
        // Plan Data
        $plan_list = DB::table('plan')
            ->leftJoin('project', 'project.id', '=', 'plan.project')
            ->where('plan.year', $request->reg_year)
            ->where('plan.plan_status', 1)
            ->where('project.id', $request->reg_project)
            ->first();

        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        if (is_null($user_check) or is_null($plan_list)) {
            return redirect()->back()->with('AlertError', 'ไม่พบการจัดทำประวัติ/ข้อมูลผู้สมัคร หรือ แผนการรับนักศึกษา กรุณาลองใหม่อีกครั้ง');
        } else {

            $check_register = DB::table('register')
                ->where('reg_project', $request->reg_project)
                ->where('reg_year', $request->reg_year)
                ->where('reg_course', $request->reg_major1)
                ->where('reg_student', $user_check->person_code)
                ->orderBy('reg_datetime', 'DESC')
                ->first();

            if (is_null($check_register)) {

                $count_reg = DB::table('register')
                    ->where('reg_project', $request->reg_project)
                    ->where('reg_year', $request->reg_year)
                    ->count();

                if ($count_reg == 0) {
                    $num_register = $count_reg + 1;

                    $num_sec1 = Str::substr($request->reg_year, 2, 2);
                    $num_sec2 = str_pad($request->reg_project, 2, '0', STR_PAD_RIGHT) . '1';
                    $num_sec3 = str_pad($num_register, 5, '0', STR_PAD_LEFT);
                    $reg_code_final = $num_sec1 . '' . $num_sec2 . '' . $num_sec3;
                } else {

                    $register = DB::table('register')
                        ->where('reg_project', $request->reg_project)
                        ->where('reg_year', $request->reg_year)
                        ->orderBy('reg_id', 'DESC')
                        ->first();

                    $reg_code_final = $register->reg_code + 1;
                }

                $data = [
                    'reg_student' => $request->reg_student,
                    'reg_project' => $request->reg_project,
                    'reg_year' => $request->reg_year,
                    'reg_course' => $request->reg_major1,

                    'reg_status_register' => 1,
                    'reg_status_pay' => 1,
                    'reg_status_confirm' => 1,
                    'reg_code' => $reg_code_final,
                    'reg_datetime' => date('Y-m-d H:i'),
                    'reg_date' => date('Y-m-d'),
                ];
                DB::table('register')->insert($data);
                return redirect('register/' . $plan_list->project_alias . '/preview/' . $user_check->person_ident . '')->with('AlertSuccess', 'บันทึกข้อมูล และความประสงค์ที่จะเข้าศึกษาต่อในหลักสูตร/สาขาวิชา ที่ผู้สมัครเลือกแล้ว กรุณาตรวจสอบความถูกต้องของข้อมูล');
            } else {
                if ($request->reg_project == 9) {
                    $mytime = Carbon::now();
                    $start_date = $check_register->reg_datetime;
                    $end_date = date("Y-m-d H:i:s", strtotime('+1 days', strtotime($start_date)));

                    if ($mytime > $end_date) {
                        $count_reg = DB::table('register')
                            ->where('reg_project', $request->reg_project)
                            ->where('reg_year', $request->reg_year)
                            ->count();

                        if ($count_reg == 0) {
                            $num_register = $count_reg + 1;

                            $num_sec1 = Str::substr($request->reg_year, 2, 2);
                            $num_sec2 = str_pad($request->reg_project, 2, '0', STR_PAD_RIGHT) . '1';
                            $num_sec3 = str_pad($num_register, 5, '0', STR_PAD_LEFT);
                            $reg_code_final = $num_sec1 . '' . $num_sec2 . '' . $num_sec3;
                        } else {

                            $register = DB::table('register')
                                ->where('reg_project', $request->reg_project)
                                ->where('reg_year', $request->reg_year)
                                ->orderBy('reg_id', 'DESC')
                                ->first();

                            $reg_code_final = $register->reg_code + 1;
                        }

                        $data = [
                            'reg_student' => $request->reg_student,
                            'reg_project' => $request->reg_project,
                            'reg_year' => $request->reg_year,
                            'reg_course' => $request->reg_major1,

                            'reg_status_register' => 1,
                            'reg_status_pay' => 1,
                            'reg_status_confirm' => 1,
                            'reg_code' => $reg_code_final,
                            'reg_datetime' => date('Y-m-d H:i'),
                            'reg_date' => date('Y-m-d'),
                        ];
                        DB::table('register')->insert($data);
                        return redirect('register/' . $plan_list->project_alias . '/preview/' . $user_check->person_ident . '')->with('AlertSuccess', 'บันทึกข้อมูล และความประสงค์ที่จะเข้าศึกษาต่อในหลักสูตร/สาขาวิชา ที่ผู้สมัครเลือกแล้ว กรุณาตรวจสอบความถูกต้องของข้อมูล');

                    } else {
                        return redirect()->back()->with('AlertError', 'ไม่สามารถส่งใบสมัครได้ กรุณาเลือกหลักสูตรหรือสาขาวิชาอื่นที่ไม่ซ้ำกับที่ส่งใบสมัครในโครงการนี้ (เด็กดีเพื่อการพัฒนาท้องถิ่น)');
                    }
                } else {
                    return redirect()->back()->with('AlertError', 'ไม่สามารถส่งใบสมัครได้ กรุณาเลือกหลักสูตรหรือสาขาวิชาอื่นที่ไม่ซ้ำกับที่ส่งใบสมัครในโครงการนี้');
                }
            }
        }
    }

    public function page_register_form_step5($project_alias, $person_ident)
    {
        $year_SQL = DB::table('year')->first();
        // Student Data
        $user_check =   DB::table('person')
            ->where('person_ident', $person_ident)
            ->first();

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

                    ]);
                }
            }
            //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        }
    }
    public function page_register_form_preview(Request $request)
    {
        $year_SQL = DB::table('year')->first();
        // Student Data
        $user_check =   DB::table('person')
            ->where('person_ident', $request->person_ident)
            ->first();

        $plan_list = DB::table('plan')
            ->leftJoin('project', 'project.id', '=', 'plan.project')
            ->where('plan.year', $year_SQL->year_name)
            ->where('plan.plan_status', 1)
            ->where('project.project_alias', $request->project_alias)
            ->first();

        if (is_null($plan_list) or is_null($user_check)) {
            return redirect()->back()->with('AlertError', 'ไม่พบแผนการรับนักศึกษา หรือข้อมูลผู้ทำรายการ กรุณาลองใหม่อีกครั้ง');
        } else {

            $register = DB::table('register')
                ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                ->where('reg_student', $user_check->person_code)
                ->where('reg_project', $plan_list->id)
                ->where('reg_year', $year_SQL->year_name)
                ->orderBy('reg_datetime', 'DESC')
                ->first();

            if (is_null($register)) {
                return redirect()->back()->with('AlertError', 'ไม่พบข้อมูลผู้สมัคร กรุณาลองใหม่อีกครั้ง');
            } else {

                $my_age = Carbon::parse($user_check->person_birthday)->diff($register->reg_date)->format('%y ปี %m เดือน %d วัน');

                return view('page_student.register_form_preview', [
                    'page_name' => 'register',
                    'plan_list' => $plan_list,
                    'users' => $user_check,
                    'register' => $register,
                    'person_ident' =>  $request->person_ident,
                    'my_age' =>  $my_age,

                ]);
            }

            //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
            //::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        }
    }

    public function page_register_form_cancel(Request $request)
    {
        $register = DB::table('register')
            ->where('reg_student', $request->reg_student)
            ->where('reg_project', $request->reg_project)
            ->where('reg_year', $request->reg_year)
            ->where('reg_id', $request->reg_id)
            ->first();
        if (is_null($register)) {
            return redirect()->back()->with('AlertError', 'ไม่พบข้อมูลการสมัครดังกล่าว กรุณาลองใหม่อีกครั้ง');
        } else {
            DB::table('register')->where('reg_id', '=', $request->reg_id)->delete();
            return redirect('/')->with('AlertWarning', 'ยกเลิกการสมัครรายการที่เลือกแล้ว ## ผู้สมัครยังสามารถเลือกความประสงค์ที่จะเข้าศึกษาต่อในสาขาวิชา/หลักสูตรใหม่ได้ ในระยะเวลาของการรับสมัคร ##');
        }
    }
}
