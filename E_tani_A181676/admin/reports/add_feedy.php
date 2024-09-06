<?php
require_once('./../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
	$stmt = $conn->prepare("SELECT * FROM `feedback` WHERE id = ? AND delete_flag = 0");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the row as an associative array
        $row = $result->fetch_assoc();

        // Assign the values to variables (optional)
        $id = $row['id'];
        $title = $row['title'];
        $feedy = $row['feedy'];
    }else{
?>
		<center>Unknown Shop Type</center>
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

<div class="container-fluid">
	<form action="" id="feedback-form">
		<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="row">
			<div class="col-md-9">
				<div class="form-group">
					<label for="name" class="control-label">Tajuk</label>
					<input name="name" id="title" type="text"class="form-control form-control-sm form-control-border" value="<?php echo isset($title) ? $title : ''; ?>" required>
				</div>
				
				<div class="form-group">
					<label for="description" class="control-label">Penerangan</label>
					<textarea name="description" id="feedy" rows="4"class="form-control form-control-sm rounded-0 summernote" required><?php echo isset($feedy) ? $feedy : ''; ?></textarea>
				</div>
			</div>
		
		</div>
		
	</form>
</div>

<script>
	$(document).ready(function(){
		$('#uni_modal #feedback-form').submit(function(e){
				e.preventDefault();
				var _this = $(this)
				$('.err-msg').remove();
				if(_this[0].checkValidity() == false){
					_this[0].reportValidity();
					return false;
				}
				var el = $('<div>')
					el.addClass("alert err-msg")
					el.hide()
				start_loader();
				$.ajax({
					url:_base_url_+"classes/Master.php?f=save_feedy",
					data: new FormData($(this)[0]),
					cache: false,
					contentType: false,
					processData: false,
					method: 'POST',
					type: 'POST',
					dataType: 'json',
					error:err=>{
						console.error(err)
						el.addClass('alert-danger').text("Terdapat masalah teknikal");
						_this.prepend(el)
						el.show('.modal')
						end_loader();
					},
					success:function(resp){
						if(typeof resp =='object' && resp.status == 'success'){
							location.reload();
						}else if(resp.status == 'failed' && !!resp.msg){
							el.addClass('alert-danger').text(resp.msg);
							_this.prepend(el)
							el.show('.modal')
						}else{
							el.text("Terdapat masalah teknikal");
							console.error(resp)
						}
						$("html, body").scrollTop(0);
						end_loader()

					}
				})
			})
			$('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
	})
</script>