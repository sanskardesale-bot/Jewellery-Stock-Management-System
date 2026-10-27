<?php
/**
 * JewelTrack — Dashboard
 * All statistics from real MySQL queries.
 */

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/includes/header.php';

$db = getDB();
$sym = currencySymbol();

// ── Stat Cards ─────────────────────────────────────────────────────────────
$totalProducts   = $db->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$totalPieces     = $db->query("SELECT COALESCE(SUM(quantity),0) FROM products WHERE is_active=1")->fetchColumn();
$totalCostValue  = $db->query("SELECT COALESCE(SUM(quantity*purchase_cost),0) FROM products WHERE is_active=1")->fetchColumn();
$todaySales      = $db->query("SELECT COALESCE(SUM(grand_total),0) FROM sales WHERE sale_date=CURDATE()")->fetchColumn();
$todayPurchases  = $db->query("SELECT COALESCE(SUM(grand_total),0) FROM purchases WHERE purchase_date=CURDATE()")->fetchColumn();
$lowStockCount   = $db->query("SELECT COUNT(*) FROM products WHERE quantity<=reorder_level AND quantity>0 AND is_active=1")->fetchColumn();
$outOfStockCount = $db->query("SELECT COUNT(*) FROM products WHERE quantity=0 AND is_active=1")->fetchColumn();

// ── 7-day Sales Chart ───────────────────────────────────────────────────────
$salesLabels = [];
$salesValues = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $salesLabels[] = date('d M', strtotime($date));
    $stmt = $db->prepare("SELECT COALESCE(SUM(grand_total),0) FROM sales WHERE sale_date=?");
    $stmt->execute([$date]);
    $salesValues[] = (float)$stmt->fetchColumn();
}

// ── Category Distribution ───────────────────────────────────────────────────
$catRows = $db->query(
    "SELECT c.name, COUNT(p.id) AS cnt
     FROM categories c
     LEFT JOIN products p ON p.category_id=c.id AND p.is_active=1
     GROUP BY c.id, c.name ORDER BY cnt DESC"
)->fetchAll();
$catLabels = array_column($catRows, 'name');
$catValues = array_map('intval', array_column($catRows, 'cnt'));

// ── Recent Sales ────────────────────────────────────────────────────────────
$recentSales = $db->query(
    "SELECT s.invoice_number, s.sale_date, s.grand_total, s.payment_status,
            COALESCE(c.name,'Walk-in') AS customer_name,
            u.name AS staff_name
     FROM sales s
     LEFT JOIN customers c ON c.id=s.customer_id
     JOIN users u ON u.id=s.user_id
     ORDER BY s.created_at DESC LIMIT 5"
)->fetchAll();

// ── Recent Purchases ────────────────────────────────────────────────────────
$recentPurchases = $db->query(
    "SELECT p.ref_number, p.purchase_date, p.grand_total, p.payment_status,
            s.name AS supplier_name
     FROM purchases p
     JOIN suppliers s ON s.id=p.supplier_id
     ORDER BY p.created_at DESC LIMIT 5"
)->fetchAll();

// ── Low Stock Alerts ────────────────────────────────────────────────────────
$lowStockItems = $db->query(
    "SELECT p.name, p.sku, p.quantity, p.reorder_level, c.name AS category
     FROM products p JOIN categories c ON c.id=p.category_id
     WHERE p.quantity <= p.reorder_level AND p.is_active=1
     ORDER BY p.quantity ASC LIMIT 8"
)->fetchAll();
?>

<!-- Page Header -->
<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-speedometer2 text-gold me-2"></i>Dashboard</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active">Overview</li>
      </ol>
    </nav>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="/jeweltrack/modules/products/create.php" class="btn btn-gold btn-sm">
      <i class="bi bi-plus-lg me-1"></i>Add Product
    </a>
    <a href="/jeweltrack/modules/purchases/create.php" class="btn btn-emerald btn-sm">
      <i class="bi bi-cart-plus me-1"></i>Record Purchase
    </a>
    <a href="/jeweltrack/modules/sales/create.php" class="btn btn-outline-gold btn-sm">
      <i class="bi bi-receipt-cutoff me-1"></i>New Sale
    </a>
  </div>
</div>

<!-- ── STAT CARDS ─────────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon emerald"><i class="bi bi-grid-3x3-gap-fill"></i></div>
      <div class="stat-body">
        <div class="stat-label">Active Products</div>
        <div class="stat-value"><?= number_format((int)$totalProducts) ?></div>
        <div class="stat-sub">SKUs in catalogue</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon gold"><i class="bi bi-boxes"></i></div>
      <div class="stat-body">
        <div class="stat-label">Total Pieces</div>
        <div class="stat-value"><?= number_format((int)$totalPieces) ?></div>
        <div class="stat-sub">Available inventory</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon purple"><i class="bi bi-currency-rupee"></i></div>
      <div class="stat-body">
        <div class="stat-label">Inventory Value</div>
        <div class="stat-value" style="font-size:1.05rem;"><?= formatMoney((float)$totalCostValue, $sym) ?></div>
        <div class="stat-sub">At cost price</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon teal"><i class="bi bi-bag-check"></i></div>
      <div class="stat-body">
        <div class="stat-label">Today's Sales</div>
        <div class="stat-value" style="font-size:1.05rem;"><?= formatMoney((float)$todaySales, $sym) ?></div>
        <div class="stat-sub"><?= date('d M Y') ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon info"><i class="bi bi-box-arrow-in-down"></i></div>
      <div class="stat-body">
        <div class="stat-label">Today's Purchases</div>
        <div class="stat-value" style="font-size:1.05rem;"><?= formatMoney((float)$todayPurchases, $sym) ?></div>
        <div class="stat-sub"><?= date('d M Y') ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="stat-card">
      <div class="stat-icon danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
      <div class="stat-body">
        <div class="stat-label">Low / Out of Stock</div>
        <div class="stat-value"><?= (int)$lowStockCount ?> / <?= (int)$outOfStockCount ?></div>
        <div class="stat-sub">Products need attention</div>
      </div>
    </div>
  </div>
