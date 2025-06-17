<?php
// fetch_products.php

// **สำคัญมาก:** คุณต้อง include ไฟล์ connect.php หรือไฟล์ที่เชื่อมต่อฐานข้อมูลของคุณ
// เพื่อให้ $conn สามารถใช้งานได้

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($conn)) {
    die("Database connection not established.");
}

$cat_where = "";
if (isset($_GET['cid']) && is_numeric($_GET['cid'])) {
    $cat_where = " and `category_id` = '{$_GET['cid']}' ";
}

$order_by = "`date_created` DESC"; // กำหนดค่าเริ่มต้น

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
            break;
    }
}

$qry = $conn->query("SELECT *, 
(COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = product_list.id ), 0) 
- COALESCE((SELECT SUM(quantity) FROM `order_items` WHERE product_id = product_list.id), 0)) as `available` 
FROM `product_list` 
WHERE status = 1 AND delete_flag = 0 {$cat_where} 
ORDER BY {$order_by}");

// เริ่มต้น buffer เพื่อเก็บ HTML output
ob_start();

if ($qry->num_rows > 0):
    while ($row = $qry->fetch_assoc()):
?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex" style="margin-top: 1rem;">
            <a class="card rounded-0 shadow product-item text-decoration-none text-reset h-100 <?= ($row['available'] <= 0 ? 'out-of-stock' : '') ?>"
                href="./?p=products/view_product&id=<?= $row['id'] ?>">
                <div class="position-relative">
                    <div class="img-top position-relative product-img-holder">
                        <?php if ($row['available'] <= 0): ?>
                            <div class="out-of-stock-label">สินค้าหมด</div>
                        <?php endif; ?>
                        <img src="<?= validate_image($row['image_path']) ?>" alt="" class="product-img">
                    </div>
                </div>

                <div class="card-body">
                    <div style="line-height:1em">
                        <div class="card-title w-100 mb-0"><?= $row['name'] ?></div>
                        <div class="d-flex justify-content-between w-100 mb-3">
                            <div><small class="text-muted"><?= $row['brand'] ?></small></div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <span class="banner-price"><?= format_num($row['price'], 2) ?> ฿</span>
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

// ส่ง HTML output กลับไป
echo ob_get_clean();
?>