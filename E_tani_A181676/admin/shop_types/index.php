<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Jenis Jualan</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" class="btn btn-flat btn-primary" id="create_new"><span class="fas fa-plus"></span>  Cipta Baharu</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="25%">
					<col width="25%">
					<col width="25%">
					<col width="20%">
				</colgroup>
				<thead>
					<tr class="bg-gradient-secondary">
						<th>#</th>
						<th>Tarikh Dibuat</th>
						<th>Jenis Jualan</th>
						<th>Status</th>
						<th>Tindakan</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `shop_type_list` where delete_flag = 0 order by `name` asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['name'] ?></td>
							<td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success bg-gradient-success px-3 rounded-pill">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-danger bg-gradient-danger px-3 rounded-pill">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Tindakan
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Ubah</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Padam</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#create_new').click(function(){
			uni_modal('Tambah Jenis Jualan',"shop_types/manage_shop_type.php")
		})
		$('.edit_data').click(function(){
			uni_modal('Ubah Jenis Jualan',"shop_types/manage_shop_type.php?id="+$(this).attr('data-id'))
		})
		$('.delete_data').click(function(){
			_conf("Adakah anda pasti untuk membuang data ini?","delete_shop_type",[$(this).attr('data-id')])
		})
		$('table .th,table .td').addClass('align-middle px-2 py-1')
		$('.table').dataTable();
	})
	function delete_shop_type($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_shop_type",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("Terdapat Masalah Teknikal.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("Terdapat Masalah Teknikal.",'error');
					end_loader();
				}
			}
		})
	}
</script>