<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการคูปอง</title>
    <!-- Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts for a modern look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;700;900&display=swap" rel="stylesheet">

</head>

<body class="">

    <?php
    // ข้อมูลคูปองสมมุติจากฐานข้อมูล
    $coupons = [
        ['code' => 'COUPON1', 'discount' => '20% OFF', 'description' => 'Save 20% on all items!'],
        ['code' => 'COUPON2', 'discount' => '15% OFF', 'description' => 'Get 15% off on electronics!'],
        ['code' => 'COUPON3', 'discount' => '30% OFF', 'description' => 'Save 30% on your next order!'],
        ['code' => 'COUPON4', 'discount' => '10% OFF', 'description' => 'Exclusive 10% discount for you!'],
        ['code' => 'COUPON5', 'discount' => '25% OFF', 'description' => 'Enjoy 25% off on fashion items!'],
        ['code' => 'COUPON6', 'discount' => '5% OFF', 'description' => 'Get 5% off your first purchase!'],
        ['code' => 'COUPON7', 'discount' => '50% OFF', 'description' => 'Limited offer - 50% off sitewide!'],
        ['code' => 'COUPON8', 'discount' => '20% OFF', 'description' => 'Save 20% on all categories!'],
        ['code' => 'COUPON9', 'discount' => '30% OFF', 'description' => 'Get 30% off on electronics only!'],
        ['code' => 'COUPON10', 'discount' => '10% OFF', 'description' => 'Save 10% on your next shopping spree!']
    ];
    ?>

    <div class="container mt-4">
        <div class="row">
            <?php foreach ($coupons as $coupon): ?>
                <div class="col-md-4 mb-4">
                    <div class="card" style="width: 18rem;">
                        <!-- ใช้รูปที่อัพโหลดมาเป็นพื้นหลังของการ์ด -->
                        <div class="card-img" style="position: relative;">
                            <img src="new-blackcoupon.png" class="card-img-top" alt="Coupon Image">
                            <div class="card-body" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;  color: white; padding: 10px;">
                                <h5 class="card-title"><?= $coupon['code'] ?></h5>
                                <p class="card-text"><?= $coupon['discount'] ?></p>
                                <p class="card-text"><?= $coupon['description'] ?></p>
                                <a href="#" class="btn btn-light btn-sm">Use Coupon</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


</body>

</html>