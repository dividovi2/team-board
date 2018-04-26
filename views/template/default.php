<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $_title?></title>
		<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<link rel="shortcut icon" href="<?php echo base_url('favicon.ico'); ?>">

		<link rel="stylesheet"
			  type="text/css"
			  href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
			  integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4"
			  crossorigin="anonymous"/>

		<link rel="stylesheet"
			  href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
			  integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg"
			  crossorigin="anonymous"/>

		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" crossorigin="anonymous"></script>
	</head>
	<body>
		<nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light">
			<div class="navbar-brand">Welcome <?php echo $user ? $user->user_displayname : 'Anonymous';?></div>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item active">
						<a class="nav-link" href="<?php echo site_url('vote/result');?>">Food voter</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Challenges
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
						<?php foreach($challenge_menus as $cm): ?>
							<a class="dropdown-item" href="<?php echo site_url('challenge/result/'.$cm->challenge_id);?>"><?php echo $cm->challenge_name;?></a>
						<?php endforeach; ?>
						</div>
					</li>
					<li class="nav-item active">
						<a class="nav-link" href="<?php echo site_url('lottery/index');?>">Lottery</a>
					</li>
				</ul>
				<?php if($user && $user->user_admin): ?>
				<?php $uri = rtrim(strtr(base64_encode($this->uri->uri_string()), '+/', '-_'), '='); ?>
				<ul class="navbar-nav ml-auto">
					<li class="nav-item active">
					<?php if($adminState): ?>
						<a class="nav-link btn btn-success"
						   href="<?php echo site_url('challenge/toggleAdminState/'.$uri);?>"
						>Admin state: ON</a>
						<?php else: ?>
						<a class="nav-link btn btn-danger"
						   href="<?php echo site_url('challenge/toggleAdminState/'.$uri);?>"
						>Admin state: OFF</a>
					<?php endif; ?>
					</li>
				</ul>
				<?php endif; ?>
			</div>
		</nav>
		<?php if($user): ?>
			<?php $this->load->view($_view)?>
		<?php else: ?>
			<?php $this->load->view('no_user')?>
		<?php endif; ?>
		
		<hr>
		<footer class="container text-center">
			<p>CsD 2018 &copy; <a href="https://github.com/csd-dev/lunch-of-fortune" target="_blank">GitHub - Lunch of Fortune</a></p>
			<p>DaC 2018 &copy; <a href="https://github.com/dividovi2/team-board" target="_blank">GitHub - Team Board</a></p>
		</footer>

		<?php if(isset($_SESSION['result_error'])): ?>
		<nav class="ua navbar fixed-bottom navbar-light alert-warning">
			<div class="navbar-brand" ><?php echo $_SESSION['result_error']; ?></div>
		</nav>
		<?php endif; ?>
		<?php if(isset($_SESSION['result_success'])): ?>
		<nav class="ua navbar fixed-bottom navbar-light alert-success">
			<div class="navbar-brand" ><?php echo $_SESSION['result_success']; ?></div>
		</nav>
		<?php endif; ?>
		<script>
    		setTimeout(function(){$('.ua').fadeOut();}, 4000);
		</script>
	</body>
</html>
