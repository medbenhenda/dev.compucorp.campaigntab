<?php

require_once 'CRM/Core/Page.php';

class CRM_Campaigntab_Page_CampaignTab extends CRM_Core_Page {
  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('CampaignTab'));

    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));

    $result = $this->getCampaignSummary();
    $this->assign('campaignsData', $result);
    parent::run();
  }

  public static function getCampaignSummary($params = array()) {
    $campaignsData = array();

    //get the campaigns.
    //$campaigns = CRM_Campaign_BAO_Campaign::getCampaignSummary($params);
    $properties = array(
        'id',
        'title',
        'status_id',
        'goal_revenue',
    );

    // Query Campaign
    $selectClause = '
      SELECT  campaign.id   as id,
      campaign.title        as title,
      campaign.status_id    as status_id,
      campaign.goal_revenue as goal_revenue';
    $fromClause = 'FROM  civicrm_campaign campaign';

    $query = "{$selectClause} {$fromClause}";

    $campaign = CRM_Core_DAO::executeQuery($query);
    while ($campaign->fetch()) {
      foreach ($properties as $property) {
        $campaigns[$campaign->id][$property] = $campaign->$property;
      }
    }
    if (!empty($campaigns)) {
      $campaignStatus = CRM_Campaign_PseudoConstant::campaignStatus();

      foreach ($campaigns as $cmpid => $campaign) {
        // Query Contribution
        $selectClause = '
        SELECT  contribution.id           as id,
        contribution.total_amount         as contrib_amount,
        contribution.contribution_page_id as contrib_page_id';

        $fromClause = 'FROM  civicrm_contribution contribution';

        $whereClause = " WHERE contribution.campaign_id = {$cmpid}";

        $query = "{$selectClause} {$fromClause} {$whereClause}";

        $proContrib = array(
            'id',
            'contrib_amount',
            'contrib_page_id',
        );
        $contribution = CRM_Core_DAO::executeQuery($query);
        $total_amount = 0;
        while ($contribution->fetch()) {
          $campaignsData[$cmpid]['contribution'][] = array(
              'contrib_id' => $contribution->id,
              'contrib_amount' => $contribution->contrib_amount,
              'contrib_page_id' => $contribution->contrib_page_id,
          );
          $total_amount += $contribution->contrib_amount;
        }

        // Query Events
        $eventSummary = CRM_Event_BAO_Event::getEventSummary();

        $actionColumn = FALSE;
        if (!empty($eventSummary) &&
            isset($eventSummary['events']) &&
            is_array($eventSummary['events'])
        ) {
          foreach ($eventSummary['events'] as $e) {
            $campaignsData[$cmpid]['events'][] = array(
                'event_id' => $e['id'],
                'event_title' => $e['eventTitle'],
            );
          }
        }

        foreach ($properties as $prop) {
          $campaignsData[$cmpid][$prop] = CRM_Utils_Array::value($prop, $campaign);
        }
        $statusId = CRM_Utils_Array::value('status_id', $campaign);
        $campaignsData[$cmpid]['status'] = CRM_Utils_Array::value($statusId, $campaignStatus);
        $campaignsData[$cmpid]['campaign_id'] = $campaign['id'];
        $campaignsData[$cmpid]['total_amount'] = $total_amount;
      }
    }

    return $campaignsData;
  }
}
