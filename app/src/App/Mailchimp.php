<?php

namespace SLONline\App;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;

/**
 * Mailchimp integration class
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class Mailchimp
{
    use Injectable;
    use Configurable;

    private static string $api_key = '';
    private static string $list_id = '';

    /**
     * @throws \Exception
     */
    public function subscribe(string $email)
    {
        $returnData = [
            'success' => false,
            'message' => _t(__CLASS__ . '.ERROR', 'Connection error'),
        ];

        $client = new \DrewM\MailChimp\MailChimp($this->config()->get('api_key'));
        $subscriberHash = \DrewM\MailChimp\MailChimp::subscriberHash($email);

        $memberInfo = $client->get(sprintf(
            'lists/%s/members/%s',
            $this->config()->get('list_id'),
            $subscriberHash
        ));
        $memberFound = $client->success();

        if ($memberFound && $memberInfo && isset($memberInfo['status']) && $memberInfo['status'] == 'subscribed') {
            // The e-mail address has already subscribed, provide feedback
            $returnData['success'] = false;
            $returnData['message'] = _t(
                __CLASS__ . '.DUPLICATE',
                'This email address is already subscribed.'
            );
        } else {
            // build submission data
            $submissionData = [
                'email_address' => $email,
                'status' => 'pending',
            ];

            if (!$memberFound) {
                // no on list, new subscription
                $client->post(
                    sprintf(
                        'lists/%s/members',
                        $this->config()->get('list_id')
                    ),
                    $submissionData
                );
            } else {
                $submissionData['status'] = 'unsubscribed';
                // update existing record
                $client->patch(
                    sprintf(
                        'lists/%s/members/%s',
                        $this->config()->get('list_id'),
                        $subscriberHash
                    ),
                    $submissionData
                );

                $submissionData['status'] = 'pending';
                // update existing record
                $client->patch(
                    sprintf(
                        'lists/%s/members/%s',
                        $this->config()->get('list_id'),
                        $subscriberHash
                    ),
                    $submissionData
                );
            }

            // check if update/adding successful
            if ($client->success()) {
                // set message
                $returnData['success'] = true;
                $returnData['message'] = _t(__CLASS__ . '.SUCCESS',
                    'The email address subscribed, you will receive confirmation email.');
            } else {
                user_error('Last Error: ' . print_r($client->getLastError(), true), E_USER_WARNING);
                user_error('Last Request: ' . print_r($client->getLastRequest(), true), E_USER_WARNING);
                user_error('Last Response: ' . print_r($client->getLastResponse(), true), E_USER_WARNING);
            }
        }

        return $returnData;
    }

    public function unsubscribe(string $email): bool
    {
        $client = new \DrewM\MailChimp\MailChimp($this->config()->get('api_key'));
        $subscriberHash = \DrewM\MailChimp\MailChimp::subscriberHash($email);

        $memberInfo = $client->get(sprintf(
            'lists/%s/members/%s',
            $this->config()->get('list_id'),
            $subscriberHash
        ));
        $memberFound = $client->success();

        if ($memberFound && $memberInfo && isset($memberInfo['status']) && $memberInfo['status'] == 'subscribed') {
            $client->delete(
                sprintf(
                    'lists/%s/members/%s',
                    $this->config()->get('list_id'),
                    md5(strtolower($email))
                ));

            return $client->success();
        }

        return true;
    }

    public function isSubscribed(string $email): bool
    {
        $client = new \DrewM\MailChimp\MailChimp($this->config()->get('api_key'));
        $subscriberHash = \DrewM\MailChimp\MailChimp::subscriberHash($email);
        $memberInfo = $client->get(sprintf(
            'lists/%s/members/%s',
            $this->config()->get('list_id'),
            $subscriberHash
        ));
        $memberFound = $client->success();
        if ($memberFound && $memberInfo && isset($memberInfo['status']) && $memberInfo['status'] == 'subscribed') {
            return true;
        }

        return false;
    }
}
