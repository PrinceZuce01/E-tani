<div class="content py-3">
    <div class="card card-outline card-navy shadow ">
        <div class="card-header text-light" style="background-color:#3C6255;">
            <div class="h5 card-title"><b>Pembayaran</b></div>
        </div>
        <div class="card-body" style="background-color:#c4d7b2;">
            <div class="row">
                <div class="col-md-8">
                    <form action="" id="checkout-form">
                        <div class="form-group">
                            <label for="delivery_address" class="control-label">Alamat penghantaran</label>
                            <textarea name="delivery_address" id="delivery_address" rows="4" class="form-control rounded-0" required><?= $_settings->userdata('address') ?></textarea>
                        </div>
                        <div class="form-group text-right">
                            <button class="btn btn-flat btn-sm text-light" style="background-color:#3c6255;">Sahkan Tempahan</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="row" id="summary">
                    <div class="col-12 border" >
                        <h2 class="text-center"><b>Ringkasan</b></h2>
                    </div>
                    <?php 
                    $gtotal = 0;
                    $vendors = $conn->query("SELECT * FROM `vendor_list` where id in (SELECT vendor_id from product_list where id in (SELECT product_id FROM `cart_list` where client_id ='{$_settings->userdata('id')}')) order by `shop_name` asc");
                    while($vrow=$vendors->fetch_assoc()):    
                    $vtotal = $conn->query("SELECT sum(c.quantity * p.price) FROM `cart_list` c inner join product_list p on c.product_id = p.id where c.client_id = '{$_settings->userdata('id')}' and p.vendor_id = '{$vrow['id']}'")->fetch_array()[0];   
                    $vtotal = $vtotal > 0 ? $vtotal : 0;
                    $gtotal += $vtotal;
                    
                    

                    ?>
                    <div class="col-12 border item">
                        <b class="text-dark"><small><?= $vrow['shop_owner']." - ".$vrow['shop_name'] ?></small></b>
                        <div class="text-right"><b>RM <?= format_num($vtotal) ?></b></div>
                    </div>
                    <div class="col-12 border item">
                        <b class="text-dark"><small>Sila berurusan dengan petani untuk pembayaran :</small></b>
                        <div class="text-right"><b><?php
            $vcontact = $conn->query("SELECT contact FROM `vendor_list` WHERE id = '{$vrow['id']}'")->fetch_array()['contact'];;
            echo $vcontact;
            ?></b></div>
                    </div>
                    <?php endwhile; ?>
                    <div class="col-12 border">
                        <b class="text-dark">Jumlah Semua</b>
                        <div class="text-right h3" id="total"><b>RM <?= format_num($gtotal) ?></b></div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#checkout-form').submit(function(e){
        e.preventDefault()
        var _this = $(this)
        if(_this[0].checkValidity() == false){
            _this[0].reportValidity()
            return false;
        }
        if($('#summary .item').length <= 0){
            alert_toast("Belum ada tempahan yang disenaraikan dalam bakul pembelian.",'error')
            return false;
        }
        $('.pop_msg').remove();
        var el = $('<div>')
            el.addClass("alert alert-danger pop_msg")
            el.hide()
        start_loader()
        $.ajax({
            url:_base_url_+'classes/Master.php?f=place_order',
            method:'POST',
            data:_this.serialize(),
            dataType:'json',
            error:err=>{
                console.error(err)
                alert_toast("An error occurred.",'error')
                end_loader()
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.replace('./?page=products')
                }else if(!!resp.msg){
                    el.text(resp.msg)
                    _this.prepend(el)
                    el.show('slow')
                    $('html,body').scrollTop(0)
                }else{
                    el.text("An error occurred.")
                    _this.prepend(el)
                    el.show('slow')
                    $('html,body').scrollTop(0)
                }
                end_loader()
            }
        })
    })
</script>