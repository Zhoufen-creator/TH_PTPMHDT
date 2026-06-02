<!DOCTYPE html>
<html>
<head>
    <title>Quản lý tài khoản</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h2>Quản lý quyền tài khoản</h2>

    <table class="table table-bordered mt-4">

        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Họ tên</th>
            <th>Role hiện tại</th>
            <th>Cập nhật</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach ($accounts as $account): ?>

            <tr>

                <td><?= $account['id'] ?></td>

                <td><?= htmlspecialchars($account['username']) ?></td>

                <td><?= htmlspecialchars($account['fullname']) ?></td>

                <td><?= $account['role'] ?></td>

                <td>

                    <form method="post"
                          action="/account/updateRole">

                        <input type="hidden"
                               name="username"
                               value="<?= htmlspecialchars($account['username']) ?>">

                        <select name="role"
                                class="form-select">

                            <option value="user"
                                <?= $account['role'] == 'user' ? 'selected' : '' ?>>
                                User
                            </option>

                            <option value="admin"
                                <?= $account['role'] == 'admin' ? 'selected' : '' ?>>
                                Admin
                            </option>

                        </select>

                        <button class="btn btn-primary mt-2">
                            Cập nhật
                        </button>

                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

</body>
</html>