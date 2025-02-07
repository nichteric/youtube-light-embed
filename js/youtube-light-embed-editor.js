/* Remove YouTube block from editor */
wp.domReady(() => {
	wp.blocks.unregisterBlockVariation('core/embed', 'youtube');
});
