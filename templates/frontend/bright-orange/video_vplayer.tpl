{if $video.embed_code != ''}
	<div class="video-embedded">
		{$video.embed_code}
	</div>
{else}
<div class="video-container">
		<video id="video" class="video-js vjs-16-9 vjs-big-play-centered vjs-sublime-skin" preload="auto" controls="true" playsinline webkit-playsinline poster="{insert name=thumb_path vid=$video.VID}/default.jpg" data-setup='{
		  "autoplay": {if $player.autoplay}true{else}false{/if}{if $vast_vpaid && $player.vast_vpaid_adv},
		  "plugins": {
		  "vastClient": {
			"adTagUrl": "{$vast_vpaid.adtagurl}",
			"adCancelTimeout": {$vast_vpaid.adscanceltimeout},
			"playAdAlways": true,		
			"adsEnabled": {if $player.vast_vpaid_adv}true{else}false{/if}
			}
		  }
		{/if}}'>
		{if $video.iphone == 1}
			<source src="{$video_root}/iphone/{$video.VID}.mp4" type='video/mp4' label='SD' res='720'/>
			{if $video.hd == 1}
				<source src="{$video_root}/hd/{$video.VID}.mp4" type='video/mp4' label='HD' res ='1080'/>
			{/if}
		{else}
			{section name=i loop=$video.files}
				<source src="{$video_root}/h264/{$video.files[i].file}" type='video/{$video.files[i].format}' label='{$video.files[i].label}' res='{$video.files[i].height}'/>
			{/section}
		{/if}
		<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
	</video>		
</div>
{/if}