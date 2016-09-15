<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
* Course Options
* @version  3.0.0
*/
class LLMS_Meta_Box_Course_Options extends LLMS_Admin_Metabox {

	/**
	 * Configure the metabox settings
	 * @return void
	 * @since  3.0.0
	 */
	public function configure() {

		$this->id = 'lifterlms-course-options';
		$this->title = __( 'Course Options', 'lifterlms' );
		$this->screens = 'course';
		$this->priority = 'high';

	}

	/**
	 * Setup fields
	 * @return array
	 * @version  3.0.0
	 */
	public function get_fields() {

		global $post;

		$course = new LLMS_Course( $this->post );

		$course_tracks_options = get_terms( 'course_track', 'hide_empty=0' );
		$course_tracks = array();
		foreach ( (array) $course_tracks_options as $term ) {
			$course_tracks[] = array(
				'key' 	=> $term->term_id,
				'title' => $term->name,
			);
		}

		//setup course difficulty select options
		$difficulty_terms = get_terms( 'course_difficulty', 'hide_empty=0' );
		$difficulty_options = array();
		foreach ( $difficulty_terms as $term ) {
			$difficulty_options[] = array(
				'key' 	=> $term->slug,
				'title' => $term->name,
			);
		}

		$fields = array(
			array(
				'title' 	=> __( 'Description', 'lifterlms' ),
				'fields' 	=> array(
					array(
						'desc'   	=> __( 'If the "Non-enrolled Student Description" area below is left blank, this content will be displayed to all visitors, otherwise this content will only be displayed to enrolled students.', 'lifterlms' ),
						'id'        => '',
						'label'		=> __( 'Enrolled Student Description', 'lifterlms' ),
						'type'		=> 'post-content',
					),
					array(
						'desc' 		=> __( 'This content will only be shown to vistors who are not enrolled in this course.', 'lifterlms' ),
						'id'        => '',
						'label'		=> __( 'Non-enrolled Student Description', 'lifterlms' ),
						'type'		=> 'post-excerpt',
					),
				),
			),
			array(
				'title' 	=> __( 'General', 'lifterlms' ),
				'fields' 	=> array(
					array(
						'type'		=> 'text',
						'label'		=> __( 'Course Length', 'lifterlms' ),
						'desc' 		=> 'Enter a description of the estimated length. IE: 3 days',
						'id' 		=> $this->prefix . 'length',
						'class' 	=> 'input-full',
						'value' 	=> '',
						'desc_class' => 'd-all',
						'group' 	=> 'top',
					),
					array(
						'type'		=> 'select',
						'label'		=> __( 'Course Difficulty Category', 'lifterlms' ),
						'desc' 		=> 'Choose a course difficulty level from the difficulty categories.',
						'id' 		=> $this->prefix . 'post_course_difficulty',
						'class' 	=> 'llms-select-2',
						'value' 	=> $difficulty_options,
						'desc_class' => 'd-all',
						'group' 	=> 'bottom',
					),
					array(
						'type'		=> 'text',
						'label'		=> __( 'Featured Video', 'lifterlms' ),
						'desc' 		=> sprintf( __( 'Paste the url for a Wistia, Vimeo or Youtube video or a hosted video file. For a full list of supported providers see %s.', 'lifterlms' ), '<a href="https://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">WordPress oEmbeds</a>' ),
						'id' 		=> $this->prefix . 'video_embed',
						'class' 	=> 'code input-full',
					),
					array(
						'type'		=> 'text',
						'label'		=> __( 'Featured Audio', 'lifterlms' ),
						'desc' 		=> sprintf( __( 'Paste the url for a SoundCloud or Spotify song or a hosted audio file. For a full list of supported providers see %s.', 'lifterlms' ), '<a href="https://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">WordPress oEmbeds</a>' ),
						'id' 		=> $this->prefix . 'audio_embed',
						'class' 	=> 'code input-full',
					),
				),
			),
			array(
				'title' 	=> __( 'Restrictions', 'lifterlms' ),
				'fields' 	=> array(

					array(
						'class' 	=> 'input-full',
						'default' 	=> __( 'You must enroll in this course to access course content.', 'lifterlms' ),
						'desc'      => __( 'This message will be displayed when non-enrolled visitors attempt to access course content directly without enrolling first', 'lifterlms' ),
						'id' 		=> $this->prefix . 'content_restricted_message',
						'label'		=> __( 'Content Restricted Message', 'lifterlms' ),
						'type'		=> 'text',
					),

					array(
						'type'		    => 'checkbox',
						'label'		    => __( 'Enable Enrollment Period', 'lifterlms' ),
						'desc' 		    => __( 'Set registration start and end dates for this course', 'lifterlms' ),
						'desc_class'    => 'd-3of4 t-3of4 m-1of2',
						'id'            => $this->prefix . 'enrollment_period',
						'is_controller' => true,
						'value' 	    => 'yes',
					),
					array(
						'class' 	=> 'llms-datepicker input-full',
						'controller' => '#' . $this->prefix . 'enrollment_period',
						'controller_value' => 'yes',
						'desc'		=> __( 'Registration opens on this date.', 'lifterlms' ),
						'desc_class' => 'd-all',
						'id' 		=> $this->prefix . 'enrollment_start_date',
						'label'		=> __( 'Enrollment Start Date', 'lifterlms' ),
						'type'		=> 'date',
					),
					array(
						'class' 	=> 'llms-datepicker input-full',
						'controller' => '#' . $this->prefix . 'enrollment_period',
						'controller_value' => 'yes',
						'desc'		=> __( 'Registration closes on this date.', 'lifterlms' ),
						'desc_class' => 'd-all',
						'id' 		=> $this->prefix . 'enrollment_end_date',
						'label'		=> __( 'Enrollment End Date', 'lifterlms' ),
						'type'		=> 'date',
					),
					array(
						'class' 	=> 'input-full',
						'controller' => '#' . $this->prefix . 'enrollment_period',
						'controller_value' => 'yes',
						'default' 	=> sprintf( __( 'Enrollment in this course opens on [lifterlms_course_info id="%d" key="enrollment_start_date"].', 'lifterlms' ), $this->post->ID ),
						'desc'      => sprintf( __( 'This message will be displayed to non-enrolled visitors before the Enrollment Start Date. You may use shortcodes like [lifterlms_course_info id="%d" key="enrollment_start_date"] in this message.', 'lifterlms' ), $this->post->ID ),
						'id' 		=> $this->prefix . 'enrollment_opens_message',
						'label'		=> __( 'Enrollment Opens Message', 'lifterlms' ),
						'type'		=> 'text',
					),
					array(
						'class' 	=> 'input-full',
						'controller' => '#' . $this->prefix . 'enrollment_period',
						'controller_value' => 'yes',
						'default' 	=> sprintf( __( 'Enrollment in this course closed on [lifterlms_course_info id="%d" key="enrollment_end_date"].', 'lifterlms' ), $this->post->ID ),
						'desc'      => sprintf( __( 'This message will be displayed to non-enrolled visitors once the Enrollment End Date has passed. You may use shortcodes like [lifterlms_course_info id="%d" key="enrollment_end_date"] in this message.', 'lifterlms' ), $this->post->ID ),
						'id' 		=> $this->prefix . 'enrollment_closed_message',
						'label'		=> __( 'Enrollment Closed Message', 'lifterlms' ),
						'type'		=> 'text',
					),

					array(
						'type'		    => 'checkbox',
						'label'		    => __( 'Enable Course Time Period', 'lifterlms' ),
						'desc' 		    => __( 'Set start and end dates for this course. Content can only be viewed and completed within the selected range.', 'lifterlms' ),
						'desc_class'    => 'd-3of4 t-3of4 m-1of2',
						'id'            => $this->prefix . 'time_period',
						'is_controller' => true,
						'value' 	    => 'yes',
					),
					array(
						'class' 	=> 'llms-datepicker input-full',
						'controller' => '#' . $this->prefix . 'time_period',
						'controller_value' => 'yes',
						'desc_class' => 'd-all',
						'id' 		=> $this->prefix . 'start_date',
						'label'		=> __( 'Course Start Date', 'lifterlms' ),
						'type'		=> 'date',
					),
					array(
						'class' 	=> 'llms-datepicker input-full',
						'controller' => '#' . $this->prefix . 'time_period',
						'controller_value' => 'yes',
						'desc_class' => 'd-all',
						'id' 		=> $this->prefix . 'end_date',
						'label'		=> __( 'Course End Date', 'lifterlms' ),
						'type'		=> 'date',
					),
					array(
						'class' 	=> 'input-full',
						'controller' => '#' . $this->prefix . 'time_period',
						'controller_value' => 'yes',
						'default' 	=> sprintf( __( 'This course opens on [lifterlms_course_info id="%d" key="start_date"].', 'lifterlms' ), $this->post->ID ),
						'desc'      => sprintf( __( 'This message will be displayed to non-enrolled visitors before the Course Start Date. You may use shortcodes like [lifterlms_course_info id="%d" key="start_date"] in this message.', 'lifterlms' ), $this->post->ID ),
						'id' 		=> $this->prefix . 'course_opens_message',
						'label'		=> __( 'Enrollment Opens Message', 'lifterlms' ),
						'type'		=> 'text',
					),
					array(
						'class' 	=> 'input-full',
						'controller' => '#' . $this->prefix . 'time_period',
						'controller_value' => 'yes',
						'default' 	=> sprintf( __( 'This course closed on [lifterlms_course_info id="%d" key="end_date"].', 'lifterlms' ), $this->post->ID ),
						'desc'      => sprintf( __( 'This message will be displayed to non-enrolled visitors once the Course End Date has passed. You may use shortcodes like [lifterlms_course_info id="%d" key="end_date"] in this message.', 'lifterlms' ), $this->post->ID ),
						'id' 		=> $this->prefix . 'course_closed_message',
						'label'		=> __( 'Enrollment Closed Message', 'lifterlms' ),
						'type'		=> 'text',
					),

					array(
						'is_controller' => true,
						'type'		=> 'checkbox',
						'label'		=> __( 'Enable Prerequisite', 'lifterlms' ),
						'desc' 		=> 'Enable to choose a prerequisite course or course track',
						'id' 		=> $this->prefix . 'has_prerequisite',
						'class' 	=> '',
						'value' 	=> 'yes',
						'desc_class' => 'd-3of4 t-3of4 m-1of2',
					),
					array(
						'controller' => '#' . $this->prefix . 'has_prerequisite',
						'controller_value' => 'yes',
						'data_attributes' => array(
							'post-type' => 'course',
							'allow-clear' => true,
							'placeholder' => __( 'Select a course', 'lifterlms' ),
						),
						'class' 	=> 'llms-select2-post',
						'desc' 		=> __( 'Select a prerequisite course. Students must have completed the selected course before they can view or complete content in this course.', 'lifterlms' ),
						'id' 		=> $this->prefix . 'prerequisite',
						'type'		=> 'select',
						'label'		=> __( 'Choose Prerequisite Course', 'lifterlms' ),
						'value'     => llms_make_select2_post_array( array( $course->get( 'prerequisite' ) ) ),
					),
					array(
						'class' 	=> 'llms-select2',
						'controller' => '#' . $this->prefix . 'has_prerequisite',
						'controller_value' => 'yes',
						'desc' 		=> __( 'Select the prerequisite course track. Students must have completed the select track before they can view or complete content in this course.', 'lifterlms' ),
						'desc_class' => 'd-all',
						'id' 		=> $this->prefix . 'prerequisite_track',
						'type'		=> 'select',
						'label'		=> __( 'Choose Prerequisite Course Track', 'lifterlms' ),
						'value' 	=> $course_tracks,
					),

					array(
						'is_controller' => true,
						'type'		=> 'checkbox',
						'label'		=> __( 'Enable Course Capacity', 'lifterlms' ),
						'desc' 		=> __( 'Limit the number of users that can enroll in this course.', 'lifterlms' ),
						'id' 		=> $this->prefix . 'enable_capacity',
						'class' 	=> '',
						'value' 	=> 'yes',
						'desc_class' => 'd-3of4 t-3of4 m-1of2',
					),
					array(
						'class' 	=> 'input-full',
						'controller' => '#' . $this->prefix . 'enable_capacity',
						'controller_value' => 'yes',
						'desc_class' => 'd-all',
						'id' 		=> $this->prefix . 'capacity',
						'type'		=> 'number',
						'label'		=> __( 'Course Capacity', 'lifterlms' ),
					),
					array(
						'class' 	=> 'input-full',
						'controller' => '#' . $this->prefix . 'enable_capacity',
						'controller_value' => 'yes',
						'default' 	=> __( 'Enrollment has closed because the maximum number of allowed students has been reached.', 'lifterlms' ),
						'desc'      => __( 'This message will be displayed to non-enrolled visitors once the Course Capacity has been reached. ', 'lifterlms' ),
						'id' 		=> $this->prefix . 'capacity_message',
						'label'		=> __( 'Capacity Reached Message', 'lifterlms' ),
						'type'		=> 'text',
					),
				),
			),
		);

		return $fields;

	}

	/**
	 * Update course difficulty on save
	 * @param    int     $post_id  WP Post ID of the course
	 * @return   void
	 * @since    3.0.0
	 * @version  3.0.0
	 */
	protected function save_after( $post_id ) {

		if ( ! isset( $_POST['_llms_post_course_difficulty'] ) ) {
			$difficulty = '';
		} else {
			$difficulty = $_POST['_llms_post_course_difficulty'];
		}

		wp_set_object_terms( $post_id, $difficulty, 'course_difficulty', false );

	}

}
