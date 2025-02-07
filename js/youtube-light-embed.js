/*
NOTE: Use `localStorage.removeItem('ytle-disclaimer');` to clear value of session storage.
*/

const ytleMaxZ = 29;
const ytleAcceptDuration = 1000 * 60 * 60 * 24 * 14;
const ytleStorage = localStorage; // sessionStorage // null;

window.addEventListener('load', ytleInit);
let ytleVideos = {};

function ytleAccept() {
	try {
		ytleStorage.setItem('ytle-disclaimer', Date.now());
	} catch (e) {}
}

function ytleHasAccepted() {
	try {
		const now = Date.now();
		const ts = parseInt(ytleStorage.getItem('ytle-disclaimer'));
		if (now - ts < ytleAcceptDuration) {
			return true;
		} else {
			ytleStorage.removeItem('ytle-disclaimer');
			return false;
		}
	} catch (e) {
		return false;
	}
}

function ytleInit() {
	// Adjust z-index of container to ensure that disclaimers are not hidden by rows of videos...
	let z = ytleMaxZ;
	for (let ytle of document.querySelectorAll('.acf-block-youtube_light_embed')) {
		ytle.style.zIndex = z;
		z = z - 1;
	}

	const resizeObserver = new ResizeObserver(ytleResizeObserverHandler);

	for (let ytlePlayer of document.querySelectorAll('.ytle-player')) {
		let videoId = ytlePlayer.dataset.id;
		let listId = ytlePlayer.dataset.list;
		let start = ytlePlayer.dataset.start;
		let blockID = ytlePlayer.parentNode.id;

		ytleVideos[blockID] = { ytlePlayer: ytlePlayer, posterLoaded: false, dH: 0 };

		// If the privacy disclaimer is missing, add it (old embeds...)
		if (!ytlePlayer.querySelector('.ytle-privacy-disclaimer')) {
			console.log('adding missing disclaimer');
			let ytleDisclaimer = document.createElement('div');
			ytleDisclaimer.setAttribute('class', 'ytle-privacy-disclaimer');
			let span = document.createElement('span');
			span.innerHTML = YouTubeLightEmbedDisclaimer;
			ytleDisclaimer.appendChild(span);
			ytlePlayer.prepend(ytleDisclaimer);
		}

		// Create player container
		let ytleCont = document.createElement('div');
		ytleCont.classList.add('ytle-cont');
		ytleCont.setAttribute('data-id', videoId);
		ytleCont.setAttribute('data-list', listId);
		ytleCont.setAttribute('data-start', start);
		ytlePlayer.prepend(ytleCont);

		// Add video poster image
		let ytlePoster = document.createElement('img');

		ytlePoster.addEventListener('load', ytlePosterLoadEvent);
		ytlePoster.addEventListener('error', ytlePosterLoadEvent);
		ytlePoster.src = `${YouTubeLightEmbedImgP}?id=${videoId}`;
		ytlePoster.alt = 'video poster';
		ytleCont.appendChild(ytlePoster);

		// Accept button
		ytlePlayer.querySelector('.ytle-accept').addEventListener('click', (e) => {
			const delay = parseFloat(window.getComputedStyle(document.querySelector('.ytle-privacy-disclaimer')).transitionDuration) * 1000;
			let ytlePlayer = e.target.closest('.ytle-player');
			ytleAccept();
			ytleHideDisclaimer(ytlePlayer);
			setTimeout(() => {
				ytleInsertVideo(ytlePlayer);
			}, delay);
		});

		// Deny button
		ytlePlayer.querySelector('.ytle-deny').addEventListener('click', (e) => {
			let ytlePlayer = e.target.closest('.ytle-player');
			ytleHideDisclaimer(ytlePlayer);
		});

		ytleAddPlayButton(ytlePlayer);
		resizeObserver.observe(ytlePlayer);
	}
}

function ytlePosterLoadEvent(e) {
	let ytlePlayer = e.target.parentNode.parentNode;
	let blockID = ytlePlayer.parentNode.id;
	ytleVideos[blockID].posterLoaded = true;
	if (!ytleIsDisclaimerTooTall(ytlePlayer)) {
		ytlePlayer.querySelector('.ytle-privacy-disclaimer').style.visibility = 'visible';
	}
}

function ytleResizeObserverHandler(entries) {
	for (const entry of entries) {
		let ytlePlayer = entry.target;
		let blockID = ytlePlayer.parentNode.id;
		let vH = entry.contentBoxSize[0].blockSize;
		let dH = ytlePlayer.querySelector('.ytle-privacy-disclaimer').getBoundingClientRect().height;
		ytleVideos[blockID].dH = dH;

		if (!ytleVideos[blockID].posterLoaded) {
			continue;
		}

		if (dH > vH) {
			ytlePlayer.querySelector('.ytle-privacy-disclaimer').style.visibility = 'hidden';
		} else {
			ytlePlayer.querySelector('.ytle-privacy-disclaimer').style.visibility = 'visible';
		}
	}
}

function ytleIsDisclaimerTooTall(ytlePlayer) {
	let vH = ytlePlayer.querySelector('.ytle-cont').getBoundingClientRect().height;
	let dH = ytlePlayer.querySelector('.ytle-privacy-disclaimer').getBoundingClientRect().height;
	return dH > vH;
}

function ytleShowDisclaimer(ytlePlayer) {
	let blockID = ytlePlayer.parentNode.id;
	if (!ytleIsDisclaimerTooTall(ytlePlayer)) {
		let h = ytleVideos[blockID].dH;
		ytlePlayer.querySelector('.ytle-privacy-disclaimer').style.bottom = `-${h}px`;
	} else {
		let accept = window.confirm(YouTubeLightEmbedDisclaimer.replace(/<\/?[^>]+(>|$)/g, ''));
		if (accept) {
			ytleAccept();
			ytleHideDisclaimer(ytlePlayer);
			ytleInsertVideo(ytlePlayer);
		} else {
			ytleHideDisclaimer(ytlePlayer);
		}
	}
}

function ytleHideDisclaimer(ytlePlayer) {
	ytlePlayer.querySelector('.ytle-privacy-disclaimer').style.bottom = ``;
}

function ytleAddPlayButton(ytlePlayer) {
	let ytleCont = ytlePlayer.querySelector('.ytle-cont');
	let playButton = document.createElement('div');
	playButton.setAttribute('class', 'play');
	ytleCont.appendChild(playButton);

	playButton.addEventListener('click', function (e) {
		if (ytleHasAccepted()) {
			ytleInsertVideo(ytlePlayer);
		} else {
			ytleShowDisclaimer(ytlePlayer);
		}
	});
}

function ytleInsertVideo(ytlePlayer) {
	let playButton = ytlePlayer.querySelector('.play');

	div = playButton.parentNode;
	let iframe = document.createElement('iframe');
	let iframeURL =
		'https://www.youtube-nocookie.com/embed/' +
		div.dataset.id +
		'?autoplay=1&rel=0' +
		(div.dataset.list != '' && div.dataset.list != 'undefined' ? '&list=' + div.dataset.list : '') +
		(div.dataset.start != '' && div.dataset.start != 'undefined' ? '&start=' + div.dataset.start : '');
	iframe.setAttribute('src', iframeURL);
	iframe.setAttribute('frameborder', '0');
	iframe.setAttribute('allowfullscreen', '1');
	iframe.setAttribute('allow', 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture');
	div.parentNode.replaceChild(iframe, div);
}
