<?php
/**
 * common_evidence_logic, HumanBridge Portal
 *
 * @package   HumanBridgePortal
 * @author    FUJITSU LIMITED.
 * @copyright Copyright 2019, FUJITSU LIMITED.
 * @since     Version 1.0
 * @filesource
 */

/**
 * ベースロジック
 */
require_once(dirname(dirname(__FILE__)) . '/base/base_logic.php');
require_once(dirname(dirname(__FILE__)) . '/common/common_notice_logic.php');
require_once(dirname(dirname(__FILE__)) . '/common/common_logic.php');
/**
 * 共通証跡ビジネスロジッククラス
 *
 * @package HumanBridgePortal
 * @subpackage アプリ基盤
 * @category 共通部品
 * @author  FXS)王童
 * @version $Revision$
 */
class Common_evidence_logic extends Base_logic {

  // 田辺：スタッフとして所属するサービスを取得する
  const A00401 = 'A00401';

  // 田辺：操作対象テブルを取得する
  const A00402 = 'A00402';
  /**
   * コンストラクト
   *
   * @access protected
   */
  public function __construct() {

    parent::__construct();

    // サービスルール設定部品を読み込む
    $this->CI->load->library('service_rule');
  }


  /**
   * 更新前の証跡情報登録
   * @param Object $obj 対象テブルのモデルクラス
   * @param string $m_screen_id 画面ID
   * @param string $m_service_id サービスID
   * @param string $user_id ユーザーID
   * @return result
   */
  public function insert_history_record_before($obj, $m_screen_id, $m_service_id, $user_id=NULL) {

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    // select条件にバックアップ
    $query_param = $obj->db->ar_where;

    // データを更新或いは削除の場合
    if (Common_logic::get()->execute('is_empty', $query_param) === FALSE) {

      // DBから、情報を取得する
      $obj = $obj->get_base();

      // select条件に復帰する
      $obj->db->ar_where = $query_param;
      if (Common_logic::get()->execute('is_empty', $obj->all[0]) === TRUE) {
        // データが存在しないの場合
        return;
      }
    }

    // ユーザーIDを設定する、パラメータのユーザーIDが空の場合、グローバル変数のユーザーIDを取得する
    $user_id = Common_logic::get()->execute('is_empty', $user_id) ? $GLOBALS['user']->uid : $user_id;

    // 情報種別管理マスタ情報を取得する
    $m_information_manage = new M_information_manage();

    $m_information_manage->where('logically_delete_flg', Portal_const::LOGICALLY_UNDELETE_FLG);
    $m_information_manage->where('table_name',$obj->table);
    $m_information_manage->get_base();

    // パラメータのテブルが対象テブルではないの場合
    if (Common_logic::get()->execute('is_empty', $m_information_manage->m_info_id)) {

      $result['is_evidence_table'] = FALSE;
      
      Log_Util::$trace_logger->info("[A04] The table's name can not found from the m_information_manages.");

      // トレースログ（終了）を出力する
      if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
        Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
      }
      return $result;
    }

    // グローバル変数の操作IDが空の場合、操作IDを初期化する
    if (Common_logic::get()->execute('is_empty', $GLOBALS['operation_id'])) {

      // 操作ID
      global $operation_id;

      // レコードNo
      global $record;

      // 採番管理テーブルのモデルクラスをインスタンス化する
      $receipt = new T_receipt_number();

      // 操作IDを初期化する
      $operation_id = $receipt->get_new_number($user_id, Receipt_number_const::TABLE_AUDIT_TRAIL_MANAGE, Receipt_number_const::COLUMN_OPERATION_ID, Receipt_number_const::TYPE_AUDIT_TRAIL_MANAGE);

      // レコードNOを1に初期化する
      $record = 1;
    }
    // 証跡管理テーブル情報登録
    $this->_insert_audit_trail_manage($m_service_id, $m_screen_id, $user_id);

    // 操作管理テーブル情報登録
    $this->_insert_operation_manage($m_information_manage->m_info_id, $user_id);

