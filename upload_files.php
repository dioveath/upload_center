

<?php
// echo "Here are our files. </br> </br>";
// $path = ".";
// $dh = opendir($path);
// $i = 1;

// while(($file = readdir($dh)) !== false) {
//     if($file != "." && $file != ".." && $file != "index.php" && $file != ".htaccess" && $file != "error_log" && $file != "cgi-bin") {
// 	echo "<a href='$path/$file'> $file </a> </br> </br>";
// 	$i++;
//     }
// }

// closedir($dh);
$directory = dir("uploaded_files/");

$do_link = TRUE;
$sort_what = 3; /* 1: filename, 2: size, 3: date modified */
$sort_how = 2; /* 1: ascending, 2: descending */

$disallowed_ext = array(".", "..", ".php");
$file_array = [];
$i = 0;

$search_text = isset($_GET['search_file']) ? $_GET['search_file'] : false;


/* ------------------------------------------------------------ */
/* Get all files information here */
/* ------------------------------------------------------------ */


while($file = $directory->read()) {
    $full_file = $directory->path . strtolower($file);
    $ext = strrchr($file, '.');

    if(is_dir($full_file)) continue;
    if(isset($disallowed_ext) && (in_array($ext, $disallowed_ext))) continue;

    if($search_text){
	$percent = 0;
	similar_text(strtolower($file), strtolower($search_text), $percent);
	if($percent < 30 && !strpos(strtolower($file), strtolower($search_text))) continue;
    }
    
    $temp_info = stat($full_file);

    $file_array[$i][0] = $file;
    $file_array[$i][1] = $temp_info['size']; // size of file in bytes
    $file_array[$i][2] = $temp_info['mtime']; // last modified (as Unix timestamp)
    /* $file_array[$i][3] = date('l, M d Y | g:i A', $file_array[$i][2]); */
    $file_array[$i][3] = date('M d Y', $file_array[$i][2]);

    
    $i++;
}


/* ------------------------------------------------------------ */
/* Sorting files */
/* ------------------------------------------------------------ */

if(isset($_GET['sortby']) && is_numeric($_GET['sortby']))
    $sort_what = $_GET['sortby'];
if(isset($_GET['sortorder']) && is_numeric($_GET['sortorder']))
    $sort_how = $_GET['sortorder'];


$comp = null;
switch($sort_what){
    case 1:
	$comp = function($a, $b){
	    return strcmp(strtolower($a[0]), strtolower($b[0]));
	};
	break;
    case 2:
	$comp = function($a, $b){
	    if($a[1] == $b[1]) return 0;
	    return $a[1] < $b[1] ? -1 : 1;
	};
	break;
    case 3:
	$comp = function($a, $b){
	    if($a[2] == $b[2]) return 0;
	    return $a[2] < $b[2] ? -1 : 1;
	};
	break;
    default:
	$comp = function($a, $b) { return 0; };
	break;
}

usort($file_array, $comp);
if($sort_how == 2) {
    $file_array = array_reverse($file_array);
}



/* ------------------------------------------------------------ */
/* paging system */
/* tables */
/* ------------------------------------------------------------ */

$files_per_page = 10;
/* current page index */
$cp_index = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) ($_GET['page']) : 1;
$f_sindex = ($cp_index - 1) * $files_per_page; /* file start index */
$file_array_keys = array_keys($file_array);
$last_key = end($file_array_keys);
$total_page = ceil(($last_key + 1) / $files_per_page);

/* echo $last_key . "<br>";
 * echo $total_page . "<br>";
 * echo $files_per_page; */
/* if(f_sindex > $last_key) */
$php_self = basename($_SERVER['PHP_SELF']);

?>



<!-- Sorter UI -->
<form class="form-inline justify-content-end align-items-end" style="">
    <div class="form-group p-2">
	<label for="sortby"> Sort By</label>
	<select class="form-control" id="id_sortby" name="sortby">
        <option value="1" <?=$sort_what==1 ? 'selected' : ''?>> Name </option>
        <option value="2" <?=$sort_what==2 ? 'selected' : ''?>> Size </option>
        <option value="3" <?=$sort_what==3 ? 'selected' : ''?>> Date </option>
	</select>
    </div>
    <div class="form-group p-2" >
	<label for="sortorder">Sort Order</label>
	<select class="form-control" id="id_sortorder" name="sortorder">
        <option value="1" <?=$sort_how==1 ? 'selected' : ''?>> Asc </option>
        <option value="2" <?=$sort_how==2 ? 'selected' : ''?>> Des </option>
    </select>
    </div>
    <div class="form-group p-2" >
	<input type="submit" class="btn btn-primary btn"
	       value="Sort" name="sort" >
    </div>
</div>
</form>


<!-- Table -->
<div class="table-responsive">
    <table class="table table-sm table-hover" >
	<thead class="thead-light" >
	    <tr class="d-flex">
		<th class="col-6"> File Name </th>
		<th class="col-2"> Size (Bytes) </th>
		<th class="col-4"> Last Modified </th>
	    </tr>
	</thead>
	<tbody>
		<?php 
		?>
	    <?php for($i = $f_sindex; $i < ($f_sindex + $files_per_page) && $i <= $last_key; $i++):
		if(empty($file_array)) {
		?> 
		<p class="alert alert-danger"> There are no files currently! of search text :      '<?=isset($search_text) ? $search_text : ""?>' </p>
		<?php
			break;
		}
	    $file = $file_array[$i]; ?>
		<tr class="d-flex">
		    <td class="col-6" style="word-break: break-word"> <?php if($do_link): ?>
			<a href="<?=$directory->path . $file[0]?>"> <?php endif; ?>
			    <?=$file[0]?>
			    <?php if($do_link): ?></a> <?php endif; ?>
		    </td>
		    <td class="col-2" style="word-break: break-word">
			<?php
			/* size type
			   1 - Bytes 
			   2 - KiloBytes
			   3 - MegaBytes
			 */
			$size_type = 1;
			$size = $file[1];

			while($size >= 1024) {
			    $size = (int) ($size / 1024);
			    $size_type++;
			}
			
			echo $size;
			switch($size_type) {
			    case 1:
				echo " B";
				break;
			    case 2: 
				echo " KB";
				break;
			    case 3:
				echo " MB";
				break;
			    default:
				echo " NA";
				break;
			}


			?>

			<td class="col-4" style="word-break: break-word"> <?=$file[3]?> </td>
		</tr>
	    <?php endfor; ?>
	</tbody>
    </table>
</div>



<!-- Pagination -->
<div class="pagin_section">
    <ul class="pagination pagination-sm">
	<li class="page-item <?=($cp_index <= 1) ? "disabled" : ""?>">
	    <a class="page-link" href="<?=$php_self;?>?page=<?=$cp_index - 1?><?=$search_text?"&search_file=".$search_text:""?>">
		&laquo;
	    </a>
	</li>
	<?php for($i = 1; $i <= $total_page; $i++):  ?>
	    <li class="page-item <?=($i == $cp_index) ? "active" : ""?>">
		<a class="page-link" href="<?=$php_self;?>?page=<?=$i?><?=$search_text?"&search_file=".$search_text:""?>"> 
		    <?=$i?>
		</a>
	    </li>
	<?php endfor; ?>
	<li class="page-item <?=($cp_index >= $total_page) ? "disabled" : ""?>">
	    <a class="page-link" href="<?=$php_self;?>?page=<?=$cp_index + 1?><?=$search_text?"&search_file=".$search_text:""?>">&raquo;</a>
	</li>
    </ul>
</div>


