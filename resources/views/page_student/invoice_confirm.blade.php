@include('dateFunction')
@extends('layouts.student.master')
@section('content')
<div class="layout-px-spacing container animated fadeIn ">
    @guest

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
                                <img src="{{asset('rmu_admission/img/Payment_icon-01.png')}}" class="pl-3 pr-3 img-fluid">
                                <p class="mt-4 font-content-chula text-justify">ระบบรับสมัครนักศึกษา ใช้ <span class="text-primary">เลขประจำตัวประชาชน 13 หลัก</span> ในการยืนยันตัวตนการทำรายการใด ๆ ในระบบ "ผู้สมัคร" กรุณาตรวจสอบความถูกต้องก่อนบันทึก หากไม่มี ให้ใช้ <span class="text-warning">G-Number</span> หรือ <span class="text-warning">Passport Number</span></p>
                            </div>
                            <div class="form-group col-md-12 mb-3">
                                <label class="text-uppercase">เลขประจำตัว</label>
                                <input style="font-size: 16pt;letter-spacing: 0.8rem !important;" class="form-control text-center" type="text" name="person_code" tabindex="1" autocomplete="off" maxlength="15" required />
                                <div class="invalid-feedback">
                                    กรุณาระบุเลขประจำตัว
                                </div>
                            </div>
                            <div class="form-group col-md-12 mb-3">
                                <label class="text-uppercase">ยืนยันเลขประจำตัวอีกครั้ง</label>
                                <input style="font-size: 16pt;letter-spacing: 0.8rem !important;" class="form-control text-center  form-control-merge" id="login-password" type="password" name="password" tabindex="2" autocomplete="off" maxlength="15" required />
                                <div class="invalid-feedback">
                                    กรุณาระบุรหัสผ่าน (เลขประจำตัว)
                                </div>
                            </div>

                        </div>
                        <div class="form-group col-md-12 mb-3 text-center">
                            <button type="submit" class="btn btn-primary btn-rounded mt-3">··· เข้าสู่ระบบ ···</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


    </div>


    @else


    <div class="fq-header-wrapper">
        <div class="">
            <div class="row">
                <div class="col-md-6 align-self-center order-md-0 order-1 text-header-mobile ">
                    <h1 class="">Bill Payment</h1>
                    <h5 class="">พิมพ์ และตรวจสอบการชำระเงินค่ายืนยันสิทธิ์</h5>
                </div>
                <div class="col-md-6 order-md-0 order-0">
                    <div class="banner-img">
                        <img src="{{asset('rmu_admission/img/banner/banner-05.svg')}}" class="d-block" alt="header-image">
                    </div>
                </div>
            </div>
        </div>
    </div>


    @endguest

</div>
@endsection