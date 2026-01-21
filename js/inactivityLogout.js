$(document).ready(function() {

    let timeoutDuration = 900000; // 15 minuta = 900,000 ms
    let logoutTimer;

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    function startLogoutTimer() {
        clearTimeout(logoutTimer);
        logoutTimer = setTimeout(() => {
            toastr.info("You have been logged out due to inactivity.");
            setTimeout(() => {
                window.location.href = "login.php";
            }, 1500); // ridrejto pas 1.5 sek
        }, timeoutDuration);
    }

    $(document).on('mousemove keydown click scroll', startLogoutTimer);

    startLogoutTimer();
});

