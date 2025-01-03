<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h1>Edit User</h1>
                
                <?php $errors = errors(); ?>
                
                <form action="/users/update/<?= $user['id'] ?>" method="POST">
                    <?= \Trophphic\Core\Security\CSRF::getTokenField() ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= old('name', $user['name']) ?>" required>
                        <?php if ($errors->has('name')): ?>
                            <span class="text-danger"><?= $errors->first('name') ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= old('email', $user['email']) ?>" required>
                        <?php if ($errors->has('email')): ?>
                            <span class="text-danger"><?= $errors->first('email') ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Leave blank to keep current password">
                        <?php if ($errors->has('password')): ?>
                            <span class="text-danger"><?= $errors->first('password') ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="/users" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 