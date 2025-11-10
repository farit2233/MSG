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

<style>
    #password {
        border: none;
        /* ลบกรอบ */
        background: transparent;
        /* กำหนดให้พื้นหลังเป็นโปร่งใส */
        padding: 10px 15px;
        /* เพิ่มระยะห่างข้างใน */
        font-size: 16px;
        /* ขนาดตัวอักษร */

    }

    #password:focus {
        outline: none;
        /* ลบกรอบที่แสดงเวลาโฟกัส */
        text-decoration: underline !important;
        /* เพิ่มเส้นใต้เมื่อมีการ focus */
    }
</style>

<section class="py-5 profile-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <?php include 'user/inc/navigation.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="py-4">

                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="profile-section-title-with-line ">
                                <h4>แก้ไขข้อมูลส่วนตัว</h4>
                                <p>แก้ไขชื่อ, อีเมล, และเบอร์โทรศัพท์</p>
                            </div>
                            <form id="update-form" method="post">
                                <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
                                <input type="hidden" name="cropped_image" id="cropped_image">
                                <input type="hidden" name="old_avatar" value="<?= isset($avatar) ? $avatar : '' ?>">

                                <div class="row justify-content-center">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group d-flex justify-content-center mt-2">
                                            <img src="<?php echo validate_image(isset($avatar) ? $avatar : '') ?>" alt="Avatar Preview" id="cimg" class="img-fluid">
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile" name="img" accept="image/png, image/jpeg">
                                            <label class="custom-file-label profile-img-label" for="customFile">เปลี่ยนรูปโปรไฟล์</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="firstname" class="control-label">ชื่อ <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required name="firstname" id="firstname" value="<?= isset($firstname) ? $firstname : '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="middlename" class="control-label">ชื่อกลาง (ถ้ามี)</label>
                                            <input type="text" class="form-control" name="middlename" id="middlename" value="<?= isset($middlename) ? $middlename : '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="lastname" class="control-label">นามสกุล <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required name="lastname" id="lastname" value="<?= isset($lastname) ? $lastname : '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="gender" class="control-label">เพศ</label>
                                            <select class="custom-select" required name="gender" id="gender">
                                                <option value="Male" <?= isset($gender) && $gender == 'Male' ? "selected" : '' ?>>ชาย</option>
                                                <option value="Female" <?= isset($gender) && $gender == 'Female' ? "selected" : '' ?>>หญิง</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="contact" class="control-label">เบอร์โทร <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required name="contact" id="contact" value="<?= isset($contact) ? $contact : '' ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="control-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" required name="email" id="email" value="<?= isset($email) ? $email : '' ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="contact" class="control-label">รหัสผ่าน</label>
                                            <a class="form-control" type="button" id="password">เปลี่ยนรหัสผ่าน <i class="fa fa-pencil"></i></a>
                                        </div>

                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-md-5 col-12 text-center">
                                        <button type="submit" class="btn btn-update btn-block rounded-pill">บันทึกการเปลี่ยนแปลง</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    $(document).ready(function() {
        end_loader();

        const contactInput = document.getElementById('contact');
        contactInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, ''); // ลบทุกตัวที่ไม่ใช่เลข
        });

        $('#password').click(function() {
            modal_confirm('เปลี่ยนรหัสผ่าน <i class="fa fa-pencil"></i>', 'user/profile/password.php?pid=<?= isset($id) ? $id : '' ?>')
        })
        // --- Form Submission Logic ---
        $('#update-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            var el = $('<div>');
            el.addClass('alert alert-danger err_msg');
            el.hide();
            $('.err_msg').remove();

            if ($('#password').val() != '' && $('#password').val() != $('#cpassword').val()) {
                el.text('รหัสผ่านใหม่ไม่ตรงกัน');
                _this.prepend(el);
                el.show('slow');
                $('html, body').scrollTop(0);
                return false;
            }

            if (_this[0].checkValidity() == false) {
                _this[0].reportValidity();
                return false;
            }

            start_loader();
            var formData = new FormData($(this)[0]);

            // เช็คว่ามีการเลือกไฟล์ใหม่ไหม ถ้าไม่มีให้ส่งรูปเก่าที่มี
            if (!$('#customFile').val()) {
                formData.append('cropped_image', $('#cropped_image').val());
            }

            $.ajax({
                url: _base_url_ + "classes/Users.php?f=registration",
                method: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                error: err => {
                    console.log(err);
                    alert('An error occurred');
                    end_loader();
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.reload();
                    } else if (!!resp.msg) {
                        el.html(resp.msg);
                        el.show('slow');
                        _this.prepend(el);
                        $('html, body').scrollTop(0);
                    } else {
                        alert('An error occurred');
                        console.log(resp);
                    }
                    end_loader();
                }
            });
        });
        // --- Cropper.js Logic ---
        var $modal = $('#cropModal');
        var image = document.getElementById('image_to_crop');
        var cropper;
        var zoomSlider = document.getElementById('zoom_slider');

        function resizeImage(file, maxWidth, maxHeight, callback) {
            var reader = new FileReader();
            reader.onload = function(event) {
                var img = new Image();
                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');
                    var width = img.width;
                    var height = img.height;

                    // คำนวณขนาดใหม่
                    if (width > height) {
                        if (width > maxWidth) {
                            height = Math.round(height * (maxWidth / width));
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width = Math.round(width * (maxHeight / height));
                            height = maxHeight;
                        }
                    }

                    // กำหนดขนาดใหม่ให้กับ canvas
                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);
                    callback(canvas.toDataURL('image/jpeg'));
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }

        $('#customFile').on('change', function(e) {
            var files = e.target.files;
            if (files && files.length > 0) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    image.src = event.target.result;
                    $modal.modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                    });
                };
                reader.readAsDataURL(files[0]);
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            }
        });

        $modal.on('shown.bs.modal', function() {
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                cropBoxMovable: false,
                cropBoxResizable: false,
                wheelZoomRatio: 0,
                background: false,
                responsive: true,
                autoCropArea: 1,
                ready: function() {
                    let canvasData = cropper.getCanvasData();
                    let initialZoom = canvasData.width / canvasData.naturalWidth;
                    zoomSlider.min = initialZoom;
                    zoomSlider.max = initialZoom * 3;
                    zoomSlider.value = initialZoom;
                }
            });
        }).on('hidden.bs.modal', function() {
            cropper.destroy();
            cropper = null;
            $('#customFile').val('');
            $('#customFile').next('.custom-file-label').html('เปลี่ยนรูปโปรไฟล์');
        });

        zoomSlider.addEventListener('input', function() {
            if (cropper) {
                cropper.zoomTo(this.value);
            }
        });

        $('#crop_button').on('click', function() {
            var canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingQuality: 'high',
            });

            var base64data = canvas.toDataURL('image/webp', 0.8); // 0.8 คือ 80% quality
            $('#cimg').attr('src', base64data);
            $('#cropped_image').val(base64data);
            $modal.modal('hide');
        });
    });
</script>