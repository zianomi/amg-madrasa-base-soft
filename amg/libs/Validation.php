<?php
/**
 * Validates a form by using Validation Rules set by the user
 *
 * The validation rules you can set.
 *
 * Rule             value               Description
 * -----------   ---------           -----------
 * name             string                 the name of the field
 * required         boolean             Set if the field is required
 * validEmail    boolean             Validates an email address
 * validURL      boolean             Validates an URL
 * match         Fieldname to match  The fields value needs to match the value of the field given to match
 * numeric       boolean             Checks if the value contains only numeric characters
 * alpha         boolean             Checks if the value contains only alphabetical characters
 * aplhaNumeric  boolean             Checks if the value contains only alpha-numeric characters
 * minLength     int                  Checks if the value has less characters than the minLength
 * maxLength     int                   Checks if the value has more characters than the maxLength
 * exactLength   int                 Checks if the value is the exact length of the exactLength
 * lessThan      int                    Checks if the value is less than lessThan
 * greaterThan:  int                 Checks if the value is greater than greaterThan
 *
 * Note: No validation rules have been created for File upload validation
 */
Class Validation
{
    /**
     * @var  array  Holds the posted data
     */
    private $_post;

    /**
     * @var  array  A multidimensional array containg the validation rules for all the fields
     */
    private $_validationRules;

    /**
     * @var  array  Holds all the errors for the fields
     */
    private $_validationErrors;


    /**
     * --------------------------------------------------------------------------------------
     * Opens the form
     *
     * @param   string  $action   The page where the Form links to
     * @param   string  $method   The way the Form sends it's data
     * @return  string  $form       HTML containing the Form's opening tag
     */
    public function openForm($action = '', $method = 'POST')
    {
        $form = '<form action="'.$action.'" method="'.$method.'">';

        return $form;
    }

    /**
     * --------------------------------------------------------------------------------------
     * Closes the form
     *
     * @return  string  $form  HTML containing the Form's closing tag
     */
    public function closeForm()
    {
        $form = '</form>';

        return $form;
    }

    /**
     * --------------------------------------------------------------------------------------
     * Creates various form fields
     *
     * @param   string  $type     The input type for the field
     * @param   string  $name     The name for the field
     * @param   string  $label    The label for the field
     * @param   string  $example  An example or note to help the user with the form
     * @return  string  $field    HTML containing the field
     */
    public function setField($type, $name, $label = '', $example = '')
    {
        $field = '';

        //Check if a label is set, if so display the label
        if(isset($label))
        {
            $field .= '<label for="'.$name.'">'.$label.'</label>';
        }

        //Check if an example is set
        if(isset($example))
        {
            $field .= ' <span class="example">'.$example.'</span>';
        }

        //Check if we have errors for this field, if so display them
        if(isset($this->_validationErrors[$name]))
        {
            $field .= '<p class="alert-error">'.$this->_validationErrors[$name].'</p>';
        }

        //Create the input tag and set some attributes
        $field .= '<p><input id="'.$name.'" type="'.$type.'" name="'.$name.'"';

        //Check if we have errors, if so create a css class for a red border
        if(isset($this->_validationErrors[$name]))
        {
            $field .= ' class="input-block-level error"';
        }

        //Check if data was posted, if so set the value attribute to display it in the field
        if(isset($this->_post[$name]))
        {
            $field .= ' value="'.$this->_post[$name].'"';
        }

        //Close the field
        $field .= ' /></p>';

        return $field;
    }

    /**
     * --------------------------------------------------------------------------------------
     * Creates a checkbox buttons
     *
     * @param   string  $name      The name of the submit button
     * @param   string  $label     The label for the field
     * @param   string  $example   An example or note to help the user with the form
     * @return  string  $checkbox  HTML containing the submit button
     */
    public function setCheckbox($name, $label = '', $example = '')
    {
        $checkbox = '';

        //Check if a label is set, if so display the label
        if(isset($label))
        {
            $checkbox .= '<label for="'.$name.'">'.$label.'</label>';
        }

        //Check if an example is set
        if(isset($example))
        {
            $checkbox .= ' <span class="example">'.$example.'</span>';
        }

        //Check if we have errors, if so display them
        if(isset($this->_validationErrors[$name]))
        {
            $checkbox .= '<p class="alert-error">'.$this->_validationErrors[$name].'</p>';
        }

        //By setting a hidden field with the same name infront of the checkbox we will always recieve a $_POST[$name] whether it is checked or not
        $checkbox .= '<input type="hidden" name="'.$name.'" value="" />';

        //Check if data was posted, if so set the checkbox to checked
        if(isset($this->_post[$name]) && $this->_post[$name] != NULL)
        {
            $checkbox .= '<p><input type="checkbox" name="'.$name.'" value="checked" checked="checked" /></p>';
        }
        else
        {
            $checkbox .= '<p><input type="checkbox" name="'.$name.'" value="checked" /></p>';
        }

        return $checkbox;
    }

    /**
     * --------------------------------------------------------------------------------------
     * Creates one or more radio buttons
     *
     * @param   string  $name     The name of the submit button
     * @param   string  $label    The label for the field
     * @param   array   $options  The radio buttons and their values
     * @param   string  $example  An example to help the user with the form
     * @return  string  $radio    HTML containing the submit button
     */
    public function setRadio($name, $label = '', $options, $example = '')
    {
        $radio = '';

        //Check if a label is set, if so display the label
        if(isset($label))
        {
            $radio .= '<label for="'.$name.'">'.$label.'</label>';
        }

        //Check if an example is set
        if(isset($example))
        {
            $radio .= ' <span class="example">'.$example.'</span>';
        }

        //Check if we have errors, if so display them
        if(isset($this->_validationErrors[$name]))
        {
            $radio .= '<p class="alert-error">'.$this->_validationErrors[$name].'</p>';
        }

        //By setting a hidden field with the same name infront of the radio buttons we will always recieve a $_POST[$name] whether a button is checked or not
        $radio .= '<input type="hidden" name="'.$name.'" value="" />';

        //Loop throug the options for the radio group
        foreach($options as $option => $value)
        {
            //Check if data was posted, if so set the radio button to checked
            if(isset($this->_post[$name]) && $this->_post[$name] == $value)
            {
                $radio .= '<p><input type="radio" name="'.$name.'" value="'.$value.'" checked="checked" /> '.$option.'</p>';
            }
            else
            {
                $radio .= '<p><input type="radio" name="'.$name.'" value="'.$value.'" /> '.$option.'</p>';
            }
        }

        return $radio;
    }

    /**
     * --------------------------------------------------------------------------------------
     * Creates a Select Dropdown menu
     *
     * @param string $name the name of the submit button
     * @param string $label the label for the field
     * @param string $example An example to help the user with the form
     * @return string $checkbox HTML containing the submit button
     */
    public function setSelect($name, $label = '', $options, $example = '')
    {
        $select = '';

        //Check if a label is set, if so display the label
        if(isset($label))
        {
            $select .= '<label for="'.$name.'">'.$label.'</label>';
        }

        //Check if an example is set
        if(isset($example))
        {
            $select .= ' <span class="example">'.$example.'</span>';
        }

        //Check if we have errors, if so display them
        if(isset($this->_validationErrors[$name]))
        {
            $select .= '<p class="alert-error">'.$this->_validationErrors[$name].'</p>';
        }

        //Open the select tag
        $select .= '<p><select name="'.$name.'">';

        //Loop through the options
        foreach($options as $option => $value)
        {
            //Check if data was posted, if so set the checkbox to checked
            if(isset($this->_post[$name]) && $this->_post[$name] == $value)
            {
                $select .= '<option value="'.$value.'" selected="selected">'.$option.'</option>';
            }
            else
            {
                $select .= '<option value="'.$value.'">'.$option.'</option>';
            }
        }

        //Close the select tag
        $select .= '</select></p>';

        return $select;
    }

    /**
     * --------------------------------------------------------------------------------------
     * Creates a form submit button
     *
     * @param   string  $name    The name of the submit button
     * @param   string  $value   The value to be displayed on the submit button
     * @return  string  $submit  HTML containing the submit button
     */
    public function setSubmit($name, $value)
    {
        $submit = '<input type="submit" name="'.$name.'" value="'.$value.'" />';

        return $submit;
    }

    /**
     * --------------------------------------------------------------------------------------
     * Set Validation rules for a form field by adding the array to the main validationRules array
     *
     * @param  array  $validationRules  An array containing the rules for a field
     */
    public function setValidation($arrayName, $validationRules)
    {
        $this->_validationRules[$arrayName] = $validationRules;
    }

    /**
     * Validates the form
     *
     * @param  array  $post  The posted data
     */
    public function validateFields($post)
    {
        //Drop the last element of the array $post, this is the submit button (if you gave the submit button a name)
        array_pop($post);

        //Assign the posted data to a private property
        $this->_post = $post;

        //Loop through all the posted data
        foreach($this->_post as $key => $value)
        {
            //Trim the whitespace
            $value = trim($value);

            //Check if a name of a posted field exists as a key in the validationRules array, i.e if Rules have been set for this field
            if(array_key_exists($key, $this->_validationRules))
            {
                //Loop through all the rules
                foreach($this->_validationRules[$key] as $rule => $ruleValue)
                {
                    //Check if a method exists with the same name as $rule, if so call it
                    if(method_exists($this, $rule))
                    {
                        $this->$rule($key, $value, $ruleValue);
                    }
                }
            }
        }

        //If we don't have any errors
        if(empty($this->_validationErrors))
        {
            return TRUE;
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Check if the required field is empty
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function required($key, $value, $ruleValue)
    {
        //Check if the field is empty
        if(empty($value) && $ruleValue == TRUE)
        {
            $this->_validationErrors[$key] = 'This field is required';
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Validates an email address
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function validEmail($key, $value, $ruleValue)
    {
        //Validate the email address
        if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $value) && $ruleValue == TRUE)
        {
            //Check if there was already an error set for this field and if the field is empty, we don't want to set this error if the field is empty
            if(!isset($this->_validationErrors[$key]) && !empty($value))
            {
                $this->_validationErrors[$key] = 'Please fill in a valid email addresss';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Validates an URL
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function validURL($key, $value, $ruleValue)
    {
        //Validate the URL
        if(!preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $value) && $ruleValue == TRUE)
        {
            //Check if there was already an error set for this field and if the field is empty, we don't want to set this error if the field is empty
            if(!isset($this->_validationErrors[$key]) && !empty($value))
            {
                $this->_validationErrors[$key] = 'This is not a valid URL';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Checks if two fields match
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function match($key, $value, $ruleValue)
    {
        //Check if the values match
        if($value != $this->_post[$ruleValue])
        {
            $this->_validationErrors[$key] = 'does not match';
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Checks the minimum length of the string
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function minLength($key, $value, $ruleValue)
    {
        //Validate the minlength of the field
        if(strlen($value) < $ruleValue)
        {
            //Check if the field is empty, we don't want to set this error if the field is empty
            if(!empty($value))
            {
                $this->_validationErrors[$key] = 'must be longer than '.$ruleValue.' characters';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Checks the maximum length of the string
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function maxLength($key, $value, $ruleValue)
    {
        //Validate the maxLength of the field
        if(strlen($value) > $ruleValue)
        {
            //Check if the field is empty, we don't want to set this error if the field is empty
            if(!empty($value))
            {
                $this->_validationErrors[$key] = 'must be shorter than '.$ruleValue.' characters';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Checks the exact length of the string
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function exactLength($key, $value, $ruleValue)
    {
        //Validate the exactlength of the field
        if(strlen($value) != $ruleValue)
        {
            //Check if the field is empty, we don't want to set this error if the field is empty
            if(!empty($value))
            {
                $this->_validationErrors[$key] = 'must be '.$ruleValue.' characters long';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Checks if the value only contains alphabetical characters
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    public function alpha($key, $value, $ruleValue)
    {
        //Check if the value is alphabetical
        if(!preg_match('/^([a-z])+$/i', $value))
        {
            //Check if there was already an error set for this field and if the field is empty, we don't want to set this error if the field is empty
            if(!isset($this->_validationErrors[$key]) && !empty($value))
            {
                $this->_validationErrors[$key] = 'can only contain letters';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Checks if the value only contains alphabetical and numerical characters
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function alphaNumeric($key, $value, $ruleValue)
    {
        //Check if the value is alphabetical and numerical
        if(!preg_match('/^([a-z0-9])+$/i', $value))
        {
            //Check if there was already an error set for this field and if the field is empty, we don't want to set this error if the field is empty
            if(!isset($this->_validationErrors[$key]) && !empty($value))
            {
                $this->_validationErrors[$key] = 'can only contain letters and numbers';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Checks if the value only contains numerical characters
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function numeric($key, $value, $ruleValue)
    {
        //Check if the value is numeric
        if(!preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $value))
        {
            //Check if there was already an error set for this field and if the field is empty, we don't want to set this error if the field is empty
            if(!isset($this->_validationErrors[$key]) && !empty($value))
            {
                $this->_validationErrors[$key] = 'can only contain numbers';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Checks if the value is low enough
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function lessThan($key, $value, $ruleValue)
    {
        //Check if the value is numeric, because we can't validate this on a string
        if(preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $value))
        {
            //Check if the value is low enough
            if($value >= $ruleValue)
            {
                //Check if there was already an error set for this field and if the field is empty, we don't want to set this error if the field is empty
                if(!isset($this->_validationErrors[$key]) && !empty($value))
                {
                    $this->_validationErrors[$key] = 'Must be less than '.$ruleValue;
                }
            }
        }
        else
        {
            //Check if there was already an error set for this field and if the field is empty, we don't want to set this error if the field is empty
            if(!isset($this->_validationErrors[$key]) && !empty($value))
            {
                $this->_validationErrors[$key] = 'can only contain numbers';
            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     * Checks if the value is high enough
     *
     * @param  string  $key        The name of the posted field
     * @param  string  $value      The value of the posted field
     * @param  string  $ruleValue  The value of the rule
     */
    private function greaterThan($key, $value, $ruleValue)
    {

        //Check if the value is numeric, because we can't validate this on a string
        if(preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $value))
        {
            //Check if the value is low enough
            if($value <= $ruleValue)
            {
                if(!isset($this->_validationErrors[$key]) && !empty($value))
                {
                    $this->_validationErrors[$key] = 'Must be higher than '.$ruleValue;
                }
            }
        }
        else
        {
            //Check if there was already an error set for this field and if the field is empty, we don't want to set this error if the field is empty
            if(!isset($this->_validationErrors[$key]) && !empty($value))
            {
                $this->_validationErrors[$key] = 'can only contain numbers';
            }
        }
    }
}