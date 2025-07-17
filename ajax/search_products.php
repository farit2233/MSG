<?php
require_once('../config.php');

/* ---------------- ตรวจสอบการเชื่อมต่อ DB ---------------- */
if (!isset($conn)) {
    die("Database connection not established.");
}

/* ---------------- จัดเรียงลำดับ ---------------- */
$order_by = "`date_created` DESC";
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'date_asc':
            $order_by = "`date_created` ASC";
            break;
        case 'price_asc':
            $order_by = "`price` ASC";
            break;
        case 'price_desc':
            $order_by = "`price` DESC";
            break;
        case 'name_asc':
            $order_by = "`name` ASC";
            break;
        case 'name_desc':
            $order_by = "`name` DESC";
            break;
        default:
            $order_by = "`date_created` DESC";
    }
}

/* ---------------- เงื่อนไข WHERE ---------------- */
$where_clauses = ["status = 1", "delete_flag = 0"];

/* --- หมวดหลักเท่านั้น (ไม่มีหมวดเพิ่มเติม) --- */
if (isset($_GET['cid']) && is_numeric($_GET['cid'])) {
    $cid = intval($_GET['cid']);
    $where_clauses[] = "product_list.category_id = {$cid}";
}

/* --- คำค้นหา (search) --- */
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clauses[] = "(product_list.name LIKE '%{$search}%' OR product_list.description LIKE '%{$search}%')";
}

/* รวม WHERE */
$where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

/* ---------------- ดึงรายการสินค้า ---------------- */
$qry = $conn->query("
    SELECT *,
       (
         COALESCE((SELECT SUM(quantity) FROM stock_list WHERE product_id = product_list.id), 0)
       - COALESCE((SELECT SUM(quantity) FROM order_items WHERE product_id = product_list.id), 0)
       ) AS available
    FROM product_list
    {$where_sql}
    ORDER BY {$order_by}
");

/* ---------------- สร้าง HTML ---------------- */
ob_start();

if ($qry->num_rows > 0):
    while ($row = $qry->fetch_assoc()):
        $in_stock   = $row['available'] > 0;
        $stock_class = $in_stock ? '' : 'out-of-stock';
?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex" style="margin-top: 1rem;">
            <a class="card rounded-0 shadow product-item text-decoration-none text-reset h-100 <?= $stock_class ?>"
                href="./?p=products/view_product&id=<?= $row['id'] ?>">
                <div class="position-relative">
                    <div class="img-top position-relative product-img-holder">
                        <?php if (!$in_stock): ?>
                            <div class="out-of-stock-label">สินค้าหมด</div>
                        <?php endif; ?>
                        <img src="<?= validate_image($row['image_path']) ?>" alt="" class="product-img">
                    </div>
                </div>
                <div class="card-body">
                    <div style="line-height:1em">
                        <div class="card-title w-100 mb-0"><?= $row['name'] ?></div>
                        <div class="d-flex justify-content-between w-100 mb-3">
                            <div class=""><small class="text-muted"><?= $row['brand'] ?></small></div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <?php if (!is_null($row['discounted_price']) && $row['discounted_price'] < $row['price']): ?>
                                <div class="text-end">
                                    <div>
                                        <span class="banner-price fw-bold"><?= format_num($row['discounted_price'], 2) ?> ฿</span>
                                    </div>
                                    <div>
                                        <small class="text-muted"><del><?= format_num($row['price'], 2) ?> ฿</del></small>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="banner-price"><?= format_num($row['price'], 2) ?> ฿</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php
    endwhile;
else:
    ?>
    <div class="col-12 text-center py-5">
        <p>ไม่พบสินค้าที่ตรงกับเงื่อนไข</p>
    </div>
<?php
endif;

echo ob_get_clean();
?>