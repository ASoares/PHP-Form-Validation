<?php

/**
 *
 * ValidFluent
 *
 * A simple, flexible and easy to use PHP form validation class
 * (uses a fluent interface )
 *
 * @author Andre Soares
 * andsoa77@gmail.com
 *
 * typical use:
 
  	$valid = new ValidFluent($_POST);
 
  	$valid->name('user_name')->required('You must choose a user name!')->alfa()->minSize(5);
 
  	$valid->name('user_email')->required()->email();
 
  	$valid->name('birthdate')->date('please enter date in YYYY-MM-DD format');

 	if ($valid->isGroupValid())
 	    echo 'Validation Passed!';

 * //////////////////////////////////////
 * OR
 * 
	 $valid = new ValidFluent($_POST);
 
  	if (  $valid->name('user_name')->required('You must choose a user name!')->alfa()->minSize(5)
  		    ->name('user_email')->required()->email()
  		    ->name('birthdate')->date('please enter date in YYYY-MM-DD format')
  		    ->isGroupValid() )
  	    echo 'Validation passed!';
 
 
 * //////////////////////////////////////////////////////////////////
 *    On HTML
  <form method="POST">
 
  	    <input type="text"   name="email"
  		   value="<?php echo $valid->getValue('email'); ?>" />
  	    <span class="error">
  		<?php echo $valid->getError('email'); ?>
  	    </span>
  	    ...
  	    ...
 
 * ///////////////////////////////////////////////////////////////////
 *  To create new validation rules!

  #1 define default error message
  private static $error_myValidaton = 'my default error message';

  #2 create new validation function
  function myValidation($param , $errorMsg=NULL)
  {
  if ($this->isValid && (! empty($this->currentObj->value)))
  {
  //
  //code to check if validation pass
  //
  $this->isValid = // TRUE or FALSE ;
  if (! $this->isValid)
  $this->setErrorMsg($errorMsg, self::$error_myValidation, $param);
  }
  return $this;
  }

 #3 use it
 $Valid->name('testing')->myValidation(10, 'some error msg!');
 *
 *
 */


/**
 * helper class for ValidFluent
 */
class validFluentObj
    {

    public $value;
    public $error;


    function __construct($value)
	{
	$this->value = $value;
	$this->error = '';
	}

    }


/**
 *
 */
