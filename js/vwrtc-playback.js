//VideoWhisper.com WebRTC implemetation
let startTime;
var remoteVideo = null;
var peerConnection = null;
var peerConnectionConfig = {'iceServers': []};
var localStream = null;
var wsConnection = null;
var repeaterRetryCount = 0;
var newAPI = true;
var retrying = false;

var enhanceData = new Object();
var SDPOutput = new Object();


//WebRTC Playback

function browserReady()
{
	remoteVideo = document.getElementById('remoteVideo');

	if(navigator.mediaDevices.getUserMedia)
	{
		newAPI = true;
		
	}else if (navigator.getUserMedia)
	{
		newAPI = false;
	}

	if (audioBitrate !== undefined)
		enhanceData.audioBitrate = Number(audioBitrate);
	if (videoBitrate !== undefined)
		enhanceData.videoBitrate = Number(videoBitrate);
	if (videoFrameRate !== undefined)
		enhanceData.videoFrameRate = Number(videoFrameRate);
	
remoteVideo.addEventListener('resize', () => {
  console.log(`Remote video size changed to ${remoteVideo.videoWidth}x${remoteVideo.videoHeight}`);
  // We'll use the first onsize callback as an indication that video has started
  // playing out.
  if (startTime) {
    const elapsedTime = window.performance.now() - startTime;
    console.log('Setup time: ' + elapsedTime.toFixed(3) + 'ms');
    startTime = null;
  }
});
	
	console.log("browserReady newAPI: "+newAPI);
	
	startPlay();
}

