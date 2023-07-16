<?php
/**
 * Created by PhpStorm.
 * User: dev1
 * Date: 5.6.2015 г.
 * Time: 07:12 ч.
 */

namespace controllers;


use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use models\PhoneBookModel;
use stz184\FormValidator\Validator;
use stz184\session\Session;

class PhoneBookController extends Controller{

    public static function validatorName(Validator $validator, $field) {
        $val = $validator->getValue($field);

        if (!$val) {
            return true;
        }

        if (!preg_match('/^([a-zа-я]{2,}){1}(\s+[a-zа-я]{2,}){1,}$/ui', $val)) {
            $validator->setError($field, 'Invalid full name. Minimum 2 letters for name and surname are required');
            return false;
        }

        if (PhoneBookModel::isContactExists(Session::getFlashData('id'), $val)) {
            $validator->setError($field, 'This contact is already added.');
            return false;
        }

        return true;
    }

    public static function validatorPhoneNumber(Validator $validator, $field)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $number= $phoneUtil->parse($validator->getValue($field), 'ZZ');
        if ($phoneUtil->isValidNumber($number)) {
            return true;
        } else {
            $validator->setError($field, 'Invalid phone number. Use the international format with leading + sign');
        }
    }

    /**
     * Overkill solution
     * @param string $phoneNumber
     * @return string
     */
    protected static function normalizePhoneNumber($phoneNumber)
    {
        /**
         * Normalize phone number format
         * @var PhoneNumberUtil $phoneUtil
         */
        $phoneUtil          = PhoneNumberUtil::getInstance();
        $phoneNumberProto   = $phoneUtil->parse($phoneNumber, "ZZ");
        return $phoneUtil->format($phoneNumberProto, PhoneNumberFormat::E164);
    }

    public static function actionIndex()
    {
        self::requireLogin();

        $perPage    = 10;
        $page       = \Flight::request()->query['page'];
        $page       = is_numeric($page) ? intval($page) : 1;
        $offset     = ($page - 1) * $perPage;

        $contactsNumber = PhoneBookModel::countContacts(
            Session::getUserData('id'),
            \Flight::request()->query['search']
        );

        $contacts = PhoneBookModel::getContacts(
            Session::getUserData('id'),
            \Flight::request()->query['search'],
            \Flight::request()->query['order_by'],
            \Flight::request()->query['order_type'],
            $offset,
            $perPage
        );

        if ($search = \Flight::request()->query['search']) {
            if (is_array($search)) {
                array_walk($search, function (&$item, $key) {
                    $item = is_string($item) ? htmlspecialchars($item) : $item;
                });
            }
        }

        \Flight::render('phonebook/index', [
            'contacts'          => $contacts,
            'contactsNumber'    => $contactsNumber,
            'search'            => $search,
            'page'              => \Flight::request()->query['page'] ? \Flight::request()->query['page'] : 1,
            'perPage'           => $perPage
        ], 'content');

        \Flight::render('layout', [
            'title'     => 'My Contacts',
            'scripts'   => ['/js/grid.js']
        ]);
    }

    public static function actionAdd()
    {
        self::requireLogin();

        $userData = \Flight::request()->data->getData();
        $validator = new Validator($userData);
        $validator->registerValidator('token', function (Validator $validator, $field) {
            if ($validator->getValue($field) != Session::getCRSFToken()) {
                $validator->setError($field, 'Session expired. Please reload the page and try again');
                return false;
            }
            return true;
        });

        $validator->registerValidator('full_name', ['controllers\PhoneBookController', 'validatorName']);
        $validator->registerValidator('phone_number', ['controllers\PhoneBookController', 'validatorPhoneNumber']);

        $validator->addRule('full_name', 'Full Name', ['required', 'full_name']);
        $validator->addRule('email', 'E-mail', ['required', 'email']);
        $validator->addRule('phone', 'Phone', ['required', 'phone', 'phone_number']);
        $validator->addRule('token', 'token', ['token']);

        if (count($userData)) {
            if ($validator->validate()) {
                $userData['phone'] = self::normalizePhoneNumber($userData['phone']);
                PhoneBookModel::addContact($userData + ['user_id' => Session::getUserData('id')]);
                $response = ['success' => true, 'errors' => []];
            } else {
                $response = ['success' => false, 'errors' => $validator->getErrors()];
            }
            \Flight::json($response);
            return;
        }

        \Flight::render('phonebook/add', [
            'token'     => Session::getCRSFToken(),
            'errors'    => $validator->getErrors()
        ], 'content');

        \Flight::render('layout', [
            'title'     => 'Add a contact',
            'scripts'   => ['/js/add-contact.js']
        ]);
    }

    public static function actionUpdate($id)
    {
        self::requireLogin();

        $contact = PhoneBookModel::getContact($id, Session::getUserData('id'));
        if (!$contact) {
            Session::setFlashData('warning', 'Requested contact does not exist!');
            Session::writeClose();
            \Flight::redirect('/phonebook');
            return;
        }

        $userData = \Flight::request()->data->getData();
        $validator = new Validator($userData);
        $validator->registerValidator('token', function (Validator $validator, $field) {
            if ($validator->getValue($field) != Session::getCRSFToken()) {
                $validator->setError($field, 'Session expired. Please reload the page and try again');
                return false;
            }
            return true;
        });

        $validator->registerValidator('full_name', ['controllers\PhoneBookController', 'validatorName']);
        $validator->registerValidator('phone_number', ['controllers\PhoneBookController', 'validatorPhoneNumber']);

        $validator->addRule('full_name', 'Full Name', ['required', 'full_name']);
        $validator->addRule('email', 'E-mail', ['required', 'email']);
        $validator->addRule('phone', 'Phone', ['required', 'phone', 'phone_number']);
        $validator->addRule('token', 'token', ['token']);

        if (count($userData)) {
            if ($validator->validate()) {
                $userData['phone'] = self::normalizePhoneNumber($userData['phone']);
                PhoneBookModel::updateContact($id, $userData);
                $response = ['success' => true, 'errors' => []];
            } else {
                $response = ['success' => false, 'errors' => $validator->getErrors()];
            }
            \Flight::json($response);
            return;
        }

        array_walk($contact, function (&$item, $key) {
            $item = is_string($item) ? htmlspecialchars($item) : $item;
        });

        \Flight::render('phonebook/update', [
            'token'     => Session::getCRSFToken(),
            'userData'  => $contact
        ], 'content');

        \Flight::render('layout', [
            'title'     => 'Update a contact',
            'scripts'   => ['/js/update-contact.js']
        ]);
    }

    public static function actionDelete($id)
    {
        self::requireLogin();

        $referrer = \Flight::request()->referrer;

        if (PhoneBookModel::deleteContact($id, Session::getUserData('id'))) {
            Session::setFlashData('info', 'The contact is successfully deleted');
        } else {
            Session::setFlashData('danger', 'There was a problem deleting the contact');
        }

        /**
         * Prevent redirect loop if the action is called directly
         * i.e. the page /phonebook/delete/id is requested itself
         */
        if (!preg_match('/\/phonebook\/delete\//', $referrer)) {
            Session::writeClose();
            \Flight::redirect($referrer);
        }
    }
}