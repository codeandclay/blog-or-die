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
		if ( reset( $longest_period ) === '1' ) {
			$longest_period = [ substr( key( $longest_period ), 0, -1 ) => 'a' ];
		}

		return 'over ' . reset( $longest_period ) . ' ' . key( $longest_period ) . ' ago';
	}

	private function periods() {
		return [
			'seconds',
			'minutes',
			'hours',
			'days',
			'years',
		];
	}

	private function time_periods_passed() {
		$now  = new DateTime();
		$then = new DateTime( "@$this->reference_timestamp" );

		$difference = $now->diff( $then )->format( '%s,%i,%h,%a,%y' );
		return array_combine( $this->periods(), explode( ',', $difference ) );
	}
}
