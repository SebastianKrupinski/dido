<?xml version="1.0" encoding="utf-8"?>
<Contacts>
{{ section reiterate start }}
{% if(isset($dido)): %}
<Contact id="123">
    <Name>
        <First>{{ $dido->Name->First }}</First>
        <Middle>{{ $dido->Name->Other }}</Middle>
        <Last>{{ $dido->Name->Last }}</Last>
        <Display>{{ $dido->Name->Label }}</Display>
    </Name>
    <Info>
        <Company>{{ $dido->Occupation->Organization }}</Company>
    </Info>
    {% foreach ($dido->Phone as $entry): %}
    {% if(!empty($entry->Number)): %}
    <Phone>
        {% if($entry->Type == 'WORK' && $entry->SubType == 'VOICE'): %}
        <Type>Work</Type>
        <Type>Phone</Type>
        <Phone>{{ $entry->Number }}</Phone>
        {% elseif($entry->Type == 'WORK' && $entry->SubType == 'FAX'): %}
        <Type>Work</Type>
        <Type>Fax</Type>
        <Phone>{{ $entry->Number }}</Phone>
        {% elseif($entry->Type == 'HOME' && $entry->SubType == 'VOICE'): %}
        <Type>Home</Type>
        <Type>Phone</Type>
        <Phone>{{ $entry->Number }}</Phone>
        {% elseif($entry->Type == 'HOME' && $entry->SubType == 'FAX'): %}
        <Type>Home</Type>
        <Type>Fax</Type>
        <Phone>{{ $entry->Number }}</Phone>
        {% elseif($entry->Type == 'CELL'): %}
        <Type>Work</Type>
        <Type>Cell</Type>
        <Phone>{{ $entry->Number }}</Phone>
        {% endif; %}
        <CustomType></CustomType>
        <Account></Account>
        <Presence></Presence>
        <AccountMappingType>Service</AccountMappingType>
        <PresenceMappingType>Service</PresenceMappingType>
    </Phone>
    {% endif; %}
    {% endforeach; %}
    <Avatar>
        <URL></URL>
    </Avatar>

</Contact>
{% endif; %}
{{ section reiterate end }}
</Contacts>

