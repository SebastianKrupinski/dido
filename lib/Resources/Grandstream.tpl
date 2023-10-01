<?xml version="1.0" encoding="UTF-8"?>
<AddressBook>
{{ section reiterate start }}
{% if(isset($data)): %}
{% foreach ($data->Phone as $entry): %}
{% if($entry->Type == 'WORK' && $entry->SubType == 'VOICE' && !empty($entry->Number)): %}
{% $number = $entry->Number %}
{% break %}
{% elseif($entry->Type == 'CELL' && !empty($entry->Number)): %}
{% $number = $entry->Number %}
{% break %}
{% elseif($entry->Type == 'HOME' && $entry->SubType == 'VOICE' && !empty($entry->Number)): %}
{% $number = $entry->Number %}
{% break %}
{% elseif($entry->Type == 'CAR' && !empty($entry->Number)): %}
{% $number = $entry->Number %}
{% break %}
{% endif; %}
{% endforeach; %}
{% if(isset($number)): %}
<Contact>
    <LastName>{{ $data->Name->Last }}</LastName>
    <FirstName>{{ $data->Name->First }}</FirstName>
    <Phone>
        <phonenumber>{{ $number }}</phonenumber>
        <accountindex>1</accountindex>
    </Phone>
</Contact>
{% endif; %}
{% endif; %}
{{ section reiterate end }}
</AddressBook>