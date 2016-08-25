{*$campaignsData|@var_dump*}
<table class="campaigns">
    <thead>
    <tr class="columnheader">
        <th>{ts}Title{/ts}</th>
        <th>{ts}Status{/ts}</th>
        <th>{ts}Contribution{/ts}</th>
        <th>{ts}Events{/ts}</th>
        <th>{ts}Amount raised{/ts}</th>
        <th>{ts}Goal Revenue{/ts}</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$campaignsData item=campaign}
        <td>
            {capture assign=idcampaign}{$campaign.id}{/capture}
            <a href="{crmURL p='civicrm/campaign/add' q="reset=1&action=update&id=$idcampaign" h="1"}">{$campaign.title}</a></td>

        <td>{$campaign.status}</td>
        <td>
            <ul>
                {foreach from=$campaign.contribution item=contribution}
                    <li>
                        {capture assign=idcontribution}{$contribution.contrib_id}{/capture}
                        <a href="{crmURL p='civicrm/contact/view/contribution' q="reset=1&action=view&id=$idcontribution"}">
                            {$contribution.contrib_id}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </td>
        <td>
            {foreach from=$campaign.events item=event}
                <li>
                    {capture assign=idevent}{$event.event_id}{/capture}
                    <a href="{crmURL p='civicrm/event/info' q="reset=1&id=$idevent"}">
                        {$event.event_title}
                    </a>
                </li>
            {/foreach}
        </td>
        <td>
            {$campaign.total_amount}
        </td>
        <td>{$campaign.goal_revenue}</td>
    {/foreach}
    </tbody>
</table>
<ul>

</ul>
