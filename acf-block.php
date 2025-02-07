<?php
/**
 * Block Name: Youtube Light Embed
 */

// FIXME: localize `Click <span class="dashicon dashicons dashicons-edit"></span> to add video ID or video URL.`

global $YouTubeLightEmbed;
$options = $YouTubeLightEmbed->options;

$id = 'youtube_light_embed-' . $block['id'];
$align_class = $block['align'] ? 'align' . $block['align'] : '';

$vID = '';
$lID = '';
$time = '';
$video_ID = get_field('video_ID');
$video_url = get_field('video_url');

if ($video_url) {
	preg_match('/((v=([-a-zA-Z0-9_]{11,})(&|))|\/([-a-zA-Z0-9_]{11,})(\?|))/', $video_url, $matches);
	if (!empty($matches[3])) {
		$vID = $matches[3];
	} elseif (!empty($matches[5])) {
		$vID = $matches[5];
	}
	preg_match('/(time_continue|start|t)=([0-9hms]+)/', $video_url, $matches);
	if (!empty($matches[2])) {
		$time = $matches[2];
	}
	preg_match('/(list=([-a-zA-Z0-9_]{11,})(&|))/', $video_url, $matches);
	if (!empty($matches[2])) {
		$lID = $matches[2];
	}
} elseif ($video_ID) {
	preg_match('/([-a-zA-Z0-9_]{11,})/', $video_ID, $matches);
	if (count($matches) > 0) {
		$vID = $matches[0];
	}
}
$video_poster = plugin_dir_url(__FILE__) . 'poster.php?id=' . $vID;
?>

