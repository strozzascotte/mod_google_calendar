<?php
/**
 * @copyright (C) 2018 - David Neukirchen - Rheinsurfen
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined('_JEXEC') or die; 

if (count($events) !=0 ) : ?>
	<div class="mod-google-calendar">
		<ul class="next-events">
			<?php foreach ($events AS $event) : ?>
				<li class="event" itemscope itemtype="http://schema.org/Event">
					<meta itemprop="startDate" content="<?php echo JDate::getInstance($event->startDate)->toISO8601(true); ?>">
					<meta itemprop="endDate" content="<?php echo JDate::getInstance($event->endDate)->toISO8601(true); ?>">
					<div class='event-calendar' style="z-index: 5;">
						<div class='event-month'><?php echo strtoupper($event->startDate->format('M')); ?></div>
						<div class='event-day'><?php echo $event->startDate->format('d'); ?></div>
						<div class='event-dayname'><?php echo $event->startDate->format('l'); ?></div>
					</div>
					<?php if($event->startDate->format('Y-m-d') != $event->endDate->format('Y-m-d')) : ?>
						<div class='event-calendar' style="z-index: 3; position: absolute; left: 8px; top: 8px;">
							<div class='event-month'><?php echo strtoupper($event->endDate->format('M')); ?></div>
							<div class='event-day'><?php echo $event->endDate->format('d'); ?></div>
							<div class='event-dayname'><?php echo $event->endDate->format('l'); ?></div>
						</div>
					<?php endif; ?>

					<div class='event-info'>
						<?php if($params->get('show_link', true)) { ?>
							<a class='event-title' href="<?php echo $event->htmlLink; ?>" target="_blank">
								<?php echo $event->summary; ?>
							</a>
						<?php } else { ?>
							<span class='event-title'>
								<?php echo $event->summary; ?>
							</span>
						<?php } ?>
						<div class="event-duration">
							<?php echo $event->duration; ?>
						</div>
						<?php if($params->get('show_location', false) && !empty($event->location)) : ?>
							<div class="event-location"><?php echo $event->location; ?></div>
						<?php endif; ?>
						<?php if ($params->get('show_description') && (isset($event->description))): ?>
							<p class="event-description"><?php echo $event->description; ?>
							<a class='toggle'></a></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if($params->get('show_link_to_calendar', true)) : ?>
			<hr class='separator'>
			<div class='small center'>
				<?php echo JText::_('MOD_GOOGLE_CALENDAR_VIEW_FULL_CALENDAR'); ?>
				<a href="https://calendar.google.com/calendar/embed?src=<?php echo $params->get('calendar_id');?>&ctz=America%2FMexico_City" target="_blank">
					<i class="icon-calendar"></i>
				</a>
			</div>
		<?php endif; ?>
	</div>
<script type="text/javascript">
	jQuery(document).ready(function( $ ) {

		var eventDescription = jQuery('.mod-google-calendar .event-description');
		eventDescription.dotdotdot({
			keep: ".toggle",
			watch: 'window',
			callback: function( isTruncated ) {
				if ( !isTruncated ) {
					jQuery(this).find('.toggle').hide();
				} else {
					jQuery(this).find('.toggle').show();
				}  
			}
		});
		eventDescription.on(
			'click',
			'a.toggle',
			function(e) {
				e.preventDefault;
				var target = jQuery(e.target).parent();
				var n = target.data("dotdotdot");
				target.addClass('opened');
				n.restore();				
				return ;
			}
		);
	});
</script>
<?php endif; ?>