<?php
require_once('../config.php');

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($conn)) {
    die("Database connection not established.");
}

// กำหนดการเรียงลำดับ
$order_by = "`date_created` DESC";
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'date_asc':
            $order_by = "`date_created` ASC";
            break;
        case 'price_asc':
            $order_by = "IF(discounted_price IS NOT NULL AND discounted_price < price, discounted_price, price) ASC";
            break;
        case 'price_desc':
            $order_by = "IF(discounted_price IS NOT NULL AND discounted_price < price, discounted_price, price) DESC";
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

// เงื่อนไขหมวดหมู่หลัก + หมวดเพิ่มเติม
$cat_where = "";
if (isset($_GET['cid']) && is_numeric($_GET['cid'])) {
    $cid = intval($_GET['cid']);
    $cat_where = " AND (
        product_list.category_id = {$cid}
        OR EXISTS (
            SELECT 1 FROM product_categories pc 
            WHERE pc.product_id = product_list.id 
              AND pc.category_id = {$cid}
        )
    )";
}

// ดึงรายการสินค้า
$qry = $conn->query("
    SELECT *,
        (
            COALESCE((SELECT SUM(quantity) FROM stock_list WHERE product_id = product_list.id), 0)
            - COALESCE((SELECT SUM(quantity) FROM order_items WHERE product_id = product_list.id), 0)
        ) AS available
    FROM product_list
    WHERE status = 1 AND delete_flag = 0
    {$cat_where}
    ORDER BY {$order_by}
");

// ฟังก์ชันสำหรับจัดรูปแบบราคา (ตัด .00 ออก)
if (!function_exists('format_price_custom')) {
    function format_price_custom($price)
    {
        $formatted_price = format_num($price, 2);
        if (substr($formatted_price, -3) == '.00') {
            return format_num($price, 0);
        }
        return $formatted_price;
    }
}

// ============== ไม่จำเป็นต้องใช้ CSS ที่กำหนดเองแล้ว ==============

// แสดงผล HTML
ob_start();

if ($qry->num_rows > 0):
    while ($row = $qry->fetch_assoc()):
        $in_stock = $row['available'] > 0;
        $stock_class = $in_stock ? '' : 'out-of-stock';

        // ดึงหมวดหมู่เพิ่มเติม
        $extra_cats = [];
        $cat_q = $conn->query("SELECT c.name FROM product_categories pc 
                                  INNER JOIN category_list c ON c.id = pc.category_id 
                                  WHERE pc.product_id = {$row['id']}");
        while ($c = $cat_q->fetch_assoc()) {
            $extra_cats[] = $c['name'];
        }
?>
        <div class="col-6 col-sm-6 col-md-4 col-lg-3 d-flex" style="margin-top: 1rem;">
            <a class="card rounded-0 shadow product-item text-decoration-none text-reset h-100 <?= $stock_class ?>"
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