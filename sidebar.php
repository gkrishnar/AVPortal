<div class="bg-dark text-white border-end" id="sidebar-wrapper" style="min-height: 100vh; width: 250px; position: fixed; z-index: 1000; transition: margin 0.3s;">
    <div class="sidebar-heading text-center py-4 fs-4 fw-bold border-bottom">
        Gate Pass
    </div>
    <div class="list-group list-group-flush my-3">
        <a href="index.php" class="list-group-item list-group-item-action bg-transparent text-white fw-bold">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="create_pass.php" class="list-group-item list-group-item-action bg-transparent text-white fw-bold">
            <i class="bi bi-plus-circle me-2"></i> New Gate Pass
        </a>
        <a href="reports.php" class="list-group-item list-group-item-action bg-transparent text-white fw-bold">
            <i class="bi bi-table me-2"></i> Daily Reports
        </a>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <div class="sidebar-heading text-uppercase text-white-50 fs-6 fw-bold px-3 mt-4 mb-2">Admin</div>
            <a href="purposes.php" class="list-group-item list-group-item-action bg-transparent text-white fw-bold">
                <i class="bi bi-gear me-2"></i> Manage Purposes
            </a>
            <a href="audit_logs.php" class="list-group-item list-group-item-action bg-transparent text-white fw-bold">
                <i class="bi bi-journal-text me-2"></i> Audit Logs
            </a>
        <?php endif; ?>

        <a href="logout.php" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold mt-4 border-top border-secondary">
            <i class="bi bi-box-arrow-left me-2"></i> Logout
        </a>
    </div>
</div>