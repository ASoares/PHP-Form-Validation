 ValidFluent
======
 A simple, flexible and easy to use PHP form validation class
  (uses a fluent interface ) 


**Note:** index.php  has a typical example ,  if anyone decides to use this , please double check the spelling on error messages ;-)

@author Andre Soares  andsoa77@gmail.com

**License:**

GPL v2 http://www.gnu.org/licenses/gpl-2.0.txt


**typical use:**

    $valid = new ValidFluent($_POST); 
    $valid->name('user_name')->required('You must chose a user name!')->alfa()->minSize(5);
    $valid->name('user_email')->required()->email();
    $valid->name('birthdate')->date('please enter date in YYYY-MM-DD format');
    if ($valid->isGroupValid()) echo 'Validation Passed!';

 **OR:**
 
    $valid = new ValidFluent($_POST);
  	if ( $valid->name('user_name')->required('You must chose a user name!')->alfa()->minSize(5)
  		    ->name('user_email')->required()->email()
  		    ->name('birthdate')->date('please enter date in YYYY-MM-DD format')
  		    ->isGroupValid() )
  	    echo 'Validation passed!';


  **On HTML Form:**
  <form method="POST">

  	    <input type="text"   name="email"
  		   value="<?php echo $valid->getValue('email'); ?>" />
  	    <span class="error">
  		<?php echo $valid->getError('email'); ?>
  	    </span>




#  To create new validation rules!

**1- define default error message**

    private static $error_myValidaton = 'my default error message';

**2- create new validation function**

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

**3- use it**

    $Valid->name('testing')->myValidation(10, 'some error msg!');
 