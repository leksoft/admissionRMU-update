@include('dateFunction')
@extends('layouts.student.master')

@section('content')

@guest
<div class="layout-px-spacing container animated fadeIn ">
    <div class="text-center pt-4 mt-4 mb-4">
        <h3 class="font-weight-bold">Bill Payment</h3>
        <h5 class="">พิมพ์ และตรวจสอบการชำระเงิน</h5>
    </div>
    <div class="row">
        <div id="flStackForm" class="col-lg-6  offset-md-3 layout-spacing layout-top-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">

                </div>
                <div class="widget-content widget-content-area">


                    <form class="needs-validation" method="POST" action="{{ route('login') }}" novalidate>
                        @CSRF
                        <div class="row">
                            <div class="form-group col-md-12 mb-3">
                                <img src="{{asset('rmu_admission/img/banner/banner-04.svg')}}" class="pl-3 pr-3 img-fluid">
                                <p class="mt-4 text-justify text-uppercase">เข้าระบบรับสมัครนักศึกษา ด้วย<span class="text-primary">เลขประจำตัวประชาชน 13 หลัก</span> (เลขประจำตัวที่ใช้ในการลงทะเบียน/สมัคร) หากไม่มี ให้ใช้ <span class="text-warning">G-Number</span> หรือ <span class="text-warning">Passport Number</span></p>
                            </div>
                            <div class="form-group col-md-12 mb-3">
                                <label class="text-uppercase">เลขประจำตัว</label>
                                <input style="font-size: 16pt;letter-spacing: 0.8rem !important;" class="form-control text-center font-topic-chula" type="text" name="person_code" tabindex="1" autocomplete="off" maxlength="15" required />
                                <div class="invalid-feedback">
                                    กรุณาระบุเลขประจำตัว
                                </div>
                            </div>
                            <div class="form-group col-md-12 mb-3">
                                <label class="text-uppercase">ยืนยันเลขประจำตัวอีกครั้ง</label>
                                <input style="font-size: 16pt;letter-spacing: 0.8rem !important;" class="form-control text-center  form-control-merge font-topic-chula" id="login-password" type="password" name="password" tabindex="2" autocomplete="off" maxlength="15" required />
                                <div class="invalid-feedback">
                                    กรุณาระบุรหัสผ่าน (เลขประจำตัว)
                                </div>
                            </div>

                        </div>
                        <div class="form-group col-md-12 mb-3 text-center">
                            <button type="submit" class="btn btn-primary btn-rounded mt-3"><i class="bi bi-person-bounding-box mr-2"></i> ตรวจสอบ / ยืนยันตัวตน</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


    </div>

</div>
@else

