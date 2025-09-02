<?php
require_once('../config.php');

if (!isset($conn)) {
    die("Database connection not established.");
}

// Pagination
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// รับค่าค้นหา
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Query หา Total
$count_sql = "
    SELECT COUNT(*) as total_products
    FROM product_list
    WHERE status = 1 AND delete_flag = 0
    AND (name LIKE '%{$search}%' OR brand LIKE '%{$search}%')
";
$count_qry = $conn->query($count_sql);
$total_products = $count_qry->fetch_assoc()['total_products'];
$total_pages = ceil($total_products / $limit);

// Query สินค้า
$qry_str = "
    SELECT pl.*,
        (
            COALESCE((SELECT SUM(quantity) FROM stock_list WHERE product_id = pl.id), 0)
            - COALESCE((SELECT SUM(quantity) FROM order_items WHERE product_id = pl.id), 0)
        ) AS available
    FROM product_list pl
    WHERE pl.status = 1 AND pl.delete_flag = 0
    AND (pl.name LIKE '%{$search}%' OR pl.brand LIKE '%{$search}%')
    ORDER BY pl.date_created DESC
    LIMIT {$limit} OFFSET {$offset}
";
$qry = $conn->query($qry_str);

// ดึงสินค้าใหม่ล่าสุด 4 ชิ้น
$newest_ids = [];
$newest_qry = $conn->query("
    SELECT id FROM product_list
    WHERE status = 1 AND delete_flag = 0
    ORDER BY date_created DESC
    LIMIT 4
");
while ($r = $newest_qry->fetch_assoc()) {
    $newest_ids[] = $r['id'];
}

// ฟังก์ชันราคา
if (!function_exists('format_price_custom')) {
    function format_price_custom($price)
    {
        $formatted_price = number_format($price, 2);
        return substr($formatted_price, -3) == '.00' ? number_format($price, 0) : $formatted_price;
    }
}

// เริ่มแสดงผล
ob_start();
?>

<?php if ($qry->num_rows > 0): ?>
    <?php while ($row = $qry->fetch_assoc()):
        $in_stock = $row['available'] > 0;
        $stock_class = $in_stock ? '' : 'out-of-stock';
    ?>
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 d-flex" style="margin-top: 1rem;">
            <a class="card rounded-0 product-item text-decoration-none text-reset h-100 <?= $stock_class ?>"
                href="./?p=products/view_product&id=<?= $row['id'] ?>">
                <div class="position-relative">
                    <div class="img-top position-relative product-img-holder">
                        <?php if (!$in_stock): ?>
                            <div class="out-of-stock-label">สินค้าหมด</div>
                        <?php endif; ?>

                        <?php if (in_array($row['id'], $newest_ids)): ?>
                            <div class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 small" style="z-index: 1;">
                                ใหม่
                            </div>
                        <?php endif; ?>

                        <img src="<?= validate_image($row['image_path']) ?>" alt="<?= $row['name'] ?>" class="product-img">
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <div>
                        <div class="card-title w-100 mb-0"><?= $row['name'] ?></div>
                        <div class="d-flex justify-content-between w-100 mb-3" style="height: 2.5em; overflow: hidden;">
                            <div class="w-100">
                                <small class="text-muted" style="line-height: 1.25em; display: block;"><?= $row['brand'] ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center mt-auto">
                        <?php
                        if (!is_null($row['discounted_price']) && $row['discounted_price'] > 0 && $row['discounted_price'] < $row['price']) {
                            $discount_percentage = round((($row['price'] - $row['discounted_price']) / $row['price']) * 100);
                            echo '<span class="banner-price fw-bold me-2">' . format_price_custom($row['discounted_price']) . ' ฿</span>';
                            echo '<span class="badge badge-sm text-white">- ' . $discount_percentage . '%</span>';
                        } elseif (!is_null($row['vat_price']) && $row['vat_price'] > 0) {
                            echo '<span class="banner-price">' . format_price_custom($row['vat_price']) . ' ฿</span>';
                        } else {
                            echo '<span class="banner-price">' . format_price_custom($row['price']) . ' ฿</span>';
                        }
                        ?>
                    </div>
                </div>
            </a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="col-12 text-center py-5">
        <p>ไม่พบสินค้าที่ตรงกับเงื่อนไข</p>
    </div>
<?php endif; ?>

<?php if ($total_pages > 1): ?>
    <div class="col-12 d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?= $page - 1 ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="javascript:void(0)" data-page="<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?= $page + 1 ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>

<?php
echo ob_get_clean();
?>