<?php

namespace SLONline\App\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\DataList;

/**
 * Saved Cart Controller
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
class SavedCart extends Controller
{
    /**
     * Default URL handlers.
     *
     * @var array
     */
    private static array $url_handlers = [
        '$Action//$hash' => 'handleAction',
    ];

    private static array $allowed_actions = [
        'download',
    ];

    public function index()
    {
        return $this->httpError(403, "Action isn't allowed.");
    }

    public function download(): HTTPResponse
    {
        if (!$this->getRequest()->param('hash')){
            return $this->httpError(403, "Action isn't allowed.");
        }

        /** @var \SLONline\App\Model\SavedCart $savedCart */
        $savedCart = DataList::create(\SLONline\App\Model\SavedCart::class)
            ->filter(['Hash' => $this->getRequest()->param('hash')])
            ->first();

        if ($savedCart && $savedCart->exists()) {
            return HTTPRequest::send_file(
                $savedCart->generatePDF(),
                'quote.pdf',
                'application/pdf'
            );
        }

        return $this->httpError(403, "Action isn't allowed.");
    }
}
