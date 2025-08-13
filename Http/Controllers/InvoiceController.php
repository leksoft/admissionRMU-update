<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceController extends Controller
{
    public function invoice_application()
    {
        return view('page_student.invoice_application', ['page_name' => 'invoice']);
    }
    public function invoice_confirm()
    {
        return view('page_student.invoice_confirm', ['page_name' => 'invoice']);
    }

    public function invoice_application_pdf(Request $request)
    {
    

        $register = DB::table('register')
                ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
                ->where('reg_student', $request->reg_student)
                ->where('reg_project', $request->reg_project)
                ->where('reg_year', $request->reg_year)
                ->where('reg_id', $request->reg_id)
                ->first();

        $plan = DB::table('plan')
            ->where('project', $request->reg_project)
            ->where('year', $request->reg_year)
            ->first();

        $users = DB::table('person')->where('person_code', $request->reg_student)->first();
        // QR code with text
        //$data['qrcode'] = QrCode::generate('Welcome to Makitweb');

        // Store QR code for download
        //QrCode::generate('Welcome to Makitweb', public_path('img/qr_code/qrcode.svg'));



        if($register->reg_project == 8){
          DB::table('register')->where('reg_id',$register->reg_id)
                                                    ->where('reg_student',$register->reg_student)
                                                    ->where('reg_year',$register->reg_year)
                                                    ->where('reg_project',$register->reg_project)
                                                    ->update([
                                                        "paymentDisplay300" => "|099400040150770"."\n".$register->reg_code."\n".$register->reg_student."\n"."30000",
                                                    ]);

        }else{
          DB::table('register')->where('reg_id',$register->reg_id)
                                                    ->where('reg_student',$register->reg_student)
                                                    ->where('reg_year',$register->reg_year)
                                                    ->where('reg_project',$register->reg_project)
                                                       ->update([
                                                        "paymentDisplay200" => "|099400040150770"."\n".$register->reg_code."\n".$register->reg_student."\n"."20000",
                                                    ]);
        }


                // // ดึงข้อมูลจากฐานข้อมูล
                // $paymentDisplay200 = DB::table('register')
                //                     ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                //                     ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
                //                     ->where('reg_student', $request->reg_student)
                //                     ->where('reg_project', $request->reg_project)
                //                     ->where('reg_year', $request->reg_year)
                //                     ->where('reg_id', $request->reg_id)
                //                     ->select(DB::raw("CONCAT('|099400040150770','\n', reg_code,'\n', reg_student,'\n', '20000') AS payment_display"))
                //                     ->first();

                // $paymentDisplay300 =  DB::table('register')
                //                     ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                //                     ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
                //                     ->where('reg_student', $request->reg_student)
                //                     ->where('reg_project', $request->reg_project)
                //                     ->where('reg_year', $request->reg_year)
                //                     ->where('reg_id', $request->reg_id)
                //                     ->select(DB::raw("CONCAT('|099400040150770','\n', reg_code,'\n', reg_student,'\n', '30000') AS payment_display"))
                //                     ->first();
                // CALL `getpayment`('2568', 33606, '1449900844470', '1');
                $year = $register->reg_year ;
                $reg_code = $register->reg_id ;
                $reg_student = $register->reg_student ;
                $reg_project = $register->reg_project ;

                // $text = '|099400040150770 newline ' . $register->reg_code . ' newline ' . $register->reg_student . ' newline ' . "20000";
                // $search = 'newline';
                // $replace = "\n";
                // $message = str_replace($search, $replace, $text);

                $result = DB::select('CALL getpayment(?, ?,?,?)', [$year, $reg_code,$reg_student,$reg_project]);
                $qrCode200 = QrCode::format('png')->size(80)->generate($result[0]->payment_display200);
                $qrCode300 = QrCode::format('png')->size(80)->generate($result[0]->payment_display300);

                $data = [
                        'register' => $register,
                        'users' => $users,
                        'plan' => $plan ,
                        'qrCode200' => $qrCode200,
                        'qrCode300' => $qrCode300,
                    ];

        PDF::loadView('report.invoice_application_v3', $data, [], [
            'format' => 'A4-P'
        ])->stream('Application-' . $register->reg_id . '_' . $register->reg_year . '_' . $register->reg_code . '_std' . $register->reg_student . '_' . $register->project_alias . '.pdf');
    }
    //Test

      public function invoice_application_pdf_v3(Request $request)
    {

      $register = DB::table('register')
                ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
                ->where('reg_student', $request->reg_student)
                ->where('reg_project', $request->reg_project)
                ->where('reg_year', $request->reg_year)
                ->where('reg_id', $request->reg_id)
                ->first();

        $plan = DB::table('plan')
            ->where('project', $request->reg_project)
            ->where('year', $request->reg_year)
            ->first();

        $users = DB::table('person')->where('person_code', $request->reg_student)->first();

        if($register->reg_project == 8){
          DB::table('register')->where('reg_id',$register->reg_id)
                                                    ->where('reg_student',$register->reg_student)
                                                    ->where('reg_year',$register->reg_year)
                                                    ->where('reg_project',$register->reg_project)
                                                    ->update([
                                                        "paymentDisplay300" => "|099400040150770"."\n".$register->reg_code."\n".$register->reg_student."\n"."30000",
                                                    ]);

        }else{
          DB::table('register')->where('reg_id',$register->reg_id)
                                                    ->where('reg_student',$register->reg_student)
                                                    ->where('reg_year',$register->reg_year)
                                                    ->where('reg_project',$register->reg_project)
                                                       ->update([
                                                        "paymentDisplay200" => "|099400040150770"."\n".$register->reg_code."\n".$register->reg_student."\n"."20000",
                                                    ]);
        }


                // // ดึงข้อมูลจากฐานข้อมูล
                // $paymentDisplay200 = DB::table('register')
                //                     ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                //                     ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
                //                     ->where('reg_student', $request->reg_student)
                //                     ->where('reg_project', $request->reg_project)
                //                     ->where('reg_year', $request->reg_year)
                //                     ->where('reg_id', $request->reg_id)
                //                     ->select(DB::raw("CONCAT('|099400040150770','\n', reg_code,'\n', reg_student,'\n', '20000') AS payment_display"))
                //                     ->first();

                $paymentDisplay300 =  DB::table('register')
                                    ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                                    ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
                                    ->where('reg_student', $request->reg_student)
                                    ->where('reg_project', $request->reg_project)
                                    ->where('reg_year', $request->reg_year)
                                    ->where('reg_id', $request->reg_id)
                                    ->select(DB::raw("CONCAT('|099400040150770','%0A', reg_code,'%0A', reg_student,'%0A', '30000') AS payment_display"))
                                    ->first();
                // CALL `getpayment`('2568', 33606, '1449900844470', '1');
                $year = $register->reg_year ;
                $reg_code = $register->reg_id ;
                $reg_student = $register->reg_student ;
                $reg_project = $register->reg_project ;

                // $text = '|099400040150770 newline ' . $register->reg_code . ' newline ' . $register->reg_student . ' newline ' . "20000";
                // $search = 'newline';
                // $replace = "\n";
                // $message = str_replace($search, $replace, $text);

                $result = DB::select('CALL getpayment(?, ?,?,?)', [$year, $reg_code,$reg_student,$reg_project]);
                $qrCode200 = QrCode::format('png')->size(80)->generate($result[0]->payment_display200);
                $qrCode300 = QrCode::format('png')->size(80)->generate($result[0]->payment_display300);

      			// dd($paymentDisplay300); 
                $data = [
                        'register' => $register,
                        'users' => $users,
                        'plan' => $plan ,
                        'qrCode200' => $qrCode200,
                        'qrCode300' => $qrCode300,
                    ];



        PDF::loadView('report.invoice_application_v3', $data, [], [
            'format' => 'A4-P'
        ])->stream('Application-' . $register->reg_id . '_' . $register->reg_year . '_' . $register->reg_code . '_std' . $register->reg_student . '_' . $register->project_alias . '.pdf');
    }


    public function invoice_confirm_pdf(Request $request)
    {

        $register = DB::table('register')
            ->leftJoin('project', 'project.id', '=', 'register.reg_project')
            ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
            ->where('reg_student', $request->reg_student)
            ->where('reg_project', $request->reg_project)
            ->where('reg_year', $request->reg_year)
            ->where('reg_status_exam', 3)
            ->where('reg_id', $request->reg_id)
            ->first();

        $plan = DB::table('plan')
            ->where('project', $request->reg_project)
            ->where('year', $request->reg_year)
            ->first();

        $users = DB::table('person')->where('person_code', $request->reg_student)->first();

        $result = DB::table('register')
                                    ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                                    ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
                                    ->where('reg_student', $request->reg_student)
                                    ->where('reg_project', $request->reg_project)
                                    ->where('reg_year', $request->reg_year)
                                    ->where('reg_id', $request->reg_id)
                                    ->select(DB::raw("CONCAT('|099400040150770','\n', reg_code,'\n', reg_student,'\n', $plan->confirm_amount) AS payment_display"))
                                    ->first();


                $qrCodeDisplay = QrCode::format('png')->size(80)->generate($result->payment_display);



        $data = [
            'register' => $register,
            'users' => $users,
            'plan' => $plan,
            'qrCodeDisplay' => $qrCodeDisplay

        ];

        PDF::loadView('report.invoice_confirm', $data, [], [
            'format' => 'A4-P'
        ])->stream('FACULTY-RMU-' . $register->reg_id . '_' . $register->reg_year . '_' . $register->reg_code . '_std' . $register->reg_student . '_' . $register->project_alias . '.pdf');
    }

    public function invoice_all_pdf(Request $request)
    {

        $register = DB::table('register')
            ->leftJoin('project', 'project.id', '=', 'register.reg_project')
            ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
            ->where('reg_student', $request->reg_student)
            ->where('reg_project', $request->reg_project)
            ->where('reg_year', $request->reg_year)
            ->where('reg_id', $request->reg_id)
            ->first();

        $plan = DB::table('plan')
            ->where('project', $request->reg_project)
            ->where('year', $request->reg_year)
            ->first();

        $users = DB::table('person')->where('person_code', $request->reg_student)->first();

              $result = DB::table('register')
                                    ->leftJoin('project', 'project.id', '=', 'register.reg_project')
                                    ->leftJoin('major', 'major.major_program_code', '=', 'register.reg_course')
                                    ->where('reg_student', $request->reg_student)
                                    ->where('reg_project', $request->reg_project)
                                    ->where('reg_year', $request->reg_year)
                                    ->where('reg_id', $request->reg_id)
                                    ->select(DB::raw("CONCAT('|099400040150770','\n', reg_code,'\n', reg_student,'\n', $plan->confirm_amount) AS payment_display"))
                                    ->first();

                $qrCodeDisplay = QrCode::format('png')->size(80)->generate($result->payment_display);



        $data = [
            'register' => $register,
            'users' => $users,
            'plan' => $plan,
            'qrCodeDisplay' => $qrCodeDisplay

        ];

        PDF::loadView('report.invoice_all', $data, [], [
            'format' => 'A4-P'
        ])->stream('INVOICE-FACULTY-RMU-' . $register->reg_id . '_' . $register->reg_year . '_' . $register->reg_code . '_std' . $register->reg_student . '_' . $register->project_alias . '.pdf');
    }

}