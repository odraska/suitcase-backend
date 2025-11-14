<?php

namespace SLONline\App\GraphQL\Resolvers;

use SLONline\App\Mailchimp;

/**
 * Newsletter GraphQL Resolver
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class NewsletterResolver
{
    public static function resolveNewsletterSubscribe($obj, $args, $context, $info)
    {
        // Implement the logic to subscribe the email to the newsletter
        $email = $args['email'];
        $captchaToken = $args['captchaToken'];
        try {
            $status = Mailchimp::singleton()->subscribe($email);
        } catch (\Exception $e) {
            $status = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }

        return $status['success'];
    }

    public static function resolveNewsletterUnsubscribe($obj, $args, $context, $info)
    {
        // Implement the logic to unsubscribe the email from the newsletter
        $email = $args['email'];
        $hash = $args['captchaToken'];

        try {
            return Mailchimp::singleton()->unsubscribe($email);
        } catch (\Exception $e) {
            return false;
        }
    }
}
