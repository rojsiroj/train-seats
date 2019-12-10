<?php
	
	function prettyArray($array=[])
	{
		print("<pre>".print_r($array, true)."</pre>");
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

	// echo "Gerbongnya";
	// prettyArray($fieldGerbong);
	// // echo "Seluruh Bangku";
	// // prettyArray($fieldSeat);
	// echo "Jumlah Bangku Kosong";
	// prettyArray($seatAvailable);
	// echo "Bangku Kosong";
	// prettyArray($fieldSeatAvailable);
	// prettyArray($fieldSeatStatus);
 ?>

 <!DOCTYPE html>
<html lang="en">
<head>
  <title>Tab Train Seat Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style type="text/css">
  	.radio-toolbar input[type="radio"] {
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

	.radio-toolbar .not-active {

	}

	.radio-toolbar input[type="radio"]:checked+label {
	  background-color: #f58634;
	  color: #fff;
	}
  </style>
</head>
<body>

<div class="container">
  <h2>Ganti Kursi</h2>
  <ul class="nav nav-tabs">
  	<?php foreach($fieldGerbong as $value) { 
  		$gerbongActive = reset($fieldGerbong); ?>
    	<li class="<?php echo ($value == $gerbongActive) ? 'active' : '';?>"><a data-toggle="tab" href="#seatList-<?php echo $value ?>">Gerbong <?php echo $value; ?></a></li>
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
	      			<input type="radio" name="seat" value="" id="" disabled="disabled">
  					<label for="" class="not-active">X</label>
	      		<?php }else{ ?>
	      			<input type="radio" name="seat" value="<?php echo $seat ?>" id="<?php echo $seat ?>">

  					<label for="<?php echo $seat ?>" class="active"><?php echo $seat ?></label>
	      		<?php } ?>
	      	<?php } ?>
	      	<br>
	      <?php } ?>
	    </div>
  	<?php } ?>
  </div>
</div>

</body>
</html>
