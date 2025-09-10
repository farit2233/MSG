<?php
require_once('./config.php');            // เชื่อมต่อฐานข้อมูล
$page_title       = "ผลการค้นหา";
$page_description = "";

$search = '';
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}
?>
<?php require_once('inc/header.php'); ?>
<?php require_once('inc/topBarNav.php'); ?>

<section class="py-3">
    <div class="container">
        <div class="content py-5 px-3" align="center">
            <h1 class="">ผลการค้นหา: <?= htmlspecialchars($search) ?></h1>
        </div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="./" class="plain-link">HOME</a></li>
                <li class="breadcrumb-item active" aria-current="page">ผลการค้นหา</li>
            </ol>
        </nav>

        <div class="row mt-n3 justify-content-center">
            <div class="col-lg-10 col-md-11 col-sm-11">
                <div class="card card-outline rounded-0">
                    <div class="card-body">
                        <!-- Sort dropdown -->
                        <div class="row mb-3 align-items-center justify-content-end">
                            <div class="col-auto">
                                <label for="sort_by" class="form-label mb-0">เรียงตาม:</label>
                            </div>
                            <div class="col-auto">
                                <select class="form-select form-select-sm" id="sort_by" onchange="sortProducts()">
                                    <option value="date_desc" <?= (!isset($_GET['sort']) || $_GET['sort'] == 'date_desc') ? 'selected' : '' ?>>สินค้าใหม่ล่าสุด</option>
                                    <option value="date_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_asc')  ? 'selected' : '' ?>>สินค้าเก่าสุด</option>
                                    <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>ราคา: น้อยไปมาก</option>
                                    <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>ราคา: มากไปน้อย</option>
                                    <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc')  ? 'selected' : '' ?>>ชื่อ: A-Z</option>
                                    <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : '' ?>>ชื่อ: Z-A</option>
                                </select>
                            </div>
                        </div>

                        <!-- ผลลัพธ์สินค้า -->
                        <div class="row gy-3 gx-3" id="product-list-container">
                            <div class="col-12 text-center py-5" id="loading-spinner">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                <p class="mt-2">กำลังโหลดสินค้า...</p>
                            </div>
                        </div><!-- /product-list-container -->
                    </div><!-- /card-body -->
                </div><!-- /card -->
            </div><!-- /col -->
        </div><!-- /row -->
    </div><!-- /container -->
</section>

<script>
    /* ---------- JS เหมือน products ---------- */
    var currentSearchTerm = "<?= htmlspecialchars($search) ?>";

    function sortProducts() {
        var sortBy = $('#sort_by').val();
        var productContainer = $('#product-list-container');
        var loadingSpinner = $('#loading-spinner');

        loadingSpinner.show();
        productContainer.empty();

        $.ajax({
            url: './ajax/search_products.php',
            method: 'GET',
            data: {
                sort: sortBy,
                search: currentSearchTerm // ส่ง search term แทน cid
            },
            success: function(response) {
                loadingSpinner.hide();
                productContainer.html(response);
            },
            error: function(xhr, status, error) {
                loadingSpinner.hide();
                console.error("AJAX Error:", status, error);
                productContainer.html('<div class="col-12 text-center py-5 text-danger">เกิดข้อผิดพลาดในการโหลดสินค้า กรุณาลองใหม่อีกครั้ง</div>');
            }
        });
    }

    $(document).ready(function() {
        sortProducts(); // โหลดผลการค้นหาทันที
    });
</script>

<?php require_once('inc/footer.php'); ?>