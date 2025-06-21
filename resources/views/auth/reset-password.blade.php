<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body>
    <h1>Reset Password</h1>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="email" name="email" placeholder="Email" required value="{{ old('email', request()->query('email')) }}">
        <input type="password" name="password" placeholder="New password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>