<?php namespace App\Validation;

class CustomRules
{
	public function phone(string $phone)
	{
		if (1 == preg_match('/(\([0-9]{2}\) [0-9]{4}-[0-9]{4}|\([0-9]{2}\) [0-9] [0-9]{4}-[0-9]{4})/', $phone))
			return TRUE;
		return FALSE;
	}

	/**
	 * Validate CPF
	 * 
	 * @param	string		$cpf	CPF value to be validated
	 *
	 * @return	boolean
	 */
	public function cpf(string $cpf = '')
	{
		// Verify if cpf is not empty
		if (empty($cpf))
		{
			return FALSE;
		}

		// Delete mask
		$cpf = preg_replace('/[^0-9]/', '', $cpf);
		$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

		// Verify if length of cpf is equal 11
		if (strlen($cpf) != 11)
		{
			return FALSE;
		}
		// Verify if is not an invalid cpf knowed
		else if ($cpf == '00000000000' || 
			$cpf == '11111111111' || 
			$cpf == '22222222222' || 
			$cpf == '33333333333' || 
			$cpf == '44444444444' || 
			$cpf == '55555555555' || 
			$cpf == '66666666666' || 
			$cpf == '77777777777' || 
			$cpf == '88888888888' || 
			$cpf == '99999999999') {
			return FALSE;
		// Calculates the verifier digits
		}
		else
		{
			for ($t = 9; $t < 11; $t++)
			{
				for ($d = 0, $c = 0; $c < $t; $c++)
				{
					$d += $cpf{$c} * (($t + 1) - $c);
				}
				$d = ((10 * $d) % 11) % 10;
				if ($cpf{$c} != $d)
				{
					return FALSE;
				}
			}
			return TRUE;
		}
	}
}