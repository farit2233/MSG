<style>
    body {
        background-color: #FAFAFA;
    }

    .about {
        font-size: 50px;
        color: #ff6f00;
        padding: 2rem 2rem;
    }

    img {
        display: block;
        margin: auto;
    }

    .contact-size {
        font-size: 18px;
    }
</style>

<section class="py-5">
    <div class="container">
        <div class="d-flex flex-column justify-content-center align-items-center text-center">
            <h1 class="text-center about fw-bold text-orange">
                <i class="fa fa-envelope"></i> ติดต่อเรา
            </h1>
            <p style="font-size: 20px; padding-bottom: 2rem;"><?= $_settings->info('Synopsis') ?></p>
        </div>
        <div class="card rounded-0">
            <div class="card-body">
                <dl class="contact-info">
                    <dt class="contact-size"><i class="fa fa-location-dot" style="color: red;"></i> Address</dt>
                    <dd><?= $_settings->info('address') ?></dd>

                    <dt class="contact-size"><i class="fa fa-phone" style="color: green;"></i> เบอร์โทร</dt>
                    <dd><?= $_settings->info('mobile') ?></dd>

                    <dt class="contact-size"><i class="fa fa-envelope" style="color: #ff6f00;"></i> Email</dt>
                    <dd><?= $_settings->info('email') ?></dd>

                    <dt class="contact-size"><i class="fa fa-clock"></i> วันเวลาเปิดทำการบริษัท</dt>
                    <dd><?= $_settings->info('office_hours') ?></dd>
                </dl>

                <div class="contact-social">
                    <h5 class="contact-size"><i class="fa fa-globe" style="color: skyblue;"></i> Social Media</h5>
                    <p class="mb-1"><i class="fab fa-line text-success"></i> <a href="<?= $_settings->info('Line') ?>" target="_blank">Line</a>,
                        <i class="fab fa-facebook text-primary"></i> <a href="<?= $_settings->info('Facebook') ?>" target="_blank"> Facebook</a>,
                        <i class="fa-brands fa-tiktok"></i> <a href="<?= $_settings->info('TikTok') ?>" target="_blank"> TikTok</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>