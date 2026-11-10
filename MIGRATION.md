# Migration from this historical example

Reviewed 2026-11-10. The source is retained for existing integrations and historical reference. Current mainstream browsers do not run Flash applications. No attempt was made to certify the legacy scripts for a new public deployment.

For browser broadcasting and playback, use [the current solution](https://demo.videowhisper.com/vws-html5-livestreaming/). Other current demos are [Video Conference](https://demo.videowhisper.com/video-conference/), [Webcam Streaming WebRTC](https://demo.videowhisper.com/Webcam-Streaming-WebRTC/), [HTML5 one-way streaming](https://demo.videowhisper.com/vws-html5-livestreaming/), [HTML5 two-way call](https://demo.videowhisper.com/p2p-html5-videocall/) and [webcam/microphone/screen recorder](https://demo.videowhisper.com/webcam-microphone-screen-recorder/).

Back up the current code, private configuration and media separately. Inventory embeds, room/channel identifiers, session callbacks, recording policies and server dependencies. Build the new integration in a separate HTTPS path with a dedicated account/callback; test real media and access policy before changing landing links. Preserve old links with an explanatory landing page where possible. Do not copy configuration credentials or recordings into GitHub or a new demo, and do not delete old media as part of migration.

Video Conference is a new four-person P2P mesh app. Its camera, screen sharing, chat and host controls do not replace the historical file sharing, shared playback or large-room server functionality. Those need a separate implementation and review. The public signaling-server source update is a separate release; the live developer demo already uses the compatible managed server.

[Request migration/setup help](https://consult.videowhisper.com/).
