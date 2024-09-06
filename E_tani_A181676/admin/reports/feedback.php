<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-navy">
	<div class="card-header" style="background-color:#c4d7b2;">
		<h3 class="card-title"><b>Senarai Maklumbalas</b></h3>
        <!-- <div class="card-tools">
			<a href="javascript:void(0)" class="btn btn-flat btn-primary" id="create_new"><span class="fas fa-plus"></span>  Tambah Baharu</a>
		</div> -->
	</div>
	<div class="card-body" style="background-color:#c4d7b2;">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-stripped" style="background-color:#fff;">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="10%">
					<col width="70%">
					
				</colgroup>
				<thead>
					<tr class="text-light" style="background-color:#3c6255;">
						<th>#</th>
						<th>Tarikh Dibuat</th>
						
						<th>Tajuk</th>
						<th>Maklumbalas</th>
						
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `feedback` where delete_flag = 0 order by `date_created` asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['title'] ?></td>
							<td><?php echo $row['feedy']?></td>
							
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
			uni_modal('Tambah Maklumbalas',"admin/reports/add_feedy.php",'large')
		})
		$('table .th,table .td').addClass('align-middle px-2 py-1')
		$('table').dataTable({
		language: {
			search: 'Cari',
			searchPlaceholder: '',
			paginate: {
			first: 'Pertama',
			last: 'Akhir',
			next: 'Seterusnya',
			previous: 'Sebelum'
			},
			emptyTable: "Tiada maklum balas untuk ditunjukkan",
			info: 'Dari _START_ hingga _END_ daripada _TOTAL_ maklum balas',
			infoEmpty: "Tiada maklum balas untuk ditunjukkan",
			lengthMenu: "Menunjukkan _MENU_ rekod maklum balas"
		},
		initComplete: function () {
			var api = this.api();
			$(api.table().container()).find('input[type="search"]').attr('placeholder', '');
		}
		});
	})
</script>