function wsConnect(url)
{	
	wsConnection = new WebSocket(url);
	wsConnection.binaryType = 'arraybuffer';
	
	wsConnection.onopen = function()
	{
		retrying = false; //success
		
		console.log("wsConnection.onopen");
		
		peerConnection = new RTCPeerConnection(peerConnectionConfig);
		
		//peerConnection.addTransceiver('audio'); //chrome error
		//peerConnection.addTransceiver('video'); 

		//peerConnection.onicecandidate = gotIceCandidate;	
		peerConnection.addEventListener('icecandidate', e => gotIceCandidate(peerConnection, e));
		
		
		peerConnection.onsignalingstatechange = stateCallback2;
		//peerConnection.oniceconnectionstatechange = iceStateCallback2;
		  peerConnection.addEventListener('iceconnectionstatechange', e => onIceStateChange(peerConnection, e));

		
		if (newAPI)
		{
			//peerConnection.ontrack = gotRemoteTrack;
			peerConnection.addEventListener('track', gotRemoteTrack);
		}
		else
		{
			peerConnection.onaddstream = gotRemoteStream;
		}

		console.log("wsURL: "+wsURL);	

		sendPlayGetOffer();
	
	}
	
function stateCallback2() {
  let state;
  if (peerConnection) {
    state = peerConnection.signalingState || peerConnection.readyState;
    console.log(`peerConnection state change callback, state: ${state}`);
    
    jQuery("#sdpDataTag").html('Peer: ' + state);
  }
}


function onIceStateChange(pc, event) {
  let iceState;
  if (pc) {
    iceState = pc.iceConnectionState;
    console.log(` ICE state: ${pc.iceConnectionState}`);
    console.log('ICE state change event: ', event);
      
     jQuery("#sdpDataTag").html('Peer connnection: ' + iceState);
        
    if (iceState == "connected")
    {

		var promise = remoteVideo.play();
		
		if (promise !== undefined) {
		    promise.catch(error => {
		        console.log('Auto play error:' + error);
		        // Show a UI element to let the user manually start playback
		    }).then(() => {
		        console.log('Auto-play started');
		    });
		}
	    
    }
    
    //retry
    if (iceState == "disconnected" || iceState =="failed")
    if (!retrying)
    {
	    setTimeout(function(){ 
		    retrying = true;
		    stopPlay(); 
		    startPlay(); 
		    }, 
		    3000)
    }
    
  }
}

	
	function sendPlayGetOffer()
	{
		console.log("sendPlayGetOffer: "+JSON.stringify(streamInfo));
		wsConnection.send('{"direction":"play", "command":"getOffer", "streamInfo":'+JSON.stringify(streamInfo)+', "userData":'+JSON.stringify(userData)+'}');
	}


	wsConnection.onmessage = function(evt)
	{
		console.log("wsConnection.onmessage: "+evt.data);
		
		var msgJSON = JSON.parse(evt.data);
		
		var msgStatus = Number(msgJSON['status']);
		var msgCommand = msgJSON['command'];
		
		if (msgStatus == 514) // repeater stream not ready
		{
			repeaterRetryCount++;
			if (repeaterRetryCount < 10)
			{
				setTimeout(sendGetOffer, 500);
			}
			else
			{
				jQuery("#sdpDataTag").html('Live stream repeater timeout: '+streamName);
				stopPlay();
			}
		}
		else if (msgStatus != 200)
		{
			jQuery("#sdpDataTag").html(msgJSON['statusDescription']);
			stopPlay();
		}
		else
		{
			jQuery("#sdpDataTag").html("");

			var streamInfoResponse = msgJSON['streamInfo'];
			if (streamInfoResponse !== undefined)
			{
				streamInfo.sessionId = streamInfoResponse.sessionId;
			}

			var sdpData = msgJSON['sdp'];
			if (sdpData !== undefined)
			{
				console.log('wsConnection.onmessage: setRemoteDescription original sdp: '+JSON.stringify(msgJSON['sdp']));


				//msgJSON.sdp.sdp = enhanceSDP(msgJSON.sdp.sdp, enhanceData);
				//console.log('gotDescription: enhanceSDP: '+JSON.stringify({'sdp': msgJSON.sdp}));

				peerConnection.setRemoteDescription(new RTCSessionDescription(msgJSON.sdp), function() {
					peerConnection.createAnswer(gotDescription, errorHandler);
				}, errorHandler);
			}

			var iceCandidates = msgJSON['iceCandidates'];
			if (iceCandidates !== undefined)
			{
				for(var index in iceCandidates)
				{
					console.log('iceCandidates: '+JSON.stringify(iceCandidates[index]));
					
					//peerConnection.addIceCandidate(new RTCIceCandidate(iceCandidates[index]));	
					peerConnection.addIceCandidate(new RTCIceCandidate(iceCandidates[index])).then(() => onAddIceCandidateSuccess(peerConnection), err => onAddIceCandidateError(peerConnection, err));
    
				}
			}
		}
		
		if ('sendResponse'.localeCompare(msgCommand) == 0)
		{
			if (wsConnection != null)
				wsConnection.close();
			wsConnection = null;
		}

	}
	
	wsConnection.onclose = function()
	{
		console.log("wsConnection.onclose");
	}
	
	wsConnection.onerror = function(evt)
	{
		console.log("wsConnection.onerror: "+JSON.stringify(evt));
		
		jQuery("#sdpDataTag").html('WebSocket connection failed: '+wsURL);
	}
}

//
function addAudio(sdpStr, audioLine)
{
	var sdpLines = sdpStr.split(/\r\n/);
	var sdpSection = 'header';
	var hitMID = false;
	var sdpStrRet = '';
	var done = false;

	for(var sdpIndex in sdpLines)
	{
		var sdpLine = sdpLines[sdpIndex];

		if (sdpLine.length <= 0)
			continue;


		sdpStrRet +=sdpLine;
		sdpStrRet += '\r\n';

		if ( 'a=rtcp-mux'.localeCompare(sdpLine) == 0 && done == false )
		{
			sdpStrRet +=audioLine;
			done = true;
		}


	}
	return sdpStrRet;
}

