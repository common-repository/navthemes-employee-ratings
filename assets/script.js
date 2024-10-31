 		function month(month){
  			document.getElementById('selectedmonth').value=month;
  			window.location.href = "<?php echo site_url(); ?>/wp-admin/edit.php?post_type=rating&page=rating-view&action=view-rating&userid=<?php  echo $userid; ?>&month=" + month; 
  			}