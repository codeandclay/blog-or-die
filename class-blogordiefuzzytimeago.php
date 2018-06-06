<?php

defined( 'ABSPATH' ) || die();

class BlogOrDieFuzzyTimeAgo {
	private $reference_timestamp;

	public function __construct( $reference_timestamp ) {
		$this->reference_timestamp = $reference_timestamp;
	}

	public function description() {
		$periods        = array_filter( array_reverse( $this->time_periods_passed() ) );
		$longest_period = array_slice( $periods, 0, 1 );

		// I prefer: 'a day' over '1 days'
		if ( reset( $longest_period ) === 1 ) {
			$longest_period = [ substr( key( $longest_period ), 0, -1 ) => 'a' ];
		}

		return 'over ' . reset( $longest_period ) . ' ' . key( $longest_period ) . ' ago';
	}

	private function age_of_reference_timestamp() {
		return current_time( 'timestamp' ) - $this->reference_timestamp;
	}

	private function periods() {
		return [
			'seconds' => 1,
			'minutes' => 60,
			'hours'   => 60,
			'days'    => 24,
			'years'   => 365,
		];
	}

	private function time_periods_passed() {
		// TODO: Find a more elegant way to do this
		$periods_elapsed = array_reduce(
			array_slice( $this->periods(), 1 ), function( $acc, $period ) {
				return array_merge(
					$acc, [
						intdiv( end( $acc ), $period ),
					]
				);
			}, [ $this->age_of_reference_timestamp() ]
		);
		return array_combine( array_keys( $this->periods() ), $periods_elapsed );
	}
}
