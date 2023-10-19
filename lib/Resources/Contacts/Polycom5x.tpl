<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<directory>
<item_list>
{{ section reiterate start }}
{% if(isset($dido)): %}
<item>
    <ln>{{ $dido->Name->First }}</ln>
    <fn>{{ $dido->Name->Last }}</fn>
    <ln>{{ $dido->Occupation->Organization }}</ln>
    {% foreach ($dido->Phone as $entry): %}
    {% if($entry->Type == 'WORK' && $entry->SubType == 'VOICE' && !empty($entry->Number)): %}
    <ct>{{ $entry->Number }}</ct>
    {% elseif($entry->Type == 'CELL' && !empty($entry->Number)): %}
    <ct>{{ $entry->Number }}</ct>
    {% elseif($entry->Type == 'HOME' && $entry->SubType == 'VOICE' && !empty($entry->Number)): %}
    <ct>{{ $entry->Number }}</ct>
    {% elseif($entry->Type == 'CAR' && !empty($entry->Number)): %}
    <ct>{{ $entry->Number }}</ct>
    {% endif; %}
    {% endforeach; %}
    <rt>7</rt>
    <dc/>
    <ad>0</ad>
    <ar>0</ar>
    <bw>0</bw>
    <bb>0</bb>
</item>
{% endif; %}
{{ section reiterate end }}
</item_list>
</directory>