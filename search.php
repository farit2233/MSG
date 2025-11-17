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
<style>
    .filter-header {
        font-size: 16px;
        font-weight: 500;
        color: #333;
        text-transform: uppercase;
    }

    .price-inputs .form-control {
        border: 1px solid #ced4da;
        border-radius: 2px;
        font-size: 14px;
        height: 34px;
    }

    .price-inputs .form-control::placeholder {
        color: #bbb;
    }

    .price-inputs .form-control:focus {
        border-color: #f57421;
        box-shadow: 0 0 0 0.2rem rgba(238, 77, 45, 0.25);
    }

    .price-inputs .separator {
        color: #757575;
    }

    .btn-filter {
        background-color: #f57421;
        color: #fff;
        border: 1px solid #ee4d2d;
        border-radius: 2px;
        font-size: 14px;
        font-weight: 500;
        padding: 0.5rem;
        transition: all 0.2s ease-in-out;
    }

    .btn-filter:hover {
        filter: brightness(90%);
        color: white;
    }
</style>

<section class="py-3">
    <div class="container">
        <div class="content py-5 px-3">
            <h1 class="">
                ผลการค้นหา: <?= htmlspecialchars($search) ?>
            </h1>
            <hr>
        </div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="./" class="plain-link">HOME</a></li>
                <li class="breadcrumb-item active" aria-current="page">ผลการค้นหา</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="card rounded-0 mb-3">
                    <div class="card-body">
                        <h5 class="mb-3">หมวดหมู่สินค้า</h5>
                        <div id="category-filter-group">
                            <?php
                            $categories_qry = $conn->query("
                                SELECT id, `name`, `other` 
                                FROM `category_list` 
                                WHERE `status` = 1 AND `delete_flag` = 0 
                                ORDER BY `other` ASC, `name` DESC
                            ");
                            $counter = 0;
                            $max_categories = 10;
                            while ($cat = $categories_qry->fetch_assoc()):
                                $counter++;
                            ?>
                                <div class="form-check category-item <?= $counter > $max_categories ? 'd-none' : '' ?>">
                                    <input class="form-check-input category-filter" type="checkbox" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>">
                                    <label class="form-check-label" for="cat_<?= $cat['id'] ?>"><?= $cat['name'] ?></label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <button class="btn btn-link w-100" id="toggle-categories">
                            <span id="toggle-text">แสดงผลเพิ่มเติม</span>
                        </button>
                    </div>
                    <hr>
                    <div class="card-body">
                        <h5 class="filter-header mb-3">กรองตามราคา</h5>
                        <div class="price-inputs d-flex align-items-center mb-3">
                            <input type="number" class="form-control text-center me-2" id="min_price" placeholder="฿ ต่ำสุด">
                            <div class="separator">-</div>
                            <input type="number" class="form-control text-center ms-2" id="max_price" placeholder="฿ สูงสุด">
                        </div>
                        <button class="btn btn-filter w-100" id="apply-price-filter">กรอง <i class="fas fa-filter"></i></button>
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="card card-outline rounded-0">
                    <div class="card-body">
                        <div class="row mb-3 align-items-center justify-content-end">
                            <div class="col-auto">
                                <label for="sort_by" class="form-label mb-0">เรียงตาม:</label>
                            </div>
                            <div class="col-auto">
                                <select class="form-select form-select-sm" id="sort_by">
                                    <option value="date_desc">สินค้าใหม่ล่าสุด</option>
                                    <option value="date_asc">สินค้าเก่าสุด</option>
                                    <option value="price_asc">ราคา: น้อยไปมาก</option>
                                    <option value="price_desc">ราคา: มากไปน้อย</option>
                                    <option value="name_asc">ชื่อ: A-Z</option>
                                    <option value="name_desc">ชื่อ: Z-A</option>
                                </select>
                            </div>
                        </div>

                        <div class="row gy-3 gx-3" id="product-list-container">
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status"><span class="sr-only"></span></div>
                                <p class="mt-2">กำลังค้นหาสินค้า...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /container -->
</section>

<script>
    $(document).ready(function() {
        var currentSearchTerm = "<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>";

        function loadProducts(page = 1) {
            var sortBy = $('#sort_by').val();
            var minPrice = $('#min_price').val();
            var maxPrice = $('#max_price').val();
            var categories = $('.category-filter:checked').map(function() {
                return $(this).val();
            }).get();

            var productContainer = $('#product-list-container');
            var loadingSpinner = $('#loading-spinner');

            loadingSpinner.show();
            // ถ้าเป็นการกรองใหม่ (เริ่มที่หน้า 1) ให้ล้างข้อมูลเก่าก่อน
            if (page === 1) {
                productContainer.empty().append(loadingSpinner);
            }

            $.ajax({
                url: './ajax/search_products.php',
                method: 'GET',
                data: {
                    search: currentSearchTerm,
                    sort: sortBy,
                    min_price: minPrice,
                    max_price: maxPrice,
                    categories: categories,
                    page: page
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

        // โหลดสินค้าครั้งแรกเมื่อหน้าเว็บพร้อม
        loadProducts();

        // Event listeners สำหรับฟิลเตอร์ต่างๆ
        $('#sort_by, .category-filter').on('change', function() {
            loadProducts(1); // เมื่อมีการเปลี่ยนแปลงฟิลเตอร์ ให้กลับไปหน้า 1
        });

        $('#apply-price-filter').on('click', function() {
            loadProducts(1); // เมื่อคลิกกรองราคา ให้กลับไปหน้า 1
        });

        // Event listener สำหรับปุ่ม "แสดงผลเพิ่มเติม" ของหมวดหมู่
        $('#toggle-categories').on('click', function() {
            $('#category-filter-group .category-item.d-none').toggleClass('d-none d-block');
            var text = $('#toggle-text');
            if (text.text() === "แสดงผลเพิ่มเติม") {
                text.text("แสดงผลน้อยลง");
            } else {
                text.text("แสดงผลเพิ่มเติม");
                $('#category-filter-group .category-item.d-block').toggleClass('d-block d-none');
            }
        });

        // Event listener สำหรับ Pagination (ใช้ event delegation)
        $('#product-list-container').on('click', '.page-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            if (page) {
                loadProducts(page);
            }
        });
    });
</script>

<?php require_once('inc/footer.php'); ?>