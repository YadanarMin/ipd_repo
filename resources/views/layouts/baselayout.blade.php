<!DOCTYPE html>
<html lang="ja">
<head>
<title>@yield('title')</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="stylesheet" href="{{ asset('/public/css/style.css') }}">
<script type="text/javascript" src="/iPD/public/js/forgelogin.js"></script>
<script type="text/javascript" src="/iPD/public/js/box.js"></script>
<script type="text/javascript" src="/iPD/public/js/common.js"></script>
<meta name="viewport" content="width=device-width,initial-scale=1">
@yield('head')
</head>
<body>
    <div id="page-content">
        @include('layouts.header')
        @yield('content')
        @include('layouts.footer')
    </div>
</body>
</html>