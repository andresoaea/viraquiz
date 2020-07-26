<?php 
/**
 * Plugin Admin Dashboard Template
 *
 * @package  viraquiz
 */

if ( !defined('ABSPATH') ) exit;

 ?>

<div class="wrap">

	<!-- Today's stats -->
	<div class="vrq-panel">

		<div class="vrq-panel-header"><span class="dashicons dashicons-chart-bar"></span> Today's statistics</div>

		<div class="vrq-panel-body">
		
			<div class="vrq-stats-box">

				<div class="vrq-stats-box-left-col">
					<span class="dashicons dashicons-businessman"></span>
				</div>	

				<div class="vrq-stats-box-right-col">
					<p class="vrq-stats-number num-users"><?php echo (int)$vrq_general_stats['new_users']; ?></p>
					<p class="vrq-stats-description">New users</p>
				</div>					

				<div class="clearfix"></div>

			</div>	


			<div class="vrq-stats-box">

				<div class="vrq-stats-box-left-col">
					<span class="dashicons dashicons-nametag"></span>
				</div>	

				<div class="vrq-stats-box-right-col">
					<p class="vrq-stats-number num-results"><?php echo (int)$vrq_daily_stats[0]['generated_results']; ?></p>
					<p class="vrq-stats-description">Generated results</p>
				</div>		

				<div class="clearfix"></div>			

			</div>	


			<div class="vrq-stats-box">

				<div class="vrq-stats-box-left-col">
					<span class="dashicons dashicons-share-alt2"></span>
				</div>	

				<div class="vrq-stats-box-right-col">
					<p class="vrq-stats-number num-shares"><?php echo (int)$vrq_daily_stats[0]['shares']; ?></p>
					<p class="vrq-stats-description">Facebook Shares</p>
				</div>		

				<div class="clearfix"></div>			

			</div>

			<div class="clearfix"></div>		

		</div><!-- .vrq-panel-body -->

	</div><!-- .vrq-panel -->





	<!-- Quizzes activity stats -->
	<div class="vrq-panel">

		<div class="vrq-panel-header"><span class="dashicons dashicons-chart-line"></span> Quizzes activity stats</div>

		<div class="vrq-panel-body">

			<div class="container vrq-chart-container"></div>

			<div id="canvas-holder" class="vrq-canvas-holder">
				<canvas id="chart-area">
			</div>

			<div class="clearfix"></div>

		</div><!-- .vrq-panel-body -->

	</div><!-- .vrq-panel -->



	<!-- Quizzes recent activity and viral quizzes -->
	<div class="vrq-full-column">

		<div class="vrq-recent-activity">

			<div class="vrq-panel">

				<div class="vrq-panel-header"><span class="dashicons dashicons-backup"></span> Recent activity</div>

				<div class="vrq-panel-body">

					<div class="vrq-recent-activity-items-container">

						<?php 
						$activities = get_option( 'viraquiz_recent_activity' );
						
						if( !empty( $activities ) ) :

							foreach ( $activities as $activity ) {

								$fbid = filter_var( $activity['fbid'], FILTER_SANITIZE_NUMBER_INT );

								if( empty( $fbid ) ) {
									continue;
								}


								if( ! get_post_status( $activity['post_id'] ) ) {
									continue;
								}

								$quiz_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $activity['post_id'] ), 'viraquiz_medium' )[0];
								if( empty( $quiz_image_url ) ) {
									$quiz_image_url = VIRAQUIZ_PLUGIN_URL . 'assets/images/default-quiz-thumbnail.jpg';
								}

								$minute = (int)$activity['date_time']['minute'];
								$minute = ( $minute < 10 ? '0' . $minute : $minute );
								$date_time = '<span class="dashicons dashicons-calendar-alt"></span> ' . (int)$activity['date_time']['day'] . ' ' . date('F', mktime( 0, 0, 0, (int)$activity['date_time']['month'], 10) ) . ' <span class="dashicons dashicons-clock"></span> ' . (int)$activity['date_time']['hour'] . ':' . $minute;

								$output = '<div class="vrq-activity-item">';
								$output .= '<div class="activity-first-col vrq-activity-col"><a href="https://facebook.com/' . $fbid . '" target="_blank"><img src="http://graph.facebook.com/' . $fbid . '/picture?width=70&height=70"></a></div>';
								$output .= '<div class="activity-second-col vrq-activity-col"><span class="user-name">' . esc_attr( $activity['fullname'] ) . '</span> <span class="action ' .  esc_attr( $activity['action'] )  . '">' . ( esc_attr( $activity['action'] ) == 'shared' ? 'shared the quiz' : 'did the quiz'  ) . '</span><p class="quiz-title">' . esc_html( get_the_title( $activity['post_id'] ) ) . '</p><p class="date-time">' . $date_time . '</p></div>';
								$output .= '<div class="activity-third-col vrq-activity-col"><img src="' . esc_url( $quiz_image_url ) . '"></div>';								
								$output .='</div>';

								echo $output;

							}

						else :

							echo '<p>No recent activity</p>';

						endif;

						?>
					</div>

				</div>

				<div class="clearfix"></div>

			</div>

		</div>

		<div class="vrq-quizzes-shares">

			<div class="vrq-panel">

				<div class="vrq-panel-header"><span class="dashicons dashicons-star-empty"></span> Viral quizzes</div>

				<div class="vrq-panel-body">

					<div class="vrq-viral-items-container">


						<?php 

						$args = array( 

							'post_type' 		=> 'viraquiz-app',
							'status'    		=> 'publish',
							'posts_per_page'	=> 20,
							'meta_key' 			=> 'viraquiz_quiz_shares',
							'orderby'			=> 'meta_value_num',
							'order'				=> 'DESC'
							
						 );

						$the_query = new \WP_Query( $args );


						 ?>

						 <?php if ( $the_query->have_posts() ) : ?>
						 
						    <!-- the loop -->
						    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

						    <div class="vrq-viral-item">

						    	<div class="vrq-viral-item-overlay">
						    		
						    		<h2><?php echo (int)get_post_meta( (int)get_the_ID(), 'viraquiz_quiz_shares', true ); ?></h2>
						    		<h3>Facebook Shares</h3>
				       				<h4><?php echo esc_html( get_the_title() ); ?></h4>

						    	</div>

						    	<?php if( has_post_thumbnail() ) : ?>
						    		<img src="<?php echo esc_url( wp_get_attachment_image_src( get_post_thumbnail_id(), 'viraquiz_medium' )[0] ); ?>">
						    	<?php else : ?>	
						    		<img src="<?php echo VIRAQUIZ_PLUGIN_URL . 'assets/images/default-quiz-thumbnail.jpg'; ?>">
						  	    <?php endif; ?>

						    </div>

				       		<br>

						    <?php endwhile; ?>
						    <!-- end of the loop -->
						 
						    <?php wp_reset_postdata(); ?>
						 
						<?php else : ?>
						    <p>No viral quizzes at the moment...</p>
						<?php endif; ?>

					</div><!-- .vrq-viral-items-container -->

				</div>

				<div class="clearfix"></div>

			</div>

		</div>

		<div class="clearfix"></div>

	</div><!-- .vrq-full-column -->



	<!-- Overall stats -->
	<div class="vrq-panel">

		<div class="vrq-panel-header"><span class="dashicons dashicons-chart-bar"></span> Overall statistics</div>

		<div class="vrq-panel-body">
		
			<div class="vrq-stats-box">

				<div class="vrq-stats-box-left-col">
					<span class="dashicons dashicons-businessman"></span>
				</div>	

				<div class="vrq-stats-box-right-col">
					<p class="vrq-stats-number num-users"><?php echo (int)$vrq_general_stats['total_users']; ?></p>
					<p class="vrq-stats-description">Total users</p>
				</div>					

				<div class="clearfix"></div>

			</div>	


			<div class="vrq-stats-box">

				<div class="vrq-stats-box-left-col">
					<span class="dashicons dashicons-nametag"></span>
				</div>	

				<div class="vrq-stats-box-right-col">
					<p class="vrq-stats-number num-results"><?php echo (int)$vrq_general_stats['total_generated_results']; ?></p>
					<p class="vrq-stats-description">Generated results</p>
				</div>		

				<div class="clearfix"></div>			

			</div>	


			<div class="vrq-stats-box">

				<div class="vrq-stats-box-left-col">
					<span class="dashicons dashicons-share-alt2"></span>
				</div>	

				<div class="vrq-stats-box-right-col">
					<p class="vrq-stats-number num-shares"><?php echo (int)$vrq_general_stats['total_shares']; ?></p>
					<p class="vrq-stats-description">Facebook Shares</p>
				</div>		

				<div class="clearfix"></div>			

			</div>

			<div class="clearfix"></div>		

		</div><!-- .vrq-panel-body -->

	</div><!-- .vrq-panel -->

</div><!-- .wrap -->