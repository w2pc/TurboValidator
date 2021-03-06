<?php

/**
 * TurboValidator 1.0 by Tierlabs
 * Created by: Brian Seymour
 * 
 * License: GNU General Public License Version 2.0
 * 
 * Mandatory Dependencies:
 * - jQuery
 * 
 * Recommended Dependencies:
 * - Bootstrap
 * 
 * The purpose of this validator is to allow high speed server side
 * validation with a low maintenance foot print and an intuitive interface.
 * 
 * TurboValidator design goals:
 * - AJAX Enabled
 * - Clean Validation Errors
 * - Minimum Code Absolutely Necessary/Required (MCAN/R)
 */

class TurboValidator extends RulesBase {
    
    // temporary error store
    public static $errors = array();
    
    public static function validate($data, $overrides = null) {
        // zero out errors
        self::$errors = array();
        
        // override rules at validate time
        if ($overrides) {
            foreach ($overrides as $k => $v) {
                if (is_null($v)) {
                    // unset the rule if it is set to null
                    unset(self::$rules[$k]);
                } else {
                    // change the rule based on the overrides
                    self::$rules[$k] = $v;
                }
            }
        }
        
        // hold errors, if any
        self::$errors = array();
        
        // perform validation
        foreach ($data as $k => $v) {
            $filters = @self::$rules[$k];
            
            if ($filters) {
                foreach ($filters as $f) {
                    switch ($f) {
                        // test for empty fields
                        case 'required':
                            if ($v == '') {
                                @self::$errors[$k] = self::$errors[$k] . 'required,';
                            }
                            break;
                        
                        // test for letters in a field
                        case 'numbers_only':
                            if (!is_numeric($v)) {
                                @self::$errors[$k] = self::$errors[$k] . 'numbers_only,';
                            }
                            break;
                        
                        // test for numbers in a field
                        case 'letters_only':
                            if (!ctype_alpha($v)) {
                                @self::$errors[$k] = self::$errors[$k] . 'letters_only,';
                            }
                            break;
                        
                        // test for unchecked checkbox
                        case 'checked':
                            if ($v != '1') {
                                @self::$errors[$k] = self::$errors[$k] . 'checked,';
                            }
                            break;
                    }
                }
                
                // drop comma off the end if at least one error exists
                if (strlen(@self::$errors[$k]) != 0) {
                    self::$errors[$k] = @substr(self::$errors[$k], 0, -1);
                }
            }
        }
        
        // if no errors, send back a success, else errors and messages
        if (!self::$errors) {
            self::$errors = array(
                'Success' => self::$messages['success_message']
            );
        } else {
            $return_data = array(
                'Errors'   => self::$errors,
                'Messages' => self::$messages
            );
            
            self::$errors = $return_data;
        }
        
        return self::$errors;
    }
    
    public static function terminate() {
        // prep the json and send
        echo json_encode(self::$errors);
        exit();
    }
    
}