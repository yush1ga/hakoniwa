// interface log {
// 	date: string,
// 	id: number,
// 	name: string,
// 	fromIp: string,
// 	fromHost: string
// }

class AxesLogViewer {
	// private logs: log[] = [];

	constructor() {
		console.log('AxesLogViewer::constructor();');
	}

}

const run = () => new AxesLogViewer();

if (document.readyState !== 'loading') {
	run();
} else {
	document.addEventListener('DOMContentLoaded', run);
}
