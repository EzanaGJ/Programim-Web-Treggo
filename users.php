<?php
session_start();
global $conn;
require_once "connect.php";
require_once "includes/login/header.php";

if (!isset($_SESSION['id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role_id'] !== 1) {
    header("Location: profile.php");
    exit;
}

$query_users = "SELECT id, name, surname, email, role_id, email_verified, created_at FROM users";
$result_users = mysqli_query($conn, $query_users);
if (!$result_users) { echo mysqli_error($conn); exit; }

$data = [];
while ($row = mysqli_fetch_assoc($result_users)) {
    $data[$row['id']] = $row;
}

function getRoleName($role_id) {
    return $role_id == 1 ? "Admin" : "User";
}
?>

<body class="roles-page">
<div class="mb-4 p-3 rounded shadow-sm d-flex justify-content-between align-items-center"
     style="background:#1b4332; color:white;">
    <h4 class="mb-0">🛠 Admin Dashboard</h4>
    <span class="badge badge-light px-3 py-2">
<?= isset($_SESSION['name'], $_SESSION['surname']) ?
        htmlspecialchars($_SESSION['name'] . ' ' . $_SESSION['surname']) : '' ?>
</span>

</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Treggo Users</h2>
        <div class="d-flex gap-2">
            <a href="roles.php" class="btn btn-add-user mr-2">
                <i class="fa fa-user"></i> Roles
            </a>

            <button type="button" class="btn btn-add-user mr-2" data-toggle="modal" data-target="#addUserModal">
                <i class="fa fa-plus"></i> Add User
            </button>

            <a href="logout.php" class="btn btn-add-user mr-2">
                <i class="fa fa-user"></i> Log out
            </a>

        </div>
    </div>


    <div class="bg-white p-3 rounded shadow-sm">
        <table class="table table-striped table-bordered user-list-table" style="width:100%;">
            <thead style="background:#d8f3dc; color:#1b4332;">
            <tr>
                <th>ID</th>
                <th>Action</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Email</th>
                <th>Role</th>
                <th>Email Verified</th>
                <th>Registered At</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $id => $user): ?>
                <tr id="row_<?= $id ?>">
                    <td><?= $id ?></td>

                    <td>
                        <button class="btn btn-edit" data-id="<?= $id ?>"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-delete" data-id="<?= $id ?>"><i class="fa fa-trash"></i></button>
                    </td>
                    <td class="name"><?= $user['name'] ?></td>
                    <td class="surname"><?= $user['surname'] ?></td>
                    <td class="email"><?= $user['email'] ?></td>
                    <td class="role"><?= getRoleName($user['role_id']) ?></td>
                    <td class="verified"><?= $user['email_verified'] ? "Yes" : "No" ?></td>
                    <td><?= $user['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header" style="background:#d8f3dc;">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_modal">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="name_modal" class="form-control">
                </div>
                <div class="form-group">
                    <label>Surname</label>
                    <input type="text" id="surname_modal" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email_modal" class="form-control">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select id="role_modal" class="form-control">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="saveUserBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded">
            <div class="modal-header" style="background:#a7d7a7;">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="name_add" class="form-control">
                </div>
                <div class="form-group">
                    <label>Surname</label>
                    <input type="text" id="surname_add" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="email_add" class="form-control">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select id="role_add" class="form-control">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="addUserBtn">Add User</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="js/inactivityLogout.js"></script>
<script>
    $(document).ready(function() {

        var table = $('.user-list-table').DataTable({
        pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
        responsive: true,
        dom: 'Bfrtip',
        buttons: ['copy','csv','excel','pdf','print']
        });

        // Edit
        $(document).on('click', '.btn-edit', function(){
        var id = $(this).data('id');
        console.log("Edit clicked, ID:", id);

        $.post('user/ajax.php', {action:'fillModalData', id:id}, function(resp){
        if(resp.status==200){
        $("#id_modal").val(resp.data.id);
        $("#name_modal").val(resp.data.name);
        $("#surname_modal").val(resp.data.surname);
        $("#email_modal").val(resp.data.email);
        $("#role_modal").val(resp.data.role_id==1?'admin':'user');
        $('#userModal').modal('show');
    } else toastr.warning(resp.message,"Warning");
    }, 'json');
    });

        // Delete
        $(document).on('click', '.btn-delete', function(){
        var id = $(this).data('id');
        if(!confirm("Are you sure you want to delete this user?")) return;

        $.post('user/ajax.php', {action:'delete_user', id:id}, function(resp){
        if(resp.status==200){
        toastr.success(resp.message,"Success");
        table.row($("#row_"+id)).remove().draw(false);
    } else toastr.warning(resp.message,"Warning");
    });
    });

        // Save edited user
        $("#saveUserBtn").click(function(){
        var id = $("#id_modal").val();
        var name = $("#name_modal").val();
        var surname = $("#surname_modal").val();
        var email = $("#email_modal").val();
        var role_id = $("#role_modal").val() === "admin" ? 1 : 2;

        if(!id) { toastr.error("User ID missing"); return; }

        $.post('user/ajax.php', {
        action: 'update_user_data',
        id: id,
        name: name,
        surname: surname,
        email: email,
        role_id: role_id
    }, function(resp){
        if(resp.status === 200){
        toastr.success(resp.message, "Success");
        var row = $("#row_" + id);
        row.find('.name').text(name);
        row.find('.surname').text(surname);
        row.find('.email').text(email);
        row.find('.role').text(role_id === 1 ? "Admin" : "User");
        $('#userModal').modal('hide');
    } else {
        toastr.warning(resp.message, "Warning");
    }
    }, 'json');
    });

        // Add new user
        $(document).on('click', '#addUserBtn', function(){

        var name = $("#name_add").val().trim();
        var surname = $("#surname_add").val().trim();
        var email = $("#email_add").val().trim();
        var role = $("#role_add").val() === "admin" ? 1 : 2;

        if(!name || !surname || !email){
        toastr.error("Please fill all fields.");
        return;
    }

        $.post('user/ajax.php', {
        action:'add_user',
        name:name,
        surname:surname,
        email:email,
        role_id:role
    }, function(resp){
        if(resp.status==200){
        toastr.success(resp.message, "Success");

        $('#addUserModal').modal('hide');
        $('#name_add, #surname_add, #email_add').val('');
        $('#role_add').val('user');

        var newId = resp.data.id;
            var newRowNode = table.row.add([
                newId,
                `<button class="btn btn-edit" data-id="${newId}"><i class="fa fa-edit"></i></button>
     <button class="btn btn-delete" data-id="${newId}"><i class="fa fa-trash"></i></button>`,
                name,
                surname,
                email,
                role===1?"Admin":"User",
                "No",
                new Date().toLocaleString()
            ]).draw(false).node();

            $(newRowNode).attr('id','row_'+newId);

            $(newRow).attr('id','row_'+newId);

    } else {
        toastr.warning(resp.message,"Warning");
    }
    }, 'json');

    });

    });
</script>

</body>
</html>
