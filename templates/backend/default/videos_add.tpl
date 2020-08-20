	<!-- BEGIN PAGE CONTAINER-->
	<div class="page-content"> 
		<div class="content">  
			<!-- BEGIN PAGE TITLE -->
			<div class="page-title">
				<i class="icon-custom-left"></i>
				<h3>Videos - <span class="semi-bold">Add Videos</span></h3>
			</div>
			{include file="errmsg.tpl"}
			{if $grabbing}
				<div id="g_failed" class="col-xs-12" style="display:none;">
					<div class="alert alert-error">
						<button class="close" data-dismiss="alert"></button>
						Failed to download video!
					</div>
				</div>			
				<div id="g_ready" class="col-xs-12" style="display:none;">
					<div class="alert alert-success">
						<button class="close" data-dismiss="alert"></button>
						Video was successfully added!
					</div>
				</div>	
			{/if}
			<!-- END PAGE TITLE -->
			<!-- BEGIN PlACE PAGE CONTENT HERE -->
			<div class="col-md-12">
				<div class="grid simple">
					<div class="grid-title no-border">
						<h4>Grab <span class="semi-bold">Video</span></h4>
					</div>
					<div class="grid-body no-border">
						<form class="form-no-horizontal-spacing" name="save_video" method="POST" action="videos.php?m=add">
							<div class="row">					
								{if $video.site != ''}		
									{if $grabbing}
										<div class="col-lg-6 col-lg-offset-3 col-md-12">
											<div class="row">		
												<div class="col-xs-12 m-b-5">
													<h3>Grabbing: {$video.site} - <span class="semi-bold">{$video.title}</span></h3>
												</div>										
												<div class="form-group">
													<label class="col-lg-4 control-label">Download Progress</label>
													<div class="col-lg-8">
														<div class="progress active progress-large" style="margin-top: 9px;">
															<div id="download_progress" data-percentage="0%" style="width: 0%;" class="progress-bar progress-bar-info" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>													
														</div>
													</div>
												</div>
												<div id="sd_video_c" class="form-group" style="display:none">
													<label class="col-lg-4 control-label">SD Video</label>
													<div class="col-lg-8">
														<span id="sd_video" class="grabber-size">0</span>
													</div>
													<div class="clearfix"></div>													
												</div>
												<div id="mobile_video_c" class="form-group" style="display:none">
													<label class="col-lg-4 control-label">SD/Mobile Video</label>
													<div class="col-lg-8">
														<span id="mobile_video" class="grabber-size">0</span>
													</div>
													<div class="clearfix"></div>													
												</div>											
												<div id="hd_video_c" class="form-group" style="display:none">
													<label class="col-lg-4 control-label">HD Video</label>
													<div class="col-lg-8">
														<span id="hd_video" class="grabber-size">0</span>
													</div>
													<div class="clearfix"></div>
												</div>
												<div id="thumbnails_c" class="form-group" style="display:none">
													<label class="col-lg-4 control-label">Thumbnails</label>
													<div class="col-lg-8">
														<span id="thumbnails" class="grabber-size">0</span>
													</div>
													<div class="clearfix"></div>
												</div>												
											</div>
										</div>
									{else}								
									<div class="col-lg-6 col-lg-offset-3 col-md-12">
										<div class="row">		
											<div class="col-xs-12 m-b-5">
												<h3>Save: {$video.site} - <span class="semi-bold">{$video.title}</span></h3>
											</div>
											<input name="video_id" type="hidden" value="{$video.id}" />
											<input name="site" type="hidden" value="{$video.site}" />										
											<div class="form-group">
												<label class="col-lg-4 control-label">Username</label>
												<div class="col-lg-8">
													<input class="form-control" name="username" type="text" value="{$video.username}">
												</div>
												<div class="clearfix"></div>
											</div>
											<div class="form-group">
												<label class="col-lg-4 control-label">Title</label>
												<div class="col-lg-8">
													<input class="form-control" name="title" type="text" value="{$video.title|escape:'html'}">
												</div>
												<div class="clearfix"></div>
											</div>
											<div class="form-group">
												<label class="col-lg-4 control-label">Description</label>
												<div class="col-lg-8">
													<textarea class="form-control" name="description" rows="5" style="resize: vertical">{$video.description|escape:'html'}</textarea>
												</div>
												<div class="clearfix"></div>
											</div>											
											<div class="form-group">
												<label class="col-lg-4 control-label">Category</label>
												<div class="col-lg-8">
													<select id="category" name="category" style="width:100%">
														{section name=i loop=$categories}
														<option value="{$categories[i].CHID}"{if $video.category == $categories[i].CHID} selected="selected"{/if}>{$categories[i].name|escape:'html'}</option>
														{/section}
													</select>
												</div>
												<div class="clearfix"></div>
											</div>
											<div class="form-group">
												<label class="col-lg-4 control-label">Tags</label>
												<div class="col-lg-8">
													 <textarea class="form-control" name="tags" rows="3" style="resize: vertical">{$video.tags|escape:'html'}</textarea>
													 <span class="help">Comma separated</span>
												</div>
												<div class="clearfix"></div>
											</div>
											<div class="form-group">
												<label class="col-lg-4 control-label">Type</label>
												<div class="col-lg-8">
													<div class="radio p-t-9">
														<input id="type_pb" type="radio" name="type" value="public" {if $video.type != 'private'}checked="checked"{/if} class="radio-enabled">
														<label for="type_pb">Public</label>
														<input id="type_pv" type="radio" name="type" value="private" {if $video.type == 'private'}checked="checked"{/if} class="radio-disabled">
														<label for="type_pv">Private</label>												
													</div>
												</div>
												<div class="clearfix"></div>
											</div>
											<div class="m-b-30"></div>
											<label class="col-lg-4 control-label" style="margin-top: -9px;">Cut Intro</label>
											<div class="col-lg-8">
												<div class="checkbox check-default">
													<input name="cut_intro" id="cut_intro" type="checkbox" value="1" {if $video.cut_intro == '1'}checked{/if}>
													<label for="cut_intro">
														<input class="form-control" name="cut" type="text" value="{$video.cut}" style="margin-top:-9px;">
														<span class="help">Seconds / E.g. 3.2</span>
													</label>
												</div>
											</div>
											<div class="clearfix"></div>
											<div class="m-b-10"></div>											
											<div class="form-group grabber-url">
												{foreach from=$videos key=k item=v name=url}
													<label class="col-lg-4 control-label">{$k} / {$v.filetype} / {$v.filesize}</label>
													<div class="col-lg-8 m-b-10">
														<div class="radio">
															<input class="col-xs-9 col-sm-10" name="url[{$k}]" type="text" value="{$v.url}">
															<input id="selected_url_{$k}" name="selected_url" type="radio" value="{$k}" {if $video.selected_url == $k}checked{else}{if $smarty.foreach.url.last && $video.selected_url == ''}checked{/if}{/if}  class="radio-enabled">
															<label for="selected_url_{$k}">&nbsp;</label>
														</div>
														<input name="filesize[{$k}]" type="hidden" value="{$v.filesize}">
														<input name="filetype[{$k}]" type="hidden" value="{$v.filetype}">
													</div>
													<div class="clearfix"></div>												
												{/foreach}
											</div>
										</div>
									</div>
									{/if}
								{else}
									{if $warnings}
										<div class="col-xs-12">
											<div class="alert alert-info">
												<button class="close" data-dismiss="alert"></button>
												No classes loaded!
											</div>
										</div>
									{else}
										<div class="col-lg-6 col-lg-offset-3 col-md-12">
											<div class="row">								
												<div class="form-group">											
													{foreach from=$sites key=current item=domain}
														<div class="col-md-4 grabber-label">{$domain.name}</div>
													{/foreach}
													<div class="clearfix"></div>
												</div>
												<div class="col-xs-12 m-b-5">
													<h3>Video <span class="semi-bold">Details</span></h3>
												</div>											
												<div class="form-group">
													<label class="col-lg-4 control-label">Video URL</label>
													<div class="col-lg-8">
														<input  class="form-control" name="url" type="text" value="">
													</div>
													<div class="clearfix"></div>
												</div>
											</div>
										</div>								
									{/if}
								{/if}
							</div>
							<div class="form-actions">
								<div class="pull-right">
									{if !$warnings}
										{if $video.site == ''}
											<input name="grab_video" type="submit" value="Next" class="btn btn-success btn-cons">
											<a href="index.php" class="btn btn-white btn-cons">Cancel</a>												
										{else}
											{if $grabbing}
												<a href="videos.php?m=add" class="btn btn-success btn-cons">Grab Another Video</a>
												<a href="index.php" class="btn btn-white btn-cons">Go to Dashboard</a>
											{else}
												<input name="save_video" type="submit" value="Grab Video" id="save_video_button" class="btn btn-success btn-cons" onClick="document.getElementById('save_video_button').value='Grabbing...'">
												<a href="videos.php?m=add" class="btn btn-white btn-cons">Back</a>
											{/if}											
										{/if}
									{/if}
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>		
			<!-- END PLACE PAGE CONTENT HERE -->
		</div>
	</div>
	<!-- END PAGE CONTAINER -->	