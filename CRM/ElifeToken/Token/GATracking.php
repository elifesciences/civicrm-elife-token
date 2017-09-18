<?php

class CRM_ElifeToken_Token_GATracking{

  function get(){
    return 'utm_source=content_alert&utm_medium=email&utm_content=fulltext&utm_campaign=' . date('j-F-y') . '-elife-alert';
  }
}