</div>

<!-- ── CHARTS ROW ─────────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="jt-card h-100">
      <div class="jt-card-header">
        <h2><i class="bi bi-bar-chart-line me-2 text-gold"></i>Last 7 Days — Sales Revenue</h2>
      </div>
      <div class="jt-card-body">
        <div class="chart-container" style="height:240px;">
          <canvas id="salesChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="jt-card h-100">
      <div class="jt-card-header">
        <h2><i class="bi bi-pie-chart me-2 text-gold"></i>Inventory by Category</h2>
      </div>
      <div class="jt-card-body">
        <div class="chart-container" style="height:240px;">
          <canvas id="categoryChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── TABLES ROW ─────────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">

  <!-- Recent Sales -->
  <div class="col-lg-6">
    <div class="jt-card">
      <div class="jt-card-header">
        <h2><i class="bi bi-receipt me-2 text-gold"></i>Recent Sales</h2>
        <a href="/jeweltrack/modules/sales/index.php" class="btn btn-sm btn-outline-gold">View All</a>
      </div>
      <div class="table-responsive">
        <?php if ($recentSales): ?>
        <table class="jt-table">
          <thead>
            <tr>
              <th>Invoice</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentSales as $s): ?>
            <tr>
              <td>
                <a href="/jeweltrack/modules/sales/view.php?id=<?= $s['invoice_number'] ?>" class="fw-600 text-emerald">
                  <?= e($s['invoice_number']) ?>
                </a>
              </td>
              <td><?= e($s['customer_name']) ?></td>
              <td class="text-muted fs-13"><?= date('d M Y', strtotime($s['sale_date'])) ?></td>
              <td class="fw-600"><?= formatMoney((float)$s['grand_total'], $sym) ?></td>
              <td>
                <?php
                $sBadge = match($s['payment_status']) {
                  'paid'    => 'success',
                  'partial' => 'warning',
                  default   => 'secondary',
                };
                ?>
                <span class="badge bg-<?= $sBadge ?>"><?= ucfirst(e($s['payment_status'])) ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state"><i class="bi bi-receipt-cutoff d-block"></i><p>No sales yet</p></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Recent Purchases -->
  <div class="col-lg-6">
    <div class="jt-card">
      <div class="jt-card-header">
        <h2><i class="bi bi-cart-plus me-2 text-gold"></i>Recent Purchases</h2>
        <a href="/jeweltrack/modules/purchases/index.php" class="btn btn-sm btn-outline-gold">View All</a>
      </div>
      <div class="table-responsive">
        <?php if ($recentPurchases): ?>
        <table class="jt-table">
          <thead>
            <tr>
              <th>Ref #</th>
              <th>Supplier</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentPurchases as $p): ?>
            <tr>
              <td>
                <a href="/jeweltrack/modules/purchases/view.php?id=<?= $p['ref_number'] ?>" class="fw-600 text-emerald">
                  <?= e($p['ref_number']) ?>
                </a>
              </td>
              <td><?= e($p['supplier_name']) ?></td>
              <td class="text-muted fs-13"><?= date('d M Y', strtotime($p['purchase_date'])) ?></td>
              <td class="fw-600"><?= formatMoney((float)$p['grand_total'], $sym) ?></td>
              <td>
                <?php
                $pBadge = match($p['payment_status']) {
                  'paid'    => 'success',
                  'partial' => 'warning',
                  default   => 'secondary',
                };
                ?>
                <span class="badge bg-<?= $pBadge ?>"><?= ucfirst(e($p['payment_status'])) ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state"><i class="bi bi-cart d-block"></i><p>No purchases yet</p></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<!-- ── LOW STOCK ALERTS ───────────────────────────────────────────────────── -->
<?php if ($lowStockItems): ?>
<div class="jt-card mb-4">
  <div class="jt-card-header">
    <h2 class="text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Low Stock Alerts</h2>
    <a href="/jeweltrack/modules/products/index.php?filter=low_stock" class="btn btn-sm btn-outline-gold">View All</a>
  </div>
  <div class="table-responsive">
    <table class="jt-table">
      <thead>
        <tr>
          <th>SKU</th><th>Product</th><th>Category</th>
          <th>In Stock</th><th>Reorder At</th><th>Status</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lowStockItems as $item): ?>
        <tr>
          <td class="text-muted fs-13"><?= e($item['sku']) ?></td>
          <td class="fw-600"><?= e($item['name']) ?></td>
          <td><?= e($item['category']) ?></td>
          <td>
            <span class="fw-700 <?= $item['quantity'] == 0 ? 'text-danger' : 'text-warning' ?>">
              <?= (int)$item['quantity'] ?>
            </span>
          </td>
          <td><?= (int)$item['reorder_level'] ?></td>
          <td><?= stockBadge((int)$item['quantity'], (int)$item['reorder_level']) ?></td>
          <td>
            <a href="/jeweltrack/modules/purchases/create.php" class="btn btn-sm btn-emerald">
              <i class="bi bi-cart-plus me-1"></i>Reorder
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Chart data injected from PHP -->
<script>
  const salesData    = <?= json_encode(['labels' => $salesLabels, 'values' => $salesValues]) ?>;
  const categoryData = <?= json_encode(['labels' => $catLabels,   'values' => $catValues])   ?>;
  document.addEventListener('DOMContentLoaded', () => {
    JT.initDashboardCharts(salesData, categoryData);
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
