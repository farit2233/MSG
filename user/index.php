<?php
// --- PHP Block (No changes needed) ---
if ($_settings->userdata('id') != '') {
    $qry = $conn->query("SELECT * FROM `customer_list` WHERE id = '{$_settings->userdata('id')}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            if (!is_numeric($k)) {
                $$k = $v;
            }
        }
    } else {
        echo "<script>alert('You don\\'t have access for this page'); location.replace('./');</script>";
    }
} else {
    echo "<script>alert('You don\\'t have access for this page'); location.replace('./');</script>";
}

?>
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <?php include './user/inc/navigation.php'; ?>
            </div>

            <div class="col-lg-9" id="main-content">
                <?php include 'my_account.php'; ?>
            </div>
        </div>
    </div>
</section>

<script>

</script>