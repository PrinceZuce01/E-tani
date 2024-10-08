<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `category_list` where id = '{$_GET['id']}' and delete_flag = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
?>
		<center>Unknown Category</center>
		<style>
			#uni_modal .modal-footer{
				display:none
			}
		</style>
		<div class="text-right">
			<button class="btn btndefault bg-gradient-dark btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
		</div>
		<?php
		exit;
		}
}
?>
<style>
	#uni_modal .modal-footer{
		display:none
	}
</style>
<div class="container-fluid">
	<dl>
        <dt class="text-muted">Kategori</dt>
        <dd class="pl-3"><?= isset($name) ? $name : "" ?></dd>
        <dt class="text-muted">Penerangan</dt>
        <dd class="pl-3"><?= isset($description) ? $description : "" ?></dd>
        <dt class="text-muted">Yuran</dt>
        <dd class="pl-3"><?= isset($fee) ? format_num($fee) : "" ?></dd>
        <dt class="text-muted">Status</dt>
        <dd class="pl-3">
            <?php if($status == 1): ?>
                <span class="badge badge-success bg-gradient-success px-3 rounded-pill">Aktif</span>
            <?php else: ?>
                <span class="badge badge-danger bg-gradient-danger px-3 rounded-pill">Tidak Aktif</span>
            <?php endif; ?>
        </dd>
    </dl>
	<div class="clear-fix mb-3"></div>
	<div class="text-right">
		<button class="btn btn-default bg-gradient-dark btn-sm btn-flat" type="button" data-dismiss="modal"><i class="fa f-times"></i> Tutup</button>
	</div>
</div>
