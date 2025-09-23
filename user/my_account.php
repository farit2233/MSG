<style>
    .account-card {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .account-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        color: inherit;
        text-decoration: none;
    }
</style>

<section class="py-4">
    <div class="container">
        <div class="card-body">
            <div class="container-fluid">
                <div class="profile-section-title-with-line">
                    <h4>บัญชีของฉัน</h4>
                    <p>จัดการข้อมูลส่วนตัว ที่อยู่ และดูประวัติการสั่งซื้อของคุณ</p>
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-4 mb-4">
                        </a>
                        <a href="?p=user/profile" class="list-group-item-action account-card <?php echo (isset($_GET['p']) && $_GET['p'] == 'profile') ? 'active' : '' ?>">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">ข้อมูลส่วนตัว <i class="fa-solid fa-pen-to-square"></i></h5>
                                </div>
                                <div class="card-footer">
                                    <p class="card-text text-muted">แก้ไขชื่อ, อีเมล, และเบอร์โทรศัพท์ของคุณ</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <a href="?p=user/address" class="list-group-item-action account-card <?php echo (isset($_GET['p']) && $_GET['p'] == 'profile') ? 'active' : '' ?>">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">ที่อยู่ของฉัน <i class="fa-solid fa-map-location-dot"></i></h5>
                                </div>
                                <div class="card-footer">
                                    <p class="card-text text-muted">จัดการ และเพิ่มที่อยู่ <br>สำหรับจัดส่ง</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <a href="?p=user/orders" class="list-group-item-action account-card <?php echo (isset($_GET['p']) && $_GET['p'] == 'profile') ? 'active' : '' ?>">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">การสั่งซื้อของฉัน <i class="fa-solid fa-box"></i></h5>
                                </div>
                                <div class="card-footer">
                                    <p class="card-text text-muted">ดูประวัติการสั่งซื้อและสถานะสินค้า</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>