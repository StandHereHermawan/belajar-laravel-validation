<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
</head>

<body>
    <div>
        <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->

        @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        @endif

        <!-- form[action="/form"][method="post"]>(((label{Username : }>input[type=text][value=username])+br)+((label{Username : }>input[type=text][value=username])+br)+input[type=submit][value=login]) -->

        <form action="/form" method="post">
            @csrf
            <label>Username : <input type="text" name="username" /> </label>
            <br />
            <label>Password : <input type="password" name="password" /> </label>
            <br />
            <input type="submit" value="login" />
        </form>
    </div>

</body>

</html>