
@include('dateFunction')
@extends('layouts.student.master')
@section('title','ผลการสมัคร')
@section('content')

<div class="layout-px-spacing container animated fadeIn  mb-5">
    <div class="fq-header-wrapper">
        <div class="">
            <div class="row">
                <div class="col-md-6 align-self-center order-md-0 order-1 text-header-mobile ">
                    <h1 class="">ผลการสมัคร</h1>
                    <nav class="breadcrumb-one mt-1" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">หน้าแรก</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">สมัครเรียน
                                    {{$plan_list->year}}</a></li>
                            <li class="breadcrumb-item">{{$plan_list->name_short}}</li>
                            @if(request()->is('register/*/forms/*/select-course'))
                            <li class="breadcrumb-item active">เลือกสาขาวิชา</li>
                            @else
                            <li class="breadcrumb-item active">ผลการสมัคร</li>
                            @endif
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 order-md-0 order-0">
                    <div class="banner-img">
                        <img src="{{asset('rmu_admission/img/banner/banner-03.svg')}}" class="d-block"
                            alt="header-image">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row p-2">
        <div class="col-lg-12 align-self-center">
            <!--
            <div class="mb-4 mt-4">
                <ul class="menu-ribbon">
                    @if($register->reg_status_pay != 3)
                    <li class="_bg-warning">
                        <a href="#" class="hover-underline-animation text-center" data-toggle="modal"
                            data-target="#deleteConformation" disabled>
                            <i class="bi  bi-dash-circle-dotted" style="font-size: 2em;color:#fff"></i>
                            <h6 class="text-light">ยกเลิกการสมัคร</h6>
                        </a>
                    </li>
                    @endif
                    <li class="_bg-primary">
                        <a href="{{URL('/invoice/application/pdf_v2?reg_id='.$register->reg_id.'&reg_year='.$register->reg_year.'&reg_project='.$register->reg_project.'&reg_student='.$register->reg_student.'')}}"
                            class="hover-underline-animation text-center" target="_blank">
                            <i class="bi bi-printer-fill" style="font-size: 2em;color:#fff"></i>
                            <h6 class="text-light">พิมพ์ใบแจ้งชำระค่าสมัคร</h6>
                        </a>
                    </li>
                    <li class="_bg-dark">
                        <a href="{{URL('/status/student-admissions')}}" class="hover-underline-animation text-center">
                            <i class="bi bi-ui-checks" style="font-size: 2em;color:#fff"></i>
                            <h6 class="text-light">ตรวจสอบสถานะการสมัคร</h6>
                        </a>
                    </li>
                </ul>
            </div>
        -->
        <div class="mb-4 mt-4">
            <ul class="menu-ribbon">
                <li class="_bg-dark">
                    <a href="{{URL('/payment/application')}}"
                        class="hover-underline-animation text-center">
                        <i class="bi bi-printer-fill" style="font-size: 2em;color:#fff"></i>
                        <h6 class="text-light">คลิก เข้าสู่ระบบรับสมัครเพื่อพิมพ์ใบแจ้งชำระเงิน</h6>
                    </a>
                </li>
            </ul>
        </div>
            <blockquote class="blockquote"
                style="border-bottom-right-radius: 0.5em;border-top-right-radius: 0.5em;border-left: 2px solid #E57373;">
                <p class="d-inline">รอบการสมัคร&nbsp;:&nbsp;{{$plan_list->name_full}} ประจำปีการศึกษา
                    {{$plan_list->year}}</p>
                <small><cite title="Source Title">ระบบรับสมัครนักศึกษาเข้าศึกษาต่อในสถาบันอุดมศึกษา
                        (มหาวิทยาลัยราชภัฏมหาสารคาม) </cite></small>

                <br />
                <hr style="margin-top: 1px;margin-bottom: 1px;border-top: 1px dashed #ebedf2;" />
                <br />


                <div class="alert alert-light-primary border-0 mb-4" role="alert">
                    <strong>กรุณาอ่าน!</strong> ใบแจ้งชำระเงินแต่ละใบ จะได้รับ QR Code / Barcode
                    และเลขที่ผู้สมัครใหม่ทุกครั้ง สำหรับนำไปจ่ายตามช่องทางต่าง ๆ และหากผู้สมัครยกเลิกใบสมัครแล้ว
                    ห้ามนำใบแจ้งชำระเงินไปจ่าย เนื่องจากเมื่อตรวจสอบเลขที่ผู้สมัครแล้ว <span
                        class=" font-weight-bold">ใบสมัครที่ถูกยกเลิกจะไม่มีในฐานข้อมูล หากยกเลิกและสมัครใหม่
                        กรุณาพิมพ์ใบแจ้งชำระเงินใหม่ทุกครั้ง</span></button>
                </div>
                <div class="alert alert-light-danger border-0 mb-4" role="alert">
                    <strong>กรณีชำระผ่าน Mobile Banking!</strong> ผู้สมัครสามารถสแกนจ่านผ่าน Mobile Banking
                    บางธนาคารที่รองรับการจ่าย และหากต้องการให้บุคคลอื่นที่ไม่ใช่ผู้สมัครสแกนจ่าย สามารถทำได้
                    แต่ต้องสแกนจ่ายที่ใบแจ้งชำระเงินของผู้สมัครเท่านั้น กรุณาตรวจสอบเลขที่ผู้สมัคร/เลขประจำตัว
                    บนใบแจ้งชำระค่าสมัคร และบน Application (Ref No.1 และ Ref No.2)
                    ให้ตรงกันก่อนชำระค่าสมัคร</span></button>
                </div>

                <div style="letter-spacing: 0.1px;">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-4">
                            <tr>
                                <th class="text-center text-nowrap">เลขที่ใบสมัคร</th>
                                <th class="text-center text-nowrap">เลขประจำตัว</th>
                                <th class="text-center text-nowrap">วันที่สมัคร</th>
                            </tr>
                            <tr>
                                <th class="text-center text-nowrap"><span
                                        style="font-size: 14pt;">{{$register->reg_code}}</span></th>
                                <th class="text-center text-nowrap"><span
                                        style="font-size: 14pt;">{{$register->reg_student}}</span></th>
                                <th class="text-center text-nowrap"><span
                                        style="font-size: 14pt;">{{DateThai($register->reg_date)}}</span></th>
                            </tr>
                        </table>
                    </div>

                    <ol>
                        <li>
                            ข้อมูลส่วนตัว
                            <ul>
                                <li class="mb-1">ชื่อ - สกุล (ไทย)&nbsp;&nbsp;&nbsp;&nbsp;<span
                                        style="border-bottom: 1px dashed #bfc9d4;">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->person_tname}}
                                        {{$users->person_fname}} {{$users->person_lname}}&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </li>
                                <li class="mb-1">ชื่อ-นามสกุล (อังกฤษ)&nbsp;&nbsp;&nbsp;&nbsp;<span
                                        style="border-bottom: 1px dashed #bfc9d4;"
                                        class="text-uppercase">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->person_etname}}
                                        {{$users->person_efname}}
                                        {{$users->person_elname}}&nbsp;&nbsp;&nbsp;&nbsp;</span></li>
                                <li class="mb-1">วัน เดือน ปี เกิด&nbsp;&nbsp;&nbsp;&nbsp;<span
                                        style="border-bottom: 1px dashed #bfc9d4;"
                                        class="text-uppercase">&nbsp;&nbsp;&nbsp;&nbsp;{{DateThaiFull($users->person_birthday)}}
                                        <cite>(อายุ {{$my_age}} นับจากวันที่สมัคร)</cite>&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </li>
                                <li class="mb-1">สัญชาติ&nbsp;&nbsp;&nbsp;&nbsp;<span
                                        style="border-bottom: 1px dashed #bfc9d4;"
                                        class="text-uppercase">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->person_nationality}}&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </li>
                                <li class="mb-1"> ความพิการ&nbsp;&nbsp;&nbsp;&nbsp;<span
                                        style="border-bottom: 1px dashed #bfc9d4;">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->person_disability}}&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </li>
                            </ul>
                        </li>
                        <li class="pt-3">
                            ข้อมูลการติดต่อ
                            <ul>
                                <li class="mb-1"> ที่อยู่ @if($users->person_address1 !=
                                    "-")<span>{{$users->person_address1}}</span>@endif
                                    @if($users->person_address2 != "-")หมู่บ้าน
                                    <span>{{$users->person_address2}}</span>@endif
                                    @if($users->person_address3 != "-")ซ.<span>{{$users->person_address3}}</span>@endif
                                    @if($users->person_address4 != "-")ถ.<span>{{$users->person_address4}}</span>@endif
                                    ต.{{$users->person_tambon}}</span>
                                    อ.<span>{{$users->person_district}}</span>
                                    ({{$users->person_province}} {{$users->person_zipcode}})
                                </li>
                                <li class="mb-1">หมายเลขโทรศัพท์ (บ้าน) <span
                                        style="border-bottom: 1px dashed #bfc9d4;">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->person_phone_home}}&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </li>
                                <li class="mb-1">หมายเลขโทรศัพท์ (มือถือ) <span
                                        style="border-bottom: 1px dashed #bfc9d4;">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->person_phone_home}}&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </li>
                                <li class="mb-1">อีเมล <span
                                        style="border-bottom: 1px dashed #bfc9d4;">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->email}}&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </li>
                            </ul>
                        </li>
                        <li class="pt-3">
                            ข้อมูลการศึกษา
                            <ul>
                                <li class="mb-1">สำเร็จการศึกษา/อยู่ระหว่างการศึกษา <span
                                        style="border-bottom: 1px dashed #bfc9d4;">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->school_level}}
                                        แผนการเรียน {{$users->school_plan}}&nbsp;&nbsp;&nbsp;&nbsp;</span></li>
                                <li class="mb-1">สถาบันการศึกษา <span
                                        style="border-bottom: 1px dashed #bfc9d4;">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->school_name}}
                                        ({{$users->school_province}})&nbsp;&nbsp;&nbsp;&nbsp;</span></li>
                                <li class="mb-1">เกรดเฉลี่ย <span
                                        style="border-bottom: 1px dashed #bfc9d4;">&nbsp;&nbsp;&nbsp;&nbsp;{{$users->school_gpa}}&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                </li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </blockquote>

            @php( $data_course_1 = DB::table('major')
            ->where('major_program_code',$register->reg_course)
            ->first() )

            <style>
                .widget_first::after {
                    position: absolute;
                    content: 'สาขาที่สมัครเรียน';
                    top: 5px;
                    padding: 0.5rem;
                    width: 8rem;
                    background: #00897B;
                    color: white;
                    text-align: center;
                    border-top-left-radius: 0.1em;
                    border-bottom-right-radius: 0.3em;
                }
            </style>
            <div class="widget widget_first box box-shadow mt-3">
                <div class="widget-header" style="background-color: #00897B;padding: 3px 3px 3px 3px;">
                    <div class="row">
                    </div>
                </div>
                <div class="widget-content widget-content-area pt-5 pb-4"
                    style="background: rgb(0,77,64);background: linear-gradient(135deg, rgba(0,77,64,1) 0%, rgba(0,121,107,1) 100%);border-bottom-left-radius:0.5em;border-bottom-right-radius: 0.5em;">
                    <table class="" width="100%">
                        <tr>
                            <td width="80%" class="text-light">
                                <span style="font-size:1.5em;">มหาวิทยาลัยราชภัฏมหาสารคาม</span><br /><span
                                    style="font-size:0.97em;">80 ถ.นครสวรรค์ ต.ตลาด อ.เมืองมหาสารคาม จ.มหาสารคาม
                                    (44000)</span>
                            </td>
                            <td width="20%" class="text-right">
                                <img src="{{asset('rmu_admission/img/favicon_rmu-02.png')}}" width="95px"
                                    class="img-fluid">
                            </td>
                        </tr>
                    </table>
                    <span class="text-light">
                        <i class="bi bi-plus-circle-dotted mr-2"></i>รหัส {{$data_course_1->major_program_id}} -
                        {{$data_course_1->major_program_code}}<br />
                        <i class="bi bi-caret-right-fill mr-2"></i>{{$data_course_1->major_faculty_name}}<br />
                        <i class="bi bi-caret-right-fill mr-2"></i>ระดับ {{$data_course_1->major_level_name}}<br />
                        <i class="bi bi-caret-right-fill mr-2"></i>หลักสูตร{{$data_course_1->major_course}} สาขาวิชา
                        {{$data_course_1->major_program_name}}
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>


