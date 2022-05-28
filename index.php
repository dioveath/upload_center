<?php

include_once("header.php");


?>

<div class="container" >
    <div class="row" >
	<div class="col-10 offset-1" >
	    <form action="index.php?status=uploaded" method="post"
		  enctype="multipart/form-data" id="id_upfileform">
		<div class="form-group">
		    <div class="input-group">
			<div class="custom-file">
			    <input type='file' class="custom-file-input"
				   name="uploaded_file" id="upload_file_id">
			    <label for="upload_file_id" class="custom-file-label"> Choose File </label>
			</div>
			<div class="input-group-append" >
			    <input type="submit" class="btn btn-primary" value="Upload File" name="upload" >
			</div>
		    </div>
		</div>
	    </form> <br>

	    <!-- <a href="upload_files.php" style="color:#11ff89"> Files that you uploaded </a> -->
	    <!-- <h2> Something is Happening below, Don't read!!!!!!! </h2> -->
	</div>
    </div> <!-- first row -->


    <!-- Errors uploadfiles button row without row div --> 
    <?php if(!isset($_GET['status'])): ?>
	<div class="alert alert-dismissible alert-info">
	    <button type="button" class="close" data-dismiss="alert">&times;</button>
	    <strong>No Worries!</strong> <a href="#" class="alert-link"> </a>
	    No processing needed!
	</div>


    <?php
    endif;
    if(isset($_GET['status']) && $_GET['status'] != 'uploaded'): ?>
	<div class="alert alert-dismissible alert-info">
	    <button type="button" class="close" data-dismiss="alert">&times;</button>
	    <strong>Buckle Up!</strong> This <a href="#" class="alert-link"> </a>
	    Processing needed!
	</div>
    <?php
    endif;
    if(isset($_POST['upload'])) {

	$target_dir = "uploaded_files/";
	$target_file = $target_dir . basename($_FILES['uploaded_file']['name']);
	$upload_ok = true;

	if(file_exists($target_file)): ?>
	<div class="alert alert-dismissible alert-danger" >
	    <button type="button" class="close" data-dismiss="alert" >&times; </button>
	    <strong> Error! </strong> <a href="<?=$target_file?>"> <?=basename($_FILES['uploaded_file']['name'])?> </a> File already exists 
	</div>
    <?php
    $upload_ok = false;
    endif;

    if($upload_ok == false): ?>
	<div class="alert alert-dismissible alert-danger" >
	    <button type="button" class="close" data-dismiss="alert" >&times; </button>
	    <strong> Error! </strong> Your File not uploaded plain and simple! 
	</div>
    <?php
    else:
    if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $target_file)): ?>
	    <div class="alert alert-dismissible alert-success" >
		<button type="button" class="close" data-dismiss="alert" >&times; </button>
		<strong> Success! </strong> The file <a href="<?=$target_file?>"> <?=basename($_FILES['uploaded_file']['name'])?> </a> has been uploaded. 
	    </div>
    <?php else: ?>
	    <div class="alert alert-dismissible alert-danger" >
		<button type="button" class="close" data-dismiss="alert" >&times; </button>
		<strong> Error! </strong> Couldn't move the file! 
	    </div>
    <?php
    endif;
    endif;
    } /* if(isset['upload']) */
    ?>

    <div class="row">
	 <div class="col">
	      <?php include_once("upload_files.php"); ?>
	 </div>
    </div>
    
    <!-- <button type="button" onclick="location.href='upload_files.php'"
	 class="btn btn-info btn-lg btn-block mb-4" >
	 All Uploaded Files
	 </button> -->
    


</div> <!-- container -->



<?php
include_once("footer.php");
?>









