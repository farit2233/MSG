<?php
require_once('../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `stock_list` where id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<style>
    .text-size-input {
        font-size: 16px;
    }

    section {
        font-size: 16px;
    }
</style>
<section class="container-fluid">
    <form action="" id="stock-form">
        <input type="hidden" name="id" value='<?= isset($id) ? $id : '' ?>'>
        <input type="hidden" name="product_id" value='<?= isset($product_id) ? $product_id : (isset($_GET['pid']) ? $_GET['pid'] : '') ?>'>
        <div class="form-group">
            <label for="sku" class="control-label">SKU</label>
            <input type="text" class="form-control form-control-sm rounded-0 text-size-input" name="sku" id="sku" required="required" value="<?= isset($sku) ? $sku : '' ?>">
        </div>
        <div class="form-group">
            <label for="quantity" class="control-label">จำนวน</label>
            <input type="number" class="form-control form-control-sm rounded-0 text-right text-size-input" name="quantity" id="quantity" required="required" value="<?= isset($quantity) ? $quantity : '' ?>">
        </div>
    </form>
</section>
<script>
    $(function() {
        $('#stock-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this)
            $('.err-msg').remove();
            var el = $('<div>')
            el.addClass("alert alert-danger err-msg")
            el.hide()
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_stock",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("An error occured", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.replace('./?page=inventory')
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        el.text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                        $("html, body, .modal").scrollTop(0);
                    } else {
                        alert_toast("An error occured", 'error');
                        end_loader();
                    }
                    end_loader()
                }
            })
        })
    })
</script>