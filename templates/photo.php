<?php if( ! empty( $text ) ): ?>
	<p><?php echo $text ?></p>
<?php endif; ?>
<div class="fbfpi_photo">
	<a href="<?php echo $photo_url ?>" target="<?php echo $link_taqrget; ?>"><img src="<?php echo $photo_src ?>"></a>
	<div class="fbfpi_text">
		<?php if( ! empty( $photo_title ) ): ?>
		<h4><?php echo $photo_title ?></h4>
		<?php endif; ?>
		<?php if( ! empty( $photo_text ) ): ?>
		<p><?php echo $photo_text ?></p>
		<?php endif; ?>
	</div>
</div>
