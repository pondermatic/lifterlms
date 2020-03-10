<?php
/**
 * Test LLMS_Comments
 *
 * @package LifterLMS/Tests
 *
 * @group comments
 *
 * @since [version]
 */
class LLMS_Test_Comments extends LLMS_Unit_Test_Case {

	/**
	 * Test wp_count_comments() when passing in a specific post id.
	 *
	 * @since [version]
	 *
	 * @return void
	 */
	public function test_wp_count_comments_specific_post() {

		$expect = array();
		$this->assertEquals( $expect, LLMS_Comments::wp_count_comments( $expect, 123 ) );

	}

	/**
	 * Test wp_count_comments() when the transient already exists.
	 *
	 * @since [version]
	 *
	 * @return void
	 */
	public function test_wp_count_comments_transient_exists() {

		$expect = array( 1 );
		set_transient( 'llms_count_comments', $expect, 10 );

		$this->assertEquals( $expect, LLMS_Comments::wp_count_comments( $expect, 0 ) );

	}

	/**
	 * Test wp_count_comments()
	 *
	 * @since [version]
	 *
	 * @return void
	 */
	public function test_wp_count_comments() {

		// Insert 5 regular comments.
		$this->factory->comment->create_many( 5 );

		// Insert 5 other custom comment types (we don't want to mess with other plugins).
		$this->factory->comment->create_many( 5, array( 'comment_type' => 'custom_type' ) );

		// Insert 5 order notes, these will be excluded.
		$this->factory->comment->create_many( 5, array( 'comment_type' => 'llms_order_note' ) );

		$res = LLMS_Comments::wp_count_comments( array(), 0 );

		// Ensure the function creates the stats object in the correct format.
		$keys = array( 'approved', 'moderated', 'spam', 'trash', 'post-trashed', 'total_comments', 'all' );
		$this->assertEqualSets( $keys, array_keys( get_object_vars( $res ) ) );

		// Order notes should be excluded.
		$this->assertEquals( 10, $res->total_comments );
		$this->assertEquals( 10, $res->all );
		$this->assertEquals( 10, $res->approved );

		// All of these are default 0.
		$this->assertEquals( 0, $res->moderated );
		$this->assertEquals( 0, $res->spam );
		$this->assertEquals( 0, $res->trash );
		$this->assertEquals( 0, $res->{'post-trashed'} );

	}

}
