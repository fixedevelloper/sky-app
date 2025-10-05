<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>@yield('title') | {!! config('app.name') !!}</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{!! asset('assets/css/dashlite.min.css') !!}?ver=3.2.3">
    <link id="skin-default" rel="stylesheet" href="{!! asset('assets/css/theme.css') !!}?ver=3.2.3">
</head>

<body class="nk-body bg-white npc-default pg-auth">
<div class="nk-app-root">
    <!-- main @s -->
    <div class="nk-main ">
        <!-- wrap @s -->
        <div class="nk-wrap nk-wrap-nosidebar">
            <!-- content @s -->
        @yield('content')
            <!-- wrap @e -->
        </div>
        <!-- content @e -->
    </div>
    <!-- main @e -->
</div>
<!-- app-root @e -->
<!-- JavaScript -->
<script src="{!! asset('assets/js/bundle.js') !!}?ver=3.2.3"></script>
<script src="{!! asset('assets/js/scripts.js') !!}?ver=3.2.3"></script>


</html>
