<div class="jumbotron text-center">
	<div class="container">
		<h1>Please give me, your vote</h1>
	</div>
</div>
<div class="container">
	<?php echo form_open(); ?>
		<?php if(validation_errors()): ?>
			<div class="ua alert alert-warning" role="alert">
				<?php echo validation_errors(); ?>
			</div>
		<?php endif; ?>
		<div class="form-group">
			<label for="vote">Where do you want to eat?</label>
			<input type="text" name="vote" class="form-control" id="vote" aria-describedby="voteHelp" placeholder="" value="<?php echo $vote; ?>">
			<small id="voteHelp" class="form-text text-muted">Please tell me :) </small>
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>
