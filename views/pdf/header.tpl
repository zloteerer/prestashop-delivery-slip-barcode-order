<table style="width: 100%">
    <tr>
        <td style="width: 50%">
            {if $logo_path}
                <img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
            {/if}
        </td>
        <td style="width: 50%; text-align: right;">
            <table style="width: 100%">
                <tr>
                    <td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%;">{if isset($header)}{$header|escape:'html':'UTF-8'|upper}{/if}</td>
                </tr>
                <tr>
                    <td style="font-size: 14pt; color: #9E9F9E">{$date|escape:'html':'UTF-8'}</td>
                </tr>
                <tr>
                    <td style="font-size: 14pt; color: #9E9F9E">{$title|escape:'html':'UTF-8'}</td>
                </tr>
                {if $barcode_url && $barcode_code}
                    <tr>
                        <table style="100%; padding-top: 10px">
                            <tr>
                                <td style="width: 50%"></td>
                                <td style="vertical-align:top; width: 50%; text-align: center;">
                                    <img src="{$barcode_url}" />
                                    {$barcode_code}
                                </td>
                            </tr>
                        </table>
                    </tr>
                {/if}
            </table>
        </td>
    </tr>
</table>

