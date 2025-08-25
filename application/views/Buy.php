<div class="col-md-12" style="margin-top:110px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb fixed-top bg-transparent w-50" style="margin-top:5rem">
            <li class="breadcrumb-item">
                <a class="text-decoration-none text-white" href="<?=base_url()?>dashboard/">
                    <i class="fas fa-home fa-fw"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-decoration-none text-secondary" href="<?=base_url()?>transaction/">Transaction</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-decoration-none text-secondary" href="<?=base_url()?>">Buy</a>
            </li>
        </ol>
    </nav>
    <h3 class="text-center text-white">BUY</h3>
    <h3 class="text-center text-white">Choose Material</h3>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3 mb-5">
                <a href="<?=base_url()?>transaction/" class="btn btn-light btn-icon-split btn-lg">
                    <span class="icon text-white-50">
                        <i class="fas fa-arrow-left text-dark"></i>
                    </span>
                    <span class="text">Back</span>
                </a>
            </div>
        </div>
        <div class="row">
            <?php 
            $materials = [
                ["url" => "buy/1", "img" => "diamond.png", "name" => "Diamond"],
                ["url" => "buy/2", "img" => "gold.png", "name" => "Gold"],
                ["url" => "buy/lm/select", "img" => "ubs.png", "name" => "LM & UBS"],
                ["url" => "buy/silver/select", "img" => "silver.png", "name" => "Silver"],
                ["url" => "buy/platinum/select", "img" => "platinum.png", "name" => "Platinum"],
                ["url" => "buy/paladium/select", "img" => "paladium.png", "name" => "Paladium"],
                ["url" => "buy/iridium/select", "img" => "iridium.png", "name" => "Iridium"],
                ["url" => "buy/rhodium/select", "img" => "rhodium.png", "name" => "Rhodium"],
                ["url" => "buy/10", "img" => "material-au.png", "name" => "C. Profesional"],
                ["url" => "buy/ruthenium/select", "img" => "ruthenium.png", "name" => "Ruthenium"],
                ["url" => "buy/21", "img" => "tantalum.png", "name" => "Tantalum"],
            ];
            foreach ($materials as $material) { ?>
            <div class="col-md-3 mb-4">
                <a href="<?=base_url()?>transaction/<?=$material['url']?>/" class="text-decoration-none">
                    <div class="card shadow text-center h-100" style="border-radius: 10px;">
                        <div class="card-body d-flex flex-column align-items-center">
                            <img src="<?=base_url()?>assets/offline/<?=$material['img']?>" class="img-fluid" style="max-width: 80%; max-height: 120px; object-fit: contain; border-radius: 5px;">
                        </div>
                        <div class="card-footer bg-light">
                            <span class="font-weight-bold text-dark"><?=$material['name']?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