<section id="<?= $id ?>" class="acf-block-youtube_light_embed <?= $align_class ?>">
    <?php if (is_admin()): ?>
    <?php if (empty($video_ID) && empty($video_url)): ?>
    <svg width="32" height="32" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false" fill="red" stytle="vertical-align: middle;">
        <path d="M21.8 8s-.195-1.377-.795-1.984c-.76-.797-1.613-.8-2.004-.847-2.798-.203-6.996-.203-6.996-.203h-.01s-4.197 0-6.996.202c-.39.046-1.242.05-2.003.846C2.395 6.623 2.2 8 2.2 8S2 9.62 2 11.24v1.517c0 1.618.2 3.237.2 3.237s.195 1.378.795 1.985c.76.797 1.76.77 2.205.855 1.6.153 6.8.2 6.8.2s4.203-.005 7-.208c.392-.047 1.244-.05 2.005-.847.6-.607.795-1.985.795-1.985s.2-1.618.2-3.237v-1.517C22 9.62 21.8 8 21.8 8zM9.935 14.595v-5.62l5.403 2.82-5.403 2.8z"></path>
    </svg><b>YouTube Light Embed</b><br>
    Click <span class="dashicon dashicons dashicons-edit"></span> to add video ID or video URL.
    <?php else: ?>
    <div stytle="position: relative">
        <img src="<?= $video_poster ?>" stytle="width: 100%; display: block" alt="video poster">
        <div style="width: 100%; background-size: 60px 60px; background-position: center; background-repeat: no-repeat; background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGFyaWEtaGlkZGVuPSJ0cnVlIiByb2xlPSJpbWciIHdpZHRoPSIxZW0iIGhlaWdodD0iMWVtIiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCBtZWV0IiB2aWV3Qm94PSIwIDAgMjQgMjQiPjxnIGZpbGw9Im5vbmUiPjxnIGNsaXAtcGF0aD0idXJsKCNzdmdJRGEpIj48cGF0aCBmaWxsPSIjZmYwMDAwIiBkPSJNMjMuNSA2LjUwN2EyLjc4NiAyLjc4NiAwIDAgMC0uNzY2LTEuMjdhMy4wNSAzLjA1IDAgMCAwLTEuMzM4LS43NDJDMTkuNTE4IDQgMTEuOTk0IDQgMTEuOTk0IDRhNzYuNjI0IDc2LjYyNCAwIDAgMC05LjM5LjQ3YTMuMTYgMy4xNiAwIDAgMC0xLjMzOC43NmMtLjM3LjM1Ni0uNjM4Ljc5NS0uNzc4IDEuMjc2QTI5LjA5IDI5LjA5IDAgMCAwIDAgMTJjLS4wMTIgMS44NDEuMTUxIDMuNjguNDg4IDUuNDk0Yy4xMzcuNDc5LjQwNC45MTYuNzc1IDEuMjY5Yy4zNzEuMzUzLjgzMy42MDggMS4zNDEuNzQzYzEuOTAzLjQ5NCA5LjM5LjQ5NCA5LjM5LjQ5NGE3Ni44IDc2LjggMCAwIDAgOS40MDItLjQ3YTMuMDUgMy4wNSAwIDAgMCAxLjMzOC0uNzQyYTIuNzggMi43OCAwIDAgMCAuNzY1LTEuMjdBMjguMzggMjguMzggMCAwIDAgMjQgMTIuMDIzYTI2LjU3OSAyNi41NzkgMCAwIDAtLjUtNS41MTdaTTkuNjAyIDE1LjQyNFY4LjU3N2w2LjI2IDMuNDI0bC02LjI2IDMuNDIzWiIvPjwvZz48ZGVmcz48Y2xpcFBhdGggaWQ9InN2Z0lEYSI+PHBhdGggZmlsbD0iI2ZmZiIgZD0iTTAgMGgyNHYyNEgweiIvPjwvY2xpcFBhdGg+PC9kZWZzPjwvZz48L3N2Zz4K');position: absolute; top: 0; right: ; left: 0; bottom: 0;"></div>
    </div>
    <?php endif; ?>
    <?php else: ?>
    <div class="ytle-player <?= $align_class ?>" data-id="<?= $vID ?>" data-list="<?= $lID ?>" data-start="<?= $time ?>">
    <div class="ytle-privacy-disclaimer">
			<div>
				<svg width="16" height="16" fill="none" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M16 8C16 10.1217 15.1571 12.1566 13.6569 13.6569C12.1566 15.1571 10.1217 16 8 16C5.87827 16 3.84344 15.1571 2.34315 13.6569C0.842855 12.1566 0 10.1217 0 8C0 5.87827 0.842855 3.84344 2.34315 2.34315C3.84344 0.842855 5.87827 0 8 0C10.1217 0 12.1566 0.842855 13.6569 2.34315C15.1571 3.84344 16 5.87827 16 8ZM9 4C9 4.26522 8.89464 4.51957 8.70711 4.70711C8.51957 4.89464 8.26522 5 8 5C7.73478 5 7.48043 4.89464 7.29289 4.70711C7.10536 4.51957 7 4.26522 7 4C7 3.73478 7.10536 3.48043 7.29289 3.29289C7.48043 3.10536 7.73478 3 8 3C8.26522 3 8.51957 3.10536 8.70711 3.29289C8.89464 3.48043 9 3.73478 9 4ZM7 7C6.73478 7 6.48043 7.10536 6.29289 7.29289C6.10536 7.48043 6 7.73478 6 8C6 8.26522 6.10536 8.51957 6.29289 8.70711C6.48043 8.89464 6.73478 9 7 9V12C7 12.2652 7.10536 12.5196 7.29289 12.7071C7.48043 12.8946 7.73478 13 8 13H9C9.26522 13 9.51957 12.8946 9.70711 12.7071C9.89464 12.5196 10 12.2652 10 12C10 11.7348 9.89464 11.4804 9.70711 11.2929C9.51957 11.1054 9.26522 11 9 11V8C9 7.73478 8.89464 7.48043 8.70711 7.29289C8.51957 7.10536 8.26522 7 8 7H7Z" fill="white"/>
				</svg>
				<div><?= $options['disclaimer'] ?></div>
				<div class="ytle-buttons">
					<button class="ytle-deny"><?= $options['deny_button'] ?></button>
					<button class="ytle-accept"><?= $options['accept_button'] ?></button>
				</div>
			</div>
		</div>
</div>
    <?php endif; ?>
</section>
