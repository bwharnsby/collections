<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    </head>
    <body>
        <div>
            <div class="content">
                @foreach($schedule as $week_no => $fixtures)
                    <h2>Week {{$week_no+1}}</h2>
                    <ul>
                    @foreach($fixtures as $fixRow)
                         <li>{{
                            $fixRow['Home'] . ' ' .
                            $fixRow['HomeScore'] . '-' .
                            $fixRow['AwayScore'] . ' ' .
                            $fixRow['Away']
                         }}</li>
                    @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    </body>
</html>
