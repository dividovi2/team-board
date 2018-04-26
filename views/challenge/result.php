<div class="jumbotron text-center">
	<div class="container">
		<h1><?php echo $challenge_name; ?></h1>
	</div>
</div>

<style>
.fa-sigma:before {
   content: '\03A3';
   font-weight: 700;
}
</style>

<div class="container">
	<h5>NOTE: Click on the cell, what you want to edit.</h5>
	<div class="row">
		<div class="col-md-12 table-responsive">
			<table class="table table-striped table-bordered table-hover table-sm ">
				<thead class="thead-dark">
					<tr>
						<th class="table-info align-middle text-center" scope="col">Name</th>
						<?php foreach($days as $day): ?>
							<th class="align-middle text-center" scope="col">
								<?php echo $day; ?><br>
								<?php echo date('D', strtotime($day)); ?>
							</th>
						<?php endforeach; ?>
						<th class="table-info align-middle text-center" scope="col"><i class="fas fa-sigma"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$sum_total = 0;
						$day_total = array();
					?>
					<?php foreach($participants as $p):?>
						<tr>
							<?php 
								$is_user = $p->user_displayname == $user->user_displayname;
								$user_total = 0;
							?>
							<th class="<?php echo $is_user ? 'table-info': 'table-dark'; ?> " scope="row"><?php echo $p->user_displayname; ?></th>
							<?php foreach($days as $day): ?>
								<?php
									if(!array_key_exists($day, $day_total)){
										$day_total[$day] = 0;
									}
									$is_today = $day == $tday;
									$class = '';
									if($is_user && $is_today){
										$class = 'table-warning';
									}elseif($is_user){
										$class = 'table-info';
									}elseif($is_today){
										$class = 'table-primary';
									}

									$value = 0;
									$unit = '';
									$display = '-';
									if(array_key_exists($p->user_displayname, $this_week['data']) && $this_week['data'][$p->user_displayname][$day]){
										$value = $this_week['data'][$p->user_displayname][$day]->cr_result;
										$unit = $this_week['data'][$p->user_displayname][$day]->challenge_unit;
										$display = $value.' '.$unit;
										$sum_total += (int)$value;
										$day_total[$day] += (int)$value;
										$user_total += (int)$value;
									}
								?>
								<td class="cell-value <?php echo $class;?>"
								    scope="col"
									data-date="<?php echo $day; ?>"
									data-user="<?php echo $p->user_id; ?>"
									data-value="<?php echo $value; ?>"
									data-unit="<?php echo $unit; ?>"
								>
									<?php echo $display; ?>
								</td>
							<?php endforeach; ?>
							<th class="<?php echo $is_user ? 'table-info': 'table-dark'; ?>" scope="row"><?php echo $user_total; ?></th>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot class="thead-dark">
					<tr>
						<th scope="row"><i class="fas fa-sigma"></i></th>
						<?php foreach($day_total as $d):?>
							<th><?php echo $d; ?></th>
						<?php endforeach; ?>
						<th scope="row"><?php echo $sum_total; ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<canvas id="monthLineChart" width="400" height="200"></canvas>
		</div>
		<div class="col-md-6">
			<canvas id="myChart" width="400" height="200"></canvas>
		</div>
	</div>
</div>
<pre>
<?php
	$datas = array();
	$datas_stacked = array();
	foreach($this_month['data'] as $name => $r){
		$d = array(
			'label' => $name,
			'data' => array(),
			'fill' => false,
			'backgroundColor' => '#000000',
			'borderColor' => '#000000'
		);

		foreach($r as $day => $v){
			if(in_array($day, $this_month['days'])){
				if($v){
					$d['data'][] = $v->cr_result;
					$d['backgroundColor'] = '#'.$v->user_color;
					$d['borderColor'] = '#'.$v->user_color;
				}else{
					$d['data'][] = 0;
				}
			}
			
		}
		$datas[] = $d;
		$d['fill'] = true;
		$datas_stacked[] = $d;
	}
