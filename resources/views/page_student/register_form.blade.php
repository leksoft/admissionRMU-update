@extends('layouts.student.master')
@section('title','สมัครเรียน '.$year_SQL)
@section('content')
<div class="layout-px-spacing container animated fadeIn ">
    <div class="text-center pt-4 mt-4 mb-4">
        <h3 class="">ระบบรับสมัครนักศึกษา</h3>
        <h5 class="">มหาวิทยาลัยราชภัฏมหาสารคาม ประจำปีการศึกษา {{$year_SQL}}</h5>
    </div>
    <div class="row">
        <div id="flStackForm" class="col-lg-6  offset-md-3 layout-spacing layout-top-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                </div>
                <div class="widget-content widget-content-area">
                    <form class="needs-validation" method="POST" action="{{route('register.form.check.user')}}" novalidate>
                        @CSRF
                        <div class="row">
                            <div class="form-group col-md-12 mb-3">
                                <img src="{{asset('rmu_admission/img/thai_id_card-01.png')}}" class="pl-3 pr-3 img-fluid">
                                <p class="mt-4 text-justify">ระบบรับสมัครนักศึกษา ใช้ <span class="text-primary">เลขประจำตัวประชาชน 13 หลัก</span> ในการยืนยันตัวตนการทำรายการ ในระบบ "ผู้สมัคร" กรุณาตรวจสอบความถูกต้องก่อนบันทึก หากไม่มี ให้ใช้ <span class="text-warning">G-Number</span> หรือ <span class="text-warning">Passport Number</span></p>
                            </div>
                            <div class="form-group col-md-12 mb-3">
                                <label class="text-uppercase">เลขประจำตัว</label>
                                <input type="text" class="form-control text-center" maxlength="13" style="letter-spacing: 0.5rem !important;" autocomplete="off" name="id_card" required>
                                <div class="invalid-feedback">
                                    <i class="bi bi-patch-exclamation-fill mr-2 pl-2"></i>กรุณาระบุเลขประจำตัว
                                </div>
                            </div>
                            <div class="form-group col-md-12 mb-3">
                                <label class="text-uppercase">ยืนยันเลขประจำตัวอีกครั้ง</label>
                                <input type="text" class="form-control text-center" maxlength="13" style="letter-spacing: 0.5rem !important;" autocomplete="off" name="id_card_con" required>
                                <div class="invalid-feedback">
                                    <i class="bi bi-patch-exclamation-fill mr-2 pl-2"></i>กรุณาระบุเลขประจำตัว
                                </div>
                            </div>
                            <input type="text" class="form-control" name="year" hidden value="{{$year_SQL}}" required>
                            <input type="text" class="form-control" name="project" hidden value="{{$plan_list->id}}" required>
                            <input type="text" class="form-control" name="project_alias" hidden value="{{$plan_list->project_alias}}" required>
                        </div>
                        <div class="form-group col-md-12 mb-3 text-center">
                            <button type="submit" class="btn btn-primary btn-rounded mt-3"><i class="bi bi-check-circle-fill mr-2"></i> ยืนยันข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection