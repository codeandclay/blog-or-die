<?php
/**
 * Class SampleTest
 *
 * @package Blog_Or_Die
 */

require_once 'class-blogordiefuzzytime.php';

class FuzzyTimeAgoTest extends WP_UnitTestCase {
	private function subject( $time_difference ) {
		return new BlogOrDieFuzzyTime( current_time( 'timestamp' ) - $time_difference );
	}

	public function test_over_one_second() {
		// initialize object with one second
		$subject = $this->subject( - 1 );
		$this->assertSame( $subject->over_rough_period(), 'over a second' );
	}

	public function test_over_multiple_seconds() {
		// initialize object with 10 seconds
		$subject = $this->subject( - 10 );
		$this->assertSame( $subject->over_rough_period(), 'over 10 seconds' );
	}

	public function test_over_one_minute() {
		// initialize object with one minute and one second
		$subject = $this->subject( - 61 );
		$this->assertSame( $subject->over_rough_period(), 'over a minute' );
	}

	public function test_over_multiple_minutes() {
		// initialize object with 10 minutes
		$subject = $this->subject( - 600 );
		$this->assertSame( $subject->over_rough_period(), 'over 10 minutes' );
	}

	public function test_over_an_hour() {
		// initialize object with an hour
		$subject = $this->subject( - ( 60 * 60 ) );
		$this->assertSame( $subject->over_rough_period(), 'over an hour' );
	}

	public function test_over_multiple_hours() {
		// initialize object with ten hours
		$subject = $this->subject( - ( 60 * 60 * 10 ) );
		$this->assertSame( $subject->over_rough_period(), 'over 10 hours' );
	}

	public function test_over_one_day() {
		// initialize object with a day and one second
		$subject = $this->subject( - ( 60 * 60 * 24 ) );
		$this->assertSame( $subject->over_rough_period(), 'over a day' );
	}

	public function test_over_multiple_days() {
		// initialize object with 10 days
		$subject = $this->subject( - 864000 );
		$this->assertSame( $subject->over_rough_period(), 'over 10 days' );
	}

	public function test_over_one_year() {
		// initialize object with a year and one day
		$subject = $this->subject( - 31622400 );
		$this->assertSame( $subject->over_rough_period(), 'over a year' );
	}

	public function test_over_multiple_years() {
		// initialize object with 10 years
		$now  = time();
		$then = strtotime( '+10 years', $now );

		$subject = $this->subject( - ( $now - $then ) );
		$this->assertSame( $subject->over_rough_period(), 'over 10 years' );
	}
}
