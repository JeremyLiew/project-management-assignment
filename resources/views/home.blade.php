@extends('layouts.app')

@section('content')
<html lang="en">
    <body>
        <div id="content">
            <h1>Welcome</h1>
            <p>This is a simple page.</p>
        </div>

        <select id="languageSelect">
            <option value="english">English</option>
            <option value="malay">Malay</option>
            <option value="chinese">Chinese</option>
        </select>

        <script>
            $('#languageSelect').change(function () {
                var language = $(this).val();
                $.get('/change-language/' + language, function (data) {
                    $('#content').html(data);
                });
            });
        </script>
    </body>
</html>
@endsection
