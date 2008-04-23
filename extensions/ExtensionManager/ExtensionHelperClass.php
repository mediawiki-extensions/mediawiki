<?php
/**
 * @author Jean-Lou Dupont
 * @package ExtensionManager
 * @category ExtensionManager
 * @version @@package-version@@
 * @Id $Id$
 * @dependency PEAR::Validate package [optional]
 * 
 * Use Cases:
 * - Sanitization (e.g. htmlspecialchars)
 * - Verify that mandatory parameters are provided
 * - Type verification
 * - List pruning
 * 
 * Parameters of the reference list:
 * m:  mandatory TRUE/FALSE
 * s:  perform HTML sanitization TRUE/FALSE
 * l:  include in string list TRUE/FALSE
 * d:  default value
 * r:  restricted parameter TRUE/FALSE
 * t:  value type verification [number, email, string, date, uri]
 * o:  options (used when 't' parameter specified) (see PEAR::Validate package)
 * dq: perform double-quote leading/trailing removal TRUE/FALSE
 * sq: perform double-quote leading/trailing removal TRUE/FALSE
 * tr: perform leading & trailing trimming TRUE/FALSE
 * 
 */
/* EXAMPLE REFERENCE LIST:
	var $parameters = array(
		'image'		=> array( 'm' => true,  's' => false, 'l' => false, 'd' => null ),
		'default'	=> array( 'm' => false, 's' => false, 'l' => false, 'd' => null ),		
		'page'		=> array( 'm' => false, 's' => false, 'l' => false, 'd' => '' ),
		'alt'		=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true ),
		'height'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true  ),
		'width' 	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true  ),
		'alt'		=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true  ),
		'title' 	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true  ),
		'border'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true  ),
		'class'		=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true  ),

		// Events
		// Restricted parameters
		'onchange'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true, 'r' => true  ),
		'onsubmit'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true, 'r' => true  ),
		'onreset'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true, 'r' => true  ),
		'onselect'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true, 'sq' => true, 'r' => true  ),
		
	);
	
 */
//<source lang=php>

include_once 'Validate.php'; // PEAR::Validate package

class ExtensionHelperClass
{
	/**
	 * Gets filled by the PHING build file
	 */
	const VERSION = '@@package-version@@';
	
 	/**
 	 * The reference list
 	 * @access public
 	 */
 	var $refList = array();
	
	/**
	 * The input list
	 * @access public
	 */
	var $iList = array();
	
 	/**
 	 * Result (output) list
 	 * @access public
 	 */
 	var $oList = array();
	
	/**
	 * List of missing mandatory parameters
	 * @access public
	 */
	var $missingList = array();
	
	/**
	 * List containing invalid parameters found
	 * @access public
	 */
	var $invalidList = array();
	
	/**
	 * Status of found invalid parameters
	 * @access public
	 */
	var $invalidFound = null;
	
	/**
	 * List of restricted parameters found
	 * @access public
	 */
	var $restrictedList = array();
	
	/**
	 * Status of found restricted parameters
	 * in the output list.
	 * @access public
	 */
	var $restrictedFound = null;
	
	/**
	 * Array containing the list
	 * of type errors
	 * @access public
	 */
	var $typeErrorsList = array();
	
	/**
	 * Status of found type errors
	 * in the output list
	 * @access public
	 */
	var $typeErrorsFound = null;
	
	/**
	 * The resulting key=value string list
	 * @access public
	 */
	var $stringList = null;
	
	/**
	 * Valid parameter types
	 * @see PEAR::Validate for more information
	 * @access private
	 */
	static $validTypes = array( 
		'number', 
		'email', 
		'string', 
		'date', 
		'uri' 
	);
	
	/**
	 * List of valid parameters for the
	 * reference list
	 * @access private
	 */
	static $validRefParameters = array(
		'm',
		's',
		'l',
		'd',
		'r',
		't',
		'o',
		'dq',
		'sq',
		'tr',
	);
	
