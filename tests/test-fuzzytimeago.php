<?php
/**
 * Class SampleTest
 *
 * @package Blog_Or_Die
 */

require_once 'class-blogordiefuzzytimeago.php';

class FuzzyTimeAgoTest extends WP_UnitTestCase {
	public function test_over_one_day() {
		// initialize object with a day and one second
		$subject = new BlogOrDieFuzzyTimeAgo( current_time( 'timestamp' ) - 86401 );
		$this->assertTrue( $subject->description() === 'over a day ago' );
	}
}
