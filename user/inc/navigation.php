<style>
    .profile-nav .list-group-item {
        border: none;
        padding: 1rem 1.25rem;
    }

    .profile-nav .list-group-item.active {
        background-color: #f0f0f0;
        color: #333;
        font-weight: bold;
        border-left: 3px solid #007bff;
    }

    .profile-nav .list-group-item i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .profile-header {
        text-align: center;
        padding: 20px;
        border-bottom: 1px solid #eee;
    }

    .profile-header img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 10px;
    }

    /* สไตล์ของลิงก์ */
    .profile-link {
        position: relative;
        /* เพื่อให้ไอคอนอยู่เหนือรูปภาพ */
        display: inline-block;
    }

    /* รูปภาพโปรไฟล์ */
    .profile-img {
        width: 80px;
        /* ขนาดของรูปภาพ */
        height: 80px;
        border-radius: 50%;
        /* ทำให้เป็นวงกลม */
        transition: transform 0.3s ease;
        /* เพิ่ม transition เวลา hover */
        object-fit: cover;
        border-radius: 100%;
        border: 3px solid #f57421;
        padding: 4px;
    }

    /* ไอคอนดินสอ */
    .edit-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        font-size: 20px;
        color: rgba(0, 0, 0, 0.7);
        /* สีไอคอน */
        background-color: #fff;
        border-radius: 50%;
        padding: 5px;
        /* ซ่อนไอคอนเมื่อไม่ได้ hover */
        transition: opacity 0.2s ease;
    }

    /* เมื่อ hover จะทำให้ไอคอนแสดง */
    .profile-link:hover .edit-icon {
        display: block;
        /* แสดงไอคอนเมื่อ hover */
        opacity: 1;
        /* ความชัดเจนของไอคอน */
    }

    /* เมื่อ hover ที่รูปภาพจะมีการ zoom เพิ่มขนาด */
    .profile-img:hover {
        transform: scale(1.1);
        /* ขยายรูปภาพนิดหน่อย */
    }
</style>

<?php
// ดึงข้อมูลผู้ใช้เพื่อมาแสดงชื่อและรูปภาพ (เหมือนใน profile.php)
$user_qry = $conn->query("SELECT * FROM `customer_list` WHERE id = '{$_settings->userdata('id')}'");
if ($user_qry->num_rows > 0) {
    $user_data = $user_qry->fetch_assoc();
}
?>
<input type="hidden" name="old_avatar" value="<?= isset($avatar) ? $avatar : '' ?>">
<div class="card shadow-sm profile-nav">
    <div class="profile-header">
        <a href="?p=user/profile" class="profile-link">
            <img src="<?php echo validate_image(isset($user_data['avatar']) ? $user_data['avatar'] : '') ?>" alt="User Avatar" class="profile-img">
            <i class="fa-solid fa-pen-to-square edit-icon"></i>
        </a>
        <h5><?php echo isset($user_data['firstname']) ? $user_data['firstname'] . ' ' . $user_data['middlename'] . ' ' . $user_data['lastname'] : 'สวัสดี'; ?></h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="?p=user/" class="list-group-item list-group-item-action <?php echo (!isset($_GET['p']) || $_GET['p'] == 'my_account') ? 'active' : '' ?>">
            <i class="fa-solid fa-user-circle"></i> บัญชีของฉัน
        </a>
        <a href="?p=user/profile" class="list-group-item list-group-item-action <?php echo (isset($_GET['p']) && $_GET['p'] == 'profile') ? 'active' : '' ?>">
            <i class="fa-solid fa-pen-to-square"></i> ข้อมูลส่วนตัว
        </a>
        <a href="?p=user/address" class="list-group-item list-group-item-action <?php echo (isset($_GET['p']) && $_GET['p'] == 'address') ? 'active' : '' ?>">
            <i class="fa-solid fa-map-location-dot"></i> ที่อยู่ของฉัน
        </a>
        <a href="?p=user/slip_payment" class="list-group-item list-group-item-action <?php echo (isset($_GET['p']) && $_GET['p'] == 'slip_payment') ? 'active' : '' ?>">
            <i class="fa-solid fa-receipt"></i> แจ้งยอดชำระเงิน
        </a>
        <a href="?p=user/orders" class="list-group-item list-group-item-action <?php echo (isset($_GET['p']) && $_GET['p'] == 'orders') ? 'active' : '' ?>">
            <i class="fa fa-truck"></i> ประวัติการสั่งซื้อ
        </a>
        <a href="<?= base_url . '../../classes/Login.php?f=logout_customer' ?>" class="list-group-item list-group-item-action">
            <i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ
        </a>
    </div>
</div>