<?php
/*
 * DatabaseMysqlex.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * History:
 * ========
 * - makeList: from SVN MW 1.10 rev 21466
 *
 */

class DatabaseMysqlex extends DatabaseMysql
{
	const thisName = 'DatabaseMySQLex';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	
	public function DatabaseMysqlex()
	{ 
		global $wgExtensionCredits;
		
		$wgExtensionCredits['other'][] = array(
		    'name'        => self::thisName,
			'version'     => '$LastChangedRevision$',
			'author'      => 'Jean-Lou Dupont [http://www.bluecortex.com]',
			'description' => 'Extends the standard DatabaseMysql class. '
		);

		global $wgHooks;
		$wgHooks['SpecialVersionExtensionTypes'][] = array( &$this, 'hUpdateExtensionCredits' );
		
		// WATCH THIS !
		// the way 'LoadBalancer' creates this class of object,
		// no need to send parameters across.
		parent::__construct();			
	}
###################################################################################
/*
    New Methods
*/
###################################################################################
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	{
		global $wgExtensionCredits, $wgDBclass, $wgDBtype;
	
		if (!isset( $wgDBclass)) return;
			
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
		{
			if ($el['name']==self::thisName)
				$el['description'].=" \$wgDBtype is set to <b>{$wgDBtype}</b> and \$wgDBclass is set to a <b>{$wgDBclass}</b>.";	
		}
	}
	private function fixConditions( &$cond )
	{
		
	}

###################################################################################
/*
    Overloaded Methods
*/
###################################################################################

	function makeList( $a, $mode = LIST_COMMA ) 
	{
		/*
		if ( !is_array( $a ) ) {
			throw new DBUnexpectedError( $this, 'Database::makeList called with incorrect parameters' );
		}

		$first = true;
		$list = '';
		foreach ( $a as $field => $value ) {
			if ( !$first ) {
				if ( $mode == LIST_AND ) {
					$list .= ' AND ';
				} elseif($mode == LIST_OR) {
					$list .= ' OR ';
				} else {
					$list .= ',';
				}
			} else {
				$first = false;
			}
			if ( ($mode == LIST_AND || $mode == LIST_OR) && is_numeric( $field ) ) {
				$list .= "($value)";
			} elseif ( ($mode == LIST_SET) && is_numeric( $field ) ) {
				$list .= "$value";
			} elseif ( ($mode == LIST_AND || $mode == LIST_OR) && is_array ($value) ) {
				$list .= $field." IN (".$this->makeList($value).") ";
			} else {
				if ( $mode == LIST_AND || $mode == LIST_OR || $mode == LIST_SET ) {
					$list .= "$field = ";
				}
				$list .= $mode == LIST_NAMES ? $value : $this->addQuotes( $value );
			}
		}
		return $list;
		*/
		
		return parent::makeList( $a, $mode );
	}


	/* WATCH THIS! FIX THIS TOO?
	function conditional( $cond, $trueVal, $falseVal ) {
		return " IF($cond, $trueVal, $falseVal) ";
	}
	*/

} // end class definition.
?>