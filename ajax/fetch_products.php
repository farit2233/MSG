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

// A. จัดการตัวกรองหมวดหมู่ (แบบเลือกได้หลายอัน)
if (isset($_GET['cids']) && is_array($_GET['cids']) && count($_GET['cids']) > 0) {
    // Sanitize input: แปลงทุกค่าใน array ให้เป็น integer เพื่อความปลอดภัย
    $cids_sanitized = array_map('intval', $_GET['cids']);
    $cids_list = implode(',', $cids_sanitized);
    if (!empty($cids_list)) {
        $additional_where .= " AND pl.category_id IN ({$cids_list})";
    }
}

if (isset($_GET['tid']) && is_numeric($_GET['tid'])) {
    $tid = intval($_GET['tid']);
    $additional_where .= " AND cl.product_type_id = {$tid}";
}

// เช็คว่าเลือก promotion_id หรือไม่
if (isset($_GET['pid']) && is_numeric($_GET['pid'])) {
    $pid = intval($_GET['pid']);
    $additional_where .= " AND pp.promotion_id = {$pid}";
}

// B. จัดการตัวกรองราคา
$price_logic = "IF(pl.discounted_price IS NOT NULL AND pl.discounted_price > 0 AND pl.discounted_price < pl.vat_price, pl.discounted_price, pl.vat_price)";

// กรองราคาขั้นต่ำ
if (isset($_GET['min_price']) && is_numeric($_GET['min_price']) && $_GET['min_price'] >= 0) {
    $min_price = floatval($_GET['min_price']);
    $additional_where .= " AND {$price_logic} >= {$min_price}";
}

// กรองราคาขั้นสูงสุด
if (isset($_GET['max_price']) && is_numeric($_GET['max_price']) && $_GET['max_price'] > 0) {
    $max_price = floatval($_GET['max_price']);
    $additional_where .= " AND {$price_logic} <= {$max_price}";
}

// === START: QUERY FOR TOTAL COUNT ===
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

// ดึงรายการสินค้าหลัก (ตาม filter และ pagination)
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
    GROUP BY pl.id
    ORDER BY {$order_by}
    LIMIT {$limit} OFFSET {$offset}
";
$qry = $conn->query($qry_str);

// === ดึง ID สินค้าใหม่ล่าสุด 4 ชิ้น ===
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

// ฟังก์ชันแสดงราคาแบบไม่มี .00
if (!function_exists('format_price_custom')) {
    function format_price_custom($price)
    {
        $formatted_price = format_num($price, 2);
        return substr($formatted_price, -3) == '.00' ? format_num($price, 0) : $formatted_price;
    }
}

// เริ่มต้นการสร้าง HTML Output
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

                        <?php
                        // 1. ดึง Path หลัก
                        $list_main_path = $row['image_path'];
                        // 2. แปลงเป็น Path ขนาดกลาง (Medium)
                        $list_medium_path = preg_replace('/(\.webp)(\?.*)?$/', '_medium.webp$2', $list_main_path);
                        ?>
                        <img src="<?= validate_image($list_medium_path) ?>" alt="<?= $row['name'] ?>" class="product-img">
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <div>
                        <div class="card-title card-title-product w-100 mb-0"><?= $row['name'] ?></div>
                        <div class="d-flex justify-content-between w-100 mb-3" style="height: 2.5em; overflow: hidden;">
                            <div class="w-100">
                                <small class="text-muted" style="line-height: 1.25em; display: block;"><?= $row['brand'] ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center mt-auto">
                        <?php
                        if (!is_null($row['discounted_price']) && $row['discounted_price'] > 0 && $row['discounted_price'] < $row['vat_price']) {
                            // มีราคาส่วนลด → แสดง discounted_price + %
                            $discount_percentage = round((($row['vat_price'] - $row['discounted_price']) / $row['vat_price']) * 100);
                            echo '<span class="banner-price fw-bold me-2">' . format_price_custom($row['discounted_price']) . ' ฿</span>';
                            echo '<span class="badge badge-sm prdouct-badge text-white">- ' . $discount_percentage . '%</span>';
                        } elseif (!is_null($row['vat_price']) && $row['vat_price'] > 0) {
                            // ไม่มีส่วนลด แต่มี VAT → แสดง vat_price
                            echo '<span class="banner-price">' . format_price_custom($row['vat_price']) . ' ฿</span>';
                        } else {
                            // ไม่มีทั้งสอง → แสดง price ปกติ
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
<?php
// (ส่วน Pagination... เหมือนเดิม)
if ($total_pages > 1) {
    // --- กำหนดค่า ---
    $num_fixed_pages = 5;
    $adjacents = 2;
?>
    <div class="col-12 d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?= $page - 1 ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
                </li>

                <?php
                // --- Logic การแสดงผลตัวเลขหน้า ---

                // 1. กรณีที่จำนวนหน้ารวมน้อย
                if ($total_pages <= ($num_fixed_pages + 1)) {
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                        echo '<a class="page-link" href="javascript:void(0)" data-page="' . $i . '">' . $i . '</a>';
                        echo '</li>';
                    }
                }

                // 2. สถานะเริ่มต้น
                elseif ($page < $num_fixed_pages) {
                    for ($i = 1; $i <= $num_fixed_pages; $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                        echo '<a class="page-link" href="javascript:void(0)" data-page="' . $i . '">' . $i . '</a>';
                        echo '</li>';
                    }
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    echo '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="' . $total_pages . '">' . $total_pages . '</a></li>';
                }

                // 3. สถานะท้าย
                elseif ($page >= ($total_pages - ($num_fixed_pages - 2))) {
                    echo '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="1">1</a></li>';
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    $start = $total_pages - ($num_fixed_pages - 1);
                    for ($i = $start; $i <= $total_pages; $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                        echo '<a class="page-link" href="javascript:void(0)" data-page="' . $i . '">' . $i . '</a>';
                        echo '</li>';
                    }
                }

                // 4. สถานะกลาง
                else {
                    echo '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="1">1</a></li>';
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';

                    $start = $page - $adjacents;
                    $end = $page + $adjacents;
                    for ($i = $start; $i <= $end; $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                        echo '<a class="page-link" href="javascript:void(0)" data-page="' . $i . '">' . $i . '</a>';
                        echo '</li>';
                    }

                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    echo '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="' . $total_pages . '">' . $total_pages . '</a></li>';
                }
                ?>

                <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="javascript:void(0)" data-page="<?= $page + 1 ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>
                </li>
            </ul>
        </nav>
    </div>
<?php } // สิ้นสุด if ($total_pages > 1) 
?>