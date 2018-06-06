<?php
/**
 * Class SampleTest
 *
 * @package Blog_Or_Die
 */

require_once 'class-blogordiefuzzytimeago.php';

class FuzzyTimeAgoTest extends WP_UnitTestCase {
	private function subject( $time_difference ) {
		return new BlogOrDieFuzzyTimeAgo( current_time( 'timestamp' ) - $time_difference );
	}

	public function test_over_one_second() {
		// initialize object with one second
		$subject = $this->subject( - 1 );
		$this->assertSame( $subject->description(), 'over a second ago' );
	}

	public function test_over_multiple_seconds() {
		// initialize object with 10 seconds
		$subject = $this->subject( - 10 );
		$this->assertSame( $subject->description(), 'over 10 seconds ago' );
	}

	public function test_over_one_minute() {
		// initialize object with one minute and one second
		$subject = $this->subject( - 61 );
		$this->assertSame( $subject->description(), 'over a minute ago' );
	}

	public function test_over_multiple_minutes() {
		// initialize object with 10 minutes
		$subject = $this->subject( - 600 );
		$this->assertSame( $subject->description(), 'over 10 minutes ago' );
	}

	public function test_over_an_hour() {
		// initialize object with an hour
		$subject = $this->subject( - ( 60 * 60 ) );
		$this->assertSame( $subject->description(), 'over an hour ago' );
	}

	public function test_over_multiple_hours() {
		// initialize object with ten hours
		$subject = $this->subject( - ( 60 * 60 * 10 ) );
		$this->assertSame( $subject->description(), 'over 10 hours ago' );
	}

	public function test_over_one_day() {
		// initialize object with a day and one second
		$subject = $this->subject( - ( 60 * 60 * 24 ) );
		$this->assertSame( $subject->description(), 'over a day ago' );
	}

	public function test_over_multiple_days() {
		// initialize object with 10 days
		$subject = $this->subject( - 864000 );
		$this->assertSame( $subject->description(), 'over 10 days ago' );
	}

	public function test_over_one_year() {
		// initialize object with a year and one second
		$subject = $this->subject( - 31536000 );
		$this->assertSame( $subject->description(), 'over a year ago' );
	}

	public function test_over_multiple_years() {
		// initialize object with 10 years
		$now  = time();
		$then = strtotime( '+10 years', $now );

		$subject = $this->subject( - ( $now - $then ) );
		$this->assertSame( $subject->description(), 'over 10 years ago' );
	}
}
