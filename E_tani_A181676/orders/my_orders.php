<div class="content py-3">
    <div class="card card-outline card-navy rounded-0 shadow" style="background-color:#c4d7b2;">
        <div class="card-header">
            <h5 class="card-title"><b>Senarai Tempahan</b></h5>
        </div>
        <div class="card-body" style="background-color:#c4d7b2;">
            <div class="w-100 overflow-auto">
            <table class="table table-bordered table-striped" >
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                </colgroup>
                <thead class="text-light"style="background-color:#3c6255;">
                    <tr>
                        <th class="p1 text-center">#</th>
                        <th class="p1 text-center">Tarikh Tempahan</th>
                        <th class="p1 text-center">Kod Rujukan</th>
                        <th class="p1 text-center">Jumlah</th>
                        <th class="p1 text-center">Status</th>
                        <th class="p1 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody style="background-color:#fff;">
                    <?php 
                    $i = 1;
                    $orders = $conn->query("SELECT * FROM `order_list` where client_id = '{$_settings->userdata('id')}' order by `status` asc,unix_timestamp(date_created) desc ");
                    while($row = $orders->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="px-2 py-1 align-middle text-center"><?= $i++; ?></td>
                        <td class="px-2 py-1 align-middle"><?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
                        <td class="px-2 py-1 align-middle"><?= $row['code'] ?></td>
                        <td class="px-2 py-1 align-middle text-right">RM <?= format_num($row['total_amount']) ?></td>
                        <td class="px-2 py-1 align-middle text-center">
                            <?php 
                                switch($row['status']){
                                    case 0:
                                        echo '<span class="badge badge-secondary bg-gradient-secondary px-3 rounded-pill">Menunggu</span>';
                                        break;
                                    case 1:
                                        echo '<span class="badge badge-primary bg-gradient-primary px-3 rounded-pill">Disahkan</span>';
                                        break;
                                    case 2:
                                        echo '<span class="badge badge-info bg-gradient-info px-3 rounded-pill">Sudah Dibungkus</span>';
                                        break;
                                    case 3:
                                        echo '<span class="badge badge-warning bg-gradient-warning px-3 rounded-pill">Dalam Perjalanan</span>';
                                        break;
                                    case 4:
                                        echo '<span class="badge badge-success bg-gradient-success px-3 rounded-pill">Sudah Dihantar</span>';
                                        break;
                                    case 5:
                                        echo '<span class="badge badge-danger bg-gradient-danger px-3 rounded-pill">Dibatalkan</span>';
                                        break;
                                    default:
                                        echo '<span class="badge badge-light bg-gradient-light border px-3 rounded-pill">Tiada Maklumat</span>';
                                        break;
                                }
                            ?>
                        </td>
                        <td class="px-2 py-1 align-middle text-center">
                            <button type="button" class="btn border btn-light text-light btn-sm dropdown-toggle dropdown-icon" style="background-color:#3c6255;" data-toggle="dropdown">
                                Tindakan
                            <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>" data-code="<?= $row['code'] ?>"><span class="fa fa-eye text-dark"></span> Lihat</a>
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
    $(function(){
        $('.view_data').click(function(){
            uni_modal("Butiran Tempahan - <b>"+($(this).attr('data-code'))+"</b>","orders/view_order.php?id="+$(this).attr('data-id'),'mid-large')
        })
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
			emptyTable: "Tiada tempahan untuk ditunjukkan",
			info: 'Dari _START_ hingga _END_ daripada _TOTAL_ tempahan',
			infoEmpty: "Tiada tempahan untuk ditunjukkan",
			lengthMenu: "Menunjukkan _MENU_ rekod tempahan"
		},
		initComplete: function () {
			var api = this.api();
			$(api.table().container()).find('input[type="search"]').attr('placeholder', '');
		}
		});
    })
</script>