<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Forms\Form;

echo "<div class='trail'>";
echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>".__($guid, 'Home')."</a> > </div><div class='trailEnd'>".__($guid, 'Password Reset').'</div>';
echo '</div>';

$step = 1;
if (isset($_GET['step'])) {
	if ($_GET['step'] == 2) {
		$step = 2;
	}
}

if ($step == 1) {
	?>
	<p>
		<?php echo sprintf(__($guid, 'Enter your %1$s username, or the email address you have listed in the system, and press submit: a unique password reset link will be emailed to you.'), $_SESSION[$guid]['systemName']); ?>
	</p>
	<?php
	$returns = array();
	$returns['error0'] = __($guid, 'Email address not set.');
	$returns['error3'] = __($guid, 'Failed to send update email.');
	$returns['error4'] = __($guid, 'Your request failed due to non-matching passwords.');
	$returns['error5'] = __($guid, 'Your request failed due to incorrect or non-existent or non-unique email address.');
    $returns['error6'] = __($guid, 'Your request failed because your password to not meet the minimum requirements for strength.');
    $returns['error7'] = __($guid, 'Your request failed because your new password is the same as your current password.');
    $returns['error8'] = __($guid, 'Your request failed because you do not have sufficient privileges to login.');
    $returns['error9'] = __($guid, 'Your request failed because your account has not been activated. <a href="'.$_SESSION[$guid]['absoluteURL'].'/index.php?q=parentInformation.php">Please visit the account confirmation page to continue</a>.');
    $returns['success0'] = __($guid, 'Password reset request successfully initiated, please check your email.');
	if (isset($_GET['return'])) {
	    returnProcess($guid, $_GET['return'], null, $returns);
	}

	$form = Form::create('action', $_SESSION[$guid]['absoluteURL'].'/passwordResetProcess.php?step=1');

	$form->setClass('smallIntBorder fullWidth');
	$form->addHiddenValue('address', $_SESSION[$guid]['address']);

	$row = $form->addRow();
	    $row->addLabel('email', __('Username/Email'));
	    $row->addTextField('email')->maxLength(255)->isRequired();

	$row = $form->addRow();
	    $row->addFooter();
	    $row->addSubmit();

	echo $form->getOutput();
}
else {
	// Sanitize the whole $_GET array
    $validator = new \Gibbon\Data\Validator();
    $_GET = $validator->sanitize($_GET);

	//Get URL parameters
	$input = $_GET['input'];
	$key = $_GET['key'];
	$gibbonPersonResetID = $_GET['gibbonPersonResetID'];

	//Verify authenticity of this request and check it is fresh (within 48 hours)
	try {
        $data = array('key' => $key, 'gibbonPersonResetID' => $gibbonPersonResetID);
        $sql = "SELECT * FROM gibbonPersonReset WHERE `key`=:key AND gibbonPersonResetID=:gibbonPersonResetID AND (timestamp > DATE_SUB(now(), INTERVAL 2 DAY))";
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) {
        echo "<div class='error'>".$e->getMessage().'</div>';
    }

	if ($result->rowCount() != 1) {
		echo "<div class='error'>";
		echo __($guid, 'Your reset request is invalid: you may not proceed.');
		echo '</div>';
	} else {
		echo "<div class='success'>";
		echo __($guid, 'Your reset request is valid: you may proceed.');
		echo '</div>';

		$form = Form::create('action', $_SESSION[$guid]['absoluteURL']."/passwordResetProcess.php?input=$input&step=2&gibbonPersonResetID=$gibbonPersonResetID&key=$key");

		$form->setClass('smallIntBorder fullWidth');
		$form->addHiddenValue('address', $_SESSION[$guid]['address']);

		$form->addRow()->addHeading(__('Reset Password'));

		$policy = getPasswordPolicy($guid, $connection2);
		if ($policy != false) {
			$form->addRow()->addAlert($policy, 'warning');
		}

		$row = $form->addRow();
			$row->addLabel('passwordNew', __('New Password'));
			$column = $row->addColumn('passwordNew')->addClass('inline right');
			$column->addButton(__('Generate Password'))->addClass('generatePassword');
			$password = $column->addPassword('passwordNew')->isRequired()->maxLength(30);

	    $alpha = getSettingByScope($connection2, 'System', 'passwordPolicyAlpha');
		$numeric = getSettingByScope($connection2, 'System', 'passwordPolicyNumeric');
		$punctuation = getSettingByScope($connection2, 'System', 'passwordPolicyNonAlphaNumeric');
		$minLength = getSettingByScope($connection2, 'System', 'passwordPolicyMinLength');

		if ($alpha == 'Y') {
			$password->addValidation('Validate.Format', 'pattern: /.*(?=.*[a-z])(?=.*[A-Z]).*/, failureMessage: "'.__('Does not meet password policy.').'"');
		}
		if ($numeric == 'Y') {
			$password->addValidation('Validate.Format', 'pattern: /.*[0-9]/, failureMessage: "'.__('Does not meet password policy.').'"');
		}
		if ($punctuation == 'Y') {
			$password->addValidation('Validate.Format', 'pattern: /[^a-zA-Z0-9]/, failureMessage: "'.__('Does not meet password policy.').'"');
		}
		if (!empty($minLength) && is_numeric($minLength)) {
			$password->addValidation('Validate.Length', 'minimum: '.$minLength.', failureMessage: "'.__('Does not meet password policy.').'"');
		}

		$row = $form->addRow();
			$row->addLabel('passwordConfirm', __('Confirm New Password'));
			$row->addPassword('passwordConfirm')
				->isRequired()
				->maxLength(30)
				->addValidation('Validate.Confirmation', "match: 'passwordNew'");

		$row = $form->addRow();
			$row->addFooter();
			$row->addSubmit();

		echo $form->getOutput();

		?>
		<script type="text/javascript">
	        // Password Generation
	        $(".generatePassword").click(function(){
	            var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789![]{}()%&*$#^~@|';
	            var text = '';
	            for(var i=0; i < <?php echo $minLength + 4 ?>; i++) {
	                if (i==0) { text += chars.charAt(Math.floor(Math.random() * 26)); }
	                else if (i==1) { text += chars.charAt(Math.floor(Math.random() * 26)+26); }
	                else if (i==2) { text += chars.charAt(Math.floor(Math.random() * 10)+52); }
	                else if (i==3) { text += chars.charAt(Math.floor(Math.random() * 19)+62); }
	                else { text += chars.charAt(Math.floor(Math.random() * chars.length)); }
	            }
	            $('input[name="passwordNew"]').val(text);
	            $('input[name="passwordConfirm"]').val(text);
	            alert('<?php echo __('Copy this password if required:') ?>' + '\r\n\r\n' + text) ;
	        });
	    </script>
		<?php
	}
}
?>
