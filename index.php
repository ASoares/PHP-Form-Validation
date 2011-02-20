<?php
include 'validFluent.php';


if (empty($_POST))
    {
    // setting some start values
    $vf = new ValidFluent(array());
    $vf->name('userName')
	    ->setValue('legolas')
	    ->setError('ooopps, name already in use!');
    }


else
    {
    //validate $_POST data
    $vf = new ValidFluent($_POST);

    $vf->name('email')
	    ->required('you need to type someting here')
	    ->email()
	    ->minSize(8);

    $vf->name('date')
	    ->required()
	    ->date();

//can also be used like this....
    if ($vf->name('userName')
	    ->alfa()
	    ->minSize(3)
	    ->maxSize(12)

	    ->name('choseOne')
	    ->oneOf('en:es:fr:pt:other')

	    ->name('password1')
	    ->required()
	    ->minSize(3)
	    ->alfa()

	    ->name('password2')
	    ->required()
	    ->equal($_POST['password1'], 'passwords didnt match')

	    ->isGroupValid())
	echo "Validation Passed \n";
    else
	echo "validation errors";
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<style type="text/css">
	    .error
	    {
		color:red;
	    }
	</style>
        <title></title>
    </head>
    <body>
	<form method="POST">

	    <label for="email">EMAIL</label>
	    <input type="text"   name="email"
		   value="<?php echo $vf->getValue('email'); ?>"
		   />
	    <span class="error">
<?php echo $vf->getError('email'); ?>
	    </span>
	    <br><br>


	    <label for="date">DATE</label>
	    <input type="text"   name="date"
		   value="<?php echo $vf->getValue('date'); ?>"
		   />
	    <span class="error">
<?php echo $vf->getError('date'); ?>
	    </span>
	    <br><br>

	    <label for="userName">User Name</label>
	    <input type="text"   name="userName"
		   value="<?php echo $vf->getValue('userName'); ?>"
		   />
	    <span class="error">
<?php echo $vf->getError('userName'); ?>
	    </span>
	    <br><br>

	    <label for="date">language 'pt' 'en' 'es' 'fr' or 'other'</label>
	    <input type="text"   name="choseOne"
		   value="<?php echo $vf->getValue('choseOne'); ?>"
		   />
	    <span class="error">
<?php echo $vf->getError('choseOne'); ?>
	    </span>
	    <br><br>


	    <label for="password1">Password</label>
	    <input type="text"   name="password1"
		   value="<?php echo $vf->getValue('password1'); ?>"
		   />
	    <span class="error">
<?php echo $vf->getError('password1'); ?>
	    </span>
	    <br><br>


	    <label for="password2">Confirm Password</label>
	    <input type="text"   name="password2"
		   value="<?php echo $vf->getValue('password2'); ?>"
		   />
	    <span class="error">
<?php echo $vf->getError('password2'); ?>
	    </span>
	    <br><br>


	    <input type="submit" />
	</form>
<?php
// put your code here
?>
    </body>
</html>
