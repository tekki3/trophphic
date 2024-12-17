<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Trophphic\Core\Security::sanitizeOutput($title); ?></title>
</head>
<body>
    <h1>Welcome to the Home Page</h1>
    <form method="POST" action="/submit">
    <input type="hidden" name="csrf_token" value="<?php echo Trophphic\Core\Security::getCsrfToken(); ?>">
    <button type="submit">Submit</button>
</form>

</body>
</html>