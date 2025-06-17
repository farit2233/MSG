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
</style>
<section class="py-5">
    <div class="container">
        <h1 class="text-center about fw-bold text-orange">
            <i class="fa fa-info-circle" style="font-size: 24px; vertical-align: middle;"></i>เกี่ยวกับเรา
        </h1>

        <div class="card rounded-0">
            <div>
                <img src="./uploads/logo.png" alt="...">
            </div>
            <div class="card-body">
                <?= htmlspecialchars_decode(file_get_contents('./about.html')) ?>
            </div>
        </div>
    </div>
</section>