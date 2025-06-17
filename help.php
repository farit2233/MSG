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
            <i class="fa fa-circle-question" style="font-size: 24px; vertical-align: middle;"></i>ศูนย์ช่วยเหลือ
        </h1>

        <div class="card rounded-0">
            <div class="card-body">
                <?= htmlspecialchars_decode(file_get_contents('./help.html')) ?>
            </div>

        </div>
    </div>
</section>