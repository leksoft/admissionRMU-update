@include('dateFunction')
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="RMU Admission" />
    <meta name="keywords" content="RMU Admission" />
    <meta name="author" content="RMU Admission" />

    <title>Bill-Payment-Confirm-{{ $register->reg_student }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'sarabun', sans-serif;
        }

        @page {
            header: page-header;
            footer: page-footer;
            margin-top: 0.5cm;
            margin-left: 0.5cm;
            margin-right: 0.5cm;
            margin-bottom: 0.5cm;
        }



        table {
            width: 100% !important;
            border-collapse: collapse !important;
            overflow: wrap !important;
            font-size: 13pt;
        }

        .table th,
        .table td {
            padding: 0.4rem 0.7rem !important;
            border: 0.5px solid gray;
        }


        p {
            font-size: 13pt;
        }

        span {
            font-size: 13pt;
        }


        #rcorners2 {
            border: 1px solid gray;
        }

        .center {
            margin-left: auto;
            margin-right: auto;
        }
    </style>

</head>

<body>

    <table width="100%">
        <tr>
            <td width="22%" style="vertical-align:top;font-size:12pt;">
            </td>
            <td width="56%" style="text-align:center;">
                <p>
                    <span style="font-size: 13pt;font-weight:bold;">ใบแทนใบสำคัญรับเงิน</span><br />
                    <span style="font-size: 13pt;">(แต่ไม่สามารถนำไปเบิกค่าใช้จ่ายทางราชการได้)</span><br />


                </p>
            </td>
            <td width="22%" style="vertical-align:bottom;text-align:right;font-size:12pt;">
                <span style="font-weight:bold;">( ส่วนที่ 1 สำหรับผู้สมัคร )</span>
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td width="10%" style="vertical-align:top;">
                <img src="img/favicon_rmu-03.png" width="60px">
            </td>

            <td width="53%">
                <p>
                    <span style="font-size:14pt;font-weight:bold;">มหาวิทยาลัยราชภัฏมหาสารคาม</span><br />
                    <span style="font-size:10.1pt;">RAJABHAT MAHA SARAKHAM UNIVERSITY</span><br />
                    <span
                        style="font-size:13pt;font-weight:bold;">ใบแจ้งชำระเงินค่าสมัครเข้าศึกษาต่อและค่าธรรมเนียมแรกเข้า</span>
                </p>
            </td>

            <td width="37%">
                <span style="font-size: 13pt;">มหาวิทยาลัยราชภัฏมหาสารคาม</span><br />
                <span style="font-size: 13pt;">เลขที่ 80 ถ.นครสวรรค์ อ.เมือง จ.มหาสารคาม 44000</span><br />
                <span style="font-size: 13pt;color:#fff;">RMU.AC>TH</span>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <span style="font-size: 13pt;">ชื่อ - นามสกุล : </span><span
                    style="font-size: 13pt;font-weight:bold;">{{ $users->person_tname }} {{ $users->person_fname }}
                    {{ $users->person_lname }}</span>
            </td>

            <td>
                <span style="font-size: 13pt;">รหัสผู้สมัคร : </span><span
                    style="font-size: 13pt;font-weight:bold;">{{ $register->reg_code }}</span>
            </td>
        </tr>

    </table>


    <table class="table" style="margin-top: 10px;">
        <thead>
            <tr style="background-color:#d3d3d3;">
                <th class="text-center">ที่<br />No.</th>
                <th class="text-center">รายการ<br />Description.</th>
                <th class="text-center">จำนวนเงิน<br />Amount (Baht).</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td width="10%" style="height:6cm;text-align:center;vertical-align:top;">
                    1.
                </td>
                <td width="72%" style="vertical-align:top;">
                    <span style="font-weight:bold;">ค่าธรรมเนียมแรกเข้า ระดับปริญญาตรี ปีการศึกษา
                        {{ $register->reg_year }} </span>
                    <span style="font-weight:bold;">{{ $register->name_full }} </span><br />
                    <u>สาขาที่สมัครเรียน</u><br />
                    <span style="font-size: 13pt;">&nbsp;&nbsp;&nbsp;ระดับ : {{ $register->major_level_name }}
                    </span><br />
                    <span style="font-size: 13pt;">&nbsp;&nbsp;&nbsp;หลักสูตร :
                        {{ $register->major_course }}</span><br />
                    <span style="font-size: 13pt;">&nbsp;&nbsp;&nbsp;สาขาวิชา :
                        <b>{{ $register->major_program_name }}</b></span>
                </td>

                <td width="18%" style="text-align:center;vertical-align:top;">
                    @php($payment_amount = substr($plan->confirm_amount, 0, -2))
                    {{ number_format($payment_amount) }}
                </td>

            </tr>

            <tr style="background-color:#d3d3d3;">
                <td colspan="2" style="text-align:center;font-weight:bold;">
                    {{ $plan->confirm_amount_text }}
                </td>

                <td style="text-align:center;font-weight:bold;">
                    @php($payment_amount = substr($plan->confirm_amount, 0, -2))
                    {{ number_format($payment_amount) }}.00
                </td>

            </tr>

            <tr>
                <td colspan="2"
                    style="font-weight:bold; border-bottom: 1px solid #fff;border-left: 1px solid #fff;vertical-align:top;">
                    กำหนดชำระเงินภายในวันที่ {{ DateThaiFull($plan->confirm_open) }} -
                    {{ DateThaiFull($plan->confirm_close) }}
                    <br /><br /><br />
                    <span style="font-size: 11pt;">
                        ( เก็บเอกสารนี้ไว้เป็นหลักฐานเพื่อรับคูปองแลกวัสดุการศึกษา )<br />
                        เพ่ือความสะดวกของของท่าน กรุณานําเอกสารฉบับนี้ไปชําระเงินได้ที่ธนาคารท่ีระบุ
                        ทุกสาขาทั่วประเทศ</span>

                </td>
                <td style="text-align:center;font-size: 10pt;font-weight:bold;">
                    สำหรับเจ้าหน้าที่การเงิน<br /><br /><br />
                    ผู้รับเงิน...................................<br />
                    วันที่........../.............../............<br />
                    (ลงลายมือชื่อและประทับตรา)
                </td>

            </tr>


        </tbody>
    </table>


    <table width="100%">
        <tr>
            <td style="font-size:10pt;text-align:center;">
                ···························································································································································
                (กรุณาตัดตามรอยปรุ payment v.2 )
                ···························································································································································
            </td>
        </tr>
    </table>



    <table width="100%" style="margin-top: 15px;">
        <tr>
            <td style="vertical-align:bottom;text-align:right;font-size:12pt;">
                <span style="font-weight:bold;">( ส่วนที่ 2 สำหรับธนาคาร )</span>
            </td>
        </tr>
    </table>


    <table width="100%">
        <tr>
            <td width="74%" style="border-top: 0.5px solid gray;border-left: 0.5px solid gray;">

            </td>
            <td width="26%"
                style="font-weight:bold;border-top: 0.5px solid gray;border-right: 0.5px solid gray;padding: 0.3rem 0.1rem !important;">
                ใบแจ้งการชําระเงิน ( เพื่อนําเข้าบัญชี )
                <span style="font-size: 11pt;">(โปรดเรียกเก็บค่าธรรมเนียมจากผู้ชำระเงิน)</span>
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td width="10%" rowspan="2" style="border-left: 0.5px solid gray;text-align:center;">
                <img src="img/favicon_rmu-03.png" width="60px">
            </td>
            <td width="45%" rowspan="2">
                <p>
                    <span style="font-size:16pt;font-weight:bold;">มหาวิทยาลัยราชภัฏมหาสารคาม</span><br />
                    <span style="font-size: 13pt;">เลขที่ 80 ถนนนครสวรรค์ ตำบลตลาด<br />อำเภอเมืองมหาสารคาม
                        จังหวัดมหาสารคาม 44000</span><br />
                    <span style="font-size: 13pt;">เพื่อเข้าบัญชี มหาวิทยาลัยราชภัฏมหาสารคาม</span>
                </p>
            </td>
            <td width="45%" style="font-weight:bold;border-right: 0.5px solid gray;font-size:12pt;">
                กำหนดชำระเงินภายในวันที่ {{ DateThaiFull($plan->confirm_open) }} -
                {{ DateThaiFull($plan->confirm_close) }}
            </td>
        </tr>
        <tr>
            <td style="border: 0.5px solid gray;">
                <span style="font-weight: bold;">&nbsp;&nbsp;ชื่อ / Name :</span> {{ $users->person_fname }}
                {{ $users->person_lname }}<br />
                <span style="font-weight: bold;">&nbsp;&nbsp;รหัสผู้เข้าสมัคร / Ref No.1 :
                </span>{{ $register->reg_code }}<br />
                <span style="font-weight: bold;">&nbsp;&nbsp;เลขที่อ้างอิง / Ref No.2 :
                </span>{{ $register->reg_student }}<br />
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td width="7%" style="border-left: 0.5px solid gray;text-align:center;">
                <img src="img/Square.png" width="20px">
            </td>
            <td width="93%" style="border-right: 0.5px solid gray;">
                <div id="photo" style="text-align: center">
                    <img style="vertical-align:middle" src="img/b_1.png" height="20px" alt="">
                    <span style="vertical-align:middle"> บมจ.ธนาคารกรุงเทพ Br.No. 0298&nbsp;&nbsp;&nbsp;&nbsp;Service
                        Code: RMUENT&nbsp;&nbsp;&nbsp;&nbsp;Comp Code: 12334</span>
                </div>
                <span style="font-size:10pt">( ค่าธรรมเนียมไม่เกิน 15 บาทต่อรายการ ในช่องทางอิเล็กทรอนิกส์ และไม่เกิน
                    15 บาทต่อรายการ ในเขต หรือ ไม่เกิน 30 บาทต่อรายการ ข้ามเขต ในช่องทางสาขา )</span>
            </td>
        </tr>
        <tr>
            <td width="7%" style="border-left: 0.5px solid gray;text-align:center;"></td>
            <td width="93%" style="border-right: 0.5px solid gray;"></td>
        </tr>
        <tr>
            <td width="7%" style="border-left: 0.5px solid gray;text-align:center;">
                <img src="img/Square.png" width="20px">
            </td>
            <td width="93%" style="border-right: 0.5px solid gray;">
                <div id="photo" style="text-align: center">
                    <span style="vertical-align:middle">ธนาคารอื่น ๆ ที่ให้บริการชําระบิล* (Biller ID :
                        099400040150770) </span>
                    <img style="vertical-align:middle" src="img/b_2.png" height="25px" alt="">
                </div>
                <span style="font-size:10pt">( ค่าธรรมเนียมไม่เกิน 15 บาทต่อรายการ ในช่องทางอิเล็กทรอนิกส์ และไม่เกิน
                    20 บาทต่อรายการ ในช่องทางสาขา )</span>
            </td>
        </tr>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th class="text-center" style="font-size:11pt;padding: 0.1rem 0.1rem !important;">รายการ</th>
                <th class="text-center" style="font-size:11pt;padding: 0.1rem 0.1rem !important;">เลขที่</th>
                <th class="text-center" style="font-size:11pt;padding: 0.1rem 0.1rem !important;">ธนาคาร-สาขา</th>
                <th class="text-center" style="font-size:11pt;padding: 0.1rem 0.1rem !important;">จำนวนเงิน (บาท)</th>
            </tr>
        </thead>
        <tbody>


            <tr>
                <td width="25%" style="text-align:center;padding: 0.1rem 0.1rem !important;">

                    <div id="photo" style="text-align: center">
                        <img style="vertical-align:middle" src="img/Square.png" height="15px" alt="">
                        <span style="vertical-align:middle">&nbsp;&nbsp;&nbsp;เงินสด</span>&nbsp;&nbsp;&nbsp;&nbsp;
                        <img style="vertical-align:middle" src="img/Square.png" height="15px" alt="">
                        <span style="vertical-align:middle">&nbsp;&nbsp;&nbsp;เช็ค</span>
                    </div>

                </td>
                <td width="25%">

                </td>

                <td width="25%">

                </td>

                <td width="25%" style="text-align:center;padding: 0.1rem 0.1rem !important;">
                    @php($payment_amount = substr($plan->confirm_amount, 0, -2))
                    {{ number_format($payment_amount) }}
                </td>
            </tr>
            <tr>
                <td width="25%" style="text-align:center;font-size:11pt;padding: 0.1rem 0.1rem !important;">
                    จำนวนเงินที่เป็นตัวอักษร
                </td>
                <td width="25%" colspan="3"
                    style="text-align:center;font-size:12pt;padding: 0.1rem 0.1rem !important;">
                    {{ $plan->confirm_amount_text }}
                </td>
            </tr>

        </tbody>
    </table>

    @php($generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG())


    <table style="margin-top: 10px;" width="100%">
        <tr>
            <td width="40%" style="text-align:left;">
                <img style="padding:7px;border:0.5px solid #021a40;background-color:#fff;" width="80px"
                    height="80px" src="data:image/png;base64,  {!! base64_encode($qrCodeDisplay) !!}">
            </td>
            <td width="60%" style="text-align:center;">
                <img width="450px" height="40px"
                    src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode('|099400040150770' . "\n" . $register->reg_code . "\n" . $register->reg_student . "\n" . $plan->confirm_amount . '', $generatorPNG::TYPE_CODE_128)) }}">
                <br />
                |<span style="font-size: 11pt;">I099400040150770 {{ $register->reg_code }}
                    {{ $register->reg_student }} {{ $plan->confirm_amount }}</span>
            </td>
        </tr>
    </table>


    <htmlpagefooter name="page-footer">
        <table style="margin-top: 20px;" width="100%">
            <tr>
                <td width="7%" style="font-size:12pt;">ชื่อผู้นำฝาก</td>
                <td width="23%"
                    style="border-bottom: 1px dotted gray;text-align:center;font-weight:bold;font-size:12pt;"></td>
                <td width="3%" style="font-size:12pt;">โทร.</td>
                <td width="22%"
                    style="border-bottom: 1px dotted gray;text-align:center;font-weight:bold;font-size:12pt;"></td>
                <td width="20%" style="font-size:12pt;">สำหรับเจ้าหน้าที่ธนาคาร ผู้รับเงิน</td>
                <td width="25%"
                    style="border-bottom: 1px dotted gray;text-align:center;font-weight:bold;font-size:12pt;"></td>
            </tr>
        </table>
    </htmlpagefooter>

</body>

</html>
