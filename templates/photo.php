<?php if( ! empty( $text ) ): ?>
	<p><?php echo $text ?></p>
<?php endif; ?>
<div class="fbfpi_photo">
	<a href="<?php echo $photo_url ?>" target="<?php echo $link_taqrget; ?>"><img src="<?php echo $photo_src ?>"></a>
	<?php if( ! empty( $photo_title ) && ! empty( $photo_text ) ): ?>
	<div class="fbfpi_text">
		<h4><?php echo $photo_title ?></h4>
		<p><?php echo $photo_text ?></p>
	</div>
	<?php endif; ?>
</div>
