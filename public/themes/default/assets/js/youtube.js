async function getTranscript(videoUrl, defaultLang = 'en') {
	try {
		const response = await fetch(videoUrl, { method: 'GET' });
		const responseText = await response.text();
		const httpCode = response.status;

		// Check for IP block (e.g., status code 403)
		if (httpCode === 403) {
			return { error: 'IP blocked', status_code: 403 };
		}

		// Search for "captionTracks" in the HTML content
		const captionTracksMatch = responseText.match(/"captionTracks":(\[.*?\])/);
		if (captionTracksMatch && captionTracksMatch[1]) {
			const captionTracks = JSON.parse(captionTracksMatch[1]);

			for (const track of captionTracks) {
				if (track.languageCode.includes(defaultLang)) {
					const baseUrl = decodeURIComponent(track.baseUrl);
					const captions = await fetchCaptions(baseUrl);
					return {
						captions: cleanYouTubeTranscript(captions).text,
						status_code: 200
					};
				}
			}

			// If the default language is not found, return the first available caption
			if (captionTracks[0] && captionTracks[0].baseUrl) {
				const baseUrl = decodeURIComponent(captionTracks[0].baseUrl);
				const captions = await fetchCaptions(baseUrl);
				return { captions, status_code: 200 };
			} else {
				return { error: 'No caption tracks found.', status_code: 404 };
			}
		} else {
			return { error: 'No captionTracks found in the response.', status_code: 404 };
		}
	} catch (error) {
		return { error: error.message, status_code: 500 };
	}
}

async function fetchCaptions(baseUrl) {
	const response = await fetch(baseUrl, { method: 'GET' });
	return await response.text();
}

function cleanYouTubeTranscript(xmlString) {
	const parser = new DOMParser();
	const xmlDoc = parser.parseFromString(xmlString, 'text/xml');
	const textElements = xmlDoc.getElementsByTagName('text');

	const transcriptArray = [];
	let cleanedTranscript = '';

	for (let textElement of textElements) {
		const start = textElement.getAttribute('start');
		const dur = textElement.getAttribute('dur');
		let text = textElement.textContent.trim();

		// Remove any unwanted characters or HTML entities
		text = decodeURIComponent(text.replace(/&#(\d+);/g, (match, dec) => String.fromCharCode(dec)));
		text = text.replace(/\s+/g, ' '); // Replace multiple spaces with a single space

		// Add to the transcript array
		transcriptArray.push({
			start,
			duration: dur,
			text
		});

		// Append to the cleaned transcript string
		cleanedTranscript += text + ' ';
	}

	return {
		array: transcriptArray,
		text: cleanedTranscript.trim()
	};
}

// Example usage:
// (Assume you have a video URL)
// getTranscript('https://www.youtube.com/watch?v=R0hAI0qUvmk')
//     .then(data => console.log(data))
//     .catch(error => console.error('Error:', error));
