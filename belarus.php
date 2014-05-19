<?php

require_once 'belarus.civix.php';

function belarus_listcounties() {
 $counties = array(
   //belarus counties
		1996 => array(
      'Барановичский',
			'Берёзовский',
			'Брестский',
      'Ганцевичский',
      'Дрогичинский',
      'Жабинковский',
      'Ивановский',
      'Ивацевичский',
      'Каменецкий',
      'Кобринский',
      'Лунинецкий',
      'Ляховичский',
      'Малоритский',
      'Пинский',
      'Пружанский',
      'Столинский',
    ),
    2001 => array(
      'Бешенковичский',
      'Браславский',
      'Верхнедвинский',
			'Витебский',
      'Глубокский',
      'Городокский',
      'Докшицкий',
      'Дубровенский',
      'Лепельский',
      'Лиозненский',
      'Миорский',
      'Оршанский',
      'Полоцкий',
      'Поставский',
      'Россонский',
      'Сенненский',
      'Толочинский',
      'Ушачский',
      'Чашникский',
      'Шарковщинский',
      'Шумилинский',
    ),
    2000 => array(
      'Березинский',
      'Борисовский',
      'Вилейский',
      'Воложинский',
      'Дзержинский',
      'Клецкий',
      'Копыльский',
      'Крупский',
      'Любанский',
      'Минский',
      'Молодечненский',
      'Мядельский',
      'Несвижский',
      'Пуховичский',
      'Слуцкий',
      'Смолевичский',
      'Солигорский',
      'Стародорожский',
      'Столбцовский',
      'Узденский',
      'Червенский',
    ),
   1999 => array(
     'Белыничский',
     'Бобруйский',
     'Быховский',
     'Глусский',
     'Горецкий',
     'Дрибинский',
     'Кировский',
     'Климовичский',
     'Кличевский',
     'Костюковичский',
     'Краснопольский',
     'Кричевский',
     'Круглянский',
     'Могилевский',
     'Мстиславский',
     'Осиповичский',
     'Славгородский',
     'Хотимский',
     'Чаусский',
     'Чериковский',
     'Шкловский',
   ),
   1998 => array(
   	 'Берестовицкий',
     'Волковысский',
     'Вороновский',
     'Гродненский',
     'Дятловский',
     'Зельвенский',
     'Ивьевский',
     'Кореличский',
     'Лидский',
     'Мостовский',
     'Новогрудский',
     'Островецкий',
     'Ошмянский',
     'Свислочский',
     'Слонимский',
   ),
  1997 => array(
    'Брагинский',
    'Буда-Кошелевский',
    'Ветковский',
    'Гомельский',
    'Добрушский',
    'Ельский',
    'Житковичский',
    'Жлобинский',
    'Калинковичский',
    'Кормянский',
    'Лельчицкий',
    'Мозырский',
    'Октябрьский',
    'Петриковский',
    'Речицкий',
    'Рогачевский',
    'Светлогорский',
    'Хойникский',
    'Чечерский',
    ),
  13769 => array(
    'Заводской',
    'Ленинский',
    'Московский',
    'Октябрьский',
    'Партизанский',
    'Первомайский',
    'Советский',
    'Фрунзенский',
    'Центральный',
    ),
  );
  return $counties;
}

function belarus_updateStates() {
	CRM_Core_DAO::executeQuery("UPDATE civicrm_state_province SET name='Витебская область' WHERE id=2001", CRM_Core_DAO::$_nullArray);
	CRM_Core_DAO::executeQuery("UPDATE civicrm_state_province SET name='Минская область' WHERE id=2000", CRM_Core_DAO::$_nullArray);
  CRM_Core_DAO::executeQuery("UPDATE civicrm_state_province SET name='Могилевская область' WHERE id=1999", CRM_Core_DAO::$_nullArray);
  CRM_Core_DAO::executeQuery("UPDATE civicrm_state_province SET name='Гродненская область' WHERE id=1998", CRM_Core_DAO::$_nullArray);
  CRM_Core_DAO::executeQuery("UPDATE civicrm_state_province SET name='Гомельская область' WHERE id=1997", CRM_Core_DAO::$_nullArray);
  CRM_Core_DAO::executeQuery("UPDATE civicrm_state_province SET name='Брестская область' WHERE id=1996", CRM_Core_DAO::$_nullArray);
 // CRM_Core_DAO::executeQuery("INSERT into  civicrm_state_province (id, name, country_id) VALUES (13769,'Минск',1019)", CRM_Core_DAO::$_nullArray);
}

function belarus_loadcounties() {

  $counties = belarus_listcounties();

  static $dao = NULL;
  if (!$dao) {
    $dao = new CRM_Core_DAO();
  }

  // go state-by-state to check existing counties

  foreach ($counties as $id => $state) {
    $check = "SELECT name FROM civicrm_county WHERE state_province_id = $id";
    $results = CRM_Core_DAO::executeQuery($check);
    $existing = array();
    while ($results->fetch()) {
      $existing[] = $results->name;
    }

    // identify counties needing to be loaded
    $add = array_diff($state, $existing);
    
    $insert = array();
    foreach ($add as $county) {
      $countye = $dao->escape($county);
      $insert[] = "('$countye', $id)";
    }
    
    // put it into queries of 50 counties each
    for($i = 0; $i < count($insert); $i = $i+50) {
      $inserts = array_slice($insert, $i, 50);
      $query = "INSERT INTO civicrm_county (name, state_province_id) VALUES ";
      $query .= implode(', ', $inserts);
      CRM_Core_DAO::executeQuery($query);
    }
  }
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function belarus_civicrm_install() {
  belarus_updateStates();
  belarus_loadcounties();
}
/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function belarus_civicrm_enable() {
  belarus_updateStates();
  belarus_loadcounties();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function belarus_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  belarus_updateStates();
  belarus_loadcounties();
}