function addVideo(sdpStr, videoLine)
{
	var sdpLines = sdpStr.split(/\r\n/);
	var sdpSection = 'header';
	var hitMID = false;
	var sdpStrRet = '';
	var done = false;

	var rtcpSize = false;
	var rtcpMux = false;

	for(var sdpIndex in sdpLines)
	{
		var sdpLine = sdpLines[sdpIndex];

		if (sdpLine.length <= 0)
			continue;

		if ( sdpLine.includes("a=rtcp-rsize") )
		{
			rtcpSize = true;
		}

		if ( sdpLine.includes("a=rtcp-mux") )
		{
			rtcpMux = true;
		}

	}

	for(var sdpIndex in sdpLines)
	{
		var sdpLine = sdpLines[sdpIndex];

		sdpStrRet +=sdpLine;
		sdpStrRet += '\r\n';

		if ( ('a=rtcp-rsize'.localeCompare(sdpLine) == 0 ) && done == false && rtcpSize == true)
		{
			sdpStrRet +=videoLine;
			done = true;
		}

		if ( 'a=rtcp-mux'.localeCompare(sdpLine) == 0 && done == true && rtcpSize == false)
		{
			sdpStrRet +=videoLine;
			done = true;
		}

		if ( 'a=rtcp-mux'.localeCompare(sdpLine) == 0 && done == false && rtcpSize == false )
		{
			done = true;
		}

	}
	return sdpStrRet;
}

function enhanceSDP(sdpStr, enhanceData)
{
	var sdpLines = sdpStr.split(/\r\n/);
	var sdpSection = 'header';
	var hitMID = false;
	var sdpStrRet = '';
	var audioMLines = null;


	if ( !sdpStr.includes("THIS_IS_SDPARTA") || videoChoice.includes("VP9") )
	{
		for(var sdpIndex in sdpLines)
		{
			var sdpLine = sdpLines[sdpIndex];

			if (sdpLine.length <= 0)
				continue;

			var doneCheck = checkLine(sdpLine);
			if ( !doneCheck )
				continue;

			sdpStrRet +=sdpLine;
			sdpStrRet += '\r\n';

		}
		sdpStrRet =  addAudio(sdpStrRet, deliverCheckLine(audioChoice,"audio"));
		sdpStrRet =  addVideo(sdpStrRet, deliverCheckLine(videoChoice,"video"));
		sdpStr = sdpStrRet;
		sdpLines = sdpStr.split(/\r\n/);
		sdpStrRet = '';
	}

	for(var sdpIndex in sdpLines)
	{
		var sdpLine = sdpLines[sdpIndex];

		if (sdpLine.length <= 0)
			continue;

		if ( sdpLine.indexOf("m=audio") ==0 && audioIndex !=-1 )
		{
			audioMLines = sdpLine.split(" ");
			sdpStrRet+=audioMLines[0]+" "+audioMLines[1]+" "+audioMLines[2]+" "+audioIndex+"\r\n";
			continue;
		}

		if ( sdpLine.indexOf("m=video") == 0 && videoIndex !=-1 )
		{
			audioMLines = sdpLine.split(" ");
			sdpStrRet+=audioMLines[0]+" "+audioMLines[1]+" "+audioMLines[2]+" "+videoIndex+"\r\n";
			continue;
		}

		sdpStrRet += sdpLine;

		if (sdpLine.indexOf("m=audio") === 0)
		{
			sdpSection = 'audio';
			hitMID = false;
		}
		else if (sdpLine.indexOf("m=video") === 0)
		{
			sdpSection = 'video';
			hitMID = false;
		}
		else if (sdpLine.indexOf("a=rtpmap") == 0 )
		{
			sdpSection = 'bandwidth';
			hitMID = false;
		}

		if (sdpLine.indexOf("a=mid:") === 0 || sdpLine.indexOf("a=rtpmap") == 0 )
		{
			if (!hitMID)
			{
				if ('audio'.localeCompare(sdpSection) == 0)
				{
					if (enhanceData.audioBitrate !== undefined)
					{
						sdpStrRet += '\r\nb=CT:' + (enhanceData.audioBitrate);
						sdpStrRet += '\r\nb=AS:' + (enhanceData.audioBitrate);
					}
					hitMID = true;
				}
				else if ('video'.localeCompare(sdpSection) == 0)
				{
					if (enhanceData.videoBitrate !== undefined)
					{
						sdpStrRet += '\r\nb=CT:' + (enhanceData.videoBitrate);
						sdpStrRet += '\r\nb=AS:' + (enhanceData.videoBitrate);
						if ( enhanceData.videoFrameRate !== undefined )
							{
								sdpStrRet += '\r\na=framerate:'+enhanceData.videoFrameRate;
							}
					}
					hitMID = true;
				}
				else if ('bandwidth'.localeCompare(sdpSection) == 0 )
				{
					var rtpmapID;
					rtpmapID = getrtpMapID(sdpLine);
					if ( rtpmapID !== null  )
					{
						var match = rtpmapID[2].toLowerCase();
						if ( ('vp9'.localeCompare(match) == 0 ) ||  ('vp8'.localeCompare(match) == 0 ) || ('h264'.localeCompare(match) == 0 ) ||
							('red'.localeCompare(match) == 0 ) || ('ulpfec'.localeCompare(match) == 0 ) || ('rtx'.localeCompare(match) == 0 ) )
						{
							if (enhanceData.videoBitrate !== undefined)
								{
								sdpStrRet+='\r\na=fmtp:'+rtpmapID[1]+' x-google-min-bitrate='+(enhanceData.videoBitrate)+';x-google-max-bitrate='+(enhanceData.videoBitrate);
								}
						}

						if ( ('opus'.localeCompare(match) == 0 ) ||  ('isac'.localeCompare(match) == 0 ) || ('g722'.localeCompare(match) == 0 ) || ('pcmu'.localeCompare(match) == 0 ) ||
								('pcma'.localeCompare(match) == 0 ) || ('cn'.localeCompare(match) == 0 ))
						{
							if (enhanceData.audioBitrate !== undefined)
								{
								sdpStrRet+='\r\na=fmtp:'+rtpmapID[1]+' x-google-min-bitrate='+(enhanceData.audioBitrate)+';x-google-max-bitrate='+(enhanceData.audioBitrate);
								}
						}
					}
				}
			}
		}
		sdpStrRet += '\r\n';
	}
	console.log("Resuling SDP: "+sdpStrRet);
	return sdpStrRet;
}

