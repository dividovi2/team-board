<div class="jumbotron text-center">
	<div class="container">
		<h1>SUGGESTION FOR YOUR LOTTERY DAYS:</h1>
		<h3 style="color:red;">
			<div class="row">
				<?php for($i = 0; $i < LOTTERY_DAY_NUMS; $i++): ?>
					<div class="col-md-<?php echo floor(12/LOTTERY_DAY_NUMS); ?>"><?php echo $weekDays[$i]; ?></div>
				<?php endfor; ?>
			</div>
		</h3>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-md-4"></div>
		<div class="col-md-4">
			<?php if(!$user->user_disabled && $user->user_admin && $adminState):?>
				<h5>Debug:</h5>
				<?php
					echo '<p>Seed: ' . $seed . '</p>';
					echo '<p>Order: </p><pre>';
					print_r($weekDays);
					echo '</pre>';
				?>
			<?php endif; ?>
		</div>
		<div class="col-md-4"></div>
	</div>
</div>