<?xml version="1.0" encoding="UTF-8"?>
<tbook complete="true" e="2" version="2.0">
{{ section reiterate start }}
{% if(isset($data)): %}
<contact fav="false" vip="false" blocked="false">
    <first_name>{{ $data->Name->First }}</first_name>
    <last_name>{{ $data->Name->Last }}</last_name>
    <nick_name>{{ $data->Name->Aliases }}</nick_name>
    <title>{{ $data->Occupation->Title }}</title>
    <organization>{{ $data->Occupation->Organization }}</organization>
    {% if(count($data->Email) > 0): %}
    <email>{{ $data->Email[0]->Address }}</email>
    {% else: %}
    <email></email>
    {% endif; %}
    <note>{{ $data->Notes }}</note>
    <group></group>
    <source_id>2</source_id>
    <photo></photo>
    <numbers>
    {% foreach ($data->Phone as $entry): %}
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