function deliverCheckLine(profile,type)
{
	var outputString = "";
	for(var line in SDPOutput)
	{
		var lineInUse = SDPOutput[line];
		outputString+=line;
		if ( lineInUse.includes(profile) )
		{
			if ( profile.includes("VP9") || profile.includes("VP8"))
			{
				var output = "";
				var outputs = lineInUse.split(/\r\n/);
				for(var position in outputs)
				{
					var transport = outputs[position];
					if (transport.indexOf("transport-cc") !== -1 || transport.indexOf("goog-remb") !== -1 || transport.indexOf("nack") !== -1)
					{
						continue;
					}
					output+=transport;
					output+="\r\n";
				}

				if (type.includes("audio") )
				{
					audioIndex = line;
				}

				if (type.includes("video") )
				{
					videoIndex = line;
				}

				return output;
			}
			if (type.includes("audio") )
			{
				audioIndex = line;
			}

			if (type.includes("video") )
			{
				videoIndex = line;
			}
			return lineInUse;
		}
	}
	return outputString;
}

function checkLine(line)
{
	if ( line.startsWith("a=rtpmap") || line.startsWith("a=rtcp-fb") || line.startsWith("a=fmtp"))
	{
		var res = line.split(":");

		if ( res.length>1 )
		{
			var number = res[1].split(" ");
			if ( !isNaN(number[0]) )
			{
				if ( !number[1].startsWith("http") && !number[1].startsWith("ur") )
				{
					var currentString = SDPOutput[number[0]];
					if (!currentString)
					{
						currentString = "";
					}
					currentString+=line+"\r\n";
					SDPOutput[number[0]]=currentString;
					return false;
				}
			}
		}
	}

	return true;
}

function getrtpMapID(line)
{
	var findid = new RegExp('a=rtpmap:(\\d+) (\\w+)/(\\d+)');
	var found = line.match(findid);
	return (found && found.length >= 3) ? found: null;
}


//
	
function onAddIceCandidateSuccess(pc) {
  console.log(` onAddIceCandidateSuccess success`);
}

