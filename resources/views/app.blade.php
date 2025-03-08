<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/app.jsx')
    @routes
    @inertiaHead
</head>

<body style="margin: 0;">
    @inertia
</body>

</html>
