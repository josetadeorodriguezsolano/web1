<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @section('header')
        @include('layouts.components.header')
        @show
    {{$slot}}
    @section('footer')
        @include('layouts.components.footer')
        @show
</body>
</html>