@if($register->reg_status_pay == 1)
<div class="modal fade" id="deleteConformation" tabindex="-1" role="dialog" aria-labelledby="deleteConformationLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content" id="deleteConformationLabel">
            <div class="modal-body text-center">
                <!-- <h4>ยกเลิกผลการสมัครนี้หรือไม่ ?</h4>
               
                <form id="cancel_reg" method="POST" action="{{route('register.form.cancel')}}">
                    @csrf
                    <input type="text" class="form-control" name="reg_id" hidden value="{{$register->reg_id}}" required>
                    <input type="text" class="form-control" name="reg_year" hidden value="{{$register->reg_year}}"
                        required>
                    <input type="text" class="form-control" name="reg_project" hidden value="{{$register->reg_project}}"
                        required>
                    <input type="text" class="form-control" name="reg_student" hidden value="{{$register->reg_student}}"
                        required>
            
                    <button form="cancel_reg" type="submit" class="btn btn-success btn-rounded mt-2"
                        data-remove="task">ยืนยัน</button>
                </form> -->
                <h5>ไม่สามารถยกเลิกใบสมัครได้ทุกกรณี</h5>
                <button type="button" class="btn btn-danger btn-rounded mt-2" data-dismiss="modal">รับทราบ</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="BillPayment" tabindex="-1" role="dialog" aria-labelledby="BillPayment" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase" id="BillPayment">ใบแจ้งชำระค่าสมัคร</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body pt-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-bordered mb-4">
                        <tr>
                            <td class="text-center text-nowrap">เลขที่ใบสมัคร</td>
                            <td class="text-center text-nowrap">เลขประจำตัว</td>
                            <td class="text-center text-nowrap">วันที่สมัคร</td>
                        </tr>
                        <tr>
                            <td class="text-center text-nowrap"><span
                                    style="font-size: 14pt;">{{$register->reg_code}}</span></td>
                            <td class="text-center text-nowrap"><span
                                    style="font-size: 14pt;">{{$register->reg_student}}</span></td>
                            <td class="text-center text-nowrap"><span
                                    style="font-size: 14pt;">{{DateThai($register->reg_date)}}</span></td>
                        </tr>
                    </table>
                </div>

                <div class="text-center">
                    <img src="{{asset('rmu_admission/img/banner/banner-04.svg')}}" class="img-fluid">
                </div>

                <table class="table table-bordered mt-2" style="font-weight: 300 !important;">
                    <thead>
                        <tr>
                            <th class="text-center text-dark">No.</th>
                            <th class="text-center text-dark">Description.</th>
                            <th class="text-center text-dark">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="10%" style="height:6cm;text-align:center;vertical-align:top;">
                                1.
                            </td>
                            <td width="72%" style="vertical-align:top;">
                                <span>สมัครเข้าศึกษาต่อระดับปริญญาตรี {{$register->name_full}} ปีการศึกษา
                                    {{$register->reg_year}} </span><br />
                                @php( $data_course_1 = DB::table('major')
                                ->where('major_program_code',$register->reg_course)
                                ->first() )
                                <p>
                                    สาขาที่สมัครเรียน<br />
                                    <i class="bi bi-caret-right pl-3 mr-2"></i>ระดับ :
                                    {{$data_course_1->major_level_name}}<br />
                                    <i class="bi bi-caret-right pl-3 mr-2"></i>หลักสูตร :
                                    {{$data_course_1->major_course}}<br />
                                    <i class="bi bi-caret-right pl-3 mr-2"></i>สาขาวิชา :
                                    <b>{{$data_course_1->major_program_name}}<br />
                                </p>
                            </td>
                            <td width="18%" style="text-align:center;vertical-align:top;">200</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:center;">สองร้อยบาทถ้วน</td>
                            <td style="text-align:center;">200.00</td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center">
                    <form class="needs-validation" method="POST" action="{{route('register.invoice.application.pdf')}}"
                        target="_blank">
                        @csrf
                        <input type="text" class="form-control" name="reg_id" hidden value="{{$register->reg_id}}"
                            required>
                        <input type="text" class="form-control" name="reg_year" hidden value="{{$register->reg_year}}"
                            required>
                        <input type="text" class="form-control" name="reg_project" hidden
                            value="{{$register->reg_project}}" required>
                        <input type="text" class="form-control" name="reg_student" hidden
                            value="{{$register->reg_student}}" required>

                        <button type="submit" class="btn btn-success btn-rounded pr-4 pl-4">P R I N T</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endif

@endsection