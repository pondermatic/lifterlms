<?php
/**
 * Test LLMS_Forms_Unsupported_Versions
 *
 * @package LifterLMS/Tests/Forms
 *
 * @group forms
 * @group forms_unsupported_versions
 *
 * @since 5.0.0
 * @version 5.0.0
 */
class LLMS_Test_Forms_Unsupported_Versions extends LLMS_UnitTestCase {

	/**
	 * Set up before class
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() {
		require_once LLMS_PLUGIN_DIR . 'includes/forms/class-llms-forms-unsupported-versions.php';
	}

	public function setUp() {

		parent::setUp();
		$this->init_main();

	}

	/**
	 * Construct a new main class for testing.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function init_main() {
		$this->main = new LLMS_Forms_Unsupported_Versions();
	}

	/**
	 * Test constructor
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function test_constructor() {

		global $wp_version;
		$temp = $wp_version;

		$versions = array(
			'5.8.0' => false,
			'5.7.2' => false,
			'5.7.0' => false,
			'5.6.2' => 10,
			'5.6.0' => 10,
			'5.5.0' => 10,
		);

		foreach ( $versions as $wp_version => $expect ) {
			$this->init_main();
			$this->assertEquals( $expect, has_action( 'current_screen', array( $this->main, 'init' ) ) );
			remove_action( 'current_screen', array( $this->main, 'init' ) );
		}

		$wp_version = $temp;

	}

	/**
	 * Test init() when nothing should happen
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function test_init_for_other() {

		set_current_screen( 'admin.php' );

		$this->main->init();

		$this->assertFalse( has_action( 'admin_print_styles', array( $this->main, 'print_styles' ) ) );
		$this->assertFalse( has_action( 'admin_notices', array( $this->main, 'output_notice' ) ) );

		set_current_screen( 'front' );

	}

	/**
	 * Test init() for the forms post table list
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function test_init_for_post_table() {

		set_current_screen( 'edit-llms_form' );

		$this->main->init();

		$this->assertEquals( 10, has_action( 'admin_print_styles', array( $this->main, 'print_styles' ) ) );
		$this->assertEquals( 10, has_action( 'admin_notices', array( $this->main, 'output_notice' ) ) );

		set_current_screen( 'front' );

	}

	/**
	 * Test init() when accessing a form block editor directly
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function test_init_for_form_post() {

		$this->expectException( LLMS_Unit_Test_Exception_Redirect::class );
		$this->expectExceptionMessage( 'http://example.org/wp-admin/edit.php?post_type=llms_form [302] YES' );

		try {

			set_current_screen( 'llms_form' );
			$this->main->init();

		} catch ( LLMS_Unit_Test_Exception_Redirect $exception ) {

			set_current_screen( 'front' );
			throw $exception;

		}

	}
}
