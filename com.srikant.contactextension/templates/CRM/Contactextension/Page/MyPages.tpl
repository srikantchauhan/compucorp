{if count($data) == 0}
<h1>This Contact has not created any campaign page.</h2>
{else}
<table>
  <tr>
    <th>
      Page Name
    </th>
    <th>
      Status
    </th>
    <th>
      Cause Title
    </th>
    <th>
      Contributions
    </th>
    <th>
      Amount Raised
    </th>
    <th>
      Target Amount
    </th>
    <th>
      Edit
    </th>
  </tr>
  {foreach from=$data item=entry}
    <tr>
      <td>
        <a href={crmURL p='civicrm/pcp/info' q="reset=1&id=`$entry.id`"}>{$entry.c_title}</a>
      </td>
      <td>
        {$entry.status}
      </td>
      <td>
        {$entry.cause_title}
      </td>
      <td>
        {$entry.contributors}
      </td>
      <td>
        {$entry.amount_raised}
      </td>
      <td>
        {$entry.goal_amount}
      </td>
      <td>
        <a href={crmURL p='civicrm/pcp/info' q="action=update&reset=1&id=`$entry.id`"}>edit</a>
      </td>
    </tr>
  {/foreach}
</table>
{/if}
