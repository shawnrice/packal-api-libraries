<?php

define( 'PACKAL_BASE_API_URL', 'http://localhost:3000/api/v1/alfred2/' );

class Packal {

	public function __construct( $type, $params ) {
		$types = [ 'workflow', 'theme', 'report' ];
		if ( ! in_array( $type, $types ) ) {
			throw new Exception( "$type is not a valid type. Valid types are: " . implode( ', ', $types ) );
		}
		$this->params = $params;
		// Standard setup
		$this->ch = curl_init();
		// PACKAL_BASE_API_URL
		curl_setopt( $this->ch, CURLOPT_URL, PACKAL_BASE_API_URL . $type . '/submit' );
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $this->ch, CURLOPT_POST, true );
		curl_setopt( $this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' ) );

		// Call the submit method
		if ( ! call_user_func_array( [ $this, $type ], [ $params ] ) ) {
			throw new Exception( "Could not call {$type} method" );
			// This should be an exception
			return false;
		}

		// Add in username and password
		$this->postData = array_merge( $this->postData, $this->standard() );
		$this->build_data();
	}

	public function execute() {
		$result = curl_exec($this->ch);
		curl_close($this->ch);
		return $result;
	}

	private function workflow( $params ) {
		if ( ! $this->ensure_keys([ 'file' ], $params ) ) {
			return false;
		}
		$file = $params['file'];
		$contents = file_get_contents( $file );
		$data = 'data:' . explode( ';', exec("file --mime -b $ '{$file}'") )[0] . ';base64,' . base64_encode( $contents );
		$this->postData = [ 'workflow_revision' => [ 'file' => $data ], 'alfred_version' => 2 ];
		return true;
	}

	private function theme( $params ) {
		if ( ! $this->ensure_keys([ 'name', 'description', 'tags', 'uri' ], $params ) ) {
			return false;
		}
		$params['alfred2'] = true;
		$this->postData = [ 'theme' => $params ];
		return true;
	}

	private function report( $params ) {
		if ( ! $this->ensure_keys([ 'workflow_revision_id', 'report_type', 'message' ], $params ) ) {
			return false;
		}
		$this->postData = [ 'report' => $params ];
		return true;
	}

	private function standard() {
		return [
			'username' => $this->get_username(),
			'password' => $this->get_password()
		];
	}
	private function get_username() {
		return 'Shawn Patrick Rice';
	}

	private function get_password() {
		return '12345678';
	}

	private function build_data( ) {
		curl_setopt( $this->ch, CURLOPT_POSTFIELDS, json_encode( $this->postData ) );
	}

	private function ensure_keys( $keys, $params ) {
		foreach ( $keys as $key ) :
			if ( ! isset( $params[ $key ] ) ) {
				return false;
			}
		endforeach;
		return true;
	}
}

/**
 * Example Usage: (WORKFLOW)
 * 	$submission = new Packal(
 * 		'workflow',
 * 		[ 'file' => '/full/path/to/workflow.alfredworkflow' ]
 * 	);
 *	print_r( json_decode( $submission, true ) );
 */

/**
 * Example Usage: (THEME)
 * 	$submission = new Packal(
 * 		'workflow', [
 *			'name' => 'Glass',
 *			'description' => 'A beautiful, nearly transparent theme.',
 *			'tags' => implode(',', [
 *				'transparent',
 *				'minimal',
 *				'glass' ]
 *			),
 *			'uri' => 'alfred://theme/' .
 *							 'background=rgba(0,0,0,0.00)&' .
 *							 'border=rgba(169,189,222,0.30)&' .
 *							 'cornerRoundness=1&' .
 *							 'credits=Shawn%20Patrick%20Rice&' .
 *							 'imageStyle=4&' .
 *							 'name=Glass&' .
 *							 'resultPaddingSize=2&' .
 *							 'resultSelectedBackgroundColor=rgba(94,151,204,0.25)&' .
 *							 'resultSelectedSubtextColor=rgba(255,255,255,0.75)&' .
 *							 'resultSelectedTextColor=rgba(250,253,255,1.00)&' .
 *							 'resultSubtextColor=rgba(255,255,255,.75)&' .
 *							 'resultSubtextFont=Helvetica&' .
 *							 'resultSubtextFontSize=0&' .
 *							 'resultTextColor=rgba(255,255,255,1.00)&' .
 *							 'resultTextFont=Helvetica&' .
 *							 'resultTextFontSize=1&' .
 *							 'scrollbarColor=rgba(255,255,255,0.50)&' .
 *							 'searchBackgroundColor=rgba(0,0,0,0.00)&' .
 *							 'searchFont=Helvetica&' .
 *							 'searchFontSize=2&' .
 *							 'searchForegroundColor=rgba(255,255,255,1.00)&' .
 *							 'searchPaddingSize=1&' .
 *							 'searchSelectionBackgroundColor=rgba(184,201,117,1.00)&' .
 *							 'searchSelectionForegroundColor=rgba(255,255,255,1.00)&' .
 *							 'separatorColor=rgba(117,120,112,0.20)&' .
 *							 'shortcutColor=rgba(255,255,255,0.50)&' .
 *							 'shortcutFont=Helvetica&' .
 *							 'shortcutFontSize=0&' .
 *							 'shortcutSelectedColor=rgba(255,255,255,0.50)&' .
 *							 'widthSize=4'
 *        ]);
 *	print_r( json_decode( $submission, true ) );
 */