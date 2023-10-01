<?xml version="1.0" encoding="UTF-8"?>
<AddressBook>
{{ section reiterate start }}
{% if(isset($data)): %}
<Contact>
    <LastName>{{ $data->Name->Last }}</LastName>
    <FirstName>{{ $data->Name->First }}</FirstName>
    {% foreach ($data->Phone as $entry): %}
    {% if($entry->Type == 'WORK' && $entry->SubType == 'VOICE' && !empty($entry->Number)): %}
    <Phone>
        <phonenumber>{{ $entry->Number }}</phonenumber>
        <accountindex>1</accountindex>
    </Phone>
    {% endif; %}
    {% endforeach; %}
</Contact>
{% endif; %}
{{ section reiterate end }}
</AddressBook>