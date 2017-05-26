<?php

require_once 'civicrm_elife_article_token.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civicrm_elife_article_token_civicrm_config(&$config) {
  _civicrm_elife_article_token_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civicrm_elife_article_token_civicrm_xmlMenu(&$files) {
  _civicrm_elife_article_token_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civicrm_elife_article_token_civicrm_install() {
  _civicrm_elife_article_token_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function civicrm_elife_article_token_civicrm_postInstall() {
  _civicrm_elife_article_token_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civicrm_elife_article_token_civicrm_uninstall() {
  _civicrm_elife_article_token_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civicrm_elife_article_token_civicrm_enable() {
  _civicrm_elife_article_token_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civicrm_elife_article_token_civicrm_disable() {
  _civicrm_elife_article_token_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civicrm_elife_article_token_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civicrm_elife_article_token_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civicrm_elife_article_token_civicrm_managed(&$entities) {
  _civicrm_elife_article_token_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function civicrm_elife_article_token_civicrm_angularModules(&$angularModules) {
  _civicrm_elife_article_token_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civicrm_elife_article_token_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civicrm_elife_article_token_civix_civicrm_alterSettingsFolders($metaDataFolders);
}


function civicrm_elife_article_token_civicrm_tokens( &$tokens ){
  $generator = new CRM_ElifeArticleToken_ArticleTokenGenerator;
  $tokens['articles'] = $generator->getDefinitions();
}

function civicrm_elife_article_token_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null){
  // If $tokens['articles'] isn't set, then we have nothing to do
  if(!isset($tokens['articles'])){
    return;
  }

  $generator = new CRM_ElifeArticleToken_ArticleTokenGenerator;

  // For each token,
  foreach($tokens['articles'] as $token){
    foreach($cids as $cid){
      $values[$cid]['articles.'.$token] = $generator->getValue($token, $cid);
    }
  }
}

function civicrm_elife_article_token_get_root_dir(){
  return __DIR__;
}