<div class="layout-px-spacing container animated fadeIn ">

    <div class="fq-header-wrapper">
        <div class="">
            <div class="row">
                <div class="col-md-6 align-self-center order-md-0 order-1 text-header-mobile ">
                    <h1 class="">Bill Payment</h1>
                    <h5 class="">พิมพ์ และตรวจสอบการชำระเงินค่าสมัคร</h5>
                </div>
                <div class="col-md-6 order-md-0 order-0">
                    <div class="banner-img">
                        <img src="{{asset('rmu_admission/img/banner/banner-04.svg')}}" class="d-block" alt="header-image">
                    </div>
                </div>
            </div>
        </div>
    </div>







    @php( $register = DB::table('register')
    ->leftJoin('plan','plan.plan_id','=','register.reg_plan')
    ->leftJoin('project','project.id','=','plan.project_id')
    ->where('reg_student',Auth::user()->person_code)
    ->orderBy('register.reg_id' , 'DESC')
    ->get() )
    @foreach($register as $row)

    @if($row->reg_num == 1)

    @php( $data_course_1 = DB::table('course')
    ->where('plan_id',$row->reg_plan)
    ->where('course_id',$row->reg_course1)
    ->first() )

    @elseif($row->reg_num == 2)

    @php( $data_course_1 = DB::table('course')
    ->where('plan_id',$row->reg_plan)
    ->where('course_id',$row->reg_course1)
    ->first() )

    @php( $data_course_2 = DB::table('course')
    ->where('plan_id',$row->reg_plan)
    ->where('course_id',$row->reg_course2)
    ->first() )

    @elseif($row->reg_num == 3)

    @php( $data_course_1 = DB::table('course')
    ->where('plan_id',$row->reg_plan)
    ->where('course_id',$row->reg_course1)
    ->first() )

    @php( $data_course_2 = DB::table('course')
    ->where('plan_id',$row->reg_plan)
    ->where('course_id',$row->reg_course2)
    ->first() )

    @php( $data_course_3 = DB::table('course')
    ->where('plan_id',$row->reg_plan)
    ->where('course_id',$row->reg_course3)
    ->first() )

    @endif

    <div class="row mt-5">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-activity-five">

                <div class="widget-heading-bg-dark">
                    <h6 class="text-uppercase text-light">เลขที่ผู้สมัคร {{$row->reg_code}}</h6>
                </div>

                <div class="widget-content">
                    <div class="text-center">
                        @if($row->reg_num == 1)
                        <img class="align-self-center rounded" style="width:150px;" src="data:image/png;base64,  {!! DNS2D::getBarcodePNG('|099400040150770 '.$row->reg_code.' '.$row->reg_year.'1 20000', 'QRCODE') !!}">

                        @elseif($row->reg_num == 2)
                        <img class="align-self-center rounded" style="width:150px;" src="data:image/png;base64,  {!! DNS2D::getBarcodePNG('|099400040150770 '.$row->reg_code.' '.$row->reg_year.'1 30000', 'QRCODE') !!}">

                        @elseif($row->reg_num == 3)
                        <img class="align-self-center rounded" style="width:150px;" src="data:image/png;base64,  {!! DNS2D::getBarcodePNG('|099400040150770 '.$row->reg_code.' '.$row->reg_year.'1 40000', 'QRCODE') !!}">
                        @endif
                        <p class="mt-3 text-uppercase">QR Code Payment</p>

                        <form class="needs-validation" method="POST" action="{{route('register.invoice.application.pdf')}}" target="_blank">
                            @csrf
                            <input type="text" class="form-control" name="reg_id" hidden value="{{$row->reg_id}}" required>
                            <input type="text" class="form-control" name="reg_year" hidden value="{{$row->reg_year}}" required>
                            <input type="text" class="form-control" name="reg_plan" hidden value="{{$row->reg_plan}}" required>
                            <button type="submit" class="btn btn-primary btn-rounded">พิมพ์ ใบแจ้งชำระค่าสมัคร</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-activity-five">

                <div class="widget-heading-bg-primary">
                    <h6 class="text-light">
                        {{$row->name_etc}} ปีการศึกษา {{$row->reg_year}}
                    </h6>
                </div>

                <div class="widget-content">

                    <p class="">
                        เลขที่ผู้สมัคร {{$row->reg_code}}
                        &nbsp;&nbsp;&nbsp;
                        เลขประจำตัว {{$row->reg_student}}
                        &nbsp;&nbsp;&nbsp;
                        วันที่สมัคร {{DateThai($row->reg_date)}}
                    </p>

                    <p class="">{{$row->name_full}}</ย>

                    <p class="">
                    <ul class="">

                        @if($row->reg_num == 1)

                        <li class=" mb-1 mt-2">ลำดับที่ 1 หลักสูตร <span class="text-primary ">{{$data_course_1->course_name}} สาขาวิชา{{$data_course_1->course_program_name}} </span> {{$data_course_1->course_faculty_name}} {{$data_course_1->course_level_name}}</li>

                        @elseif($row->reg_num == 2)

                        <li class="mb-1 mt-2">ลำดับที่ 1 หลักสูตร <span class="text-primary ">{{$data_course_1->course_name}} สาขาวิชา{{$data_course_1->course_program_name}} </span> {{$data_course_1->course_faculty_name}} {{$data_course_1->course_level_name}}</li>
                        <li class="mb-1">ลำดับที่ 2 หลักสูตร <span class="text-secondary ">{{$data_course_2->course_name}} สาขาวิชา{{$data_course_2->course_program_name}} </span> {{$data_course_2->course_faculty_name}} {{$data_course_2->course_level_name}}</li>

                        @elseif($row->reg_num == 3)

                        <li class="mb-1 mt-2">ลำดับที่ 1 หลักสูตร <span class="text-primary ">{{$data_course_1->course_name}} สาขาวิชา{{$data_course_1->course_program_name}} </span> {{$data_course_1->course_faculty_name}} {{$data_course_1->course_level_name}}</li>
                        <li class="mb-1">ลำดับที่ 2 หลักสูตร <span class="text-secondary ">{{$data_course_2->course_name}} สาขาวิชา{{$data_course_2->course_program_name}} </span> {{$data_course_2->course_faculty_name}} {{$data_course_2->course_level_name}}</li>
                        <li class="mb-1">ลำดับที่ 3 หลักสูตร <span class="text-warning ">{{$data_course_3->course_name}} สาขาวิชา{{$data_course_3->course_program_name}} </span> {{$data_course_3->course_faculty_name}} {{$data_course_3->course_level_name}}</li>

                        @endif

                    </ul>
                    </p>
                    <!--
                    <button type="submit" class="btn btn-warning rounded-circle bs-tooltip" data-placement="bottom" data-toggle="tooltip" data-html="true" title="ใบสมัคร"><i data-feather="user"></i></button>
                    <button type="submit" class="btn btn-secondary rounded-circle bs-tooltip" data-placement="bottom" data-toggle="tooltip" data-html="true" title="อัปโหลดหลักฐานการชำระเงินค่าสมัคร"><i data-feather="upload"></i></button>
-->
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endguest
@endsection