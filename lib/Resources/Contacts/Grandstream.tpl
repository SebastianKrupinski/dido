<?xml version="1.0" encoding="UTF-8"?>
<AddressBook>
{{ section reiterate start }}
{% if(isset($dido)): %}

<Contact>
    <LastName>{{ $dido->Name->Last }}</LastName>
    <FirstName>{{ $dido->Name->First }}</FirstName>
    <Primary>0</Primary>
    {% foreach ($dido->Phone as $entry): %}
    {% if($entry->Type == 'WORK' && $entry->SubType == 'VOICE' && !empty($entry->Number)): %}
    <Phone type="Work">
        <phonenumber>{{ $entry->Number }}</phonenumber>
    </Phone>
    {% elseif($entry->Type == 'HOME' && $entry->SubType == 'VOICE' && !empty($entry->Number)): %}
    <Phone type="Work">
        <phonenumber>{{ $entry->Number }}</phonenumber>
    </Phone>
    {% elseif($entry->Type == 'CELL' && !empty($entry->Number)): %}
    <Phone type="Home">
        <phonenumber>{{ $entry->Number }}</phonenumber>
    </Phone>
    {% elseif($entry->Type == 'CAR' && !empty($entry->Number)): %}
    <Phone type="Cell">
        <phonenumber>{{ $entry->Number }}</phonenumber>
    </Phone>
    {% endif; %}
    {% endforeach; %}
</Contact>
{% endif; %}
{{ section reiterate end }}
</AddressBook>
