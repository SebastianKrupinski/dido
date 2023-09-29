<?php
//declare(strict_types=1);

/**
* @copyright Copyright (c) 2023 Sebastian Krupinski <krupinski01@gmail.com>
*
* @author Sebastian Krupinski <krupinski01@gmail.com>
*
* @license AGPL-3.0-or-later
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace OCA\Data\Objects;

use DateTime;

class ContactObject {

    public ?string $ID = null;
	public ?string $UID = null;
    public ?string $CID = null;
    public ?string $State = null;
    public ?DateTime $CreatedOn = null;
    public ?DateTime $ModifiedOn = null;
    public ?string $Label = null;
	public ?ContactNameObject $Name = null;
    public ?string $Aliases = null;
    public ?ContactPhotoObject $Photo = null;
    public ?DateTime $BirthDay = null;
    public ?string $Gender = null;
    public ?string $Partner = null;
	public ?DateTime $AnniversaryDay = null;
	public array $Address = [];
	public array $Phone = [];
    public array $Email = [];
    public array $IMPP = [];
    public ?string $TimeZone = null;
    public ?string $Geolocation = null;
    public ?string $Manager = null;
    public ?string $Assistant = null;
    public ?ContactOccupationObject $Occupation = null;
    public ?array $Relation = [];
    public array $Tags = [];
    public ?string $Notes = null;
    public ?string $Sound = null;
    public ?string $URI = null;
    public array $Attachments = [];
    public ?array $Other = [];
	
	public function __construct() {
        $this->Name = new ContactNameObject();
        $this->Photo = new ContactPhotoObject();
        $this->Occupation = new ContactOccupationObject();
	}

    public function addEmail(string $type, string $address) {
        $this->Email[] = new ContactEmailObject($type, $address);
    }

    public function addPhone(string $type, ?string $subtype, string $number) {
        $this->Phone[] = new ContactPhoneObject($type, $subtype, $number);
    }

    public function addAddress($type, ?string $street = null, ?string $locality = null, ?string $region = null, ?string $code = null, ?string $country = null) {
        $this->Address[] = new ContactAddressObject($type, $street, $locality, $region, $code, $country);
    }

    public function addIMPP(string $type, string $address) {
        $this->IMPP[] = new ContactIMPPObject($type, $address);
    }

    public function addTag(string $tag) {
        $this->Tags[] = $tag;
    }

    public function addRelation(string $type, string $value) {
        $this->Phone[] = new ContactRelationObject($type, $value);
    }
    
    public function addAttachment(string $id, ?string $name = null, ?string $type = null, ?string $encoding = null, ?string $flag = null, ?string $size = null,  ?string $data = null) {
        $this->Attachments[] = new ContactAttachmentObject($id, $name, $type, $encoding, $flag, $size, $data);
    }

}
