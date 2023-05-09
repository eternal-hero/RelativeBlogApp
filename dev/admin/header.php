	<div class="tab">
		<!-- <button class="tablinks active" onclick="openCity(event, 'setting')">Settings</button>
		<button class="tablinks" onclick="openCity(event, 'design')">Design</button> -->
		<a href="home1.php?shop=<?= $shop; ?>" class="homesett"><button class="tablinks <?php if (strstr($_SERVER['PHP_SELF'], "home1.php") != '') { ?>active<?php } ?>">Settings</button></a>
		
		<a href="settings1.php?shop=<?= $shop; ?>"><button class="tablinks <?php if (strstr($_SERVER['PHP_SELF'], "settings1.php") != '') { ?>active<?php } ?>">Design</button></a>
	</div>
	
	<script>
	
	<?php if (strstr($_SERVER['PHP_SELF'], "home1.php") != '') { ?>
		if (localStorage.getItem("page_loaded") == null) {
			localStorage.setItem("page_loaded", "home");
			location.reload(true);
		}	
	<?php } else if (strstr($_SERVER['PHP_SELF'], "settings1.php") != '') { ?>
		localStorage.removeItem("page_loaded");
		//location.reload(true);
	<?php } ?>		
	</script>