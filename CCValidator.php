<?php

/*
 * Validator for Credit Cards in Yii2.
 * @author David Webb <ravenger@dpwlabs.com>
 * 
 * 
 */

namespace dpwlabs\ccvalidator;

use Yii;
use yii\validators\Validator;
use Inacho\CreditCard;

class CCValidator extends Validator {
    /**
     * This is called by the model validation. Attach it to the attribute that
     * contains the credit card number. The following additional attributes are
     * required for complete functionality:
     * * cardType - The type of credit card.
     * * cardExpMonth - The expiry month of the credit card.
     * * cardExpYear - The expiry year of the credit card.
     * * cvc - The security code found on the reverse side of most credit cards.
     * 
     * @param $model a yii\base\Model derived object.
     * @param $attribute the credit card number attribute of $model.
     */
    public function validateAttribute($model, $attribute) {
        $type = str_replace(' ', '', strtolower($this->cardType));
        $result = false;
        try {
            if (CreditCard::validCreditCard($model->$attribute, $type)['valid']
                    == true) {
                $result = false;
            } else if (CreditCard::validDate($this->cardExpiryYear, 
                    $this->cardExpiryMonth) == false) {
                $model->addError('cardExpiryYear', 'Validation failure.');
                $model->addError('cardExpiryMonth', 'Validation failure.');
                $result = false;
            } else if (CreditCard::validCvc($this->cvc, $type) === false) {
                $model->addError('cvc', 'Validation failure.');
                $result = false;
            } else {
                return true;
            }
        } catch (\Exception $ex) {
            $result = false;
        }
        $model->addError($attribute, 'Credit Card validation failure.');
        return false;
    }

}