    // 更新前証跡テーブル（table_name_histories）情報を登録する
    $history_tables = $this->_insert_table_history_before($obj, $user_id);

    $result['is_evidence_table'] = TRUE;
    $result['history_tables'] = $history_tables;

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    return $result;
  }

  /**
   * 更新後の証跡情報登録
   * @param object $obj 対象テブルのモデルクラス
   * @param string $sql_type sql種別
   * @param array $update_data 更新データ
   * @param string $user_id ユーザーID
   */
  public function insert_history_record_after($obj, $sql_type, $update_data, $m_service_id, $user_id=NULL) {

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    // ユーザーIDを設定する、パラメータのユーザーIDが空の場合、グローバル変数のユーザーIDを取得する
    $user_id = Common_logic::get()->execute('is_empty', $user_id) ? $GLOBALS['user']->uid : $user_id;

    // 証跡テーブル（table_name_histories）情報登録
    $this->_insert_table_history_after($obj, $update_data, $user_id, $sql_type);

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }
  }

  /**
   * 証跡管理テーブル情報登録
   *
   * @param String $m_service_id サービスID
   * @param String $m_screen_id 画面ID
   * @param String $user_id ユーザーID
   * @throws System_exception
   */
  private function _insert_audit_trail_manage($m_service_id, $m_screen_id, $user_id) {

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    // 証跡管理テーブルのモデルクラスをインスタンス化する
    $t_audit_trail_manage = new T_audit_trail_manage();

    // DBから、操作IDで証跡管理データを取得する
    $t_audit_trail_manage->where('t_operation_id', $GLOBALS['operation_id']);

    $t_audit_trail_manage->where('logically_delete_flg', Portal_const::LOGICALLY_UNDELETE_FLG);

    $t_audit_trail_manage->get_base();

    // 同じな操作は一つのレコードを設定するので、証跡管理データを取得できなかったの場合、新規のデータをデータベースに挿入する
    if (Common_logic::get()->execute('is_empty', $t_audit_trail_manage->t_operation_id)) {

      // 作成日時を取得する
      $current_time = $t_audit_trail_manage->get_generated_timestamp();
      // 操作ID
      $t_audit_trail_manage->t_operation_id = $GLOBALS['operation_id'];
      // 先行操作ID
      $t_audit_trail_manage->t_pre_operation_id = $GLOBALS['pre_operation_id'];
      // サービスID
      $t_audit_trail_manage->m_service_id = $m_service_id;
      // 画面ID
      $t_audit_trail_manage->m_screen_id = $m_screen_id;
      // 操作ユーザーID
      $t_audit_trail_manage->operater_id = $user_id;
      // 操作日時
      $t_audit_trail_manage->operate_dt = $current_time;
      // 作成者ID
      $t_audit_trail_manage->creator_id = $user_id;
      // 作成日時
      $t_audit_trail_manage->created_dt = $current_time;

      $insert_result = $t_audit_trail_manage->save_as_new();

      // 新規登録が失敗した場合
      if ($insert_result === FALSE) {

        // システム例外をスローする
        throw new System_exception(Message_const::INSERT_FAILURE_MID, NULL, __FUNCTION__);
      }
    }

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }
  }

  /**
   * 操作管理テーブル情報登録
   * @param String $m_info_id 情報ID
   * @param String $user_id ユーザーID
   * @throws System_exception
   */
  private function _insert_operation_manage($m_info_id, $user_id) {

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    // 操作管理テーブルのモデルクラスをインスタンス化する
    $t_operation_manage = new T_operation_manage();

    // DBから、操作IDと情報IDで操作管理データを取得する
    $t_operation_manage->where('t_operation_id', $GLOBALS['operation_id']);

    $t_operation_manage->where('m_info_id', $m_info_id);

    $t_operation_manage->where('logically_delete_flg', Portal_const::LOGICALLY_UNDELETE_FLG);

    $t_operation_manage->get_base();

    // 同じな操作且つ同じなテブルは一つのレコードを設定するので、操作管理データを取得できなかったの場合、新規のデータをデータベースに挿入する
    if (Common_logic::get()->execute('is_empty', $t_operation_manage->t_operation_id)) {

      // 作成日時を取得する
      $current_time = $t_operation_manage->get_generated_timestamp();
      // 操作ID
      $t_operation_manage->t_operation_id = $GLOBALS['operation_id'];
      // 情報ID
      $t_operation_manage->m_info_id = $m_info_id;
      // 作成者ID
      $t_operation_manage->creator_id = $user_id;
      // 作成日時
      $t_operation_manage->created_dt = $current_time;

      $insert_result = $t_operation_manage->save_as_new();

      // 新規登録が失敗した場合
      if ($insert_result === FALSE) {

        // システム例外をスローする
        throw new System_exception(Message_const::INSERT_FAILURE_MID, NULL, __FUNCTION__);
      }
    }

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }
  }

  /**
   * 証跡テーブル（table_name_histories）情報登録
   * @param Object $obj 対象テブルのモデルクラス
   * @param String $update_type 更新種別
   * @param string $sql_type sql種別
   * @throws System_exception
   */
  private function _insert_table_history_before($obj, $user_id) {

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    // 証跡テーブルのモデル
    // 例：ユーザ情報テブルの証跡テーブル t_users -> T_users_history
    $history_table_name = ucfirst($obj->table.'_history');

    // 論理削除フラグ～バージョンは属性（物理）の先頭にop_をつける。
    $items = array('logically_delete_flg', 'creator_id', 'created_dt', 'updater_id', 'updated_dt', 'version');

    // 証跡テーブルのリストを作成する
    $history_tables = array();

    // 新規挿入の場合：証跡テーブルの対象テブルのデータを設定しない
    if (Common_logic::get()->execute('is_empty', $obj->all) && Common_logic::get()->execute('is_empty', $obj->creator_id)=== FALSE) {

      // 対象の証跡テブルのモデルクラスをインスタンス化する
      $history_table = new $history_table_name;
      // 作成日時を取得する
      $current_time = $history_table->get_generated_timestamp();
      // 操作ID
      $history_table->t_operation_id = $GLOBALS['operation_id'];
      // レコードNo
      $history_table->record_no = $GLOBALS['record'];
      // 更新区分
      $history_table->update_type = Portal_const::UPDATE_TYPE_BEFORE;
      // 作成者ID
      $history_table->creator_id = $user_id;
      // 作成日時
      $history_table->created_dt = $current_time;

      $insert_result = $history_table->save_as_new();

      // 新規登録が失敗した場合
      if ($insert_result === FALSE) {

        // システム例外をスローする
        throw new System_exception(Message_const::INSERT_FAILURE_MID, NULL, __FUNCTION__);
      }

      // 挿入後の証跡テブル対象テブルのデータを設定ので、対象オブジェクトのデータを証跡オブジェクトに設定する
      foreach ($obj->fields as $field){

        // 論理削除フラグ～バージョンは属性（物理）の先頭にop_をつける。
        if (in_array($field, $items)) {
          $key = 'op_'.$field;
          $history_table->$key = $obj->$field;
        } else {
          $history_table->$field = $obj->$field;
        }
      }

      // apply_end_dtはデータベースにデフォルトを設定するので、ソスが省略するかもしれない、初期化する
      if (array_search('apply_end_dt', $obj->fields) != FALSE &&
          Common_logic::get()->execute('is_empty', $obj->apply_end_dt)) {
        $history_table->apply_end_dt = date(Portal_const::MAX_APPLY_END_DT);
      }

      // newest_browse_dtはデータベースにデフォルトを設定するので、ソスが省略するかもしれない、初期化する
      if (array_search('newest_browse_dt', $obj->fields) != FALSE &&
          Common_logic::get()->execute('is_empty', $obj->newest_browse_dt)) {
        $history_table->newest_browse_dt = date('1000-01-01');
      }

      // cellular_certify_codeはデータベースにデフォルトを設定するので、ソスが省略するかもしれない、初期化する
      if (array_search('cellular_certify_code', $obj->fields) != FALSE &&
          Common_logic::get()->execute('is_empty', $obj->cellular_certify_code)) {
        $history_table->cellular_certify_code = 0;
      }
      
      // statusはデータベースにデフォルトを設定するので、ソスが省略するかもしれない、初期化する
      if (array_search('status', $obj->fields) != FALSE &&
      Common_logic::get()->execute('is_empty', $obj->status)) {
        $history_table->status = 0;
      }
      
      // password_mishit_countsはデータベースにデフォルトを設定するので、ソスが省略するかもしれない、初期化する
      if (array_search('password_mishit_counts', $obj->fields) != FALSE &&
      Common_logic::get()->execute('is_empty', $obj->password_mishit_counts)) {
        $history_table->password_mishit_counts = 0;
      }
      
      // lockout_flgはデータベースにデフォルトを設定するので、ソスが省略するかもしれない、初期化する
      if (array_search('lockout_flg', $obj->fields) != FALSE &&
      Common_logic::get()->execute('is_empty', $obj->lockout_flg)) {
        $history_table->lockout_flg = 0;
      }

      // 論理削除フラグと作成日時とバージョンはデータベースにデフォルトを設定するので、ソスが省略するかもしれない、初期化する
      $history_table->op_created_dt = $current_time;
      $history_table->op_logically_delete_flg = 0;
      $history_table->op_version = 1;

      array_push($history_tables, $history_table);

      // 同じなデータを変更、レコードNOを同じ値、違うデータを変更、レコードNOを一つ追加する
      $GLOBALS['record'] += 1;

      // トレースログ（終了）を出力する
      if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
        Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
      }
      return $history_tables;
    }

    // 更新或いは削除の場合：証跡テーブル中対象テブルのデータを対象テブルの内容に設定する
    foreach ($obj->all as $o) {

      // 対象テブルのモデルクラスをインスタンス化する
      $history_table = new $history_table_name;
      // 作成日時を取得する
      $current_time = $history_table->get_generated_timestamp();

      $obj_arr = (array) $o->stored;

      // 対象オブジェクトのデータを証跡オブジェクトに設定する
      foreach ($obj_arr as $key=>$value) {

        // 論理削除フラグ～バージョンは属性（物理）の先頭にop_をつける。
        if (in_array($key, $items)) {
          $key = 'op_'.$key;
          $history_table->$key = $value;
        } else {
          $history_table->$key = $value;
        }
      }
      // 操作ID
      $history_table->t_operation_id = $GLOBALS['operation_id'];
      // レコードNo
      $history_table->record_no = $GLOBALS['record'];
      // 更新区分
      $history_table->update_type = Portal_const::UPDATE_TYPE_BEFORE;
      // SQL種別
      $history_table->sql_type = $sql_type;
      // 作成者ID
      $history_table->creator_id = $user_id;
      // 作成日時
      $history_table->created_dt = $current_time;
      // version
      $history_table->version = 1;

      $insert_result = $history_table->save_as_new();

      array_push($history_tables, $history_table);

      // 同じなデータを変更、レコードNOを同じ値、違うデータを変更、レコードNOを一つ追加する
      $GLOBALS['record'] += 1;

      // 新規登録が失敗した場合
      if ($insert_result === FALSE) {

        // システム例外をスローする
        throw new System_exception(Message_const::INSERT_FAILURE_MID, NULL, __FUNCTION__);
      }
    }

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    return $history_tables;
  }

  /**
   * 証跡テーブル（table_name_histories）情報登録
   * @param Object $obj 対象テブルのモデルクラス
   * @param String $update_type 更新種別
   * @param array $update_data 更新データ
   * @param string $user_id ユーザーID
   * @param string $sql_type sql種別
   * @throws System_exception
   */
  private function _insert_table_history_after($obj, $update_data, $user_id, $sql_type=NULL) {

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    // 論理削除フラグ～バージョンは属性（物理）の先頭にop_をつける。
    $items = array('logically_delete_flg', 'creator_id', 'created_dt', 'updater_id', 'updated_dt', 'version');

    foreach ($obj as $o) {

      // sql_typeが更新の場合
      if ($sql_type == Portal_const::SQL_TYPE_UPDATE) {

        // 更新操作対象テブルの内容
        foreach ($update_data as $key=>$value){

          // 論理削除フラグ～バージョンは属性（物理）の先頭にop_をつける。
          if (in_array($key, $items)) {
            $key = 'op_'.$key;
            $o->$key = $value;
          } else {
            $o->$key = $value;
          }
        }
        // 更新日時を取得する
        $current_time = $o->get_generated_timestamp();
        // 操作対象テブルの更新日時
        $o->op_updated_dt = $current_time;
        // 操作対象テブルのversion
        $o->op_version = $o->op_version + 1;
      }

      // sql_typeが削除の場合
      if ($sql_type == Portal_const::SQL_TYPE_DELETE) {

        $items = array('t_operation_id', 'record_no', 'logically_delete_flg', 'creator_id', 'created_dt', 'updater_id', 'updated_dt', 'version');

        // 更新操作対象テブルの内容
        foreach ($o->fields as $field){

          // 論理削除フラグ～バージョンは属性（物理）の先頭にop_をつける。
          if (in_array($field, $items)) {
            $o->$field = $o->stored->$field;
          } else {
            $o->$field = NULL;
          }
        }
      }

      // 更新区分
      $o->update_type = Portal_const::UPDATE_TYPE_AFTER;
      // SQL種別
      $o->sql_type = $sql_type;

      $insert_result = $o->save_as_new();

      // 新規登録が失敗した場合
      if ($insert_result === FALSE) {

        // システム例外をスローする
        throw new System_exception(Message_const::INSERT_FAILURE_MID, NULL, __FUNCTION__);
      }
    }

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }
  }

  /**
   * 監査証跡の通知処理
   * @param String $m_service_id サービスID
   * @param String $user_id ユーザーID
   */
  public function notify_evidence($m_service_id, $user_id = NULL) {

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }
    // S 2019/11/08 FXS)王童 監査証跡の通知処理前に、トランザクションをコミット

    // Personalデーターベーストランザクションをコミット
    $this->CI->db->trans_commit();

    foreach ($this->CI->db_array as $additional_db) {
      // 追加データーベースとのトランザクションをコミットする
      $additional_db->trans_commit();
    }

    // Personalデーターベースに対するトランザクションを開始する
    $this->CI->db->trans_begin();

    // ←★Personalデーターベース以外はfor文でまわす
    foreach ($this->CI->db_array as $additional_db) {

      // 追加データーベースへのトランザクションを開始する
      $additional_db->trans_begin();
    }

    // E 2019/11/08 FXS)王童 監査証跡の通知処理前に、トランザクションをコミット

    // グローバル変数の操作IDが空の場合、対応テブルではない
    if (Common_logic::get()->execute('is_empty', $GLOBALS['operation_id'])) {

      // トレースログ（終了）を出力する
      if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
        Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
      }
      return;
    }
    
    // ユーザーIDを設定する、パラメータのユーザーIDが空の場合、グローバル変数のユーザーIDを取得する
    $user_id = Common_logic::get()->execute('is_empty', $user_id) ? (string)$GLOBALS['user']->uid : $user_id;

    // サービスIDを取得できた場合、
    if (Common_logic::get()->execute('is_empty',$m_service_id) === FALSE && $m_service_id != 'PORTAL') {

      $this->_call_notify_api($m_service_id, $user_id);
      return ;
    }

    // サービスIDを取得できなかった場合

    // 「グループ構成ユーザーテーブル」のモデルクラスをインスタンス化する
    $t_group_configure_user = new T_group_configure_user();

    // パラメータを設定する
    $param = array($user_id, $user_id);

    // 更新処理を行うユーザーが所属するすべてのサービスを取得する:スタッフとして所属するサービスとサービス利用者として所属するサービス
    $services = $t_group_configure_user->get_user_service_info(Common_evidence_logic::A00401, $param);

    foreach ($services as $service) {

      $this->_call_notify_api($service->m_service_id, $user_id);
    }

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }
  }

  /**
   * 監査証跡の通知処理
   * @param String $service_id
   * @param String $user_id
   * @throws System_exception
   * @return
   */
  private function _call_notify_api($service_id, $user_id) {

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    // サービスルール設定マスタの通知の要否の設定値を取得する、
    $signup_notification = $this->CI->service_rule->item(Portal_const::TRAIL_INFO_KEY, Portal_const::TRAIL_INFO_CATEGORY, $service_id);

    // 設定が１：通知する場合のみ、通知を行う。
    if($signup_notification != Portal_const::SERVICE_RULE_VALUE_1) {

      Log_Util::$trace_logger->info('The trail_info_flag which was found from the m_service_rules by service_id : '.$service_id.' is not 1.');
      return ;
    }

    // パスの変数を取得
    $humanbridge_path = $this->CI->config->item(WebAPI_const::WEBAPI_SERVICE_PATH, WebAPI_const::CATEGORY_WEBAPI);

    // データ取得が失敗しました。
    if(Common_logic::get()->execute('is_empty', $humanbridge_path)) {

      // トレースログ（終了）を出力する
      if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
        Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
      }
      Log_Util::$trace_logger->info('The humanbridge_path can not found from the m_system_configs');
      throw new System_exception(Message_const::CANNOT_GET_MID, array('システム設定マスタ', 'webapiのポータルパス'), __FUNCTION__);
    }

    $m_service = new M_service();
    $m_service = $m_service->get_service_info_by_id($service_id);

    // データ取得が失敗しました。
    if(Common_logic::get()->execute('is_empty', $m_service->service_path)) {

      // トレースログ（終了）を出力する
      if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
        Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
      }
      Log_Util::$trace_logger->info('The service_path of the service : '.$service_id.' is empty');
      return ;
    }

    $webapi_url = 'http://' . $humanbridge_path . '/' . $m_service->service_path . '/portal/notification/trail';

    // 通知処理のリクエストボディを取得
    $body = $this->_get_body_info($service_id);

    $webapi_url = $webapi_url.'?'.$body;

    // 通知処理を行う
    file_get_contents($webapi_url);

    $response_header = explode(' ', $http_response_header[0]);
    $http_result = $response_header[1];

    // 通信エラーの場合(HTTPステータスが返却されない場合)は”communication_error”を設定
    if(Common_logic::get()->execute('is_empty', $http_result)) {
      $http_result = 'communication_error';
    }
    // ログ出力内容を作成します
    $log_content = $this->_get_log_content($webapi_url, $http_result, $service_id);

    Log_Util::notice_log(Log_Util::$INFO, $log_content, null);

    // 登録通知WebAPIのレスポンスが200(OK)以外の場合
    if($http_result != WebAPI_const::RESPONSE_CODE_OK) {

      // 通知状況管理テーブルのレコード追加
      Common_notice_logic::get()->execute('add_notice_status', Portal_const::NOTIFICATION_DIVISION_EVIDNECE, $webapi_url, '{}', $http_result, $service_id, NULL, NULL, $user_id);
    }

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }
  }

  /**
   * ログ出力内容を作成します
   *
   * @param string $webapi_url   通知先URL
   * @param string $http_result   通知結果
   * @param string $service_id   サービスＩＤ
   * @return string $file_content ログ出力内容
   */
  private function _get_log_content($webapi_url, $http_result, $service_id){

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    // ファイル出力内容を初期化する
    $file_content = '';

    // タイムZONEを東京に設定する
    date_default_timezone_set('Asia/Tokyo');
    $curr_date = new DateTime('NOW');

    // 通知日時
    $file_content = '"' . substr($curr_date->format('Y-m-d H:i:s.u'),0,23) . '"';

    // 通知種別 :1：サービス利用者登録
    $file_content = $file_content . ',"' . Portal_const::NOTIFICATION_DIVISION_EVIDNECE . '"';

    // オンライン・バッチ種別:1：オンライン
    $file_content = $file_content . ',"1"';

    // 通知先URL:WebAPIのURL
    $file_content = $file_content . ',"' . $webapi_url . '"';

    // 通知結果
    $file_content = $file_content . ',"' . $http_result . '"';

    // サービスID
    $file_content = $file_content . ',"' . $service_id . '"';

    $file_content = $file_content.',,';
    
    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    return $file_content;
  }

  /**
   * 通知処理のリクエストボディの設定値を取得
   * @param string $service_id サービスID
   * @return array $result ボディの設定値
   */
  private function _get_body_info($service_id){

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    $body_info = $this->_get_data_list();

    $result = 'operation_id='.$GLOBALS['operation_id'].'&service_id='.$service_id.'&info_id='.$body_info['info_id'].'&sql_type_id='.$body_info['sql_type_id'];

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    return $result;
  }

  /**
   * 変更された対象の情報を取得します
   * @return array $patient_extensions 変更された対象の情報
   */
  private function _get_data_list(){

    // トレースログ（開始）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_start(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_START, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    $t_audit_trail_manage = new T_audit_trail_manage();

    $param = array($GLOBALS['operation_id']);

    // SQL文を取得する
    $sql = $this->CI->sql_manager->get_sql(Common_evidence_logic::A00402);

    // 検索結果を取得する
    $query_results = $t_audit_trail_manage->query_base($sql, $param)->result();

    $t_pre_operation_id = $GLOBALS['pre_operation_id'];
    
    // 一時テーブルのm_info_id
    $temp_info_ids = array('101', '102', '103', '108', '109','110');

    // 操作IDに先行操作IDが紐づいている場合、先行操作IDを操作IDとして、再度証跡管理テーブルを検索し、取得した情報IDも設定
    if ($t_pre_operation_id !== NULL) {
      $pre_query_results = $t_audit_trail_manage->query_base($sql, array($t_pre_operation_id))->result();
      
      foreach($pre_query_results as $pre_result) {
        if (in_array($pre_result->m_info_id, $temp_info_ids)) {
          array_push($query_results, $pre_result);
        }
      }
    }

    // XXX証跡テーブル．SQL種別 操作　SQL名 （1:INSERT、2:UPDATE,3:DELETE）
    $sql_kind_arr = array();

    // 情報種別管理マスタ．情報ID
    $info_id_arr = array();

    foreach($query_results as $result) {
      
      // 仮登録の時に、一時テーブルのm_info_idを通知しない
      $temp_info_ids = array('101', '102', '103', '108', '109','110');
      if ($t_pre_operation_id == NULL && in_array($result->m_info_id, $temp_info_ids) ) {
        continue;
      }

      array_push($info_id_arr, $result->m_info_id);

      // 証跡テーブルのモデル
      $history_table_name = ucfirst($result->table_name.'_history');

      // 対象テブルのモデルクラスをインスタンス化する
      $objs = new $history_table_name;
      $objs->where('t_operation_id', $GLOBALS['operation_id']);
      $objs->where_in('sql_type',array(Portal_const::SQL_TYPE_INSERT, Portal_const::SQL_TYPE_UPDATE, Portal_const::SQL_TYPE_DELETE));
      $objs->get_base();

      foreach ($objs as $obj) {
        array_push($sql_kind_arr, $obj->sql_type);
      }
    }

    // 重複の情報IDを削除する
    $info_id_arr = array_unique($info_id_arr);
    
    sort($info_id_arr);

    $body_info['info_id'] = implode(',', $info_id_arr);

    // SQL種別が複数ある場合、数字が一番大きい番号を設定
    $body_info['sql_type_id'] = max($sql_kind_arr);

    // トレースログ（終了）を出力する
    if (Log_Util::is_enable_for_trace_log(Log_Util::$DEBUG)) {
      Log_Util::trace_log_end(Log_Util::$DEBUG, Message_const::LOG_FUNCTION_END, NULL, __FILE__, __CLASS__, __FUNCTION__, __LINE__);
    }

    return $body_info;
  }

}
/* End of file common_evidence_logic.php */
/* Location: ./application/controllers/common/common_evidence_logic.php */
