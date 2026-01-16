<?php
global $conn;

/* DEMO ADMIN SESSION - FOR TESTING ONLY */
if (!isset($_SESSION['role_id'])) {
    $_SESSION['role_id'] = 1; // Treat as admin
    $_SESSION['name'] = 'Demo';
    $_SESSION['surname'] = 'Admin';
    $_SESSION['email'] = 'admin@example.com';
}

if ($_SESSION['role_id'] !== 1) {
    header("Location: profile.php");
     exit;
}

require_once "includes/login/header.php";

/* Fetch roles */
$query = "SELECT role_id, role_name FROM roles ORDER BY role_id ASC";
$result = mysqli_query($conn, $query);
if (!$result) { die(mysqli_error($conn)); }
?>

<body class="roles-page">

<h2 class="mb-3">🛡 Roles Management</h2>

<button class="btn btn-add mb-3" data-toggle="modal" data-target="#addRoleModal">
    + Add Role
</button>

<table class="table table-bordered role-table">
    <thead style="background:#d8f3dc;">
    <tr>
        <th>ID</th>
        <th>Role list</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr id="row_<?= $row['role_id'] ?>">
            <td><?= $row['role_id'] ?></td>
            <td class="role_name"><?= htmlspecialchars($row['role_name']) ?></td>
            <td>
                <button class="btn btn-sm btn-edit edit-btn" data-id="<?= $row['role_id'] ?>">Edit</button>
                <button class="btn btn-sm btn-delete delete-btn" data-id="<?= $row['role_id'] ?>">Delete</button>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php
require_once "includes/login/copyright.php";
?>

<!-- ADD ROLE MODAL -->
<div class="modal fade" id="addRoleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Add Role</h5></div>
            <div class="modal-body">
                <input type="text" id="role_add" class="form-control" placeholder="Role name">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-success" id="addRoleBtn">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT ROLE MODAL -->
<div class="modal fade" id="editRoleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Edit Role</h5></div>
            <div class="modal-body">
                <input type="hidden" id="role_id">
                <input type="text" id="role_edit" class="form-control">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-success" id="saveRoleBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
        $(document).ready(function () {

        /* DataTable */
        var table = $('.role-table').DataTable({
        pageLength: 10,
    });

        /* ADD ROLE */
        $("#addRoleBtn").click(function () {

        var roleName = $("#role_add").val().trim();

        if (roleName.length < 3) {
        toastr.error("Role name must be at least 3 characters");
        return;
    }

        $.post("roles/ajax.php", {
        action: "add_role",
        role_name: roleName
    }, function (resp) {

        if (resp.status === 200) {
        toastr.success(resp.message);

        $('#addRoleModal').modal('hide');
        $("#role_add").val("");

        location.reload();

    } else {
        toastr.warning(resp.message);
    }
    }, "json");
    });

        /*OPEN EDIT MODAL*/
        $(document).on("click", ".edit-btn", function () {

        var row = $(this).closest("tr");
        var roleId = $(this).data("id");
        var roleName = row.find(".role_name").text();

        $("#role_id").val(roleId);
        $("#role_edit").val(roleName);

        $("#editRoleModal").modal("show");
    });

        /*SAVE EDIT */
        $("#saveRoleBtn").click(function () {

        var roleId = $("#role_id").val();
        var roleName = $("#role_edit").val().trim();

        if (roleName.length < 3) {
        toastr.error("Role name must be at least 3 characters");
        return;
    }

        $.post("roles/ajax.php", {
        action: "update_role",
        role_id: roleId,
        role_name: roleName
    }, function (resp) {

        if (resp.status === 200) {
        toastr.success(resp.message);

        $("#row_" + roleId).find(".role_name").text(roleName);

        $("#editRoleModal").modal("hide");

    } else {
        toastr.warning(resp.message);
    }
    }, "json");
    });

        /*DELETE ROLE */
        $(document).on("click", ".delete-btn", function () {

        if (!confirm("Are you sure you want to delete this role?")) return;

        var roleId = $(this).data("id");

        $.post("roles/ajax.php", {
        action: "delete_role",
        role_id: roleId
    }, function (resp) {

        if (resp.status === 200) {
        toastr.success(resp.message);

        table.row($("#row_" + roleId)).remove().draw(false);

    } else {
        toastr.warning(resp.message);
    }
    }, "json");
    });

    });
</script>
</body>
</html>
