<?php
require_once('../config.php');

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($conn)) {
    die("Database connection not established.");
}

// === START: PAGINATION SETUP ===
$limit = 20; // จำนวนสินค้าต่อหน้า
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
// === END: PAGINATION SETUP ===


// การเรียงลำดับ
$order_by = "`pl`.`date_created` DESC";
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'date_asc':
            $order_by = "`pl`.`date_created` ASC";
            break;
        case 'price_asc':
            $order_by = "IF(pl.discounted_price IS NOT NULL AND pl.discounted_price < pl.price, pl.discounted_price, pl.price) ASC";
            break;
        case 'price_desc':
            $order_by = "IF(pl.discounted_price IS NOT NULL AND pl.discounted_price < pl.price, pl.discounted_price, pl.price) DESC";
            break;
        case 'name_asc':
            $order_by = "`pl`.`name` ASC";
            break;
        case 'name_desc':
            $order_by = "`pl`.`name` DESC";
            break;
        default:
            $order_by = "`pl`.`date_created` DESC";
    }
}

// สร้างเงื่อนไขเพิ่มเติมจาก GET
$additional_where = "";
if (isset($_GET['cid']) && is_numeric($_GET['cid'])) {
    $cid = intval($_GET['cid']);
    $additional_where .= " AND pl.category_id = {$cid}";
}
if (isset($_GET['tid']) && is_numeric($_GET['tid'])) {
    $tid = intval($_GET['tid']);
    $additional_where .= " AND cl.product_type_id = {$tid}";
}

// เช็คว่าเลือก promotion_id หรือไม่
if (isset($_GET['pid']) && is_numeric($_GET['pid'])) {
    $pid = intval($_GET['pid']);
    // เพิ่มเงื่อนไขในการดึงสินค้าที่เชื่อมโยงกับ promotion_id
    $additional_where .= " AND pp.promotion_id = {$pid}";
}

// === START: QUERY FOR TOTAL COUNT ===
// สร้าง query เพื่อนับจำนวนสินค้าทั้งหมดที่ตรงตามเงื่อนไข
$count_qry_str = "
    SELECT COUNT(DISTINCT pl.id) as total_products
    FROM product_list pl
    INNER JOIN category_list cl ON pl.category_id = cl.id
    LEFT JOIN promotion_products pp ON pp.product_id = pl.id
    WHERE pl.status = 1 AND pl.delete_flag = 0
    {$additional_where}
";
$count_qry = $conn->query($count_qry_str);
$total_products = $count_qry->fetch_assoc()['total_products'];
$total_pages = ceil($total_products / $limit);
// === END: QUERY FOR TOTAL COUNT ===


// ดึงรายการสินค้า พร้อม JOIN ตาราง category_list และเพิ่ม LIMIT OFFSET
$qry_str = "
    SELECT pl.*,
        (
            COALESCE((SELECT SUM(quantity) FROM stock_list WHERE product_id = pl.id), 0)
            - COALESCE((SELECT SUM(quantity) FROM order_items WHERE product_id = pl.id), 0)
        ) AS available
    FROM product_list pl
    INNER JOIN category_list cl ON pl.category_id = cl.id
    LEFT JOIN promotion_products pp ON pp.product_id = pl.id
    WHERE pl.status = 1 AND pl.delete_flag = 0
    {$additional_where}
    GROUP BY pl.id  -- Group by product id
    ORDER BY {$order_by}
    LIMIT {$limit} OFFSET {$offset} -- เพิ่มส่วนนี้เข้ามา
";
$qry = $conn->query($qry_str);


// ฟังก์ชันแสดงราคาแบบไม่มี .00
if (!function_exists('format_price_custom')) {
    function format_price_custom($price)
    {
        $formatted_price = format_num($price, 2);
        return substr($formatted_price, -3) == '.00' ? format_num($price, 0) : $formatted_price;
    }
}

// แสดง HTML
ob_start();

// ส่วนแสดงผลสินค้า (เหมือนเดิม)
if ($qry->num_rows > 0):
    while ($row = $qry->fetch_assoc()):
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
                        <img src="<?= validate_image($row['image_path']) ?>" alt="<?= $row['name'] ?>" class="product-img">
                    </div>
                </div>
                <div class="card-body">
                    <div style="line-height:1.5em">
                        <div class="card-title w-100 mb-0"><?= $row['name'] ?></div>
                        <div class="d-flex justify-content-between w-100 mb-3" style="height: 2.5em; overflow: hidden;">
                            <div class="w-100">
                                <small class="text-muted" style="line-height: 1.25em; display: block;">
                                    <?= $row['brand'] ?>
                                </small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center">
                            <?php if (!is_null($row['discounted_price']) && $row['discounted_price'] < $row['price']): ?>
                                <span class="banner-price fw-bold me-2"><?= format_price_custom($row['discounted_price']) ?> ฿</span>
                                <?php $discount_percentage = round((($row['price'] - $row['discounted_price']) / $row['price']) * 100); ?>
                                <span class="badge badge-sm text-white">- <?= $discount_percentage ?>%</span>
                            <?php else: ?>
                                <span class="banner-price"><?= format_price_custom($row['price']) ?> ฿</span>
                            <?php endif; ?>
                        </div>
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

<?php if ($total_pages > 1) { ?>
    <div class="col-12 d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- ปุ่มก่อนหน้า -->
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?= $page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="javascript:void(0)" data-page="<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- ปุ่มถัดไป -->
                <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
<?php }
echo ob_get_clean();
?>