<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="col-md-6 offset-md-3">
            <h1>Create New User</h1>
            
            <?php $errors = errors(); ?>
            
            <form method="POST" action="/users/store">
                <?= \Trophphic\Core\Security\CSRF::getTokenField() ?>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                    <?php if ($errors->has('name')): ?>
                        <span class="text-danger"><?= $errors->first('name') ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                    <?php if ($errors->has('email')): ?>
                        <span class="text-danger"><?= $errors->first('email') ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <?php if ($errors->has('password')): ?>
                        <span class="text-danger"><?= $errors->first('password') ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
                
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Create User</button>
                    <a href="/users" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 