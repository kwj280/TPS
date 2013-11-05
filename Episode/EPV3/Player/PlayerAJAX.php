<?php
	$admin = $_GET['adnum'];
?>
<script type="text/javascript" src="/js/jQuery.jPlayer.2.2.0/jquery.jplayer.min.js"></script>
<script type="text/javascript">
//<![CDATA[

$(document).ready(function(){

	/*
	 * jQuery UI ThemeRoller
	 *
	 * Includes code to hide GUI volume controls on mobile devices.
	 * ie., Where volume controls have no effect. See noVolume option for more info.
	 *
	 * Includes fix for Flash solution with MP4 files.
	 * ie., The timeupdates are ignored for 1000ms after changing the play-head.
	 * Alternative solution would be to use the slider option: {animate:false}
	 */

	var myPlayer = $("#jquery_jplayer_1"),
		myPlayerData,
		fixFlash_mp4, // Flag: The m4a and m4v Flash player gives some old currentTime values when changed.
		fixFlash_mp4_id, // Timeout ID used with fixFlash_mp4
		ignore_timeupdate, // Flag used with fixFlash_mp4
		options = {
			ready: function (event) {
				// Hide the volume slider on mobile browsers. ie., They have no effect.
				if(event.jPlayer.status.noVolume) {
					// Add a class and then CSS rules deal with it.
					$(".jp-gui").addClass("jp-no-volume");
				}
				// Determine if Flash is being used and the mp4 media type is supplied. BTW, Supplying both mp3 and mp4 is pointless.
				fixFlash_mp4 = event.jPlayer.flash.used && /m4a|m4v/.test(event.jPlayer.options.supplied);
				// Setup the player with media.
				$(this).jPlayer("setMedia", {
					// mp3: "http://www.jplayer.org/audio/mp3/Miaow-07-Bubble.mp3",
					m4a: "http://www.jplayer.org/audio/m4a/Miaow-07-Bubble.m4a",
					oga: "http://www.jplayer.org/audio/ogg/Miaow-07-Bubble.ogg"
				});
			},
			timeupdate: function(event) {
				if(!ignore_timeupdate) {
					myControl.progress.slider("value", event.jPlayer.status.currentPercentAbsolute);
				}
			},
			volumechange: function(event) {
				if(event.jPlayer.options.muted) {
					myControl.volume.slider("value", 0);
				} else {
					myControl.volume.slider("value", event.jPlayer.options.volume);
				}
			},
			swfPath: "../js",
			supplied: "m4a, oga",
			cssSelectorAncestor: "#jp_container_1",
			wmode: "window"
		},
		myControl = {
			progress: $(options.cssSelectorAncestor + " .jp-progress-slider"),
			volume: $(options.cssSelectorAncestor + " .jp-volume-slider")
		};

	// Instance jPlayer
	myPlayer.jPlayer(options);

	// A pointer to the jPlayer data object
	myPlayerData = myPlayer.data("jPlayer");

	// Define hover states of the buttons
	$('.jp-gui ul li').hover(
		function() { $(this).addClass('ui-state-hover'); },
		function() { $(this).removeClass('ui-state-hover'); }
	);

	// Create the progress slider control
	myControl.progress.slider({
		animate: "fast",
		max: 100,
		range: "min",
		step: 0.1,
		value : 0,
		slide: function(event, ui) {
			var sp = myPlayerData.status.seekPercent;
			if(sp > 0) {
				// Apply a fix to mp4 formats when the Flash is used.
				if(fixFlash_mp4) {
					ignore_timeupdate = true;
					clearTimeout(fixFlash_mp4_id);
					fixFlash_mp4_id = setTimeout(function() {
						ignore_timeupdate = false;
					},1000);
				}
				// Move the play-head to the value and factor in the seek percent.
				myPlayer.jPlayer("playHead", ui.value * (100 / sp));
			} else {
				// Create a timeout to reset this slider to zero.
				setTimeout(function() {
					myControl.progress.slider("value", 0);
				}, 0);
			}
		}
	});

	// Create the volume slider control
	myControl.volume.slider({
		animate: "fast",
		max: 1,
		range: "min",
		step: 0.01,
		value : $.jPlayer.prototype.options.volume,
		slide: function(event, ui) {
			myPlayer.jPlayer("option", "muted", false);
			myPlayer.jPlayer("option", "volume", ui.value);
		}
	});


	//$("#jplayer_inspector").jPlayerInspector({jPlayer:$("#jquery_jplayer_1")});
});
//]]>

  </script>
  <style>
