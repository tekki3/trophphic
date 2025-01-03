<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h1>Create New User</h1>
                
                <form method="POST" action="/users/store">
                    <?= CSRF::getTokenField() ?>
                    
                    <div>
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?= old('name') ?>">
                        <?php if ($errors->has('name')): ?>
                            <span class="error"><?= $errors->first('name') ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Create User</button>
                        <a href="/users" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 