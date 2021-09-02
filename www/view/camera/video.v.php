<h1>Video</h1>
<?php if (empty($cameraGroups)) { ?>
	<p class="alert alert-info">No camera to display.</p>
<?php } ?>
<?php foreach ($cameraGroups as $cameraGroup) { ?>
	<section class="cameras">
		<h6><?=$cameraGroup['groupName']?></h6>
		<div class="row">
			<?php foreach ($cameraGroup[CAMERAS] as $camera) { ?>
				<article class="col-md-6">
					<h5 style="margin-bottom:0px"><?=$camera[CAM_NAME]?></h5>
					<img src="<?=sprintf(CAM_URL_FORMAT, $camera[URL], $camera[CAM_NAME], $camera[PASSWORD])?>" width="100%">
				</article>
			<?php } ?>
		</div>
	</section>
<?php } ?>