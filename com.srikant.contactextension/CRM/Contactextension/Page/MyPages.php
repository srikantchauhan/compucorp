<?php

require_once 'CRM/Core/Page.php';

class CRM_Contactextension_Page_MyPages extends CRM_Core_Page {
  public function run() {
    CRM_Utils_System::setTitle(ts("Contact's campagin pages"));
    $data = $this->getCampaignPagesData();
    $this->assign('data', $data);
    parent::run();
  }

  private function getCampaignPagesData() {
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
    if (empty($cid)) {
      return array();
    }

    #get pages
    $query = "SELECT cp.id, cp.title as c_title, cp.status_id, ccp.title as cause_title, cp.goal_amount FROM civicrm_pcp cp LEFT JOIN civicrm_contribution_page ccp ON cp.page_id = ccp.id WHERE contact_id = {$cid}";
    $dao = CRM_Core_DAO::executeQuery($query);
    $data = array();
    $counter = 0;
    $status = array();
    $contributors = array();
    $pcpIds = array();
    while ($dao->fetch()) {
       $pcpIds[] = $dao->id;
       $data[$counter]['id'] = $dao->id;
       $data[$counter]['c_title'] = $dao->c_title;
       $data[$counter]['status_id'] = $dao->status_id;
       $status[] = $dao->status_id;
       $data[$counter]['cause_title'] = $dao->cause_title;
       $data[$counter]['goal_amount'] = $dao->goal_amount;
       $data[$counter]['amount_raised'] = 0;
       $data[$counter]['contributors'] = 0;
       $counter++;
    }


    if (!empty($data)) {
      //get status
      $statusData = array();
      $queryStatus = "SELECT v.label as label ,v.value as value FROM civicrm_option_value v, civicrm_option_group g WHERE v.option_group_id = g.id AND g.name = 'pcp_status' AND v.is_active = 1 AND g.is_active = 1 AND v.value in (" . implode(",", $status). ")";
      $daoStatus = CRM_Core_DAO::executeQuery($queryStatus);
      while ($daoStatus->fetch()) {
        $statusData[$daoStatus->value] = $daoStatus->label;
      }

      //get contributions
      $pcpData = array();
      $queryContrib = "SELECT count(cc.id) as contributors, sum(cc.total_amount) as amount_raised, cs.pcp_id FROM civicrm_contribution cc LEFT JOIN civicrm_contribution_soft cs ON cc.id = cs.contribution_id WHERE cs.pcp_id in (" . implode(",", $pcpIds). ") AND cs.pcp_display_in_roll = 1 AND contribution_status_id = 1 AND is_test = 0 GROUP BY cs.pcp_id";
      $daoPcp = CRM_Core_DAO::executeQuery($queryContrib);
      while ($daoPcp->fetch()) {
        $pcpData[$daoPcp->pcp_id] = array('amount_raised' => $daoPcp->amount_raised, 'contributors' => $daoPcp->contributors);
      }
    }

    foreach ($data as &$entry) {
      $entry['status'] = $statusData[$entry['status_id']];
      unset($entry['status_id']);

      if (isset($pcpData[$entry['id']])) {
        $entry['amount_raised'] = $pcpData[$entry['id']]['amount_raised'];
        $entry['contributors'] = $pcpData[$entry['id']]['contributors'];
      }

    }

    return $data;
    #$data = CRM_Core_DAO::singleValueQuery($query, array());
  }
}