class ValidFluent
    {

    public $isValid = TRUE;
    public $isGroupValid = TRUE;
    public $validObjs; //array of validFluentObj
    private $currentObj; //pointer to current validFluentObj , set by ->name()
    //default error messages
    private static $error_required = 'This field is required';
    private static $error_date = 'Please enter a date in the YYYY-MM-DD format';
    private static $error_email = 'Please enter a valid email';
    private static $error_url = 'Please enter a valid url';
    private static $error_alfa = 'Only letters and numbers are permited';
    private static $error_text = 'Only letters are permited';
    private static $error_minSize = 'Please enter more than %s characters';
    private static $error_maxSize = 'Please enter less than %s characters';
    private static $error_numberFloat = 'Only numbers are permitted';
    private static $error_numberInteger = 'Only numbers are permitted';
    private static $error_numberMax = 'Please enter a value lower than %s ';
    private static $error_numberMin = 'Please enter a value greater than %s ';
    private static $error_oneOf = 'Please choose one of " %s "';
    private static $error_equal = 'Fields did not match';
    private static $error_regex = 'Please choose a valid value';
    // some regEx's
    private $pattern_email = '/^([a-zA-Z0-9_\+\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/';
    private $pattern_url = '/^((http|ftp|https):\/\/)?www\..*.\.\w\w\w?(\/.*)?(\?.*)?$/'; //check...
    ////private $pattern_alfa = '/^[a-zA-Z0-9_\-\. ]+$/';
    private $pattern_alfa = '/^(\d|\-|_|\.| |(\p{L}\p{M}*))+$/u';
    private $pattern_text = '/^( |(\p{L}\p{M}*)+$/u';
    private $pattern_numberInteger = '/^[\+\-]?[0-9]+$/';
    private $pattern_numberFloat = '/^[\+\-]?[0-9\.]+$/';
    private $pattern_date = '/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';


    /**
     *
     * @param Array $post  ($Key => $value) array
     */
    function __construct($post)
	{
	foreach ($post as $key => $value)
	    {
	    $this->validObjs[$key] = new validFluentObj(trim($value));
	    }
	}


    /**
     * Helper: returns TRUE if last valiadtion passed , else FALSE
     * @return Boolean
     */
    function isValid()
	{
	return $this->isValid;
	}


    /**
     * Helper: returns TRUE if all validations passed , else FALSE
     * @return Boolean
     */
    function isGroupValid()
	{
	return $this->isGroupValid;
	}


    /**
     * Return's $name validation error
     * @param string $name
     * @return string the error
     */
    function getError($name)
	{
	if (isset($this->validObjs[$name]))
	    return $this->validObjs[$name]->error;
	return '';
	}


    /**
     * Returs $name value
     * @param string $name
     * @return string the value
     */
    function getValue($name)
	{
	if (isset($this->validObjs[$name]))
	    return $this->validObjs[$name]->value;
	return '';
	}


    /**
     * Used to set starting values on Form data
     * ex: $valid->name('user_name)->setValue($database->getUserName() );
     * @param string $value
     */
    function setValue($value)
	{
	$this->currentObj->value = $value;
	return $this;
	}


    /**
     *  used to set error messages out of the scope of validFluent
     *  ex: $valid->name('user_name')->setError('The Name "Andre" is already taken , please try another')
     * @param string $error
     */
    function setError($error)
	{
	$this->currentObj->error = $error;
	$this->isGroupValid = FALSE;
	$this->isValid = FALSE;
	return $this;
	}


    /**
     * PRIVATE Helper to set error messages
     * @param string $errorMsg custom error message
     * @param string $default  default error message
     * @param string $params   extra parameter to default error message
     */
    private function setErrorMsg($errorMsg, $default, $params=NULL)
	{
	$this->isGroupValid = FALSE;
	if ($errorMsg == '')
	    {
	    $this->currentObj->error = sprintf($default, $params);
	    }
	else
	    $this->currentObj->error = $errorMsg;
	}

////////////////////////////////////////////////
///////////////////////////////////////////////
////
///	    Validation Functions
//


    /**
     * Used to set a pointer for current validation object
     * if $name doesnt exits, it will be created with a empty value
     * 		note:validation always pass on empy not required fields
     * @param string $name as in array($name => 'name value')
     * @return ValidFluent
     */
    function name($name)
	{
	if (!isset($this->validObjs[$name]))
	    $this->validObjs[$name] = new validFluentObj('');

	$this->isValid = TRUE;

	$this->currentObj = &$this->validObjs[$name];

	return $this;
	}


    /**
     * Note if field is required , then it must me called right after name!!
     * ex: $valid->name('user_name')->required()->text()->minSize(5);
     * @param string $errorMsg
     * @return ValidFluent
     */
    function required($errorMsg=NULL)
	{
	if ($this->isValid)
	    {
	    $this->isValid = ( $this->currentObj->value != '') ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_required);
	    }
	return $this;
	}


    /**
     *  validates a Date in yyyy-mm-dd format
     * @param string $errorMsg
     * @return ValidFluent
     */
    function date($errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (preg_match($this->pattern_date, $this->currentObj->value)) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_date);
	    }
	return $this;
	}


    /**
     * validates an email address
     * @param string $errorMsg
     * @return ValidFluent
     */
    function email($errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (preg_match($this->pattern_email, $this->currentObj->value) > 0) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_email);
	    }
	return $this;
	}


    /**
     * validates a URL address
     * @param string $errorMsg
     * @return ValidFluent
     */
    function url($errorMsg=NULL)
	{

	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (preg_match($this->pattern_url, $this->currentObj->value)) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_url);
	    }
	return $this;
	}


    /**
     * ex: ->regex('/^[^<>]+$/', 'ERROR:  < and > arent valid characters')
     * @param string $regex a regular expresion '/regex/'
     * @param string $errorMsg
     * @return ValidFluent 
     */
    function regex($regex, $errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (preg_match($regex, $this->currentObj->value)) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_regex);
	    }
	return $this;
	}


    /**
     *  ex: ->name('password')->equal('passwordConfirm' , 'passwords didnt match')
     * @param string $value2 
     * @param string $errorMsg
     * @return ValidFluent 
     */
    function equal($value2, $errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = ($value2 == $this->currentObj->value);

	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_equal);
	    }
	return $this;
	}


    /**
     * Ex: ->oneOf('blue:red:green' , 'only blue , red and green permited')
     * *case insensitive*
     * @param string $items ex: 'blue:red:green'
     * @param string $errorMsg
     * @return ValidFluent
     */
    function oneOf($items, $errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {

	    $item = explode(':', strtolower($items));
	    $result = array_intersect($item, array(strtolower($this->currentObj->value)));
	    $this->isValid = (!empty($result));

	    if (!$this->isValid)
		{
		$itemsList = str_replace(':', ' / ', $items);
		$this->setErrorMsg($errorMsg, self::$error_oneOf, $itemsList);
		}
	    }
	return $this;
	}

    /////////////////////////////////////////////////////
    ////////////////////////////////////////////////////
    ////
    ///	    text validation
    //


    /**
     * Only allows A-Z a-Z and space
     * @param string $errorMsg
     * @return ValidFluent
     */
    function text($errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (preg_match($this->pattern_text, $this->currentObj->value)) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_text);
	    }
	return $this;
	}


    /**
     * Only allows A-Z a-z 0-9 space and ( - . _ )
     * @param string $errorMsg
     * @return ValidFluent
     */
    function alfa($errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (preg_match($this->pattern_alfa, $this->currentObj->value)) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_alfa);
	    }
	return $this;
	}
	
	// same function, better spelled
	function alpha($errorMsg=NULL)
	{
		$this->alfa($errorMsg);
		return $this;
	}


    /**
     * @param int $size the maximum string size
     * @param string $errorMsg
     * @return ValidFluent
     */
    function maxSize($size, $errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (strlen($this->currentObj->value) <= $size);
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_maxSize, $size);
	    }
	return $this;
	}


    /**
     * @param int $size the minimum string size
     * @param string $errorMsg
     * @return ValidFluent
     */
    function minSize($size, $errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (strlen($this->currentObj->value) >= $size);
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_minSize, $size);
	    }
	return $this;
	}

    /////////////////////////////////////////////////////
    ////////////////////////////////////////////////////
    ////
    ///	     Numbers validation
    //


    /**
     *  checks if its a float ( +  -  . ) permited
     * @param string $errorMsg
     * @return ValidFluent
     */
    function numberFloat($errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (preg_match($this->pattern_numberFloat, $this->currentObj->value)) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_numberFloat);
	    }
	return $this;
	}


    /**
     *  checks if its a integer ( +  - ) permited
     * @param string $errorMsg
     * @return ValidFluent
     */
    function numberInteger($errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = (preg_match($this->pattern_numberInteger, $this->currentObj->value)) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_numberInteger);
	    }
	return $this;
	}


    /**
     * @param number $max
     * @param string $errorMsg
     * @return ValidFluent
     */
    function numberMax($max, $errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = ($this->currentObj->value <= $max) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_numberMax, $max);
	    }
	return $this;
	}


    /**
     * @param number $min
     * @param string $errorMsg
     * @return ValidFluent
     */
    function numberMin($min, $errorMsg=NULL)
	{
	if ($this->isValid && (!empty($this->currentObj->value)))
	    {
	    $this->isValid = ($this->currentObj->value >= $min) ? TRUE : FALSE;
	    if (!$this->isValid)
		$this->setErrorMsg($errorMsg, self::$error_numberMin, $min);
	    }
	return $this;
	}

    }


?>