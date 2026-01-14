function loadUsers() {
    $.post('ajax.php', {action: 'get_users'}, function(res){
        console.log("AJAX response:", res);
    }, 'json');
}
