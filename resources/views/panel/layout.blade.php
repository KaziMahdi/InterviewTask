<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$title}} || Interview Test</title>

    <!-- favicon -->
    @include('panel.elements.style')
    <!-- dark css -->
@stack('style')
</head>

<body>

<!-- preloader area start -->
<div class="preloader" id="preloader">
    <div class="preloader-inner">
        <div class="loader_bars">
            <span></span>
        </div>
    </div>
</div>
<!-- preloader area end -->

<!-- Dashboard start -->
<div class="body-overlay"></div>
<div class="dashboard__area">
    <div class="container-fluid p-0">
        <div class="dashboard__contents__wrapper">
           @include('panel.partials.left_sidebar')
            <div class="dashboard__right">
               @include('panel.partials.header')
                <div class="dashboard__body posPadding">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Dashboard end -->



@include('panel.elements.script')

@stack('script')
</body>

</html>
