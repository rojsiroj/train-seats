<?php
	
	function prettyArray($array=[])
	{
		print("<pre>".print_r($array, true)."</pre>");
	}

	$seatSelected = [];

	if(isset($_GET['train'])){
		$selected 		= $_GET['train'];
		$wagonSelected 	= explode('-', $selected);
		for ($i=0; $i < count($wagonSelected); $i++) { 
			$arraySelected 	= explode('/', $wagonSelected[$i]);

			$trainSelected[$i]	= $arraySelected[0];
			$seatSelected[$i]	= $arraySelected[1];
		}
	}

	$train_seats = file_get_contents('kai_seats.json');

	$train_data  = json_decode($train_seats, true);

	$response_data = $train_data['response'];
	$departure_data = $response_data['departure'];

	foreach ($departure_data as $key => $departure) {
		for ($i=0; $i < count($departure[2]); $i++) {
			$fieldSeat[$departure[1]][$departure[2][$i][3]][] 	= $departure[2][$i][0].$departure[2][$i][3];
			$fieldSeatStatus[$departure[1]][$departure[2][$i][0].$departure[2][$i][3]] 	= $departure[2][$i][5];
			if(empty($departure[2][$i][5])){
				$fieldGerbong[$departure[1]]  = $departure[1];
				$seatAvailable[$departure[1]] += 1;
				$fieldSeatAvailable[$departure[1]][$departure[2][$i][3]][] 	= $departure[2][$i][0].$departure[2][$i][3];
			}
		}
	}
 ?>

 <!DOCTYPE html>
<html lang="en">
<head>
  <title>Tab Train Seat Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="bootstrap.min.css">
  <script src="jquery.min.js"></script>
  <script src="bootstrap.min.js"></script>
  <style type="text/css">
  	.radio-toolbar.active input[type="checkbox"] {
	  display: none;
	}

	.radio-toolbar label {
	  display: inline-block;
	  padding: 4px 11px;
	  font-family: Arial;
	  font-size: 16px;
	  cursor: pointer;
	}

	.radio-toolbar label.active {
	  background-color: #ddd;
	}

	.radio-toolbar label.not-active {
	  background-color: #000;
	  cursor: not-allowed;
	}

	/*.radio-toolbar.active input[type="checkbox"]:checked+label {
	  background-color: #f58634;
	  color: #fff;
	}*/

	.radio-checked+label {
	  background-color: #f58634;
	  color: #fff;
	}

	.label-checked {
	  background-color: #f58634 !important;
	  color: #fff;
	}

	.btn-save{
		margin-top: 10px;
	}
  </style>
</head>
<body>

<div class="container">
  <h2>Ganti Kursi</h2>
  <ul class="nav nav-tabs">
  	<?php foreach($fieldGerbong as $value) { 
  		$gerbongActive = reset($fieldGerbong);

  		if(isset($trainSelected)){
  			$popular = array_count_values($trainSelected);
			arsort($popular);
			reset($popular);
			$first_key = key($popular);
  			$gerbongActive = $first_key;
  		}
  	?>
    	<li class="<?php echo ($value == $gerbongActive) ? 'active' : '';?>"><a class="tab-button" id="tab-<?php echo $value ?>" href="#seatList-<?php echo $value ?>">Gerbong <?php echo $value; ?></a></li>
  	<?php } ?>
  </ul>

  <div class="tab-content">
  	<?php foreach ($fieldSeat as $key => $seatAvailable) { ?>
	    <div id="seatList-<?php echo $key;?>" class="tab-pane fade in radio-toolbar <?php echo ($key == $gerbongActive) ? 'active' : ''; ?>">
	      <h3>Gerbong <?php echo $key; ?></h3>
	      <?php foreach ($seatAvailable as $seatColumn => $seatRow) { ?>
	      	<span class="seatColumn"><?php echo $seatColumn; ?></span>
	      	<?php foreach ($seatRow as $seatKey => $seat) { ?>
	      		<?php if(!empty($fieldSeatStatus[$key][$seat])){ ?>
	      			<input type="checkbox" name="seat" value="" id="" disabled="disabled">
  					<label for="" class="not-active"><?php echo $seat ?></label>
	      		<?php }else{ ?>
	      			<input type="checkbox" name="seat" value="<?php echo $seat ?>" id="<?php echo $key.'-'.$seat ?>" <?php echo in_array($key.'/'.$seat, $wagonSelected) ? "checked='checked'" : "";?> data-key="<?php echo $key;?>" class="single-checkbox <?php echo in_array($key.'/'.$seat, $wagonSelected) ? "radio-checked" : "";?>">

  					<label for="<?php echo $key.'-'.$seat ?>" class="active <?php echo in_array($key.'/'.$seat, $wagonSelected) ? "label-checked" : "";?>"><?php echo $seat ?></label>
	      		<?php } ?>
	      	<?php } ?>
	      	<br>
	      <?php } ?>
	    </div>
  	<?php } ?>
  	<div class="row">
  		<div class="col-lg-2 pull-right btn-save">
			<input type="submit" name="" id="submit" class="btn btn-primary">
  		</div>
  	</div>
  </div>
</div>

<script type="text/javascript">
	$('.tab-button').click(function () {
	    $(this).tab('show');
	});

	$('#submit').click(function(){
		var seat  = [];
		var train = [];

        $('.radio-checked').each(function(i){
          seat[i] = $(this).val();
          train[i] = $(this).data('key');
        });
		// for (var i = 0; i < $('input[name=seat]:checked').length; i++) {
		// 	seat[i] = $('input[name=seat]:checked')[i].val()
		// 	train[i] = $('input[name=seat]:checked')[i].data('key')
		// }

		// alert(train+"/"+seat);
		console.log(train);
		console.log(seat);
	});


	var limit = 3;
	var allTabLimit = $('input.single-checkbox.radio-checked').length;

	$('label.active').click(function(){
		var id = $(this).attr('for');
		if($(this).hasClass('label-checked')){
			allTabLimit--;
			$('input.single-checkbox#'+id).removeAttr('checked');
			$('input.single-checkbox#'+id).removeClass('radio-checked');
			$(this).removeClass('label-checked');
		}else{
			if(allTabLimit < limit){
				allTabLimit++;
				$('input.single-checkbox#'+id).attr('checked', 'checked');
				$('input.single-checkbox#'+id).addClass('radio-checked');
				$(this).addClass('label-checked');
			}
		}
	})

</script>

</body>
</html>
