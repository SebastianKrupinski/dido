<?xml version="1.0" encoding="UTF-8"?>
<tbook complete="true" e="2" version="2.0">
{{ section reiterate start }}
{% if(isset($dido)): %}
<contact fav="false" vip="false" blocked="false">
    <first_name>{{ $dido->Name->First }}</first_name>
    <last_name>{{ $dido->Name->Last }}</last_name>
    <nick_name>{{ $dido->Name->Aliases }}</nick_name>
    <title>{{ $dido->Occupation->Title }}</title>
    <organization>{{ $dido->Occupation->Organization }}</organization>
    {% if(count($dido->Email) > 0): %}
    <email>{{ $dido->Email[0]->Address }}</email>
    {% else: %}
    <email></email>
    {% endif; %}
    <note>{{ $dido->Notes }}</note>
    <group></group>
    <source_id>2</source_id>
    <photo></photo>
    <numbers>
    {% foreach ($dido->Phone as $entry): %}
        {% if($entry->Type == 'WORK' && $entry->SubType == 'VOICE' && !empty($entry->Number)): %}
        <number no="{{ $entry->Number }}" type="business" outgoing_id="0"></number>
        {% elseif($entry->Type == 'HOME' && $entry->SubType == 'VOICE' && !empty($entry->Number)): %}
        <number no="{{ $entry->Number }}" type="home" outgoing_id="0"></number>
        {% elseif($entry->Type == 'CELL' && !empty($entry->Number)): %}
        <number no="{{ $entry->Number }}" type="mobile" outgoing_id="0"></number>
        {% elseif($entry->Type == 'CAR' && !empty($entry->Number)): %}
        <number no="{{ $entry->Number }}" type="mobile" outgoing_id="0"></number>
        {% endif; %}
    {% endforeach; %}
    </numbers>
</contact>
{% endif; %}
{{ section reiterate end }}
</tbook>