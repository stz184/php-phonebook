<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 9.6.2015 г.
 * Time: 22:41 ч.
 */

namespace tests;


use stz184\FormValidator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase {

    public function testRequired()
    {
        $testData = array(
            'empty'     => '',
            'empty2'    => null,
            'empty3'    => '  ',
            'empty4'    => 'test'
        );

        $validator = new Validator($testData);
        $validator->addRule('empty', 'empty', ['required']);
        $validator->addRule('empty2', 'empty2', ['required']);
        $validator->addRule('empty3', 'empty3', ['required']);
        $validator->addRule('empty4', 'empty4', ['required']);

        $this->assertFalse($validator->validate());
        $this->assertFalse($validator->isValid());


        $this->assertTrue($validator->hasErrors('empty'), $validator->getFieldError('empty'));
        $this->assertTrue($validator->hasErrors('empty2'), $validator->getFieldError('empty2'));
        $this->assertTrue($validator->hasErrors('empty3'), $validator->getFieldError('empty3'));
        $this->assertFalse($validator->hasErrors('empty4'), $validator->getFieldError('empty4'));
    }

    public function testMinLength()
    {
        $testData = array(
            'minlength'     => 'abv',
            'minlength2'    => 'cde',
            'minlength3'    => null,
            'minlength4'    => '',
            'minlength5'    => 'abcdef'
        );

        $validator = new Validator($testData);
        $validator->addRule('minlength', 'minlength', ['minlength' => 5]);
        $validator->addRule('minlength2', 'minlength2', ['required', 'minlength' => 5]);
        $validator->addRule('minlength3', 'minlength3', ['required', 'minlength' => 5]);
        $validator->addRule('minlength4', 'minlength4', ['minlength' => 5]);
        $validator->addRule('minlength5', 'minlength5', ['required', 'minlength' => 5]);

        $this->assertFalse($validator->validate());
        $this->assertFalse($validator->isValid());


        $this->assertTrue($validator->hasErrors('minlength'), $validator->getFieldError('minlength'));
        $this->assertTrue($validator->hasErrors('minlength2'), $validator->getFieldError('minlength2'));
        $this->assertTrue($validator->hasErrors('minlength3'), $validator->getFieldError('minlength3'));
        /** The field contains string with length less than 5 chars but it is not requited  */
        $this->assertFalse($validator->hasErrors('minlength4'), $validator->getFieldError('minlength4'));
        $this->assertFalse($validator->hasErrors('minlength5'), $validator->getFieldError('minlength5'));
    }
    
    public function testMaxLength()
    {
        $testData = array(
            'maxlength'     => 'abv',
            'maxlength2'    => 'cde',
            'maxlength3'    => null,
            'maxlength4'    => '',
            'maxlength5'    => 'abcdef'
        );

        $validator = new Validator($testData);
        $validator->addRule('maxlength', 'maxlength', ['maxlength' => 5]);
        $validator->addRule('maxlength2', 'maxlength2', ['required', 'maxlength' => 5]);
        $validator->addRule('maxlength3', 'maxlength3', ['required', 'maxlength' => 5]);
        $validator->addRule('maxlength4', 'maxlength4', ['maxlength' => 5]);
        $validator->addRule('maxlength5', 'maxlength5', ['required', 'maxlength' => 5]);

        $this->assertFalse($validator->validate());
        $this->assertFalse($validator->isValid());



        $this->assertFalse($validator->hasErrors('maxlength'), $validator->getFieldError('maxlength'));
        $this->assertFalse($validator->hasErrors('maxlength2'), $validator->getFieldError('maxlength2'));
        /** The field contains less than 5 symbols but it is required, so it cannot be empty */
        $this->assertTrue($validator->hasErrors('maxlength3'), $validator->getFieldError('maxlength3'));
        $this->assertFalse($validator->hasErrors('maxlength4'), $validator->getFieldError('maxlength4'));
        $this->assertTrue($validator->hasErrors('maxlength5'), $validator->getFieldError('maxlength5'));
    }

    public function testEmail()
    {
        $testData = array(
            'email'     => '',
            'email2'    => 'wrong@email',
            'email3'    => 'wrong@@email.com',
            'email4'    => 'correct@email.com',
            'email5'    => 'abcdef'
        );

        $validator = new Validator($testData);
        $validator->addRule('email', 'email', ['email']);
        $validator->addRule('email2', 'email2', ['required', 'email']);
        $validator->addRule('email3', 'email3', ['required', 'email']);
        $validator->addRule('email4', 'email4', ['email']);
        $validator->addRule('email5', 'email5', ['required', 'email']);

        $this->assertFalse($validator->validate());
        $this->assertFalse($validator->isValid());


        /** The field is not required, so it can be empty and contains no valid email address */
        $this->assertFalse($validator->hasErrors('email'), $validator->getFieldError('email'));
        $this->assertTrue($validator->hasErrors('email2'), $validator->getFieldError('email2'));
        $this->assertTrue($validator->hasErrors('email3'), $validator->getFieldError('email3'));
        $this->assertFalse($validator->hasErrors('email4'), $validator->getFieldError('email4'));
        $this->assertTrue($validator->hasErrors('email5'), $validator->getFieldError('email5'));
    }

    public function testPhone()
    {
        $testData = array(
            'phone'     => '',
            'phone2'    => '1e20',
            'phone3'    => 'test',
            'phone4'    => '0885625721',
            'phone5'    => '+359885625721'
        );
    
        $validator = new Validator($testData);
        $validator->addRule('phone', 'phone', ['phone']);
        $validator->addRule('phone2', 'phone2', ['required', 'phone']);
        $validator->addRule('phone3', 'phone3', ['required', 'phone']);
        $validator->addRule('phone4', 'phone4', ['phone']);
        $validator->addRule('phone5', 'phone5', ['required', 'phone']);

        $this->assertFalse($validator->validate());
        $this->assertFalse($validator->isValid());


        /** The field is not required, so it can be empty and contains no valid phone number */
        $this->assertFalse($validator->hasErrors('phone'), $validator->getFieldError('phone'));
        $this->assertTrue($validator->hasErrors('phone2'), $validator->getFieldError('phone2'));
        $this->assertTrue($validator->hasErrors('phone3'), $validator->getFieldError('phone3'));
        $this->assertTrue($validator->hasErrors('phone4'), $validator->getFieldError('phone4'));
        $this->assertFalse($validator->hasErrors('phone5'), $validator->getFieldError('phone5'));
    }


     public function testSkype()
    {
        $testData = array(
            'skype'     => '',
            'skype2'    => '1e20',
            'skype3'    => false,
            'skype4'    => '0invalidSkype',
            'skype5'    => 'valid.skype.name'
        );

        $validator = new Validator($testData);
        $validator->addRule('skype', 'skype', ['skype']);
        $validator->addRule('skype2', 'skype2', ['required', 'skype']);
        $validator->addRule('skype3', 'skype3', ['required', 'skype']);
        $validator->addRule('skype4', 'skype4', ['required', 'skype']);
        $validator->addRule('skype5', 'skype5', ['required', 'skype']);

        $this->assertFalse($validator->validate());
        $this->assertFalse($validator->isValid());


        /** The field is not required, so it can be empty and contains no valid skype name */
        $this->assertFalse($validator->hasErrors('skype'), $validator->getFieldError('skype'));
        $this->assertTrue($validator->hasErrors('skype2'), $validator->getFieldError('skype2'));
        $this->assertTrue($validator->hasErrors('skype3'), $validator->getFieldError('skype3'));
        $this->assertTrue($validator->hasErrors('skype4'), $validator->getFieldError('skype4'));
        $this->assertFalse($validator->hasErrors('skype5'), $validator->getFieldError('skype5'));
    }

    public function testURL()
    {
        /**
         * Actually, the pattern agains the URL is matched is loosy.
         * It allows to skip the http:// part (i.e. the protocol)
         * because it's more easy to people and always it can prepended with
         * normalizing function.
         */

        $testData = array(
            'url'     => '',
            'url2'    => '1e20',
            'url3'    => false,
            'url4'    => 'invalid.domain.111    ',
            'url5'    => 'http://abv.bg'
        );

        $validator = new Validator($testData);
        $validator->addRule('url', 'url', ['url']);
        $validator->addRule('url2', 'url2', ['required', 'url']);
        $validator->addRule('url3', 'url3', ['required', 'url']);
        $validator->addRule('url4', 'url4', ['required', 'url']);
        $validator->addRule('url5', 'url5', ['required', 'url']);

        $this->assertFalse($validator->validate());
        $this->assertFalse($validator->isValid());


        /** The field is not required, so it can be empty and contains no valid url name */
        $this->assertFalse($validator->hasErrors('url'), $validator->getFieldError('url'));
        $this->assertTrue($validator->hasErrors('url2'), $validator->getFieldError('url2'));
        $this->assertTrue($validator->hasErrors('url3'), $validator->getFieldError('url3'));
        $this->assertTrue($validator->hasErrors('url4'), $validator->getFieldError('url4'));
        $this->assertFalse($validator->hasErrors('url5'), $validator->getFieldError('url5'));
    }

    public function testNumber()
    {
        /**
         * Actually, the pattern agains the URL is matched is loosy.
         * It allows to skip the http:// part (i.e. the protocol)
         * because it's more easy to people and always it can prepended with
         * normalizing function.
         */

        $testData = array(
            'number' => '',
            'number2' => '1337',
            'number3' => 0x539,
            'number4' => 02471,
            'number5' => 0b10100111001,
            'number6' => 1337e0,
            'number7' => "not numeric",
            'number8' => array(),
            'number9' => 9.1
        );

        $validator = new Validator($testData);
        $validator->addRule('number', 'number', ['number']);
        $validator->addRule('number2', 'number2', ['required', 'number']);
        $validator->addRule('number3', 'number3', ['required', 'number']);
        $validator->addRule('number4', 'number4', ['required', 'number']);
        $validator->addRule('number5', 'number5', ['required', 'number']);
        $validator->addRule('number6', 'number6', ['required', 'number']);
        $validator->addRule('number7', 'number7', ['required', 'number']);
        $validator->addRule('number8', 'number8', ['required', 'number']);
        $validator->addRule('number9', 'number9', ['required', 'number']);

        $this->assertFalse($validator->validate());
        $this->assertFalse($validator->isValid());


        /** The field is not required, so it can be empty and contains no valid number */
        $this->assertFalse($validator->hasErrors('number'), $validator->getFieldError('number'));
        $this->assertFalse($validator->hasErrors('number2'), $validator->getFieldError('number2'));
        $this->assertFalse($validator->hasErrors('number3'), $validator->getFieldError('number3'));
        $this->assertFalse($validator->hasErrors('number4'), $validator->getFieldError('number4'));
        $this->assertFalse($validator->hasErrors('number5'), $validator->getFieldError('number5'));
        $this->assertFalse($validator->hasErrors('number6'), $validator->getFieldError('number6'));
        $this->assertTrue($validator->hasErrors('number7'), $validator->getFieldError('number7'));
        $this->assertTrue($validator->hasErrors('number8'), $validator->getFieldError('number8'));
        $this->assertFalse($validator->hasErrors('number9'), $validator->getFieldError('number9'));
    }

    public function testMatches()
    {

        $testData = array(
            'field' => '',
            'field1' => '42',
            'field2' => 42,
            'field3' => '42',
        );

        $validator = new Validator($testData);
        $validator->addRule('field', 'field', ['required', 'matches' => 'field1']);
        $validator->addRule('field1', 'field1', ['required']);
        $validator->addRule('field2', 'field2', ['required', 'matches' => 'field3']);
        $validator->addRule('field3', 'field3', ['required']);

        $this->assertFalse($validator->validate());
        $this->assertFalse($validator->isValid());


        $this->assertTrue($validator->hasErrors('field'), $validator->getFieldError('field'));
        $this->assertFalse($validator->hasErrors('field1'), $validator->getFieldError('field1'));
        $this->assertFalse($validator->hasErrors('field2'), $validator->getFieldError('field2'));
        $this->assertFalse($validator->hasErrors('field3'), $validator->getFieldError('field3'));
    }



}