<?xml version="1.0" encoding="UTF-8"?>
<tbook complete="true" e="2" version="2.0">
{{ section reiterate start }}
<contact fav="true" vip="true" blocked="false">
    {% if(isset($data)): %}
    <first_name>{{ $data->NameFirst }}</first_name>
    <last_name>{{ $data->NameLast }}</last_name>
    <nick_name>{{ $data->NameAlias }}</nick_name>
    <title>{{ $data->NamePrefix }}</title>
    <organization>{{ $OrganizationName }}</organization>
    <email>{{ $EmailWork }}</email>
    <note>{{ $Note }}</note>
    <group>{{ $Group }}</group>
    <source_id>{{ $CollectionId }}</source_id>
    <photo>{{ $Photo }}</photo>
    {% if(is_array($Phone)): %}
    <numbers>
    {% for ($i=0; $i < 3; $i++): %}
        <number no="" type="business" outgoing_id="0">
        </number>
    {% endfor; %}
    </numbers>
    {% endif; %}
    {% endif; %}
</contact>
{{ section reiterate end }}
</tbook>