?>
</pre>
<script>
	var $active_cell = null;
	$(function(){
		var myLineChart = new Chart($("#monthLineChart"), {
			type: 'line',
			data: {
				labels: JSON.parse('<?php echo json_encode($this_month['days']);?>'),
				datasets: JSON.parse('<?php echo json_encode($datas);?>')
			},
			options: {
				title: {
					display: true,
					text: '<?php echo $chname; ?>s per person'
				},
				legend: {
					position: 'bottom'
				},
				responsive: true,
				tooltips: {
					mode: 'index'
				},
				hover: {
					mode: 'index'
				}
			}
		});
		var myStackedChart = new Chart($("#myChart"), {
			type: 'line',
			data: {
				labels: JSON.parse('<?php echo json_encode($this_month['days']);?>'),
				datasets: JSON.parse('<?php echo json_encode($datas_stacked);?>')
			},
			options: {
				title: {
					display: true,
					text: 'Daily unit <?php echo $chname; ?>s'
				},
				legend: {
					position: 'bottom'
				},
				responsive: true,
				tooltips: {
					mode: 'index'
				},
				hover: {
					mode: 'index'
				},
				scales: {
					yAxes: [{
						stacked: true
					}]
				}
			}
		});

		function deactivate(){
			if($active_cell){
				var v = parseInt($active_cell.data('value'));
				var u = $active_cell.data('unit');

				$active_cell.text((v ? v : '-')+' '+u);
			}
		}

		$(document).on('click touch', '.cell-value', function(e){
			if(e.target != this){
				return false;
			}
			e.stopPropagation();

			deactivate();
			var $this = $(this);

			$active_cell = $this;
			var user = parseInt($this.data('user'));
			var cDate = $this.data('date');
			var value = parseInt($this.data('value'));

			<?php if(!($user->user_admin && $adminState)): ?>
			if(user != <?php echo $user->user_id; ?>){
				return false;
			}
			<?php endif; ?>

			$this.empty();
			$this.append(
				$('<span class="input-group input-group-sm mb-3" style="margin: 0px!important; padding: 0px!important;"></span>').append(
					$('<input type="number" value="'+value+'"/>'),
					$('<div class="input-group-append"></div>').append(
						$('<button id="btn-save" class="btn btn-primary" title="Save"><i class="fas fa-check"></i></button>'),
						$('<button id="btn-cancel" class="btn btn-danger" title="Remove"><i class="fas fa-times"></i></button>')
					)
				)
			);
			return false;
		});

		$(document).on('click touch', function(e){
			deactivate();
			$active_cell = null;
		});

		$(document).on('click touch', '#btn-cancel', function(e){
			var $parent = $(this).parents('.cell-value');
			var user = parseInt($parent.data('user'));
			var cDate = $parent.data('date');
			var value = 0;
			<?php if(!($user->user_admin && $adminState)): ?>
			if(user != <?php echo $user->user_id; ?>){
				return false;
			}
			<?php endif; ?>

			$parent.empty();
			$parent.append(
				$('<i class="fas fa-spinner fa-spin"></i>')
			);

			$.ajax({
				url: "<?php echo site_url('challenge/save/'.$challenge_id); ?>",
				method: 'post',
				data: {
					user: user,
					date: cDate,
					result: value
				},
				success: function(data){
					location.reload();
				},
				error: function(){
					location.reload();
				}
			});
			return false;
		});

		$(document).on('click touch', '#btn-save', function(e){
			var $parent = $(this).parents('.cell-value');

			var user = parseInt($parent.data('user'));
			var cDate = $parent.data('date');
			var value = parseInt($parent.find('input').val());
			<?php if(!($user->user_admin && $adminState)): ?>
			if(user != <?php echo $user->user_id; ?>){
				return false;
			}
			<?php endif; ?>

			$parent.empty();
			$parent.append(
				$('<i class="fas fa-spinner fa-spin"></i>')
			);

			$.ajax({
				url: "<?php echo site_url('challenge/save/'.$challenge_id); ?>",
				method: 'post',
				data: {
					user: user,
					date: cDate,
					result: value
				},
				success: function(data){
					location.reload();
				},
				error: function(){
					location.reload();
				}
			});
			
			return false;
		});
		$(document).on('keypress', 'input', function(e){
			if (e.which == 13) {
				$('#btn-save').click();
				return false;
			}
		});
	});
</script>