<!--
.jp-gui {
	position:relative;
	padding:20px;
	width: 628px;
}
.jp-gui.jp-no-volume {
	width:432px;
}
.jp-gui ul {
	margin:0;
	padding:0;
}
.jp-gui ul li {
	position:relative;
	float:left;
	list-style:none;
	margin:2px;
	padding:4px 0;
	cursor:pointer;
}
.jp-gui ul li a {
	margin:0 4px;
}
.jp-gui li.jp-repeat,
.jp-gui li.jp-repeat-off {
	margin-left:344px;
}
.jp-gui li.jp-mute,
.jp-gui li.jp-unmute {
	margin-left:20px;
}
.jp-gui li.jp-volume-max {
	margin-left:120px;
}
li.jp-pause,
li.jp-repeat-off,
li.jp-unmute,
.jp-no-solution {
	display:none;
}
.jp-progress-slider {
	position:absolute;
	top:28px;
	left:100px;
	width:300px;
}
.jp-progress-slider .ui-slider-handle {
	cursor:pointer;
}
.jp-volume-slider {
	position:absolute;
	top:31px;
	left:508px;
	width:100px;
	height:.4em;
}
.jp-volume-slider .ui-slider-handle {
	height:.8em;
	width:.8em;
	cursor:pointer;
}
.jp-gui.jp-no-volume .jp-volume-slider {
	display:none;
}
.jp-current-time,
.jp-duration {
	position:absolute;
	top:42px;
	font-size:0.8em;
	cursor:default;
}
.jp-current-time {
	left:100px;
}
.jp-duration {
	right:266px;
}
.jp-gui.jp-no-volume .jp-duration {
	right:70px;
}
.jp-clearboth {
	clear:both;
}
-->
</style>
<div id="jquery_jplayer_1" class="jp-jplayer"></div>

	<div id="jp_container_1">
		<div class="jp-gui ui-widget ui-widget-content ui-corner-all">
			<ul>
				<li class="jp-play ui-state-default ui-corner-all"><a href="javascript:;" class="jp-play ui-icon ui-icon-play" tabindex="1" title="play">play</a></li>
				<li class="jp-pause ui-state-default ui-corner-all"><a href="javascript:;" class="jp-pause ui-icon ui-icon-pause" tabindex="1" title="pause">pause</a></li>
				<li class="jp-stop ui-state-default ui-corner-all"><a href="javascript:;" class="jp-stop ui-icon ui-icon-stop" tabindex="1" title="stop">stop</a></li>
				<li class="jp-repeat ui-state-default ui-corner-all"><a href="javascript:;" class="jp-repeat ui-icon ui-icon-refresh" tabindex="1" title="repeat">repeat</a></li>
				<li class="jp-repeat-off ui-state-default ui-state-active ui-corner-all"><a href="javascript:;" class="jp-repeat-off ui-icon ui-icon-refresh" tabindex="1" title="repeat off">repeat off</a></li>
				<li class="jp-mute ui-state-default ui-corner-all"><a href="javascript:;" class="jp-mute ui-icon ui-icon-volume-off" tabindex="1" title="mute">mute</a></li>
				<li class="jp-unmute ui-state-default ui-state-active ui-corner-all"><a href="javascript:;" class="jp-unmute ui-icon ui-icon-volume-off" tabindex="1" title="unmute">unmute</a></li>
				<li class="jp-volume-max ui-state-default ui-corner-all"><a href="javascript:;" class="jp-volume-max ui-icon ui-icon-volume-on" tabindex="1" title="max volume">max volume</a></li>
			</ul>
			<div class="jp-progress-slider"></div>
			<div class="jp-volume-slider"></div>
			<div class="jp-current-time"></div>
			<div class="jp-duration"></div>
			<div class="jp-clearboth"></div>
		</div>
		<div class="jp-no-solution">
			<span>Update Required</span>
			To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
		</div>
	</div>
		
	</section>
	
</div>


	