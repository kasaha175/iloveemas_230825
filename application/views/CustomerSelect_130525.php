<div class="col-md-12" style="margin-top:110px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb fixed-top bg-transparent w-50" style="margin-top:5rem">
            <li class="breadcrumb-item"><a class="text-decoration-none text-white" href="<?=base_url()?>dashboard/"><i
                        class="fas fa-home fa-fw"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a class="text-decoration-none text-secondary" href="">Transaction</a></li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12 col-lg-4 offset-lg-4">
            <div class="card shadow" style="margin-top:5rem">
                <div class="card-header">
                    <h4 class="text-center font-weight-bold">TRANSACTION</h4>
                    <p class="text-center">Select Customer</p>
                </div>
                <!-- Card Header - Accordion -->
                <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseCardExample">
                    <h6 class="m-0 font-weight-bold text-info">SELECT CUSTOMER</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseCardExample" style="">
                    <div class="card-body">
                        <form action="<?=base_url()?>transaction/buy-add-to-cart/" method="post">
                            <input type="hidden" name="idMaterial" value="<?=$this->uri->segment(3)?>">
                            <div class="form-group">
                                <label>CUSTOMER</label>
                                <a href="<?=base_url()?>transaction/new-customer/" class="text-info float-right">
                                    <span class="text"><i class="fas fa-user-plus"></i> Add Customer</span>
                                </a>
                                <input id="u_name" name="u_name" class="form-control" type="text" placeholder=" Enter something...">
                                <!-- <select name="u_name" required class="form-control select2" id="u_name">
									<option value="" disable>Please select customer...</option>
									<?php foreach($customer as $c){ ?>
									<option value="<?=$c->c_id?>">
										<?=$c->c_name?>
									</option>
									<?php } ?>
								</select> -->
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <a id="sell" href="#!" class="btn btn-warning btn-icon-split btn-lg btn-block shadow-sm">
                                        <span class="text"><i class="fas fa-tag"></i>&ensp;Sell</span>
                                    </a>
                                </div>
                                <div class="form-group col-md-6">
                                    <a id="buy" href="#!" class="btn btn-success btn-icon-split btn-lg btn-block shadow-sm">
                                        <span class="text"><i class="fas fa-shopping-bag"></i>&ensp;Buy</span>
                                    </a>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <a href="<?=base_url()?>dashboard/" class="btn btn-outline-secondary btn-icon-split btn-block shadow-sm">
                                    <span class="text"><i class="fas fa-arrow-left"></i>&ensp;Back</span>
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(".u_name").select2();
    $(".select2").select2();
</script>
<script type="text/javascript">
    $(document).ready(function () {
        function edit_data(id) {
            $.ajax({
                url: "<?=base_url()?>index.php/TransactionController/updateLive/",
                method: "POST",
                data: {
                    id: id
                },
                dataType: "text",
                success: function (data) {
                    // swal("Update Success!", "You update data!", "success");
                }
            });
        }

        $(document).on('change', '#u_name', function () {
            var u_name = $(this).val();
            edit_data(u_name);
        });


    });
</script>
<script>
    $("#sell").click(function () {
        var customer = $("#u_name").val();
        if (customer == '') {
            alert("Please select customer!");
        } else {
			//var u_name = $("#u_name").val();
			//edit_data(u_name);
            window.location.href = "<?=base_url()?>transaction/sell/";
        }
    });
</script>
<script>
    $("#buy").click(function () {
        var customer = $("#u_name").val();
        if (customer == '') {
            alert("Please select customer!");
        } else {
            window.location.href = "<?=base_url()?>transaction/buy/";
        }
    });
</script>


<script>
    jQuery(function ($) {

        // QWERTY Text Input
        // The bottom of this file is where the autocomplete extension is added
        // ********************
        $('#u_name').keyboard({
            layout: 'qwerty'
        });

        $('.version').html('(v' + $('#u_name').getkeyboard().version + ')');

        // Contenteditable
        // ********************
        $.keyboard.keyaction.undo = function (base) {
            base.execCommand('undo');
            return false;
        };
        $.keyboard.keyaction.redo = function (base) {
            base.execCommand('redo');
            return false;
        };

        $('#contenteditable').keyboard({
            usePreview: false,
            useCombos: false,
            autoAccept: true,
            layout: 'custom',
            customLayout: {
                'normal': [
                    '` 1 2 3 4 5 6 7 8 9 0 - = {del} {b}',
                    '{tab} q w e r t y u i o p [ ] \\',
                    'a s d f g h j k l ; \' {enter}',
                    '{shift} z x c v b n m , . / {shift}',
                    '{accept} {space} {left} {right} {undo:Undo} {redo:Redo}'
                ],
                'shift': [
                    '~ ! @ # $ % ^ & * ( ) _ + {del} {b}',
                    '{tab} Q W E R T Y U I O P { } |',
                    'A S D F G H J K L : " {enter}',
                    '{shift} Z X C V B N M < > ? {shift}',
                    '{accept} {space} {left} {right} {undo:Undo} {redo:Redo}'
                ]
            },
            display: {
                del: '\u2326:Delete',
                redo: '↻',
                undo: '↺'
            }
        });


        // Autocomplete demo
        var availableTags = [
            <?php foreach($customer as $c){ ?>
            "<?=$c->c_id?> - <?=$c->c_name?> - <?=$c->c_id_number?>",
            <?php } ?>
        ];
        $('#u_name')
            .autocomplete({
                source: availableTags
            })
            .addAutocomplete();

        prettyPrint();

    });
</script>