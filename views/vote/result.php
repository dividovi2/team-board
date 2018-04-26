<div class="jumbotron text-center">
	<div class="container">
		<h1>SUGGESTION FOR YOUR LUNCH TODAY:</h1>
		<div class="alert alert-info" role="alert">
            <?php if(time() > strtotime($date." ".VOTE_UNTIL)): ?>
				<?php if(!$winner): ?>
					<h2><?php echo HEAD_NOVALID; ?></h2>
				<?php else: ?>
                	<h2><?php echo $winner->vote_vote;?></h2>
				<?php endif; ?>
            <?php else: ?>
                <h2><?php echo HEAD_ANNOUNCE; ?> <?php echo VOTE_UNTIL;?></h2>
            <?php endif; ?>
            
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-4"></div>
		<div class="col-md-4 table-responsive">
			<table class="table table-striped table-bordered table-hover table-sm ">
				<thead class="thead-dark">
					<tr>
						<th scope="col">Name</th>
						<th scope="col">Vote</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($order as $vote):?>
						<tr>
							<th scope="row"><?php echo $vote->user_displayname; ?></th>
							<td><?php echo $vote->vote_vote; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div class="row">
				<a class="btn btn-primary btn-block"
				href="<?php echo site_url('vote/vote'); ?>">
				Edit my Vote
				</a>
			</div>
		</div>
		<div class="col-md-4">
			<?php if(!$user->user_disabled && $user->user_admin && $adminState):?>
				<h5>Debug:</h5>
				<?php
					echo '<p>Rand value: ' . $index . '</p>';
					echo '<p>Seed: ' . $seed . '</p>';
					echo '<p>Winner: ' . ($winner ? $winner->vote_vote : 'Invalid') . '</p>';
				?>
			<?php endif; ?>
		</div>
	</div>
</div>