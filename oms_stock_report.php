<?php
// oms_stock_report.php - Instant Client-Side Search
require_once 'config.php';
ini_set('display_errors', 1); error_reporting(E_ALL);
checkLogin();

// 1. PERMISSION CHECK
if(!function_exists('hasPermission')) { include 'access_denied.php'; exit; }
if(!hasPermission('oms_stock_report_view')) { include 'access_denied.php'; exit; }

// 2. FILTERS (Category only filters SQL, Search filters HTML)
$cat_filter = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;

// 3. FETCH DATA
$sql = "SELECT i.*, c.name as cat_name 
        FROM oms_items i 
        LEFT JOIN oms_categories c ON i.category_id = c.id ";

if ($cat_filter > 0) {
    $sql .= " WHERE i.category_id = $cat_filter ";
}

$sql .= " ORDER BY c.name ASC, i.name ASC";
$res = $conn->query($sql);

// Fetch Categories for Dropdown
$cats = $conn->query("SELECT * FROM oms_categories ORDER BY name ASC");

$grand_qty = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid px-4">
        <h3 class="text-center mb-4">Current Stock Report</h3>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <div class="card shadow-sm mb-4 bg-light">
                    <div class="card-body py-3">
                        <form method="get" class="row g-2 align-items-center justify-content-center">
                            
                            <div class="col-md-5 col-12">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" id="searchInput" class="form-control" placeholder="Type to search..." autocomplete="off">
                                </div>
                            </div>

                            <div class="col-auto">
                                <select name="cat_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- All Categories --</option>
                                    <?php while($c=$cats->fetch_assoc()): ?>
                                        <option value="<?php echo $c['id']; ?>" <?php echo ($cat_filter==$c['id'])?'selected':''; ?>>
                                            <?php echo $c['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-auto">
                                <a href="oms_stock_report.php" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center fw-bold">Stock Summary</div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-striped mb-0" id="stockTable">
                            <thead class="table-light text-center">
                                <tr>
                                    <th class="text-start">Item Name</th>
                                    <th style="width: 150px;">Stock Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($res && $res->num_rows > 0): ?>
                                    <?php while($row = $res->fetch_assoc()): 
                                        $grand_qty += $row['current_stock'];
                                    ?>
                                    <tr class="item-row">
                                        <td class="fw-bold text-start ps-3 search-item">
                                            <?php echo $row['name']; ?>
                                            <span style="display:none;"><?php echo $row['cat_name']; ?></span> 
                                        </td>
                                        
                                        <td class="text-center <?php echo ($row['current_stock'] < 10) ? 'text-danger fw-bold' : ''; ?>">
                                            <?php echo number_format($row['current_stock']); ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    
                                    <tr class="table-secondary fw-bold" id="totalRow">
                                        <td class="text-end pe-3">TOTAL (Loaded):</td>
                                        <td class="text-center"><?php echo number_format($grand_qty); ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center p-4 text-muted">No items found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input = this.value.toLowerCase();
            var rows = document.querySelectorAll('.item-row');
            
            rows.forEach(function(row) {
                // Look for text in the 'search-item' cell
                var text = row.querySelector('.search-item').textContent.toLowerCase();
                
                if(text.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>