 	/**
 	 * Constructor
 	 * 
 	 * @param array $params		array where:
 	 * 							'list'  is the input list to process
 	 * 							'ref'   is the reference list
 	 * 
 	 */
	public function __construct( $params ) {
	
		parent::__construct( $params );
		
		@$this->iList = $params['list'];
		@$this->refList = $params['ref'];
		
		$this->process();
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%		
	// Interface
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 * Returns a human readable list of errors found.
	 * For debugging purposes mainly.
	 * 
	 * @return string
	 */
	public function whatError() {
	
		$o  = $this->foundInvalid()    ? "<invalid>":"<>";
		$o .= $this->foundTypeErrors() ? "<type>"   :"<>";
		$o .= $this->foundMissing()    ? "<missing>":"<>";		
		
		return $o;
	}
	/**
	 * Returns the error status.
	 * If at least one error occured during processing:
	 * - Missing mandatory parameter(s)
	 * - Invalid parameters
	 * - Type errors
	 * 
	 * @return boolean
	 */
	public function isError() {
	
		return $this->foundInvalid() || 
				$this->foundTypeErrors() ||
				$this->foundMissing();
	}				
	/**
	 * Returns the processed output list
	 * 
	 * @return array
	 */
	public function getOutputList() {
	
		return $this->oList;
	}
	/**
	 * Returns the status of found missing parameters
	 * 
	 * @return boolean
	 */
	public function foundMissing() {
	
		return !( empty( $this->missingList ));
	}
	/**
	 * Returns the list of missing parameters
	 * 
	 * @return array
	 */
	public function getMissingList() {
	
		return $this->missingList;
	}
	/**
	 * Returns the status of found restricted parameters
	 * 
	 * @return boolean
	 */
	public function foundRestricted() {
	
		return $this->restrictedFound;
	}
	/**
	 * Returns the list of restricted parameters found
	 * 
	 * @return array
	 */
	public function getRestrictedList() {
	
		return $this->restrictedList;
	}
	/**
	 * Returns the status of found invalid parameters
	 * 
	 * @return boolean
	 */
	public function foundInvalid() {
	
		return !( empty( $this->invalidList ));
	}
	/**
	 * Returns the list of invalid parameters found
	 * 
	 * @return array
	 */
	public function getInvalidList() {
	
		return $this->invalidList;
	}
	/**
	 * Returns the status of found type errors
	 * 
	 * @return boolean
	 */
	public function foundTypeErrors() {
	
		return !( empty( $this->typeErrorsList ));
	}
	/**
	 * Returns the list of type errors found
	 * 
	 * @return array
	 */
	public function getTypeErrorsList()	{
	
		return $this->typeErrorsList;
	}

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%		
	// INTERNAL
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%				
	/**
	 * Processes the input list
	 * 
	 * @return void
	 */
	protected function process() {
	
		$this->verifyReferenceList();
		$this->doListSanitization();
		$this->restrictedFound = $this->checkListForRestrictions();
		$this->doSanitization();
		$this->typeErrorsFound = $this->doTypeVerification();
		$this->buildString();	
	}
	/**
	 * Verifies the validity of the provided
	 * reference list.
	 * 
	 * @throws
	 */
	protected function verifyReferenceList() {
	
		if ( empty( $this->refList ))
			return;
			
		foreach( $this->refList as $key => &$instructions )
			foreach( $instructions as $instruction => &$o )
				if ( !in_array( $instruction, self::$validRefParameters ))
					throw new Exception( __METHOD__.": invalid instruction in reference list ( $instruction )" );
	}
	
	/**
	 * Retrieves the specified list of parameters from the 
	 * processed output list.
	 * Uses the ''l'' parameter from the reference list.
	 * Produces a 'string' of key=value pairs.
	 * 
	 * @return string  
	 */
	protected function buildString( ) {
	
		if (empty( $this->oList ))
			return null;
			
		$this->stringList = null;
		// only pick the key:value pairs that have been
		// explictly marked using the 'l' key in the
		// reference list.
		foreach( $this->oList as $key => &$value ) {
		
			$key = trim( $key );
			$val = trim( $value );
			if ( isset( $this->refList[ $key ] ) )
				if ( $this->refList[ $key ]['l'] === true )
					$this->stringList .= " $key='$val'";
		}
	}
	/**
	 * Sanitize the parameters list. 
	 * - Just keeps the parameters defined in the reference list (pruning)
	 * - Flags missing mandatory parameters
	 * - Flags invalid parameters
	 * - Initializes missing parameters with a default value (if provided)
	 * 
	 */
	protected function doListSanitization( ) {
	
		if (empty( $this->iList ))
			return array();

		// first, let's make sure we only have valid parameters
		$this->oList = array();
		foreach( $this->iList as $key => &$value )
			if (isset( $this->refList[ $key ] ))
				$this->oList[ $key ] = $value;
			else
				$this->invalidList[] = $key;
				
		// then make sure we have all mandatory parameters
		foreach( $this->refList as $key => &$instructions )
			if ( $instructions['m'] === true )
				if ( !isset( $this->oList[ $key ] ))
					$this->missingList[] = $key;
					
		// finally, initialize to default values the missing parameters
		foreach( $this->refList as $key => &$instructions )
			if ( $instructions['d'] !== null )
				if ( !isset( $this->oList[ $key ] ))
					$this->oList[ $key ] = $instructions['d'];
	}
	/**
	 * Performs various sanitization.
	 * Only valid parameters should end-up here.
	 * - Single/Double-Quote leading & trailing removal
	 * - Trimming
	 * - HTML sanitization
	 * 
	 */
	protected function doSanitization( ) {
	
		if (empty( $this->oList ))
			return null;
			
		foreach( $this->oList as $key => &$value ) {
		
			// Trimming
			if (isset( $this->refList[ $key ]['tr'] ))
					if ( $this->refList[ $key ]['tr'] === true )
						$value = trim( $value );
			
			// Remove leading & trailing double-quotes
			if (isset( $this->refList[ $key ]['dq'] ))
					if ( $this->refList[ $key ]['dq'] === true ) {

						$value = ltrim( $value, "\" \t\n\r\0\x0B" );
						$value = rtrim( $value, "\" \t\n\r\0\x0B" );
					}

			// Remove leading & trailing single-quotes
			if (isset( $this->refList[ $key ]['sq'] ))
					if ( $this->refList[ $key ]['sq'] === true ) {
					
						$value = ltrim( $value, "\' \t\n\r\0\x0B" );
						$value = rtrim( $value, "\' \t\n\r\0\x0B" );
					}
						

			// HTML sanitization
			if (isset( $this->refList[ $key ]['s'] ))
				if ( $this->refList[ $key ]['s'] === true )
					$value = htmlspecialchars( $value );
		}
	}
	/**
	 * Checks for if the output list contains 
	 * parameters marked as ''r'' (i.e. restricted)
	 * 
	 * @return boolean TRUE if at least one restricted parameter found
	 */
	protected function checkListForRestrictions( )	{
	
		$this->restrictedList = array();
		
		if (empty( $this->oList ))
			return null;

		foreach( $this->oList as $key => &$value )
			if (isset( $this->refList[ $key ]['r'] ))
				if ( $this->refList[ $key ]['r'] === true )
					$this->restrictedList[] = $key;							
		
		// true if at least one restricted parameter found
		return ( !empty( $this->restrictedList ) );
	}
	/**
	 * Verifies the type of each parameter *when* the
	 * 't' parameter is provided in the reference list.
	 * Only sanitized output list supported.
	 * 
	 * @throws Exception if an invalid type is used
	 * in the reference list
	 * 
	 * @return boolean TRUE if at least one type error uncovered
	 * @return NULL if the PEAR::Validate package isn't available
	 */	
	protected function doTypeVerification()	{
	
		if ( !get_class( 'Validate' ))
			return null;
	
		$this->typeErrorsList = array();
		
		if (empty( $this->oList ))
			return null;

		$validator = new Validate;
		
		foreach( $this->oList as $key => &$value )
			if (isset( $this->refList[ $key ]['t'] )) {

				$type = $this->refList[ $key ]['t'];
				$opt  = @$this->refList[ $key ]['o'];

				$result = $validator->$type( $value, $opt );
				
				if ( $result===false )
					$this->typeErrorsList[] = array( $key => $type);
			}
		
		return !( empty( $this->typeErrorsList ) );
	}
	
}// end class definition

