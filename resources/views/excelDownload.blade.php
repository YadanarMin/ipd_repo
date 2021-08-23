@extends('layouts.baselayout')
@section('title', 'ExcelExport Testing')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
<style>
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 auto;
	width:90%;
	display : center;
} 

</style>
@endsection
@section('content')
@include('layouts.loading')
<div class="main-content">
    <h3>Excel Export</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Khaing</td>
                <td>khaing@gmail.com</td>
                <td>Osaka</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection