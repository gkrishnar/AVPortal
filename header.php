<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-outline-dark" id="menu-toggle">
            <i class="bi bi-list"></i> Menu
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <span class="nav-link fw-bold text-dark">
                        Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        var toggleButton = document.getElementById("menu-toggle");
        var sidebar = document.getElementById("sidebar-wrapper");
        var content = document.getElementById("page-content-wrapper");

        toggleButton.onclick = function () {
            // Toggle sidebar visibility using margin
            if (sidebar.style.marginLeft === "-250px") {
                sidebar.style.marginLeft = "0";
                content.style.marginLeft = "250px";
            } else {
                sidebar.style.marginLeft = "-250px";
                content.style.marginLeft = "0";
            }
        };
    });
</script>