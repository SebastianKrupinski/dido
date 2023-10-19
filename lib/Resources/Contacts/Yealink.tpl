<?xml version="1.0" encoding="UTF-8"?>
<YealinkIPPhoneDirectory>
{{ section reiterate start }}
{% if(isset($dido)): %}
<DirectoryEntry >
    <Name>{{ $dido->Name->First }}</Name>
    {% foreach ($dido->Phone as $entry): %}
    {% if(!empty($entry->Number)): %}
    <Telephone label="{{ $entry->Type }} {{ $entry->SubType }}">{{ $entry->Number }}</Telephone>
    {% endif; %}
    {% endforeach; %}
</DirectoryEntry>
{% endif; %}
{{ section reiterate end }}
</YealinkIPPhoneDirectory>