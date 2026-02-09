<?php

namespace SLONline\App\Forms\GridField;

use LogicException;
use SilverStripe\Control\HTTPResponse_Exception;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Model\List\SS_List;
use SilverStripe\ORM\DataObject;
use SLONline\Elefont\Model\FontFamily;
use SLONline\GridFieldExtensions\GridFieldAddExistingDropdown;

/**
 * Grid Field component that adds a dropdown to select a FontFamily, and adds all related Font objects to the list.
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2026, SLONline, s.r.o.
 */
class GridFieldAddVisualStyleDropdown extends GridFieldAddExistingDropdown
{
    public function getManipulatedData(GridField $gridField, SS_List $dataList): SS_List
    {
        $dataClass = $gridField->getModelClass();

        if (!is_a($dataClass, DataObject::class, true)) {
            throw new LogicException(__CLASS__ . " must be used with DataObject subclasses. Found '$dataClass'");
        }

        $objectID = $gridField->State->GridFieldAddRelation(null);
        if (empty($objectID)) {
            return $dataList;
        }

        $gridField->State->GridFieldAddRelation = null;
        /** @var FontFamily $fontFamily */
        $fontFamily = DataObject::get_by_id(FontFamily::class, $objectID);
        if ($fontFamily) {
            $fontFamily->Fonts()->each(function ($font) use ($dataList) {
                if (!$font->canView()) {
                    throw new HTTPResponse_Exception(null, 403);
                }
                $dataList->add($font);
            });
        }

        return $dataList;
    }
}