function onAddIceCandidateError(pc, error) {
  console.log(` onAddIceCandidateError failed to add ICE Candidate: ${error}`);
}


function startPlay()
{
	repeaterRetryCount = 0;
	
	console.log("startPlay: wsURL:"+wsURL+" streamInfo:"+JSON.stringify(streamInfo));
	
	wsConnect(wsURL);
	
}

function stopPlay()
{
	if (peerConnection != null)
		peerConnection.close();
	peerConnection = null;
	
	if (wsConnection != null)
		wsConnection.close();
	wsConnection = null;
	
	remoteVideo.src = ""; // this seems like a chrome bug - if set to null it will make HTTP request
	//remoteVideo.srcObject = null; //2

	console.log("stopPlay");
}

// start button clicked
function start() 
{

	if (peerConnection == null)
		startPlay();
	else
		stopPlay();
}

function gotMessageFromServer(message) 
{
	var signal = JSON.parse(message.data);
	if(signal.sdp) 
	{
		if (signal.sdp.type == 'offer')
		{
			console.log('gotMessageFromServer sdp:offer original signal.sdp.sdp:' + signal.sdp.sdp);
		
			//signal.sdp.sdp = enhanceSDP(signal.sdp.sdp, enhanceData);
			//console.log('gotMessageFromServer: enhanceSDP signal.sdp.sdp: '+JSON.stringify({'sdp': signal.sdp.sdp}));

			peerConnection.setRemoteDescription(new RTCSessionDescription(signal.sdp), function() {
				peerConnection.createAnswer(gotDescription, errorHandler);
			}, errorHandler);
		}
		else
		{
			console.log('gotMessageFromServer sdp:not-offer: '+signal.sdp.type);
		}

	}
	else if(signal.ice)
	{
		console.log('gotMessageFromServer ice: '+JSON.stringify(signal.ice));
		
		//peerConnection.addIceCandidate(new RTCIceCandidate(signal.ice));
		peerConnection.addIceCandidate(new RTCIceCandidate(signal.ice)).then(() => onAddIceCandidateSuccess(peerConnection), err => onAddIceCandidateError(peerConnection, err));
	}
}

async function gotIceCandidate(pc, event) 
{
   try {
    // await (getOtherPc(pc).addIceCandidate(event.candidate));
     onAddIceCandidateSuccess(pc);
   } catch (e) {
     onAddIceCandidateError(pc, e);
   }
   console.log(` gotIceCandidate ICE candidate:\n${event.candidate ? event.candidate.candidate : '(null)'}`);
 }


function gotDescription(description) 
{
	console.log('gotDescription' + JSON.stringify({'sdp': description}) );
	
	//description.sdp = enhanceSDP(description.sdp, enhanceData);
	//console.log('gotDescription: enhanceSDP: '+JSON.stringify({'sdp': description}));

	peerConnection.setLocalDescription(description, function () 
	{
		console.log('sendAnswer');

		wsConnection.send('{"direction":"play", "command":"sendResponse", "streamInfo":'+JSON.stringify(streamInfo)+', "sdp":'+JSON.stringify(description)+', "userData":'+JSON.stringify(userData)+'}');

	}, function() {console.log('set description error')});
}


function gotRemoteTrack(event) 
{

	console.log('gotRemoteTrack: kind:'+event.track.kind+' stream:'+event.streams[0]);
	

if (remoteVideo.srcObject !== event.streams[0]) {
    remoteVideo.srcObject = event.streams[0];
    console.log('playback peer received remote stream');
  }
  
  /*
	if (event.streams[0] == remoteVideo.srcObject ) console.log('Same stream received');
	//else
	{
	// reset srcObject to work around minor bugs in Chrome and Edge.
	remoteVideo.srcObject = null;
	remoteVideo.srcObject = event.streams[0]; 
	}
*/

}


function gotRemoteStream(event) 
{
	console.log('gotRemoteStream: '+event.stream);
	
	remoteVideo.srcObject = event.stream; 
}

function errorHandler(error) 
{
	console.log(error);
}
